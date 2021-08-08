<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Edition;
use App\Entity\Feature;

class FOGController extends Controller {

    public function getCurrentEdition(): ?Edition {
        $edition = $this->getDoctrine()->getRepository(Edition::class)->findOneBy(['annee' => $this->getParameter('current_edition')]);
        return $edition ? $edition : new Edition();
    }

    public function getModeFog(): ?bool {
        return $this->getDoctrine()->getRepository(Feature::class)->find(4)->getState();
    }

    public function getGameStatus(): ?bool {
        return $this->getDoctrine()->getRepository(Feature::class)->find(5)->getState();
    }

    public function render(string $view, array $parameters = [], Response $response = null): Response {
        if (!array_key_exists ('dates', $parameters)) {
            $parameters['dates'] = $this->getCurrentEdition()->getDates();
        }
        if (!array_key_exists ('modeFog', $parameters)) {
            $parameters['modeFog'] = $this->getModeFog();
        }
        if (!array_key_exists ('gameOpen', $parameters)) {
            $parameters['gameOpen'] = $this->getGameStatus();
        }
        return parent::render($view, $parameters);
    }
}

?>