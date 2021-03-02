<?php

namespace GlbObjFunc;

class Gain {
  public function __construct(
    public $ressourceType,
    public $probability,
    public $valMin,
    public $valMax = 0,
  ) {
  }
}