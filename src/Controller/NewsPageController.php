<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\News;

class NewsPageController extends Controller {
    
    /**
     * @Route("/news", name="newsIndex")
     */
    public function index() {
        $news = $this->getDoctrine()->getRepository(News::class)->findAll();
        
        return $this->render('oeilglauque/news.html.twig', array(
            'dates' => "Du 19 au 21 octobre", 
            'news' => $news
        ));
    }

    /**
     * @Route("/news/{slug}")
     */
    public function showNews($slug) {
        $news = $this->getDoctrine()->getRepository(News::class)->findOneBy(['slug' => $slug]);
        if($news) {
            return $this->render('oeilglauque/showNews.html.twig', array(
                'dates' => "Du 19 au 21 octobre", 
                'news' => $news
            ));
        }else{
            throw $this->createNotFoundException('Impossible de trouver cette news');
        }
    }
}

?>