<?php

namespace App\Tests\Functional;

use App\Test\ApiTestCase;
use App\Test\UserTestTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class JWTAuthTest extends ApiTestCase
{
    use UserTestTrait;
    use RefreshDatabaseTrait;

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

    public function testJWTAuthFail()
    {
        $email = 'non-existent@user.com';
        $password = 'dummypass';

        $response = $this->request('POST', '/api/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}