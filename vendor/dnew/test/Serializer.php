<?php

namespace test;

class Serializer {
  /**
   * @param Obj   = 500o/obj + 20b/property
   * @param Array = 445o/obj + 12b/property
   */
  public static function serialize($mixed) {
    // self::preserialize($mixed);
    return igbinary_serialize($mixed);
  }

  /**
   * @param Obj   = 500o/obj + 20b/property
   * @param Array = 445o/obj + 12b/property
   */
  public static function unserialize($mixed) {
    // self::preserialize($mixed);
    return igbinary_unserialize($mixed);
  }

  public static function preserialize(&$array) {
    $chunk = [];
    $last_key = array_key_last($array);
    if (is_array($array)) {
      if (count($array, COUNT_RECURSIVE) > self::MAX_ELEMENT_AT_ONCE) {
        foreach ($array as $key => $value) {
          if (is_countable($array[$key]) && count($array[$key], COUNT_RECURSIVE) > self::MAX_ELEMENT_AT_ONCE) {
            self::preserialize($array[$key]);
            continue;
          }

          $chunk[$key] = $value;
          unset($array[$key]);

          if (count($chunk, COUNT_RECURSIVE) > self::MAX_ELEMENT_AT_ONCE or $key === $last_key) {
            $array[] = igbinary_serialize($chunk);
            $chunk = [];
          }
        }
      } else {
        $array = igbinary_serialize($array);
      }
    } else {
      $array = igbinary_serialize($array);
    }
  }
}