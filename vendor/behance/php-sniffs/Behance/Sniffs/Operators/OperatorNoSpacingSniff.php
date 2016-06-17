<?php

class Behance_Sniffs_Operators_OperatorNoSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_DOUBLE_COLON ];

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $this->_test( $phpcsFile, 1, $stackPtr );
    $this->_test( $phpcsFile, -1, $stackPtr );

  } // process

  /**
   * @param  PHP_CodeSniffer_File $phpcsFile
   * @param  int                  $offset
   * @param  int                  $stackPtr
   *
   * @return void
   */
  private function _test( PHP_CodeSniffer_File $phpcsFile, $offset, $stackPtr ) {

    $tokens     = $phpcsFile->getTokens();
    $data       = [ $tokens[ $stackPtr ]['content'] ];
    $inspectPtr = $stackPtr + $offset;
    $locString  = ( $offset === 1 )
                  ? 'after'
                  : 'before';

    if ( $tokens[ $inspectPtr ]['code'] === T_WHITESPACE ) {
      $error = '"%s" operator should have no whitespace ' . $locString . ' it';
      $fix   = $phpcsFile->addFixableError( $error, $stackPtr, $locString, $data );
      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( $inspectPtr, '' );
      }
    } // if whitespace

  } // _test

} // Behance_Sniffs_Operators_OperatorNoSpacingSniff
