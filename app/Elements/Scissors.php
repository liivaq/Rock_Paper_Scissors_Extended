<?php declare(strict_types=1);

namespace App\Elements;

class Scissors extends Element
{
    public function __construct(
        string $name = 'scissors' ,
        array $beats = ['paper', 'lizard'])
    {
        parent::__construct($name, $beats);
    }

}