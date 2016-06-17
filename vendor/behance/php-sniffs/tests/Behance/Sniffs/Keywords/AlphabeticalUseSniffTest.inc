<?php

use \A\B;
use  A\C;
use A\C\E;
use A\D ;
use A\D\G;
use A\D\F;
use JAB;
use J_J;
use H;
use I;
use kab;
use Kar;
use K as UsedK;

class Foo {

  use A;
  use C;
  use B;
  use D;
  use D\E, D\V {
    D\V::blah insteadof D\E;
  }
  use D\G\F;
  use D\G\G, E\Z {
    D\G\G::blah insteadof E\Z;
  }
  use E\Y, D\G\H {
    D\G\H::blah insteadof E\Y;
  }
  use D\Z\X;
}

$a = function ( $param ) use ( $usedVar ) {
  echo "use ignored";
};
