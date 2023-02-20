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
        if (!array_key_exists('modeFog', $parameters)) {
            $parameters['modeFog'] = $this->FogParams->getModeFog();
        }
        if (!array_key_exists('gameOpen', $parameters)) {
            $parameters['gameOpen'] = $this->FogParams->getGameStatus();
        }
        if (!array_key_exists('planning', $parameters)) {
            $parameters['planning'] = $this->FogParams->getPlanningStatus();
        }
        return parent::render($view, $parameters);
    }
}