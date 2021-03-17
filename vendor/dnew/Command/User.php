<?php

namespace Command;

class User {
  // Attention: $params[0] will be the command name
  public static function command(\User $user, string $ipAdress, array $params = []) {
    global $_world;

    $response = '';

    foreach ($user->characterList as $charac) {
      $response .= 'CHARACTER' . ',' . $charac->mapPosX . ',' . $charac->mapPosY . ',' . $charac->posX . ',' . $charac->posY . '|';
    }

    return $response;
  }
}