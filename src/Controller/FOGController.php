<?php

namespace App\Controller;

use App\Service\FOGParametersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FOGController extends AbstractController {

    protected FOGParametersService $FogParams;

    public function __construct(FOGParametersService $FogParams) {
        $this->FogParams = $FogParams;
    }

    public function render(string $view, array $parameters = [], Response $response = null) : Response {

        if (!array_key_exists('dates', $parameters)) {
            $parameters['dates'] = $this->FogParams->getCurrentEdition()->getDates();
        }

        // Différentes fonctionalités du site (celles activables via le menu admin
        if (!array_key_exists('modeFog', $parameters)) {
            $parameters['modeFog'] = $this->FogParams->getModeFog();
        }
        if (!array_key_exists('gameOpen', $parameters)) {
            $parameters['gameOpen'] = $this->FogParams->getGameStatus();
        }
        if (!array_key_exists('gameRegistration', $parameters)) {
            $parameters['gameRegistration'] = $this->FogParams->getGameRegistrationStatus();
        }
        if (!array_key_exists('planning', $parameters)) {
            $parameters['planning'] = $this->FogParams->getPlanningStatus();
        }
        if (!array_key_exists('menu', $parameters)) {
            $parameters['menu'] = $this->FogParams->getMenuStatus();
        }

        if (!array_key_exists('artistes', $parameters)) {
            $parameters['artistes'] = $this->FogParams->getArtistesStatus();
        }

        if(!array_key_exists('newHeader', $parameters)) {
            $parameters['newHeader'] = false;
        } // Si la page utilise le nouvel Header, TODO : Idéalement faire disparaitre toute occurence de l'ancien header et donc ne plus avoir besoin de ce tag
        
        return parent::render($view, $parameters);
    }
}