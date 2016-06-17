<?php
class Behance_Sniffs_Keywords_KeywordParensSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   * Taken from http://www.php.net/manual/en/reserved.keywords.php
   *
   * @return array
   */
  public function register() {

    return [
        T_ARRAY,
        T_CATCH,
        T_ECHO,
        T_EMPTY,
        T_EVAL,
        T_EXIT,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_ISSET,
        T_LIST,
        T_PRINT,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_UNSET
    ];

  } // register

  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    $nextNonEmpty = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true );
    $hasNoParens = $tokens[ $nextNonEmpty ]['code'] !== T_OPEN_PARENTHESIS;

    if ( $hasNoParens ) {
      $code = $tokens[ $stackPtr ]['code'];
      if ( $code === T_PRINT || $code === T_ECHO || $code === T_EXIT ) {
        return;
      }

      $error = 'Expected parentheses for keyword ' . $tokens[ $stackPtr ]['content'];
      $phpcsFile->addError( $error, $stackPtr + 1, 'MissingParens' );
      return;

    } // if hasNoParens

    if ( $tokens[ $stackPtr + 1 ]['code'] !== T_OPEN_PARENTHESIS ) {
      $error = 'Expected no space before opening parenthesis';
      $phpcsFile->addError( $error, $stackPtr + 1, 'NoSpaceBeforeOpenParens' );
      return;
    }

    $openingSpace = $tokens[ $stackPtr + 2 ]['code'];

    // No need to inspect calls with no arguments
    if ( $openingSpace === T_CLOSE_PARENTHESIS ) {
      return;
    }

    if ( $openingSpace !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space after opening parenthesis';
      $phpcsFile->addError( $error, $stackPtr + 2, 'SpaceAfterOpenParens' );
      return;
    }

    $closeParens = $phpcsFile->findNext( T_CLOSE_PARENTHESIS, $stackPtr + 1 );

    if ( $tokens[ $closeParens - 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space before closing parenthesis';
      $phpcsFile->addError( $error, $closeParens, 'SpaceBeforeCloseParens' );
      return;
    }

  } // process

} // Behance_Sniffs_Keywords_KeywordParensSpacingSniff
