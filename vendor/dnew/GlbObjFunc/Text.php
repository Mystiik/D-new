<?php

namespace GlbObjFunc;

class Text {
  public static function camelCase($text) {
    $camel = [];
    $text = explode('_', $text);
    foreach ($text as $word) {
      $camel[] = ucfirst($word);
    }
    return implode(' ', $camel);
  }

  public static function getClassName($class) {
    $class = explode('\\', $class);
    $class = $class[count($class) - 1];
    return strtoupper($class);
  }
}