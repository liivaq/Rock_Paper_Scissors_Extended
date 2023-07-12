<?php declare(strict_types=1);

namespace App;

use App\Elements\Element;

class Game
{
    private array $elements = [];
    private Player $player;
    private array $computerPlayers = [];
    private int $rounds = 0;
    private int $maxRounds;
    private array $results;

    public function setup()
    {
        $this->explainRules();

        $validSetup = false;

        while (!$validSetup) {
            $computerPlayerCount = readline('Enter amount of players (1 - 9): ');
            $roundCount = readline('Enter amount of rounds in each game (1 - 5): ');
            $playerName = readline('Enter your name: ');

            $validSetup = $this->validateSetup($computerPlayerCount, $roundCount);

            echo $validSetup ? 'Let the game begin!' . PHP_EOL : 'Invalid input! Try again!' . PHP_EOL;
            echo '--------------------------------------------------------------------------------' . PHP_EOL;
        }

        $this->computerPlayerFactory((int)$computerPlayerCount);
        $this->maxRounds = (int)$roundCount;
        $this->player = new Player(trim($playerName));
        $this->elements = require_once 'elements.php';
    }

    public function play()
    {
        /** @var Player $computerPlayer */
        foreach ($this->computerPlayers as $computerPlayer) {

            echo 'You are playing against: ' . $computerPlayer->getName() . PHP_EOL;
            echo '--------------------------------------------------------------------------------' . PHP_EOL;

            while ($this->rounds < $this->maxRounds) {

                $this->playersChoice();
                $this->computersChoice($computerPlayer);

                echo $computerPlayer->getName() . ' chose: '
                    . ucfirst($computerPlayer->getChosenElement()->getName()) . PHP_EOL;

                $roundWinner = $this->determineRoundWinner($computerPlayer, $this->player);

                echo $roundWinner ? $roundWinner->getName() . ' won this round!' . PHP_EOL : 'It is a tie!' . PHP_EOL;

                $this->rounds++;

                echo '--------------------------------------------------------------------------------' . PHP_EOL;
            }

            $gameWinner = $this->determineGameWinner($computerPlayer, $this->player);
            echo $gameWinner ? $gameWinner->getName() . ' has won the game ' . $computerPlayer->roundWins .
                ' to ' . $this->player->roundWins . PHP_EOL : 'The game ends in tie!'.PHP_EOL;
            $this->resetGame($computerPlayer, $this->player);
            echo '--------------------------------------------------------------------------------' . PHP_EOL;
        }

        echo 'Computers are battling with each-other: ' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        $this->computerVsComputer();
        for($i=0; $i<5; $i++){
            echo '...'.PHP_EOL;
            sleep(1);
        }
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        $this->registerScores();
        echo 'Final Tournament results: ' . PHP_EOL;
        $this->printResults();
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        echo 'The Tournament winner(s): ' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        $tournamentWinners = $this->determineTournamentWinner();
        foreach ($tournamentWinners as $winner) {
            echo $winner->getName() . PHP_EOL;
        }
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;

    }

    private function playersChoice()
    {
        $validInput = false;
        while ($validInput === false) {
            $playerInput = readline('Choose your element: ');
            $playerInput = ucfirst(strtolower(trim($playerInput)));
            $validInput = $this->validateInput($playerInput);
            echo $validInput ? 'You chose: ' . $playerInput . PHP_EOL : 'Invalid choice. Choose again!' . PHP_EOL;
        }

        $playerSymbol = "App\\Elements\\" . $playerInput;

        $this->player->setChosenElement(new $playerSymbol);
    }

    private function computersChoice(Player $computerPlayer)
    {
        $computerPlayer->setChosenElement($this->getRandomElement());
    }

