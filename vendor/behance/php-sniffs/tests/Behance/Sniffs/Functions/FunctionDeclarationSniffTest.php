<?php

class Behance_Sniffs_Functions_FunctionDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        7  => 1,
        15 => 1,
        16 => 1,
        42 => 2,
        43 => 1,
        45 => 1,
        49 => 2,
        52 => 1,
        54 => 1,
        55 => 1,
        56 => 1,
        57 => 1,
        59 => 1,
        60 => 2,
        61 => 1,
        64 => 1,
        65 => 2,
        71 => 2,
        83 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [
        4 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionDeclarationSniffTest
