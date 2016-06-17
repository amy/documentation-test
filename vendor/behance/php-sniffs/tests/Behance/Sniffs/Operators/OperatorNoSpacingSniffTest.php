<?php

class Behance_Sniffs_Operators_OperatorNoSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        3  => 1,
        4  => 1,
        5  => 2,
        6  => 2,
        9  => 1,
        10 => 1,
        11 => 2,
        12 => 2,
        15 => 1,
        16 => 1,
        17 => 2,
        18 => 2,
        20 => 2,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Operators_OperatorNoSpacingSniffTest
