<?php
namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Interface_\UserOwnedInterface;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserExtension  implements QueryCollectionExtensionInterface, QueryItemExtensionInterface 
{

    public function __construct(private Security $security)
    {
    }

	public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = array()): void 
    {
        $this->andWhere($queryBuilder, $resourceClass, true);

	}

	public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = array()): void 
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    public function andWhere(QueryBuilder $queryBuilder, string $resourceClass, $isCollection = false )
    {
        $reflectionClass = new ReflectionClass($resourceClass);
        if ($reflectionClass->implementsInterface(UserOwnedInterface::class) ) {
            $alias = $queryBuilder->getRootAliases()[0];
            
            /** @var User $user */
            $user = $this->security->getUser();

            if ($user) {
                $queryBuilder
                    ->andWhere("$alias.user = :current_user")
                    ->setParameter('current_user', $user->getId());
                if ($isCollection) {
                    $queryBuilder->orWhere("$alias.user IS NULL");
                }
            } else {
                $queryBuilder->andWhere("$alias.user IS NULL");
            }

            
        }
    }
}
