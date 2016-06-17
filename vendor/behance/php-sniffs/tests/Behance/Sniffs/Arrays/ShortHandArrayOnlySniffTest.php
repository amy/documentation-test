<?php

class Behance_Sniffs_Arrays_ShortHandArrayOnlySniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        2 => 1
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Arrays_ShortHandArrayOnlySniffTest
