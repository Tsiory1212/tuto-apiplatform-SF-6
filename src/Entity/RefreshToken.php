<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity()]
class RefreshToken extends BaseRefreshToken
{


    /**
     * (Je ne sais pas si c'est une best practice)
     * 
     * Vue que "user_identity_field" dans "gesdinet_jwt_refresh_token" ne prend pas "id" comme username dans la table BDD refresh_token
     * j'ai overridé la fontion static "createForUserWithTtl", et j'ai changer $model->setUsername()
     * 
     * NB : l'idée ici c'est de prendre l'id de l'User comme username, car l'email peut être changer par l'User à un moment donné
     */
    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        $valid = new \DateTime();
        $valid->modify('+'.$ttl.' seconds');

        $model = new static();
        $model->setRefreshToken($refreshToken);
        $model->setUsername($user->getId()); // <== Focus
        $model->setValid($valid);

        return $model;
    }

}
