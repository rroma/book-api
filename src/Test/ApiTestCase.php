<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as BaseTestCase;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTestCase extends BaseTestCase
{
    protected $client;
    protected $authToken;

    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    protected function login(User $user): void
    {
        /** @var JWTManager $jwtManager */
        $jwtManager = $this->client->getContainer()
            ->get('lexik_jwt_authentication.jwt_manager');

        $this->authToken = $jwtManager->create($user);
    }

    protected function logout(): void
    {
        $this->authToken = null;
    }

    protected function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if ($this->authToken) {
            $headers = $options['headers'];
            if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
                $headers['Authorization'] = 'Bearer '.$this->authToken;
            }
            $options['headers'] = $headers;
        }

        return $this->client->request($method, $url, $options);
    }
}