<?php
class Behance_Sniffs_Functions_FunctionDeclarationSniff implements PHP_CodeSniffer_Sniff {

  const INCORRECT_PREFIX            = 'IncorrectFunctionPrefix';
  const INCORRECT_DOUBLE_UNDERSCORE = 'IncorrectDoubleUnderscoreFunctionPrefix';
  const INCORRECT_NEWLINES          = 'InvalidFunctionNewlineFormatting';
  const INCORRECT_CURLY             = 'InvalidFunctionCurlySpacing';
  const INVALID_ARG_FORMAT          = 'InvalidArgumentListFormat';
  const MULTILINE_FUNC              = 'MultilineFunctionsNotAllowed';
  const NON_EMPTY_SINGLELINE        = 'NonEmptySingleLine';

  public $functionScopePrefixes = [
      'private'    => '_',
      'protected'  => '_',
      'public'     => ''
  ];

  /**
   * A list of methods where a double underscore is allowed as a prefix
   *
   * @var array
   */
  public $doubleUnderscoreAllowedMethods = [
      'init'
  ];

  /**
   * A list of methods where a single underscore is allowed as a prefix
   *
   * @var array
   */
  public $prefixExemptions = [
      'protected' => [
          'setUp',    // phpunit
          'tearDown'  // phpunit
      ],
      'public' => [
          '_start_work', // gearman workers
          '_end_work',   // gearman workers
          '_flush_cache', // gearman workers
      ],
      'private' => []
  ];

  /**
   * A list of all PHP magic methods. Must always be declared here in
   * all lower case.
   *
   * @var array
   */
  protected $magicMethods = [
      'construct',
      'destruct',
      'call',
      'callstatic',
      'get',
      'set',
      'isset',
      'unset',
      'sleep',
      'wakeup',
      'tostring',
      'set_state',
      'clone',
      'invoke',
      'call',
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_FUNCTION ];

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

