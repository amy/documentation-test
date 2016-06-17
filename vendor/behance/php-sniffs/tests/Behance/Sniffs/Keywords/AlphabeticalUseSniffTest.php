<?php

class Behance_Sniffs_Keywords_AlphabeticalUseSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    if ( $testFile === 'AlphabeticalUseSniffTest.1.inc' ) {
      return [];
    }

    return [
        8  => 1,
        10 => 1,
        11 => 1,
        12 => 1,
        15 => 1,
        21 => 1,
        33 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Keywords_AlphabeticalUseSniffTest
