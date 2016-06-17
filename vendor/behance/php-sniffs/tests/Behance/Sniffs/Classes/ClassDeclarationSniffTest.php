<?php

class Behance_Sniffs_Classes_ClassDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        3  => 1,
        5  => 1,
        7  => 1,

        12 => 1,
        14 => 1,
        16 => 1,

        21 => 1,
        23 => 1,
        25 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [
        9  => 1,
        18 => 1,
        27 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Classes_ClassDeclarationSniffTest
