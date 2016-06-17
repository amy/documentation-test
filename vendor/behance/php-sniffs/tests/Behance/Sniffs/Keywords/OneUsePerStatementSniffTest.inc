<?php

use A, B;

class Foo {
  use F,Z, Y;
  use X;
  use W,
      V,
      G;
  use U;
  use  K;
  use A, B {
    A::blah insteadof B;
  }
}

$g = function ( $h ) use ( $i ) {
  echo "not used";
};
