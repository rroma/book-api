<?php

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BookVoter extends Voter
{
    const BANNED_AUTHORS = [
        'Darth Wader'
    ];

    protected function supports($attribute, $subject)
    {
        if ($attribute == 'BOOK_ADD') {
            return true;
        }

        return in_array($attribute, ['BOOK_EDIT', 'BOOK_DELETE'])
            && $subject instanceof Book;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($attribute == 'BOOK_ADD') {
            if (in_array($user->getAuthorPseudonym(), self::BANNED_AUTHORS)) {
                return false;
            }

            return true;
        }

        if($subject->getAuthor() === $user) {
            return true;
        }

        return false;
    }
}
