<?php declare(strict_types=1);

namespace App\Elements;

abstract class Element
{
    protected string $name;
    protected array $beats;

    public function __construct(string $name, array $beats)
    {
        $this->name = $name;
        $this->beats = $beats;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBeats(): array
    {
        return $this->beats;
    }

}