<?php

class Behance_Sniffs_ControlStructures_InlineControlStructureSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        12 => 1,
        14 => 1,
        16 => 1,
        22 => 1,
        28 => 1,
        34 => 1,
        41 => 1,
        45 => 1,
        47 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_ControlStructures_InlineControlStructureSniffTest
