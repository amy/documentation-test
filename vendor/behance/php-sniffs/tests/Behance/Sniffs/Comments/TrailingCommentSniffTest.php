<?php

class Behance_Sniffs_Comments_TrailingCommentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        22  => 1,
        25  => 1,
        28  => 1,
        31  => 1,
        35  => 1,
        43  => 1,
        52  => 1,
        60  => 1,
        68  => 1,
        76  => 1,
        80  => 1,
        101 => 1,
        109 => 1,
        113 => 1
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Comments_TrailingCommentSniffTest
