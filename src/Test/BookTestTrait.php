<?php

namespace App\Test;

use App\Entity\Book;
use App\Entity\User;

trait BookTestTrait
{
    public function createBook(User $author, string $title, string $description, float $price): Book
    {
        $book = new Book();
        $book->setAuthor($author);
        $book->setTitle($title);
        $book->setDescription($description);
        $book->setPrice($price);

        $manager = $this->client->getContainer()
            ->get('doctrine')->getManager();

        $manager->persist($book);
        $manager->flush();

        return $book;
    }
}