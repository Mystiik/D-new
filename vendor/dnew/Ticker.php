<?php
class Ticker {

  public static function tickerInit() {
    self::saveWorldSetTicker();
    self::printServerInfoSetTicker();
  }

  public static function tickerCheck() {
    self::saveWorld();
    self::printServerInfo();
  }

  //---------------------------------------------------------------------------------------------
  // SaveWorld
  //---------------------------------------------------------------------------------------------
  private static function saveWorld() {
    global $_world;

    if (time() >= $_world['ticker']['saveWorld']) {
      $fileDefault = fopen(Server::$savePath . 'save.txt', 'w');
      $file = fopen(Server::$savePath . 'save' . time() . '.txt', 'w');

      $_worldSerialized = GlbObjFunc\Serializer::serialize($_world);

      fwrite($fileDefault, $_worldSerialized);
      fwrite($file, $_worldSerialized);
      fclose($fileDefault);
      fclose($file);

      self::saveWorldSetTicker();
    }
  }

  private static function saveWorldSetTicker() {
    global $_world;
    $_world['ticker']['saveWorld'] = time() + HOUR;
  }

  //---------------------------------------------------------------------------------------------
  // PrintServerInfo
  //---------------------------------------------------------------------------------------------
  private static function printServerInfo() {
    global $_world;

    if (time() >= $_world['ticker']['printServerInfo']) {
      $memoryUsageMb = round(memory_get_usage() / 1000 / 1000, 2);
      $memoryLimitMb = ini_get('memory_limit');
      $memoryLimitMb = str_replace('K', '000', $memoryLimitMb);
      $memoryLimitMb = str_replace('M', '000000', $memoryLimitMb);
      $memoryLimitMb = round($memoryLimitMb / 1000 / 1000, 2);
      $ratio = round($memoryUsageMb / $memoryLimitMb * 100, 2);

      echo PHP_EOL . "SERVER_INFO" . PHP_EOL;
      echo "-- Memory usage: $memoryUsageMb Mb ($ratio%)" . PHP_EOL;
      echo "-- Memory limit: $memoryLimitMb Mb" . PHP_EOL;
      echo PHP_EOL;

      self::printServerInfoSetTicker();
    }
  }

  private static function printServerInfoSetTicker() {
    global $_world;
    $_world['ticker']['printServerInfo'] = time() + MINUTE;
  }
}