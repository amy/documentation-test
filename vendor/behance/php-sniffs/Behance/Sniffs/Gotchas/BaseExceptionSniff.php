<?php
class Behance_Sniffs_Gotchas_BaseExceptionSniff implements PHP_CodeSniffer_Sniff {

  public $preferredException = 'Core_Exception';

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_CATCH, T_THROW ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
   * @param int          $stackPtr  The position of the current token in the
   *                    stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    $verb = ( $tokens[ $stackPtr ]['code'] === T_CATCH ) ? 'catch' : 'throw';
    $firstContent = $phpcsFile->findNext( T_STRING, $stackPtr + 1 );

    if ( $tokens[ $firstContent ]['content'] === 'Exception' ) {
      $phpcsFile->addError( 'Please ' . $verb . ' the most specific \Exception type possible', $firstContent );
    }

  } // process

} // Behance_Sniffs_Gotchas_BaseExceptionSniff
