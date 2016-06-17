<?php
class Behance_Sniffs_ControlStructures_ControlStructureSpacingSniff implements PHP_CodeSniffer_Sniff {

  protected $_noParens = [
      T_DO    => 'do',
      T_ELSE  => 'else',
      T_TRY   => 'try'
  ];

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

      return [
          T_IF,
          T_WHILE,
          T_FOREACH,
          T_FOR,
          T_SWITCH,
          T_DO,
          T_ELSE,
          T_ELSEIF,
          T_TRY,
          T_CATCH
      ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token
   *                                        in the stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( !isset( $tokens[ $stackPtr ]['parenthesis_opener'] ) ) {

      $whitespacePtr = $stackPtr + 1;

      if ( $tokens[ $whitespacePtr ]['code'] !== T_WHITESPACE ) {
        $type  = $this->_noParens[ $tokens[ $stackPtr ]['code'] ];
        $error = "Expected at least 1 space after '{$type}'";
        $phpcsFile->addError( $error, $whitespacePtr, 'SpacingAfterControlStructure' );
      }

      return;

    } // if T_ELSE or T_DO

    $code = $tokens[ $stackPtr ]['code'];
    $parenOpener = $tokens[ $stackPtr ]['parenthesis_opener'];
    $parenCloser = $tokens[ $stackPtr ]['parenthesis_closer'];

    if ( $tokens[ ($parenOpener + 1) ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space after opening bracket';
      $phpcsFile->addError( $error, $parenOpener, 'SpacingAfterOpenBrace' );
    } // if SpacingAfterOpenBrace

    // catch is not a control structure, it is a reserved word
    // therefore, it needs zero spaces before it, which is handled by another sniff
    if ( $code !== T_CATCH && $tokens[ ($parenOpener - 1) ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space before opening bracket';
      $phpcsFile->addError( $error, $parenOpener, 'SpacingBeforeOpenBrace' );
    } // if SpacingBeforeOpenBrace

    if ( $tokens[ $parenOpener ]['line'] === $tokens[ $parenCloser ]['line'] ) {

      if ( $tokens[ ($parenCloser + 1) ]['code'] !== T_WHITESPACE ) {
        $error = 'Expected at least 1 space after closing bracket';
        $phpcsFile->addError( $error, ($parenCloser + 1), 'SpaceAfterCloseBrace' );
      } // if SpaceAfterCloseBrace

      if ( $tokens[ ($parenCloser - 1) ]['code'] !== T_WHITESPACE ) {
        $error = 'Expected at least 1 space before closing bracket';
        $phpcsFile->addError( $error, $parenCloser, 'SpaceBeforeCloseBrace' );
      } // if SpaceBeforeCloseBrace

    } // if parens ends on same line as open

  } // process

} // Behance_Sniffs_ControlStructures_ControlStructureSpacingSniff
