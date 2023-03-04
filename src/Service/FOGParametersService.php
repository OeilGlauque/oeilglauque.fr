<?php

namespace App\Service;

use App\Repository\EditionRepository;
use App\Repository\FeatureRepository;
use App\Entity\Edition;

class FOGParametersService {

    private ?Edition $edition;
    private ?bool $modeFog;
    private ?bool $gameOpen;
    private ?bool $planning;

    public function __construct(EditionRepository $editionRepository, FeatureRepository $featureRepository, int $current_edition) {
        $this->edition = $editionRepository->findOneBy(['annee' => $current_edition]);

        $this->modeFog = $featureRepository->find(4)->getState();

        $this->gameOpen = $featureRepository->find(5)->getState();

        $this->planning = $featureRepository->find(7)->getState();
    }

    public function getCurrentEdition(): ?Edition {
        return $this->edition;
    }

    public function getModeFog(): ?bool {
        return $this->modeFog;
    }

    public function getGameStatus(): ?bool {
        return $this->gameOpen;
    }

    public function getPlanningStatus(): ?bool {
        return $this->planning;
    }
}