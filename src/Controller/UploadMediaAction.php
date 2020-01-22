<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Media;
use App\Form\MediaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadMediaAction
{
    protected $factory;
    protected $validator;

    public function __construct(FormFactoryInterface $factory, ValidatorInterface $validator)
    {
        $this->factory = $factory;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): Media
    {
        echo var_export($_FILES);exit;
        $media = new Media();
        $form = $this->factory->create(MediaType::class, $media);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $media;
        }

        throw new ValidationException(
            $this->validator->validate($media)
        );
    }
}