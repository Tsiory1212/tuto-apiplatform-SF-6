<?php 
namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Interface_\UserOwnedInterface;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserOwnedDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{

    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';

    public function __construct(private Security $security, private IriConverterInterface $iriConverter, private UserRepository $repoUser)
    {
       
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null) 
    {

        // dump($data);
        $reflectionClass = new ReflectionClass($type);
        $alreadyCalled = $data[self::ALREADY_CALLED_DENORMALIZER] ?? false;

        /**
         * Lorsqu'on va accèder à $this->denormalizer, il va rentrer en boucle car il existe beaucoup de dénormalisation dedans
         * Alors, Pour trouver les bons $data à denormalizer (ex : Article et Category)
         * Il faut les filtrer avec la condition ci-dessous avant d'entrer en action, car la boucle infinie va saturer la mémoire
         */
        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false ;
	}

	public function denormalize(mixed $data, string $type, string $format = null, array $context = array()) 
    {
        $data[self::ALREADY_CALLED_DENORMALIZER] = true;
        /** @var UserOwnedInterface $boj */
        $obj =  $this->denormalizer->denormalize($data , $type, $format, $context );
       

        /**
         * Pour récupérer l'utilisateur connecté, Grafikart utilise $currentUser, mais $currentUser ne représente pas vraiment un User, 
         * elle est seulement le résultat de JWT décodé chargé par le payload
         * 
         * Donc, on utilise la repository si on veut vraiment récupérer l'User  
         * Mais, quelqu'un dit que cette approche n'est pas propre car elle utilise la requête de la BDD
         */

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $user = $currentUser ? $this->repoUser->find($currentUser->getId()) : null;
        $obj->setUser($user);
     
        return $obj;
	}


}
