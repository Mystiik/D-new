<?php

namespace GlbObjFunc;

class Serializer {
  /**
   * @param Obj   = 500o/obj + 20b/property
   * @param Array = 445o/obj + 12b/property
   */
  public static function serialize($mixed) {
    return igbinary_serialize($mixed);
  }

  public static function unserialize($mixed) {
    // self::preserialize($mixed);
    return igbinary_unserialize($mixed);
  }
}