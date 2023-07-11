<?php declare(strict_types=1);

namespace App\Elements;

class Lizard extends Element
{
    public function __construct(
        string $name = 'lizard' ,
        array $beats = ['spock', 'paper'])
    {
        parent::__construct($name, $beats);
    }

}