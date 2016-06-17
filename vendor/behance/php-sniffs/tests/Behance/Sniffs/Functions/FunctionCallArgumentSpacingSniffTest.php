<?php

class Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        2  => 1,
        3  => 1,
        6  => 1,
        10 => 1,
        13 => 1,
        15 => 1,
        18 => 2,
        21 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniffTest
