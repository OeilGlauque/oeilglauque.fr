<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsPageController extends Controller {
    
    /**
     * @Route("/news")
     */
    public function index() {
        return $this->render('oeilglauque/news.html.twig', array(
            'dates' => "Du 10 au 31 octobre", 
            'news' => array(
                array(
                    "title" => htmlspecialchars("Première news !"), 
                    "text" => htmlspecialchars("Ceci est la première news. Il y en aura plus bientôt !"), 
                    "slug" => htmlspecialchars("premiere-news"), 
                ), 
                array(
                    "title" => htmlspecialchars("Encore une news !"), 
                    "text" => htmlspecialchars("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."), 
                    "slug" => htmlspecialchars("autre-news"), 
                ), 
                array(
                    "title" => htmlspecialchars("Encore une news !"), 
                    "text" => htmlspecialchars("Bon j'ai plus d'inspi. Tant pis !"), 
                    "slug" => htmlspecialchars("encore-news"), 
                ), 
                array(
                    "title" => htmlspecialchars("Encore une news !"), 
                    "text" => htmlspecialchars("C'était mieux avant. "), 
                    "slug" => htmlspecialchars("nouvelle-news"), 
                ), 
                array(
                    "title" => htmlspecialchars("Encore une news !"), 
                    "text" => htmlspecialchars("Touffy président ! "), 
                    "slug" => htmlspecialchars("touffy"), 
                ), 
            ), 
        ));
    }
}

?>