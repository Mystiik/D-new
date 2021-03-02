<?php

namespace GlbObjFunc;

class Glb {
  public static function getVarName($var) {
    // read backtrace
    $bt   = debug_backtrace();
    // read file
    $file = file($bt[0]['file']);
    // select exact print_var_name($varname) line
    $src  = $file[$bt[0]['line'] - 1];
    // search pattern
    $pat = '#(.*)' . __FUNCTION__ . ' *?\( *?(.*) *?\)(.*)#i';
    // extract $varname from match no 2
    $var  = preg_replace($pat, '$2', $src);
    // return the var name
    $var = str_replace('$', '', $var);
    return trim($var);
  }
}