    public function determineRoundWinner(Player $computerPlayer, Player $player): ?Player
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
        $element = "App\\Elements\\" . $this->elements[rand(0, (count($this->elements) - 1))];
        return new $element;
    }


    private function determineGameWinner(Player $computerPlayer, Player $player)
    {
        if ($computerPlayer->roundWins > $player->roundWins) {
            $computerPlayer->totalWins++;
            $player->totalLosses++;
            return $computerPlayer;
        }

        if ($computerPlayer->roundWins < $player->roundWins) {
            $player->totalWins++;
            $computerPlayer->totalLosses++;
            return $player;
        }

        if ($computerPlayer->roundWins === $player->roundWins) {
            $computerPlayer->totalTies++;
            $player->totalTies++;
            return null;
        }
    }

    public function determineTournamentWinner(): array
    {
        $this->computerPlayers[] = $this->player;

        $winner = array_reduce($this->computerPlayers, function ($a, $b) {
            if ($a === null || $a->totalWins < $b->totalWins) {
                return $b;
            }
            return $a;
        });

        $maxWins = $winner->totalWins;

        return array_filter($this->computerPlayers, function ($player) use ($maxWins) {
            return $player->totalWins == $maxWins;
        });
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

                $this->determineGameWinner($currentPlayer, $opponentPlayer);
                $this->resetGame($currentPlayer, $opponentPlayer);
            }
        }
    }

    private function resetGame(Player $computerPlayer, Player $player): void
    {
        $this->rounds = 0;
        $player->roundWins = 0;
        $player->roundLosses = 0;
        $computerPlayer->roundWins = 0;
        $computerPlayer->roundLosses = 0;
    }

    private function registerScores()
    {
        $this->computerPlayers[] = $this->player;

        foreach ($this->computerPlayers as $player) {
            $this->results[] = $player->getName() . ': wins: ' . $player->totalWins .
                ' losses: ' . $player->totalLosses .
                ' ties: ' . $player->totalTies;
        }
    }

    private function validateInput(string $elementChoice): bool
    {
        return in_array($elementChoice, $this->elements);
    }

    private function validateSetup($computerPlayerCount, $roundCount): bool
    {
        if ($computerPlayerCount < 1 || $computerPlayerCount > 9) {
            return false;
        }

        if ($roundCount < 1 || $roundCount > 5) {
            return false;
        }

        return true;
    }

    private function computerPlayerFactory(int $count)
    {
        $computerNames = [
            'May B. Win',
            'Ctr+Alt+Defeat',
            'Sir LooseALot',
            'Don Key',
            'Chris P. Bacon',
            'shaquille.oatmeal',
            'Jack Pott',
            'LactoseTheIntolerant',
            'NachoWiFi'
        ];

        $availableNames = $computerNames;

        for ($i = 0; $i < $count; $i++) {
            $randomIndex = rand(0, count($availableNames) - 1);
            $name = $availableNames[$randomIndex];
            unset($availableNames[$randomIndex]);
            $availableNames = array_values($availableNames);
            $this->computerPlayers[] = new Player($name);
        }
    }

    private function explainRules()
    {
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        echo 'Welcome to Rock, Paper, Scissors, Lizard, Spock game (Very catchy title, I know. Rolls of your tongue!)' . PHP_EOL;
        echo 'Game rules are as follows: ' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        echo '1. Choose the amount of computer players you want to play against (1 to 9)' . PHP_EOL;
        echo '2. Choose the amount of rounds in each game (1 to 5)' . PHP_EOL;
        echo '3. Choose your name for the game' . PHP_EOL;
        echo '4. Available elements for the game are (you might be able to guess this one): rock, paper, scissors, lizard, spock' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
        echo 'Play against all computer players.' . PHP_EOL;
        echo 'One of you is going to win. One of you is going to loose. It might be a tie, though.' . PHP_EOL;
        echo 'Computers will play with each-other as well.' . PHP_EOL;
        echo 'Whoever gets the most wins is going to be the winner (surprising, right?)!' . PHP_EOL;
        echo '--------------------------------------------------------------------------------' . PHP_EOL;
    }

    private function printResults()
    {
        foreach ($this->results as $result) {
            echo $result . PHP_EOL;
        }
    }

}