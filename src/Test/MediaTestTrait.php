<?php

namespace App\Test;

use App\Entity\Book;
use App\Entity\Media;
use App\Entity\User;

trait MediaTestTrait
{
    public function createMedia(User $author, string $title, string $description, float $price): Book
    {
        $media = new Media();
        $media->setAuthor($author);
        $media->setTitle($title);
        $media->setDescription($description);
        $media->setPrice($price);

        $manager = $this->client->getContainer()
            ->get('doctrine')->getManager();

        $manager->persist($media);
        $manager->flush();

        return $media;
    }
}