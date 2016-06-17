<?php

class Behance_Sniffs_Gotchas_DefaultLoggerSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    // TODO: Convert these to 1s once re-enabled in the ruleset.xml
    return [
        4  => 0,
        8  => 0,
        12 => 0,
        16 => 0,
        20 => 0,
        24 => 0,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Gotchas_DefaultLoggerSniffTest
