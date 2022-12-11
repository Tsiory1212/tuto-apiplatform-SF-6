<?php
// api/src/Controller/CreatepostPublication.php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\postPublishingHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CountPostController extends AbstractController
{
    public function __construct(
        private PostRepository $repoPost
    ) {

    }

   
    public function __invoke(Request $request)
    {
        $onlineQuery = $request->query->get('online');
        $criteria = is_null($onlineQuery) ?  []  : ['online' => $onlineQuery];
        return $this->repoPost->count($criteria);
    }


}