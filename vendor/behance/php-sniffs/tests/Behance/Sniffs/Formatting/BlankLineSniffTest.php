<?php

class Behance_Sniffs_Formatting_BlankLineSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        37 => 1,
        45 => 1,
        60 => 1,
        75 => 1,
        87 => 1,
        92 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Formatting_BlankLineSniffTest
