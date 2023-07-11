<?php declare(strict_types=1);

namespace App;

use App\Elements\Element;

class Game
{
    private Player $player;
    private array $computerPlayers = [];
    private int $rounds = 0;
    private int $maxRounds;

    private array $results;

    public function setup(int $computerPlayerCount, int $roundCount)
    {
        $this->computerPlayerFactory($computerPlayerCount);
        $this->maxRounds = $roundCount;
        $this->player = new Player('Liva');
    }

    public function play()
    {
        /** @var Player $computerPlayer */
        foreach ($this->computerPlayers as $computerPlayer) {

            echo 'YOU are playing against: ' . $computerPlayer->getName() . PHP_EOL;

            while ($this->rounds < $this->maxRounds) {

                $this->playersChoice();
                $this->computersChoice($computerPlayer);

                echo $computerPlayer->getName() . ' chose ' . $computerPlayer->getChosenElement()->getName() . PHP_EOL;

                $winner = $this->determineRoundWinner($computerPlayer, $this->player);

                echo $winner ? $winner->getName() . ' won!' . PHP_EOL : 'It is a tie!' . PHP_EOL;

                $this->rounds++;
            }

            if ($computerPlayer->roundWins > $this->player->roundWins) {
                $computerPlayer->totalWins++;
                $this->player->totalLosses++;
                echo $computerPlayer->getName() . ' has won ' . $computerPlayer->roundWins .
                    ' to ' . $this->player->roundWins . PHP_EOL;
            }

            if ($computerPlayer->roundWins < $this->player->roundWins) {
                $this->player->totalWins++;
                $computerPlayer->totalLosses++;
                echo $this->player->getName() . ' has won ' . $this->player->roundWins .
                    'to ' . $computerPlayer->roundWins . PHP_EOL;
            }

            $this->rounds = 0;
            $this->player->roundWins = 0;
            $this->player->roundLosses = 0;
            $this->results[] = $computerPlayer->getName() . ': wins ' . $computerPlayer->totalWins .
                ' losses: ' . $computerPlayer->totalLosses;
        }

        $this->computerVsComputer();
        $this->results[] = $this->player->getName() . ': wins ' . $computerPlayer->totalWins .
            ' losses: ' . $this->player->totalLosses;
        var_dump($this->results);
    }

    private function playersChoice()
    {
        $playerInput = readline('Choose your element: ');

        $playerInput = ucfirst(strtolower(trim($playerInput)));

        $playerSymbol = "App\\Elements\\" . $playerInput;

        $this->player->setChosenElement(new $playerSymbol);
    }

    private function computersChoice(Player $computerPlayer)
    {
        $computerPlayer->setChosenElement($this->getRandomElement());
    }

    public function determineRoundWinner(Player $computerPlayer, Player $player)
    {
        if ($computerPlayer->getChosenElement()->getName() == $player->getChosenElement()->getName()) {
            return null;
        }

        if (in_array($computerPlayer->getChosenElement()->getName(), $player->getChosenElement()->getBeats())) {
            $player->roundWins++;
            $computerPlayer->roundLosses++;
            return $player;
        }

        $computerPlayer->roundWins++;
        $player->roundLosses++;
        return $computerPlayer;
    }

    private function getRandomElement()
    {
        $elements = require 'elements.php';
        $element = "App\\Elements\\" . $elements[rand(0, (count($elements) - 1))];
        return new $element;
    }

    private function computerVsComputer()
    {
        $totalPlayers = count($this->computerPlayers);

        for ($i = 0; $i < $totalPlayers; $i++) {
            $currentPlayer = $this->computerPlayers[$i];

            for ($j = $i + 1; $j < $totalPlayers; $j++) {
                $opponentPlayer = $this->computerPlayers[$j];

                for ($round = 1; $round <= $this->maxRounds; $round++) {
                    $this->computersChoice($currentPlayer);
                    $this->computersChoice($opponentPlayer);
                    $this->determineRoundWinner($currentPlayer, $opponentPlayer);
                }

                if ($currentPlayer->roundWins > $opponentPlayer->roundWins) {
                    $currentPlayer->totalWins++;
                    $opponentPlayer->totalLosses++;
                }

                if ($currentPlayer->roundWins < $opponentPlayer->roundWins) {
                    $opponentPlayer->totalWins++;
                    $currentPlayer->totalLosses++;
                }
            }
        }

        foreach ($this->computerPlayers as $player) {
            $this->results[] = $player->getName() . ': wins ' . $player->totalWins . ' losses: ' . $player->totalLosses;
        }

    }

    private function computerPlayerFactory(int $count)
    {
        $computerNames = [
            'Mr. PoopyPants',
            'Ctr+Alt+Defeat',
            'Sir LooseALot',
            'God (not THE God, my parents are weird)',
            'Player5',
            'Player6',
            'Player7',
            'Player8',
            'Player9'
        ];

        for ($i = 0; $i < $count; $i++) {
            $this->computerPlayers[] = new Player($computerNames[rand(0, count($computerNames) - 1)]);
        }
    }

}