<?php
class Behance_Sniffs_ControlStructures_InlineControlStructureSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [
        T_IF,
        T_ELSE,
        T_ELSEIF,
        T_FOREACH,
        T_WHILE,
        T_DO,
        T_SWITCH,
        T_FOR,
    ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int          $stackPtr  The position of the current token in the
   *                    stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( !isset( $tokens[ $stackPtr ]['scope_opener'] ) ) {

      // Ignore the ELSE in ELSE IF. We'll process the IF part later.
      if ( ($tokens[ $stackPtr ]['code'] === T_ELSE) && ($tokens[ ($stackPtr + 2) ]['code'] === T_IF) ) {
        return;
      }

      if ( $tokens[ $stackPtr ]['code'] === T_WHILE ) {

        // This could be from a DO WHILE, which doesn't have an opening brace.
        $lastContent = $phpcsFile->findPrevious( T_WHITESPACE, ($stackPtr - 1), null, true );
        if ( $tokens[ $lastContent ]['code'] === T_CLOSE_CURLY_BRACKET ) {

          $brace = $tokens[ $lastContent ];

          if ( isset( $brace['scope_condition'] ) ) {

            $condition = $tokens[ $brace['scope_condition'] ];
            if ( $condition['code'] === T_DO ) {
              return;
            }

          } // if isset scope_condition

        } // if T_CLOSE_CURLY_BRACKET

      } // if T_WHILE

      // This is a control structure without an opening brace,
      // so it is an inline statement.
      $phpcsFile->addError( 'Inline control structures are not allowed', $stackPtr, 'NotAllowed' );

      return;

    } // if !scope_opener

  } // process

} // Behance_Sniffs_ControlStructures_InlineControlStructureSniff
