<?php

namespace Command;

class Map {
  // Attention: $params[0] will be the command name
  public static function command(\User $user, string $ipAdress, array $params = []) {
    global $_world;

    $response = '';
    $centerX = $user->mapPosX;
    $centerY = $user->mapPosY;
    $rayon = 2; // 2 -> 13 maps, 3 -> 25 maps

    for ($x = -$rayon; $x <= $rayon; $x++) {
      $rangeY = $rayon - abs($x);
      for ($y = -$rangeY; $y <= $rangeY; $y++) {
        $response .= $_world['map'][$centerX + $x][$centerY + $y]->getMapCompressedToSend() . '|';
      }
    }
    return $response;
  }
}