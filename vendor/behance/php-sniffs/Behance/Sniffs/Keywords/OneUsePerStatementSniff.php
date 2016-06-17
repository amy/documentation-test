<?php

class Behance_Sniffs_Keywords_OneUsePerStatementSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   * Taken from http://www.php.net/manual/en/reserved.keywords.php
   *
   * @return array
   */
  public function register() {

    return [
        T_USE
    ];

  } // register

  /**
   * Returns whether the current use token is inside a closure.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the stack passed in $tokens.
   *
   * @return bool
   */
  private function _isTraitUse( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();
    $next   = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );

    if ( $tokens[ $next ]['code'] === T_OPEN_PARENTHESIS ) {
      return false;
    }

    return $phpcsFile->hasCondition( $stackPtr, [ T_CLASS, T_TRAIT ] );

  } // _isTraitUse

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

    if ( !$this->_isTraitUse( $phpcsFile, $stackPtr ) ) {
      return;
    }

    $tokens = $phpcsFile->getTokens();
    $next   = $phpcsFile->findNext( [ T_COMMA, T_SEMICOLON ], ( $stackPtr + 1 ) );

    // One space after the use keyword.
    if ( $tokens[ $stackPtr + 1 ]['content'] !== ' ' ) {
      $error = 'There must be a single space after the USE keyword';
      $fix   = $phpcsFile->addFixableError( $error, $stackPtr, 'SpaceAfterUse' );
      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), ' ' );
      }
    } // if content !== ' '

    // Find a comma after `use` before a semicolon
    if ( $tokens[ $next ]['code'] === T_COMMA ) {

      // ensure the comma isn't part of a conflict resolution statement:
      // e.g. use A, B { B::blah insteadof A }
      $statementEnd = $phpcsFile->findNext( [ T_OPEN_CURLY_BRACKET, T_SEMICOLON ], ( $next + 1 ) );

      if ( $tokens[ $statementEnd ]['code'] === T_SEMICOLON ) {

        $error = 'There must be one USE keyword per declaration';
        $fix   = $phpcsFile->addFixableError( $error, $next, 'MultipleDeclarations' );

        if ( $fix ) {
          $indent = str_repeat( ' ', $tokens[ $stackPtr ]['column'] - 1 );
          $phpcsFile->fixer->replaceToken( $next, ';' . $phpcsFile->eolChar . $indent . 'use ' );
        }

      } // if semicolon

    } // if comma

  } // process

} // Behance_Sniffs_Keywords_OneUsePerStatementSniff
