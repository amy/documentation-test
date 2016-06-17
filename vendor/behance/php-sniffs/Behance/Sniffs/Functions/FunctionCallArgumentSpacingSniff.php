<?php
class Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_STRING ];

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

    // Skip tokens that are the names of functions or classes
    // within their definitions. For example:
    // function myFunction...
    // "myFunction" is T_STRING but we should skip because it is not a
    // function or method *call*.
    $functionName    = $stackPtr;
    $ignoreTokens    = PHP_CodeSniffer_Tokens::$emptyTokens;
    $ignoreTokens[]  = T_BITWISE_AND;
    $functionKeyword = $phpcsFile->findPrevious( $ignoreTokens, ($stackPtr - 1), null, true );
    if ( $tokens[ $functionKeyword ]['code'] === T_FUNCTION || $tokens[ $functionKeyword ]['code'] === T_CLASS ) {
      return;
    }

    // If the next non-whitespace token after the function or method call
    // is not an opening parenthesis then it cant really be a *call*.
    $openBracket = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ($functionName + 1), null, true );
    if ( $tokens[ $openBracket ]['code'] !== T_OPEN_PARENTHESIS ) {
      return;
    }

    // No need to inspect functions with no arguments and no space
    if ( $tokens[ $openBracket + 1 ]['code'] === T_CLOSE_PARENTHESIS ) {
      return;
    }

    // Error for spaces present with no argument
    if ( $tokens[ $openBracket + 2 ]['code'] === T_CLOSE_PARENTHESIS ) {
      $this->_ensureNoSpace( $phpcsFile, $openBracket + 1, 'NoSpaceBetweenParensWithNoArgument' );
    }

    $closeBracket = $tokens[ $openBracket ]['parenthesis_closer'];

    $this->_ensureSpaceBefore( $phpcsFile, $closeBracket, 'SpaceBeforeCloseParens' );
    $this->_ensureSpaceAfter( $phpcsFile, $openBracket, 'SpaceAfterOpenParens' );

    $nextSeparator = $openBracket;
    while ( ( $nextSeparator = $phpcsFile->findNext( [ T_COMMA, T_VARIABLE, T_CLOSURE ], $nextSeparator + 1, $closeBracket ) ) !== false ) {

      if ( $tokens[ $nextSeparator ]['code'] === T_CLOSURE ) {
        $nextSeparator = $tokens[ $nextSeparator ]['scope_closer'];
        continue;
      }

      // Make sure the comma or variable belongs directly to this function call,
      // and is not inside a nested function call or array.
      $brackets    = $tokens[ $nextSeparator ]['nested_parenthesis'];
      $lastBracket = array_pop( $brackets );
      if ( $lastBracket !== $closeBracket ) {
        continue;
      }

      if ( $tokens[ $nextSeparator ]['code'] === T_COMMA ) {
        $this->_ensureNoSpace( $phpcsFile, $nextSeparator - 1, 'SpaceBeforeComma' );
        $this->_ensureSpaceAfter( $phpcsFile, $nextSeparator, 'NoSpaceAfterComma' );
      }
      else {
        $nextToken = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $nextSeparator + 1, $closeBracket, true );
        if ( $nextToken !== false && $tokens[ $nextToken ]['code'] === T_EQUAL ) {
          $this->_ensureSpaceBefore( $phpcsFile, $nextToken, 'NoSpaceBeforeEquals' );
          $this->_ensureSpaceAfter( $phpcsFile, $nextToken, 'NoSpaceAfterEquals' );
        }
      } // else

    } // while comma, var or closure

  } // process


  /**
   * @param  PHP_CodeSniffer_File $phpcsFile
   * @param  int                  $stackPtr
   * @param  string               $tag
   */
  private function _ensureSpaceBefore( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tag ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space before';
      $fix   = $phpcsFile->addFixableError( $error, $stackPtr, $tag );
      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( $stackPtr - 1, $tokens[ $stackPtr - 1 ]['content'] . ' ' );
      }
    } // if no space before

  } // _ensureSpaceBefore


  /**
   * @param  PHP_CodeSniffer_File $phpcsFile
   * @param  int                  $stackPtr
   * @param  string               $tag
   */
  private function _ensureSpaceAfter( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tag ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space after';
      $fix   = $phpcsFile->addFixableError( $error, $stackPtr, $tag );
      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( $stackPtr + 1, ' ' . $tokens[ $stackPtr + 1 ]['content'] );
      }
    } // if no space after

  } // _ensureSpaceAfter


  /**
   * @param  PHP_CodeSniffer_File $phpcsFile
   * @param  int                  $stackPtr
   * @param  string               $tag
   */
  private function _ensureNoSpace( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tag ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr ]['code'] === T_WHITESPACE ) {
      $error = 'Expected no space';
      $fix   = $phpcsFile->addFixableError( $error, $stackPtr, $tag );
      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( $stackPtr, '' );
      }
    } // if whitespace before comma

  } // _ensureNoSpace

} // Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniff
