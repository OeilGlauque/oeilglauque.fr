<?php

namespace App\Service;

use App\Repository\EditionRepository;
use App\Repository\FeatureRepository;
use App\Entity\Edition;

class FOGParametersService {

    private ?Edition $edition;
    private ?bool $modeFog;
    private ?bool $gameOpen;
    private ?bool $gameRegistration;
    private ?bool $planning;
    private ?bool $menu;

    private ?bool $artistes;

    public function __construct(EditionRepository $editionRepository, FeatureRepository $featureRepository, int $current_edition, string $current_edition_type) {
        $this->edition = $editionRepository->findOneBy(['annee' => $current_edition,'type' => $current_edition_type]);

        $this->modeFog = $featureRepository->find(4)->getState();

        $this->gameOpen = $featureRepository->find(5)->getState();

        $this->planning = $featureRepository->find(7)->getState();

        $this->menu = $featureRepository->find(8)->getState();

        $this->gameRegistration = $featureRepository->find(9)->getState();

        $this->artistes = $featureRepository->find(10)->getState();
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

    public function getMenuStatus() : ?bool {
        return $this->menu;
    }

    public function getGameRegistrationStatus(): ?bool {
        return $this->gameRegistration;
    }

    public function getArtistesStatus() : ?bool {
        return $this->artistes;
    }
}