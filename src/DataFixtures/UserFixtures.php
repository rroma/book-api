<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $authors = [
            [
                'pseudonym' => 'J. K. Rowling',
                'email' => 'j.k@rowling.com',
                'password' => 'AjADuFyh',
            ],
            [
                'pseudonym' => 'Darth Wader',
                'email' => 'darth.wader@gmail.com',
                'password' => 'phDJT8kc',
            ],
            [
                'pseudonym' => 'Stephen King',
                'email' => 'stephen.king@mail.com',
                'password' => 'TWpPWnQq',
            ]
        ];

        foreach ($authors as $author) {
            $user = new User();
            $user->setEmail($author['email']);
            $user->setAuthorPseudonym($author['pseudonym']);
            $password = $this->encoder->encodePassword($user, $author['password']);
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
