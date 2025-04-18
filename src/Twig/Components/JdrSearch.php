<?php

namespace App\Twig\Components;

use App\Entity\Edition;
use App\Repository\GameRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class JdrSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';


    #[LiveProp(writable: false)]
    public edition $edition;

    public function __construct(private GameRepository $gameRepository)
    {

    }

    public function getGames(): array
    {
        return $this->gameRepository->search($this->query, true, $this->edition);
    }
}