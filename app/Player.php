<?php declare(strict_types=1);

namespace App;
use App\Elements\Element;

class Player
{
    private string $name;

    private Element $chosenElement;
    public int $roundWins = 0;
    public int $roundLosses = 0;
    public int $totalWins = 0;
    public int $totalLosses = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getChosenElement(): Element
    {
        return $this->chosenElement;
    }

    public function setChosenElement(Element $chosenElement): void
    {
        $this->chosenElement = $chosenElement;
    }

}