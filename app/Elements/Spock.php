<?php declare(strict_types=1);

namespace App\Elements;

class Spock extends Element
{
    public function __construct(
        string $name = 'lizard' ,
        array $beats = ['rock', 'scissors'])
    {
        parent::__construct($name, $beats);
    }

}