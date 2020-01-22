<?php

namespace App\Tests\Functional;

use App\Test\ApiTestCase;
use App\Test\UserTestTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class BookResourceTest extends ApiTestCase
{
    use UserTestTrait;
    use RefreshDatabaseTrait;

    public function testPublishBookRequiresAuth()
    {
        $response = $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'The Great Gatsby',
                'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
                'price' => '25.90',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditBookRequiresAuth()
    {
        $response = $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'The Great Gatsby',
                'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
                'price' => '25.90',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testPublishBookOk()
    {
        $user = $this->createUser('Scott Fitzgerald', 'Scott@Fitzgerald.net');

        $this->login($user);

        $response = $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'The Great Gatsby',
                'description' => 'This exemplary novel of the Jazz Age has been acclaimed by generations of readers.',
                'price' => '25.90',
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testDarthWaderCantPublishBook()
    {
        $user = $this->createUser('Darth Wader', 'darth@wader.net');

        $this->login($user);

        $response = $this->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Rise and Fall of Darth Wader',
                'description' => 'Rise and Fall of Darth Wader',
                'price' => '21.50',
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testJWTAuthOk()
    {
        $email = 'j.k@rowling.com';
        $password = '';
        $this->createUser('J. K. Rowling', $email, $password);

        $response = $this->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $decoded = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $decoded);
        $this->assertNotEmpty($decoded['token']);
    }
}