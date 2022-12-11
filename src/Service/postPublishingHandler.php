<?php
namespace App\Service;

class postPublishingHandler 
{
    public function handle($post)
    {
        $post->setOnline(1);
        return $post;
    }
}

?>
