<?php

require 'vendor/autoload.php';

use App\Game;

$game = new Game();

$game->setup();
$game->play();