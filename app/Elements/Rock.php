<?php declare(strict_types=1);

namespace App\Elements;

class Rock extends Element
{
    public function __construct(
        string $name = 'rock' ,
        array $beats = ['scissors', 'lizard'])
    {
        parent::__construct($name, $beats);
    }

}