<?php

class Behance_Sniffs_ControlStructures_TernarySniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        69  => 1,
        74  => 1,
        79  => 1,
        83  => 1,
        88  => 1,
        90  => 1,
        91  => 1,
        96  => 1,
        98  => 2,
        100 => 1,
        102 => 1,
        104 => 1,
        106 => 1,
        108 => 1,
        110 => 1,
        112 => 1,
        114 => 1,
        120 => 1,
        121 => 1,
        125 => 1,
        128 => 1,
        132 => 1,
        137 => 1,
        140 => 1,
        141 => 1,
        145 => 1,
        150 => 1,
        153 => 1,
        158 => 1,
        163 => 1,
        171 => 1,
        175 => 1,
        176 => 1,
        177 => 1,
        178 => 1,
        181 => 1,
        183 => 1,
        188 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_ControlStructures_TernarySniffTest
