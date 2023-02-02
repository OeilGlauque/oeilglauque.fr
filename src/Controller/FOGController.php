<?php

namespace App\Controller;

use App\Entity\Edition;
use App\Entity\Feature;
use App\Repository\EditionRepository;
use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FOGController extends AbstractController {

    public function getCurrentEdition(EditionRepository $editionRepository): ?Edition {
        $edition = $editionRepository->findOneBy(['annee' => $this->getParameter('current_edition')]);
        return $edition ? $edition : new Edition();
    }

    public function getModeFog(FeatureRepository $featureRepository): ?bool {
        return $featureRepository->find(4)->getState();
    }

    public function getGameStatus(FeatureRepository $featureRepository): ?bool {
        return $featureRepository->find(5)->getState();
    }

    public function getPlanningStatus(FeatureRepository $featureRepository): ?bool {
        return $featureRepository->find(7)->getState();
    }

    public function render(string $view, array $parameters = [], Response $response = null): Response {
        if (!array_key_exists('dates', $parameters)) {
            $parameters['dates'] = $this->getCurrentEdition()->getDates();
        }
        if (!array_key_exists('modeFog', $parameters)) {
            $parameters['modeFog'] = $this->getModeFog();
        }
        if (!array_key_exists('gameOpen', $parameters)) {
            $parameters['gameOpen'] = $this->getGameStatus();
        }
        if (!array_key_exists('planning', $parameters)) {
            $parameters['planning'] = $this->getPlanningStatus();
        }
        return parent::render($view, $parameters);
    }
}

?>