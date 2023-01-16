<?php
namespace App\Entity\Interface_;


use App\Entity\User;

interface UserOwnedInterface {
    
    public function getUser(): ?User;

    public function setUser(?User $user): self;
}