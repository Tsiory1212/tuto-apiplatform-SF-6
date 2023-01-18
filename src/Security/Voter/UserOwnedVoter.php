<?php

namespace App\Security\Voter;

use App\Entity\Interface_\UserOwnedInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserOwnedVoter extends Voter
{
    public const POST_EDIT = 'POST_EDIT';
    public const POST_VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        /**
         * Très attention, choisissez "application/ld+json" comme header "Accept", 
         * car si "application/json" est choisi, ce voter ne traite que le premier élément $subject
         */
        return in_array($attribute, [self::POST_EDIT, self::POST_VIEW]) && $subject instanceof UserOwnedInterface;
    }

    /**
     * @param UserOwnedInterface $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $owner = $subject->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::POST_VIEW:
                return $owner && $owner->getId() === $user->getId(); # Seul le propriétaire du ressource peut faire l'action (POST_VIEW)
        }

        return false;
    }
}
