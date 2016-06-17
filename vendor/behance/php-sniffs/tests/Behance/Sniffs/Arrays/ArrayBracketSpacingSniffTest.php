<?php

class Behance_Sniffs_Arrays_ArrayBracketSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        4 => 2,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Arrays_ArrayBracketSpacingSniffTest
