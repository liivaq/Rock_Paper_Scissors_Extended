<?php declare(strict_types=1);

namespace App\Elements;

class Paper extends Element
{
    public function __construct(
        string $name = 'paper' ,
        array $beats = ['rock', 'spock'])
    {
        parent::__construct($name, $beats);
    }

}