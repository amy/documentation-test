<?php

class Behance_Sniffs_Comments_DisallowHashCommentsSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        3  => 1,
        7  => 1
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Comments_DisallowHashCommentsSniffTest
