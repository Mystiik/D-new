<?php

namespace Command;

class Map {
  // Attention: $params[0] will be the command name
  public static function command(\User $user, string $ipAdress, array $params = []) {
    global $_world;

    $response = '';
    $centerX = $user->mapPosX;
    $centerY = $user->mapPosY;
    $rayon = 3; // 2 -> 13 maps, 3 -> 25 maps

    $response .= 'WORLD,' . \World::SIZE_X . ',' . \World::SIZE_Y  . ',' . \Map::SIZE_Y  . ',' . \Map::SIZE_Y . '|';

    for ($x = -$rayon; $x <= $rayon; $x++) {
      $rangeY = $rayon - abs($x);
      for ($y = -$rangeY; $y <= $rangeY; $y++) {
        $map = $_world['map'][$centerX + $x][$centerY + $y];
        // Map + Object
        if (0 <= $rayon and $rayon <= 3) {
          $response .= $map->getMapCompressedToSend() . '|';
        } else {
          // Map only
          $response .= 'MAP,' . $map->biomeType . ',' . $map->posX . ',' . $map->posY . ';' . '|';
        }
      }
    }

    return $response;
  }
}