    $this->_processFunctionName( $phpcsFile, $stackPtr );
    $this->_processDefinitionWhitespace( $phpcsFile, $stackPtr );
    $this->_processCurlyBraceNewlines( $phpcsFile, $stackPtr );

  } // process

  /**
   * Makes sure that words in the function definition are spaced well
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processDefinitionWhitespace( $phpcsFile, $stackPtr ) {

    $tokens     = $phpcsFile->getTokens();

    $indices    = [
        'parenthesis_opener',
        'parenthesis_closer',
        'scope_opener'
    ];

    $missingIndices = array_diff( $indices, array_keys( $tokens[ $stackPtr ] ) );

    // interface functions don't have parens
    if ( !empty( $missingIndices ) ) {
      return;
    }

    $parenOpen  = $tokens[ $stackPtr ]['parenthesis_opener'];
    $parenClose = $tokens[ $stackPtr ]['parenthesis_closer'];
    $curlyOpen  = $tokens[ $stackPtr ]['scope_opener'];

    if ( $tokens[ $parenOpen ]['line'] !== $tokens[ $parenClose ]['line'] ) {
      $error = 'Multiline function definitions not allowed';
      $phpcsFile->addError( $error, $parenOpen, static::MULTILINE_FUNC );
      return;
    }

    if ( $tokens[ $parenClose ]['line'] !== $tokens[ $curlyOpen ]['line'] ) {
      $error = 'Opening curly must be be on the same line as the closing parenthesis';
      $phpcsFile->addError( $error, $curlyOpen, static::INCORRECT_CURLY );
      return;
    }

    if ( $tokens[ $parenClose ]['column'] !== $tokens[ $curlyOpen ]['column'] - 2 ) {
      $error = 'Expected 1 space between closing parenthesis and open curly';
      $phpcsFile->addError( $error, $curlyOpen, static::INCORRECT_CURLY );
      return;
    }

    // valid - function blah() {}
    if ( $parenOpen + 1 === $parenClose ) {
      return;
    }

    // check whitespace after first parenth
    if ( $tokens[ $parenOpen + 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'No whitespace found between opening parenthesis & first argument';
      $phpcsFile->addError( $error, $parenOpen + 1, static::INVALID_ARG_FORMAT );
    }
    elseif ( strlen( $tokens[ $parenOpen + 1 ]['content'] ) > 1 ) {
      $error = 'Expected 1 space between opening parenthesis & first argument; found %s';
      $data  = [ strlen( $tokens[ $parenOpen + 1 ]['content'] ) ];
      $phpcsFile->addError( $error, $parenOpen + 1, static::INVALID_ARG_FORMAT, $data );
    }

    // whitespace after closing parenth
    if ( $tokens[ $parenClose - 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'No whitespace found between last argument & closing parenthesis';
      $phpcsFile->addError( $error, $parenClose, static::INVALID_ARG_FORMAT );
    }
    elseif ( strlen( $tokens[ $parenClose - 1 ]['content'] ) > 1 ) {
      $error = 'Expected 1 space between last argument & closing parenthesis; found %s';
      $data  = [ strlen( $tokens[ $parenClose - 1 ]['content'] ) ];
      $phpcsFile->addError( $error, $parenClose - 1, static::INVALID_ARG_FORMAT, $data );
    }

  } // _processDefinitionWhitespace

  /**
   * Makes sure that there is an empty line below the fxs opening curly brace
   * and one above the closing curly brace
   *
   * A tiny bit janky because newlines in comments are not treated as separate
   * whitespace tokens
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processCurlyBraceNewlines( $phpcsFile, $stackPtr ) {

    $tokens       = $phpcsFile->getTokens();

    // interface functions have no curly braces!
    if ( !isset( $tokens[ $stackPtr ]['scope_opener'] ) ) {
      return;
    }

    $openingBrace = $tokens[ $stackPtr ]['scope_opener'];
    $closingBrace = $tokens[ $stackPtr ]['scope_closer'];

    if ( $tokens[ $openingBrace ]['line'] === $tokens[ $closingBrace ]['line'] ) {

      if ( $openingBrace + 1 !== $closingBrace ) {
        $error = 'Single line function not empty';
        $phpcsFile->addError( $error, $stackPtr, static::NON_EMPTY_SINGLELINE );
      }

      return;

    } // if opening curly bracket on same line as closing


    if ( $tokens[ $openingBrace + 1 ]['content'] !== $phpcsFile->eolChar ) {
      $error = 'Newline not found immediately after opening curly bracket';
      $phpcsFile->addError( $error, $openingBrace, static::INCORRECT_NEWLINES );
    }

    if ( $tokens[ $openingBrace + 2 ]['content'] !== $phpcsFile->eolChar ) {
      $error = 'Empty line not found immediately after function definition; there was trailing whitespace or non-whitespace';
      $phpcsFile->addError( $error, $openingBrace, static::INCORRECT_NEWLINES );
    }

    $closingBrace = $tokens[ $openingBrace ]['bracket_closer'];
    $tracePtr     = $closingBrace - 1;
    $token        = $tokens[ $tracePtr ];

    // this can happen for multiple characters / tokens
    $whitespaceErrorAdded = false;

    while ( $tracePtr > 2 && $token['content'] !== $phpcsFile->eolChar && $token['code'] !== T_COMMENT ) {

      if ( $token['code'] !== T_WHITESPACE && !$whitespaceErrorAdded ) {
        $whitespaceErrorAdded = true;
        $error = 'Non-whitespace found before closing curly brace';
        $phpcsFile->addError( $error, $tracePtr, static::INCORRECT_NEWLINES );
      }

      --$tracePtr;

      $token = $tokens[ $tracePtr ];

    } // while content !== EOL

    $upperLineEnd   = $tokens[ $tracePtr - 1 ]['content'];
    $upperLineBegin = $tokens[ $tracePtr - 2 ]['content'];

    // should see two newlines consecutively
    // as in:
    //   ...\n
    //   \n
    //   ...}
    if ( $upperLineEnd !== $phpcsFile->eolChar && $upperLineBegin !== $phpcsFile->eolChar ) {

      $hasCommentAbove = $tokens[ $tracePtr - 1 ]['code'] === T_COMMENT;

      // special case where a comment is directly above the empty newline
      // newline is NOT treated as a separate token at this point
      if ( $hasCommentAbove ) {

        $comment = strrev( $tokens[ $tracePtr - 1 ]['content'] );

        if ( $comment[0] === $phpcsFile->eolChar ) {
          return;
        }

      } // if has comment above curly brace line

      $error = 'No empty newline found above closing curly brace';
      $phpcsFile->addError( $error, $closingBrace, static::INCORRECT_NEWLINES );

    } // if not 2x EOL

  } // _processCurlyBraceNewlines

  /**
   * Make sure that the function name is correctly formatted
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processFunctionName( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $methodProps    = $phpcsFile->getMethodProperties( $stackPtr );
    $scope          = $methodProps['scope'];
    $fxName         = $phpcsFile->getDeclarationName( $stackPtr );
    $expectedPrefix = $this->functionScopePrefixes[ $scope ];

    $doubleUnderAllowed = array_merge( $this->magicMethods, $this->doubleUnderscoreAllowedMethods );

    if ( strpos( $fxName, '__' ) === 0 ) {

      if ( in_array( strtolower( substr( $fxName, 2 ) ), $doubleUnderAllowed ) ) {
        return;
      }
      else {
        $error = '__ is a reserved prefix for magic functions';
        $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_DOUBLE_UNDERSCORE );
      }

    } // if fxName __ == 0

    // expected prefix is empty - just return, anything can happen
    if ( empty( $expectedPrefix ) ) {

      foreach ( $this->functionScopePrefixes as $prefix ) {

        if ( empty( $prefix ) ) {
          continue;
        }

        if ( strpos( $fxName, $prefix ) === 0 && ( !in_array( $fxName, $this->prefixExemptions[ $scope ] )) ) {
          $error = 'Expected no prefix for %s function "%s"; found "%s"';
          $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_PREFIX, [ $scope, $fxName, $prefix ] );
          return;
        }

      } // foreach functionScopePrefixes

    } // if empty expectedPrefix

    elseif ( strpos( $fxName, $expectedPrefix ) !== 0 ) {

      if ( isset( $this->prefixExemptions[ $scope ] ) && in_array( $fxName, $this->prefixExemptions[ $scope ] ) ) {
        return;
      }

      $error = 'Expected prefix "%s" for %s function "%s" not found';
      $data  = [ $expectedPrefix, $scope, $fxName ];

      if ( strtolower( $scope ) === 'protected' ) {
        return $phpcsFile->addWarning( $error, $stackPtr, static::INCORRECT_PREFIX, $data );
      }

      $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_PREFIX, $data );

    } // elseif !empty expectedPrefix && expected prefix not at beginning

  } // _processFunctionName

} // Behance_Sniffs_Functions_FunctionDeclarationSniff
