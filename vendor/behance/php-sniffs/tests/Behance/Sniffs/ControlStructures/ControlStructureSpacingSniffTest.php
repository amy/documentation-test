<?php

class Behance_Sniffs_ControlStructures_ControlStructureSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        2  => 1,
        6  => 1,
        10 => 1,
        14 => 1,
        18 => 4,
        22 => 1,
        24 => 4,
        26 => 1,
        28 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_ControlStructures_ControlStructureSpacingSniffTest
