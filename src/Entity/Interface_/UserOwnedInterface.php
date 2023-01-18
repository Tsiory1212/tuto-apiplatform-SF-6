<?php
namespace App\Entity\Interface_;


use App\Entity\User;

/**
 * Cet Iterface nous sert à mettre une condition dans l'EXTENSION DOCTRINE ou dans les NORMALIZATION ou dans un VOTER pour filtrer les ressources
 * (Voter ==> UserOwnedVoter> , DoctrineExtension ==> CurrentUserExtension, Normalization ==> ApiAuthNormalizer) 
 */
interface UserOwnedInterface {
    
    public function getUser(): ?User;

    public function setUser(?User $user): self;
}