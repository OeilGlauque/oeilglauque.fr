<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;
use App\Repository\FeatureRepository;
use App\Repository\NewsRepository;
use App\Service\GlauqueMarkdownParser;

class NewsPageController extends FOGController {
    
    #[Route("/news", name: "newsIndex", methods: ['GET'])]
    public function index(FeatureRepository $featureRepository, NewsRepository $newsRepository) : Response {
        if (!$featureRepository->find(6)->getState()) {
            return $this->redirectToRoute('index');
        }

        $news = $newsRepository->findAll();
        
        foreach ($news as $n) {
            $n->setText(GlauqueMarkdownParser::parse($n->getText()));
        }
        
        return $this->render('oeilglauque/news.html.twig', array(
            'news' => $news
        ));
    }

    #[Route("/news/{slug}", name: "showNews", methods: ['GET'])]
    public function showNews(FeatureRepository $featureRepository, News $news) : Response {
        if (!$featureRepository->find(6)->getState()) {
            return $this->redirectToRoute('index');
        }

        //$news = $this->getDoctrine()->getRepository(News::class)->findOneBy(['slug' => $slug]);
        if($news) {
            $news->setText(GlauqueMarkdownParser::parse($news->getText()));
            return $this->render('oeilglauque/showNews.html.twig', array(
                'news' => $news
            ));
        }else{
            throw $this->createNotFoundException('Impossible de trouver cette news');
        }
    }
}