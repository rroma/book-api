<?php

namespace App\Test;

use App\Entity\User;

trait UserTestTrait
{
    public function createUser(string $pseudonym, string $email, string &$password = null): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setAuthorPseudonym($pseudonym);
        if (!$password) {
            $password = bin2hex(random_bytes(5));
        }
        $encoded = $this->client->getContainer()
            ->get('security.password_encoder')
            ->encodePassword($user, $password);
        $user->setPassword($encoded);

        $manager = $this->client->getContainer()
            ->get('doctrine')->getManager();

        $manager->persist($user);
        $manager->flush();

        return $user;
    }
}