<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\News;
use App\Service\GlauqueMarkdownParser;

class NewsPageController extends CustomController {
    
    /**
     * @Route("/news", name="newsIndex")
     */
    public function index() {
        $news = $this->getDoctrine()->getRepository(News::class)->findAll();
        
        foreach ($news as $n) {
            $n->setText(GlauqueMarkdownParser::parse($n->getText()));
        }
        
        return $this->render('oeilglauque/news.html.twig', array(
            'dates' => $this->getCurrentEdition()->getDates(), 
            'news' => $news, 
            //'admin' => array_contains($this->getUser()->getRoles(), 'ROLE_ADMIN'), 
        ));
    }

    /**
     * ("/news/{slug}")
     */
    public function showNews($slug) {
        $news = $this->getDoctrine()->getRepository(News::class)->findOneBy(['slug' => $slug]);
        if($news) {
            $news->setText(GlauqueMarkdownParser::parse($news->getText()));
            return $this->render('oeilglauque/showNews.html.twig', array(
                'dates' => $this->getCurrentEdition()->getDates(), 
                'news' => $news
            ));
        }else{
            throw $this->createNotFoundException('Impossible de trouver cette news');
        }
    }
}

?>
