<?php
class Behance_Sniffs_Gotchas_DefaultLoggerSniff implements PHP_CodeSniffer_Sniff {

  const DEFAULT_LOGGER_CONSTANT = 'LOG_CHANNEL_DEFAULT';

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_CONST ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens                 = $phpcsFile->getTokens();

    $correct_token_sequence = function( $index ) use ( $phpcsFile, $tokens ) {

      $expected_tokens_list = [
          T_EQUAL,
          T_STRING,
          T_DOUBLE_COLON, // T_PAAMAYIM_NEKUDOTAYIM
          T_STRING,
          [ T_COMMA, T_SEMICOLON ]
      ];

      while ( $expected_tokens = (array) array_shift( $expected_tokens_list ) ) {

        $index = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $index, null, true, null, true );

        if ( !in_array( $tokens[ $index++ ]['code'], $expected_tokens ) ) {
          return false;
        }

      } // while looping through expected tokens

      return true;

    }; // tokens_match


    while ( $string_index = $phpcsFile->findNext( T_STRING, $stackPtr, null, false, null, true ) ) {

      $stackPtr = $string_index + 1;

      if ( $tokens[ $string_index ]['content'] === self::DEFAULT_LOGGER_CONSTANT ) {
        if ( !$correct_token_sequence( $stackPtr ) ) {
          $phpcsFile->addError( sprintf( 'Please use a constant for the value of "%s"', self::DEFAULT_LOGGER_CONSTANT ), $string_index );
        }
        return;
      } // if value of token is the logger constant

    } // while looping through the tokens

  } // process

} // Behance_Sniffs_Gotchas_DefaultLoggerSniff
