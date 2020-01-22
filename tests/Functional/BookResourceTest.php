<?php

namespace App\Tests\Functional;

use App\Test\ApiTestCase;
use App\Test\BookTestTrait;
use App\Test\MediaTestTrait;
use App\Test\UserTestTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BookResourceTest extends ApiTestCase
{
    use UserTestTrait;
    use BookTestTrait;
    use MediaTestTrait;
    use RefreshDatabaseTrait;

    public function testAddBookRequiresAuth()
    {
        $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'The Great Gatsby',
                'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
                'price' => '25.90',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddBookOk()
    {
        $user = $this->createUser('Scott Fitzgerald', 'Scott@Fitzgerald.net');

        $this->login($user);

        $media = $this->createMedia('book-cover-test.png');

        $response = $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'The Great Gatsby',
                'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
                'price' => '25.90',
                'coverImage' => '/api/media/'.$media->getId()
            ]
        ]);

        $this->client->getContainer()->get('doctrine')->getManager()->refresh($user);
        $this->assertResponseStatusCodeSame(201);

        $book = $user->getBooks()->get(0);

        $data = $response->toArray();
        $this->assertEquals([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books/'.$book->getId(),
            '@type' => 'Book',
            'title' => 'The Great Gatsby',
            'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
            'price' => '25.90',
            'author' => 'Scott Fitzgerald',
            'coverImage' => '/media/book-cover-test.png'
        ], $data);
    }

    public function testDarthWaderCantAddBook()
    {
        $user = $this->createUser('Darth Wader', 'darth@wader.net');

        $this->login($user);

        $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Rise and Fall of Darth Wader',
                'description' => 'Rise and Fall of Darth Wader',
                'price' => '21.50',
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditBookOk()
    {
        $author = 'George Orwell';
        $user = $this->createUser($author, 'george.orwell@mail.ru');
        $book = $this->createBook($user, 'Animal Farm', 'Some description', 19.90);

        $this->login($user);

        $newTitle = 'New Name';
        $newDescription = 'New Description';
        $newPrice = "30";

        $response = $this->request('PATCH', '/api/books/'.$book->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => $newTitle,
                'description' => $newDescription,
                'price' => $newPrice,
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $decoded = json_decode($response->getContent(), true);
        $this->assertEquals([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books/'.$book->getId(),
            '@type' => 'Book',
            'title' => $newTitle,
            'description' => $newDescription,
            'price' => $newPrice,
            'author' => $author,
            'coverImage' => '',
        ], $decoded);
    }

    public function testEditAnotherAuthorBookFail()
    {
        $georgeOrwell = $this->createUser('George Orwell', 'george.orwell@mail.ru');
        $darthWader = $this->createUser('Darth Wader', 'darth.wader@gmail.com');
        $book = $this->createBook($georgeOrwell, 'Animal Farm', 'Some description', 19.90);

        $this->login($darthWader);

        $this->request('PATCH', '/api/books/'.$book->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => []
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteBookRequiresAuth()
    {
        $author = 'George Orwell';
        $user = $this->createUser($author, 'george.orwell@mail.ru');
        $book = $this->createBook($user, 'Animal Farm', 'Some description', 19.90);

        $this->request('DELETE', '/api/books/'.$book->getId());

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteBookOk()
    {
        $author = 'George Orwell';
        $user = $this->createUser($author, 'george.orwell@mail.ru');
        $book = $this->createBook($user, 'Animal Farm', 'Some description', 19.90);

        $this->login($user);

        $this->request('DELETE', '/api/books/'.$book->getId());

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteAnotherAuthorBookFail()
    {
        $georgeOrwell = $this->createUser('George Orwell', 'george.orwell@mail.ru');
        $darthWader = $this->createUser('Darth Wader', 'darth.wader@gmail.com');
        $book = $this->createBook($georgeOrwell, 'Animal Farm', 'Some description', 19.90);

        $this->login($darthWader);

        $this->request('DELETE', '/api/books/'.$book->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testListBookOk()
    {
        $georgeOrwell = $this->createUser('George Orwell', 'george.orwell@mail.ru');
        $book = $this->createBook($georgeOrwell, 'Animal Farm', 'Some description', 19.90);

        $response = $this->request('GET', '/api/books');

        $this->assertResponseStatusCodeSame(200);

        $data = $response->toArray();
        $this->assertCount(1, $data['hydra:member']);
        $this->assertEquals([
            '@id' => '/api/books/'.$book->getId(),
            '@type' => 'Book',
            'title' => 'Animal Farm',
            'description' => 'Some description',
            'price' => '19.9',
            'author' => 'George Orwell',
            'coverImage' => '',
        ], $data['hydra:member'][0]);
    }

    public function testGetBookOk()
    {
        $georgeOrwell = $this->createUser('George Orwell', 'george.orwell@mail.ru');
        $book = $this->createBook($georgeOrwell, 'Animal Farm', 'Some description', 19.90);

        $response = $this->request('GET', '/api/books/'.$book->getId());

        $this->assertResponseStatusCodeSame(200);

        $data = $response->toArray();
        $this->assertEquals([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books/'.$book->getId(),
            '@type' => 'Book',
            'title' => 'Animal Farm',
            'description' => 'Some description',
            'price' => '19.9',
            'author' => 'George Orwell',
            'coverImage' => '',
        ], $data);
    }
}