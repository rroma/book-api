<?php

namespace App\Test;

use App\Entity\Book;
use App\Entity\Media;
use App\Entity\User;

trait MediaTestTrait
{
    public function createMedia(string $filePath): Media
    {
        $media = new Media();
        $media->setFilePath($filePath);

        $manager = $this->client->getContainer()
            ->get('doctrine')->getManager();

        $manager->persist($media);
        $manager->flush();

        return $media;
    }
}