<?php
class Behance_Sniffs_Comments_DisallowHashCommentsSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_COMMENT ];

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr ]['content'][0] === '#' ) {

      $error = 'Hash comments are prohibited; found %s';
      $data  = [ trim( $tokens[ $stackPtr ]['content'] ) ];

      $phpcsFile->addError( $error, $stackPtr, 'Found', $data );

    } // if first char

  } // process

} // Behance_Sniffs_Comments_DisallowHashCommentsSniff
