<?php
try {
  // some crap
}
catch( Core_Exception $e ) {
  // this is kay
}

throw new Core_Exception( 'this is also kay' );

throw new Exception('THIS AINT OK');

try {
  // moar crap
}
catch( Exception $e ) {
  // this is not okay
}
