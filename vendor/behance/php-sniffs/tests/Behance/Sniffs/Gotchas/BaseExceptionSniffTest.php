<?php

class Behance_Sniffs_Gotchas_BaseExceptionSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        11 => 1,
        16 => 1
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Gotchas_BaseExceptionSniffTest
