<?php
namespace App\Serializer;

use App\Attribute\ApiAuthGroups;
use App\Entity\Post;
use App\Repository\UserRepository;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class PostNormalizer  implements NormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;
    private const ALREADY_CALLED_NORMALIZER = 'PostNormalizerAlreadyCalled';


    public function __construct(private StorageInterface $storage)
    {

    }

	public function supportsNormalization(mixed $data, string $format = null) 
    {
        dump('Post_NORMALIZATION');

        $key = self::ALREADY_CALLED_NORMALIZER;
        return !isset($data->$key) && $data instanceof Post;

	}

	public function normalize(mixed $object, string $format = null, array $context = array()) 
    {
        $object->setFileUrl($this->storage->resolveUri($object, 'file'));
        $key = self::ALREADY_CALLED_NORMALIZER;
        $object->$key = true;
        return $this->normalizer->normalize($object, $format, $context);
     
	}
	
}
