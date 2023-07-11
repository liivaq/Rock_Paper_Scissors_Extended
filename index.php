<?php

require 'vendor/autoload.php';

use App\Game;

$game = new Game();
$game->setup(3, 1);
$game->play();