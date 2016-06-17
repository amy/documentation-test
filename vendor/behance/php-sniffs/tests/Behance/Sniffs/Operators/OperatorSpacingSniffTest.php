<?php

class Behance_Sniffs_Operators_OperatorSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        5   => 4,
        12  => 2,
        19  => 2,
        26  => 2,
        33  => 2,
        40  => 2,
        47  => 2,
        54  => 2,
        61  => 2,
        86  => 2,
        93  => 2,
        100 => 2,
        107 => 2,
        114 => 2,
        121 => 2,
        128 => 2,
        135 => 2,
        143 => 2,
        151 => 2,
        158 => 2,
        165 => 2,
        172 => 2,
        179 => 2,
        186 => 2,
        193 => 2,
        200 => 2,
        217 => 1,
        226 => 1,
        228 => 2,
        231 => 1,
        238 => 2,
        239 => 2,
        244 => 1,
        247 => 1,
        250 => 1,
        251 => 1,
        252 => 2,
        255 => 1,
        257 => 1,
        261 => 1,
        262 => 2,
        266 => 1,
        268 => 2,
        283 => 1,
        284 => 1,
        287 => 1,
        288 => 1,
        291 => 1,
        292 => 1,
        295 => 1,
        296 => 1,
        299 => 1,
        302 => 1,
        305 => 1,
        308 => 1,
        311 => 1,
        314 => 1,
        317 => 1,
        320 => 1,
        323 => 1,
        325 => 1,
        329 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Operators_OperatorSpacingSniffTest
