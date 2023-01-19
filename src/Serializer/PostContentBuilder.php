<?php
namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\Post;
use App\Security\Voter\PostVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostContentBuilder  implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated, 
        private AuthorizationCheckerInterface $authorizationChecker
    )
    {
        
    }

	public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array 
    {
        dump('SSSSSSSSSS');

        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        if(
            $resourceClass === Post::class &&
            isset($context['groups']) &&
            $this->authorizationChecker->isGranted('ROLE_USER')
        ){
            $context['groups'][] = 'Post:read:Auth'; 
        }
        return $context;
    }
}
