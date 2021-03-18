<?php

namespace Command;

class Map {
  // Attention: $params[0] will be the command name
  public static function command(\User $user, string $ipAdress, array $params = []) {
    global $_world;

    $response = '';
    $centerX = $params[1] ?? $user->mapX;
    $centerY = $params[2] ?? $user->mapY;
    $rayon = $params[3] ?? 2; // 2 -> 13 maps, 3 -> 25 maps

    $border = 2;
    $rayon += $border;

    $response .= 'WORLD,' . \World::SIZE_X . ',' . \World::SIZE_Y  . ',' . \Map::SIZE_Y  . ',' . \Map::SIZE_Y . '|';

    for ($x = -$rayon; $x <= $rayon; $x++) {
      $rangeY = $rayon - abs($x);
      for ($y = -$rangeY; $y <= $rangeY; $y++) {
        $map = $_world['map'][$centerX + $x][$centerY + $y] ?? null;
        if ($map == null) continue;

        // Map + Object
        $range = abs($x) + abs($y);
        if (0 <= $rayon and $rayon <= 3 + $border and $range <= $rayon - $border) {
          $response .= $map->getMapCompressedToSend() . '|';
        } else {
          // Map only
          $response .= 'MAP,' . $map->biomeType . ',' . $map->mapX . ',' . $map->mapY . ';' . '|';
        }
      }
    }

    return $response;
  }
}