<?php
class Behance_Sniffs_Operators_OperatorSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * @var array
   *
   * Unary operators that may or may not require spaces after them
   * depending on their context
   */
  protected $_unary = [
      T_EQUAL,
      T_BITWISE_AND,
      T_MINUS,
      T_BOOLEAN_NOT
  ];

  /**
   * @var array
   *
   * Tokens before an operator that *can* be unary that would indicate
   * that it's actually being used in a unary context, will be defined in process()
   */
  protected $_unaryIndicators;


  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return array_unique( array_merge(
        PHP_CodeSniffer_Tokens::$assignmentTokens,
        PHP_CodeSniffer_Tokens::$comparisonTokens,
        PHP_CodeSniffer_Tokens::$operators,
        $this->_unary,
        [ T_STRING_CONCAT ]
    ) );

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

    $this->_unaryIndicators = array_merge( PHP_CodeSniffer_Tokens::$comparisonTokens, PHP_CodeSniffer_Tokens::$assignmentTokens, [
        T_COLON,
        T_COMMA,
        T_INLINE_ELSE,
        T_INLINE_THEN,
        T_OPEN_PARENTHESIS,
        T_OPEN_SQUARE_BRACKET,
        T_OPEN_TAG,
        T_RETURN
    ] );

    $tokens = $phpcsFile->getTokens();
    // if token **can** be unary and successfully processed, return
    // otherwise, fallthrough to regular logic
    if ( in_array( $tokens[ $stackPtr ]['code'], $this->_unary ) ) {
      if ( $this->_processUnary( $phpcsFile, $stackPtr ) ) {
        return;
      }
    }

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $error = '"%s" operator requires whitespace before it';
      $data  = [ $tokens[ $stackPtr ]['content'] ];
      $phpcsFile->addError( $error, $stackPtr, 'OperatorPadding', $data );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE ) {
      $error = '"%s" operator requires whitespace after it';
      $data  = [ $tokens[ $stackPtr ]['content'] ];
      $phpcsFile->addError( $error, $stackPtr, 'OperatorPadding', $data );
    }

  } // process

  /**
   * Process an operator that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the token was evaluated as a unary operator or not
   */
  protected function _processUnary( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr ]['code'] === T_EQUAL && $tokens[ $stackPtr + 1 ]['code'] === T_BITWISE_AND ) {
      return true;
    }

    if ( $tokens[ $stackPtr ]['code'] === T_BITWISE_AND ) {
      return $this->_processAmpersand( $phpcsFile, $stackPtr );
    }
    if ( $tokens[ $stackPtr ]['code'] === T_MINUS ) {
      return $this->_processMinus( $phpcsFile, $stackPtr );
    }
    if ( $tokens[ $stackPtr ]['code'] === T_BOOLEAN_NOT ) {
      return $this->_processNot( $phpcsFile, $stackPtr );
    }

    return false;

  } // _processUnary

  /**
   * Process an ampersand that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the ampersand was evaluated as a unary operator or not
   */
  private function _processAmpersand( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens           = $phpcsFile->getTokens();
    $allowedTokens    = [
        T_EQUAL,
        T_COMMA,
        T_DOUBLE_ARROW,
        T_OPEN_PARENTHESIS,
        T_AS
    ];
    $nonWhitespacePtr = $phpcsFile->findPrevious( $allowedTokens, $stackPtr, null, false, null, true );

    if ( $nonWhitespacePtr === false || $tokens[ $nonWhitespacePtr ]['line'] !== $tokens[ $stackPtr ]['line'] ) {
      return false;
    }

    // Equal sign being used before ampersand - is unary (reference operator, not bitwise-and)

    switch ( $tokens[ $nonWhitespacePtr ]['code'] ) {

      case T_EQUAL:

        // @TODO: RE-ENABLE - per Bryan, after more talk

      //if ( $stackPtr - 1 !== $nonWhitespacePtr ) {
      //  $error = "Ampersand is not immediately after '='.";
      //  $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
      //}

      //if ( $tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE ) {
      //  $error = "Ampersand is not immediately followed by whitespace.";
      //  $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
      //}

        break;

      case T_COMMA:
      case T_DOUBLE_ARROW:
      case T_OPEN_PARENTHESIS:
      case T_AS:

        if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
          $error = "Ampersand requires whitespace before it.";
          $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
        }

        // @TODO: RE-ENABLE - per Bryan, after more talk

      //if ( $tokens[ $stackPtr + 1 ]['code'] !== T_VARIABLE ) {
      //  $error = "Ampersand is not immediately followed by a variable.";
      //  $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
      //}

        break;

    } // switch token code

    return true;

  } // _processAmpersand

  /**
   * Process an exclamation that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the exclamation was evaluated as a unary operator or not
   */
  private function _processNot( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( !in_array( $tokens[ $stackPtr - 1 ]['code'], [ T_WHITESPACE, T_BOOLEAN_NOT ] ) ) {
      $phpcsFile->addError( 'Boolean Not should have whitespace before it.', $stackPtr, 'BooleanNotSpacing' );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] === T_WHITESPACE ) {
      $phpcsFile->addError( 'Boolean Not should not have whitespace after it.', $stackPtr, 'BooleanNotSpacing' );
    }

    return true;

  } // _processNot

  /**
   * Process a minus that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the minus was evaluated as a unary operator or not
   */
  private function _processMinus( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens     = $phpcsFile->getTokens();
    $prevTokens = array_merge( $this->_unaryIndicators, [
        T_CLOSE_PARENTHESIS,
        T_CLOSE_SQUARE_BRACKET,
        T_VARIABLE,
        T_LNUMBER,
        T_STRING
    ] );
    $before     = $phpcsFile->findPrevious( $prevTokens, $stackPtr - 1, null, false, null, true );

    // if any of these are immediately before the '-', then it should be in a unary context
    if ( !in_array( $tokens[ $before ]['code'], $this->_unaryIndicators ) ) {
      return false;
    }

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $phpcsFile->addError( "'-' requires whitespace before it.", $stackPtr, 'MinusSpacing' );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] === T_WHITESPACE ) {
      $phpcsFile->addError( "'-' as unary should not have whitespace after it.", $stackPtr, 'MinusSpacing' );
    }

    return true;

  } // _processMinus

} // Behance_Sniffs_Operators_OperatorSpacingSniff
