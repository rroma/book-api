<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Book;
use App\Entity\Media;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Storage\StorageInterface;

class ResolveMediaContentUrlSubscriber implements EventSubscriberInterface
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function onPreSerialize(GetResponseForControllerResultEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        $shouldResolve = false;
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        if ($attributes) {
            $shouldResolve = \is_a($attributes['resource_class'], Media::class, true)
                || \is_a($attributes['resource_class'], Book::class, true);
        }

        if (!$shouldResolve) {
            return;
        }

        $objects = $controllerResult;

        if (!is_iterable($objects)) {
            $objects = [$objects];
        }

        foreach ($objects as $object) {
            if (!$object instanceof Media) {
                continue;
            }

            $this->resolveUrl($object);
        }

        foreach ($objects as $object) {
            if (!$object instanceof Book) {
                continue;
            }

            $media = $object->getCoverImage();

            if ($media) {
                $this->resolveUrl($media);
            }
        }
    }

    protected function resolveUrl(Media $media)
    {
        $media->setContentUrl($this->storage->resolveUri($media, 'file'));
    }
}