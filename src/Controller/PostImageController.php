<?php
namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostImageController 
{
    public function __invoke(Post $post, Request $request)
    {
        /**
         * Si au cas ou dans apliplatform 2, on ne peut pas accéder directement à $post
         * On le récupère avec $request->attributes->get('data')
         */
        $file = $request->files->get('file');
        $post->setFile($file);
        $post->setUpdatedAt(new \DateTime());
        // dd($post, $file);
        return $post;

    }
}
