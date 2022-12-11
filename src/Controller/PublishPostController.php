<?php
// api/src/Controller/CreatepostPublication.php
namespace App\Controller;

use App\Entity\Post;
use App\Service\postPublishingHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class PublishPostController extends AbstractController
{
    public function __construct(
        private postPublishingHandler $postPublishingHandler
    ) {

    }

    // #[Route(
    //     name: 'post_post_publication',
    //     path: '/posts/{id}/publication',
    //     methods: ['POST'],
    //     // defaults: [
    //     //     '_api_resource_class' => Post::class,
    //     //     '_api_operation_name' => 'publication',
    //     // ],
    // )]
    public function __invoke(Post $post): Post
    {
        $this->postPublishingHandler->handle($post);
        return $post;
    }


    // #[Route(
    //     name: 'post_post_publication',
    //     path: '/posts/{id}/publication',
    //     requirements: ["id"=>"\d+"],
    //     methods: ['POST'],
    //     defaults: [
    //         '_api_resource_class' => Post::class,
    //         '_api_operation_name' => 'post_publication',
    //     ],
    // )]
    // public function createPublication(Post $post)
    // {
    //     $this->postPublishingHandler->handle($post);
    //     return $post;
    // }    

}