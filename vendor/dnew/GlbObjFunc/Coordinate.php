<?php

namespace GlbObjFunc;

class Coordinate {
  public static function set($posXTop = 0, $posYTop = 0, $posXBot = 0, $posYBot = 0) {
    if ($posXBot > $posXTop) {
      $tmp = $posXBot;
      $posXBot = $posXTop;
      $posXTop = $tmp;
    }
    if ($posYBot > $posYTop) {
      $tmp = $posYBot;
      $posYBot = $posYTop;
      $posYTop = $tmp;
    }
    return [[$posXTop, $posYTop], [$posXBot, $posYBot]];
  }
}