<?php
namespace App\Serializer;

use App\Attribute\ApiAuthGroups;
use App\Entity\Interface_\UserOwnedInterface;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiAuthNormalizer  implements NormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;
    private const ALREADY_CALLED_NORMALIZER = 'PostApiNormalizerAlreadyCalled';

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker, private Security $security, private UserRepository $repoUser)
    {
        # code...
    }

	public function supportsNormalization(mixed $data, string $format = null) 
    {
        // return false;
        if (!is_object($data) ) {
            return false;
        }
        $class = new ReflectionClass(get_class($data));
        $classAttributes = $class->getAttributes(ApiAuthGroups::class);

        if (empty($classAttributes)) {
            return false;
        }
        $key = self::ALREADY_CALLED_NORMALIZER;
        $alreadyCalled = $data->$key ?? false;

        return $alreadyCalled === false;

	}

	public function normalize(mixed $object, string $format = null, array $context = array()) 
    {

        $class = new ReflectionClass(get_class($object));
        $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();
      
        foreach ($apiAuthGroups->groups as $role => $groups) {        

            /**
             * Cette condition verifie que l'User connecté est bien "propriétaire" d'une Ressource 
             * Après, on affiche les champs de l'Entity (ex: slug dans Post) en ajoutant la liste des groupes de l'attribut ("ApiAuthGroups") de la class (Post) dans la "normalizationContext" (ex : read:collection:Owner)
             */

            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            $owner = $object->getUser();
            if ($owner && ($owner->getId() == $currentUser->getId())) {
                $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            }

            // if ($this->authorizationChecker->isGranted($role, $object)) {
            //     $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            // }
        }
        $key = self::ALREADY_CALLED_NORMALIZER;
        $object->$key = true;

        return $this->normalizer->normalize($object, $format, $context);
     
	}
	
}
