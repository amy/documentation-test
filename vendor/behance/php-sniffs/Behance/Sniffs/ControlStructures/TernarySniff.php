<?php
class Behance_Sniffs_ControlStructures_TernarySniff implements PHP_CodeSniffer_Sniff {

  /** @var PHP_CodeSniffer_File $_phpcsFile */
  private $_phpcsFile;

  private $_stackPtr;
  private $_tokens;
  private $_start_of_statement;
  private $_previous_open_parenthesis;

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_INLINE_THEN ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param  PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param  int                  $stackPtr  The position of the current token in the stack passed in $tokens.
   *
   * @return bool|void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $this->_phpcsFile = $phpcsFile;
    $this->_tokens    = $phpcsFile->getTokens();
    $this->_stackPtr  = $stackPtr;
    $current_column   = $this->_tokens[ $stackPtr ]['column'];
    $code             = 'InlineTernary';

    if ( $this->_isOptionalAssignment() ) {
      return;
    }

    if ( $this->_isSingleLineTernary() ) {
      $error = 'Single-line ternary statements are not allowed';
      return $this->_phpcsFile->addError( $error, $this->_stackPtr, $code );
    }


    $this->_start_of_statement        = $this->_phpcsFile->findStartOfStatement( $stackPtr );
    $this->_previous_open_parenthesis = $this->_getOpeningParenthesisPrecededByWhitespace();

    if ( $this->_isMultilineTernaryWithoutWrappingParenthesis() ) {
      $error = 'The first statement must be wrapped in parenthesis';
      return $this->_phpcsFile->addError( $error, $this->_stackPtr, $code );
    }


    // only check for alignment and fix once it is determined that the ternary is a basically valid multi-line statement
    $next_inline_else         = $this->_phpcsFile->findNext( T_INLINE_ELSE, $stackPtr );
    $next_inline_else_column  = $this->_tokens[ $next_inline_else ]['column'];
    $desired_column           = $this->_tokens[ $this->_previous_open_parenthesis ]['column'];
    $correct_number_of_spaces = str_repeat( ' ', $desired_column - 1 );
    $error                    = 'Please align ternary expression. Expected %s; found %s';

    // check and add errors for inline-then row
    if ( $desired_column !== $current_column ) {

      $data               = [ $desired_column, $current_column ];
      $fix_option_enabled = $this->_phpcsFile->addFixableError( $error, $this->_stackPtr, $code, $data );

      if ( $fix_option_enabled === true ) {
        $this->_fixAlignment( $correct_number_of_spaces, $this->_stackPtr );
      }

    } // if an alignment error was found on the inline-then

    // check and add errors for inline-else row
    if ( $desired_column !== $next_inline_else_column ) {

      $data               = [ $desired_column, $next_inline_else_column ];
      $fix_option_enabled = $this->_phpcsFile->addFixableError( $error, $next_inline_else, $code, $data );

      if ( $fix_option_enabled === true ) {
        $this->_fixAlignment( $correct_number_of_spaces, $next_inline_else );
      }

    } // if an alignment error was found on the inline-else

  } // process


  /**
   * @return bool
   */
  private function _isSingleLineTernary() {

    $next_inline_then      = $this->_phpcsFile->findNext( T_INLINE_ELSE, $this->_stackPtr );
    $next_inline_then_line = $this->_tokens[ $next_inline_then ]['line'];
    $current_line          = $this->_tokens[ $this->_stackPtr ]['line'];

    return $current_line === $next_inline_then_line;

  } // _isSingleLineTernary


  /**
   * @param  int   $correct_number_of_spaces
   * @param  int   $current_index
   */
  private function _fixAlignment( $correct_number_of_spaces, $current_index ) {

    if ( $this->_tokens[ $current_index ]['column'] === 1 ) {
      return $this->_phpcsFile->fixer->addContentBefore( $current_index, $correct_number_of_spaces );
    }

    $this->_phpcsFile->fixer->replaceToken( ( $current_index - 1 ), $correct_number_of_spaces );

  } // _fixAlignment


  /**
   * @return int
   */
  private function _getOpeningParenthesisPrecededByWhitespace() {

    $previous_open_parenthesis = $this->_phpcsFile->findPrevious( T_OPEN_PARENTHESIS, $this->_stackPtr );

    while ( $this->_isPrecededByWhitespace( $previous_open_parenthesis ) ) {
      $previous_open_parenthesis = $this->_phpcsFile->findPrevious( T_OPEN_PARENTHESIS, ( $previous_open_parenthesis - 1 ) );
    }

    return $previous_open_parenthesis;

  } // _getOpeningParenthesisPrecededByWhitespace


  /**
   * @return bool
   */
  private function _isOptionalAssignment() {

    return $this->_tokens[ $this->_stackPtr + 1 ]['code'] == T_INLINE_ELSE;

  } // _isOptionalAssignment


  /**
   * @return bool
   */
  private function _isMultilineTernaryWithoutWrappingParenthesis() {

    return ( $this->_previous_open_parenthesis < $this->_start_of_statement ) || $this->_isPrecededByWhitespace( $this->_previous_open_parenthesis );

  } // _isMultilineTernaryWithoutWrappingParenthesis


  /**
   * @param  $index
   *
   * @return bool
   */
  private function _isPrecededByWhitespace( $index ) {

    return $this->_tokens[ $index - 1 ]['code'] !== T_WHITESPACE;

  } // _isPrecededByWhitespace

} // Behance_Sniffs_ControlStructures_TernarySniff
