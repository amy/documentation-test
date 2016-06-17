<?php
class Behance_Sniffs_Comments_TrailingCommentSniff implements PHP_CodeSniffer_Sniff {

  public $minLinesRequiredForTrailing = 4;

  protected $descriptionNotRequired = [
      T_TRY,
      T_ELSE
  ];

  protected $nameTrailing = [
      T_FUNCTION  => 'function',
      T_CLASS     => 'class',
      T_INTERFACE => 'interface',
      T_TRAIT     => 'trait'
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return PHP_CodeSniffer_Tokens::$scopeOpeners;

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

    $tokens          = $phpcsFile->getTokens();
    $scopeOpenerPtr  = $stackPtr;
    $scopeOpenerCode = $tokens[ $scopeOpenerPtr ]['code'];

    // ignore inline scopes
    if ( !isset( $tokens[ $scopeOpenerPtr ]['scope_opener'] ) ) {
      return;
    }

    $openCurlyPtr  = $tokens[ $scopeOpenerPtr ]['scope_opener'];
    $closeCurlyPtr = $tokens[ $openCurlyPtr ]['scope_closer'];
    $nextTokenPtr  = $closeCurlyPtr + 1;

    // ignore non-curly scopes such as the 'case' and 'default' keywords
    if ( $tokens[ $openCurlyPtr ]['content'] !== '{' ) {
      return;
    }

    if ( !isset( $tokens[ $nextTokenPtr ] ) ) {
      return;
    }

    // ignore single line scopes
    if ( $tokens[ $closeCurlyPtr ]['line'] === $tokens[ $scopeOpenerPtr ]['line'] ) {
      return;
    }

    // ignore unassigned closures
    if ( $scopeOpenerCode === T_CLOSURE && !$this->_isAssignedClosure( $scopeOpenerPtr, $phpcsFile ) ) {
      return;
    }

    // comment exists right after curly brace
    if ( $tokens[ $nextTokenPtr ]['code'] == T_COMMENT ) {
      $error = 'Single space required between closing curly brace & trailing comment';
      $phpcsFile->addError( $error, $closeCurlyPtr, 'MissingWhitespace' );
      return;
    }

    // newline right after closing brace - check that this is a control structure
    // and that it only has one line in it
    if ( $tokens[ $nextTokenPtr ]['content'] === $phpcsFile->eolChar ) {

      $numberOfLines = $this->_numberOfLinesInScope( $openCurlyPtr, $closeCurlyPtr, $phpcsFile );

      if ( $numberOfLines >= $this->minLinesRequiredForTrailing ) {
        $error = 'Missing required trailing comment for scope >= %s lines; found %s lines';
        $data  = [ $this->minLinesRequiredForTrailing, $numberOfLines ];
        $phpcsFile->addError( $error, $closeCurlyPtr, 'MissingTrailingComment', $data );
      }
      elseif ( isset( $this->nameTrailing[ $scopeOpenerCode ] ) ) {
        $error = 'Missing required trailing comment for %s';
        $data  = [ $this->nameTrailing[ $scopeOpenerCode ] ];
        $phpcsFile->addError( $error, $closeCurlyPtr, 'MissingTrailingComment', $data );
      }

      return;

    } // if there is no trailing comment

    // handle generic whitespace
    // at this point we're looking at a multiline scope
    if ( $tokens[ $nextTokenPtr ]['code'] == T_WHITESPACE ) {

      $whitespacePtr = $nextTokenPtr;
      $amountOfSpace = 0;

      while ( isset( $tokens[ $whitespacePtr ] ) && $tokens[ $whitespacePtr ]['code'] === T_WHITESPACE ) {

        $content        = str_replace( $phpcsFile->eolChar, '', $tokens[ $whitespacePtr ]['content'] );
        $amountOfSpace += strlen( $content );

        ++$whitespacePtr;

      } // while isset && is whitespace

      if ( $amountOfSpace > 1 ) {
        $phpcsFile->addError( 'Too much whitespace detected after curly brace', $closeCurlyPtr );
        return;
      }

    } // if there is whitespace right after curly brace

    $commentPtr = $closeCurlyPtr + 2;

    // make sure assignment closures have a semicolon
    if ( $this->_isAssignedClosure( $scopeOpenerPtr, $phpcsFile ) ) {
      if ( !isset( $tokens[ $closeCurlyPtr + 1 ] ) || $tokens[ $closeCurlyPtr + 1 ]['code'] !== T_SEMICOLON ) {
        $phpcsFile->addError( 'semicolon not found after anonymous function assignment', $closeCurlyPtr + 1 );

        return;
      } // if !semicolon

      $commentPtr++;
    } // if _isAssignedClosure

    if ( !isset( $tokens[ $commentPtr ] ) || $tokens[ $commentPtr ]['code'] !== T_COMMENT ) {
      $phpcsFile->addError( 'trailing comment not found after closing curly', $closeCurlyPtr );
      return;
    }

    // make sure that there is exactly 1 space between the slashes and the comment
    $comment = ltrim( $tokens[ $commentPtr ]['content'], '/' );

    if ( strlen( $comment ) < 2 || $comment[0] !== ' ' || $comment[1] === ' '  ) {

      $phpcsFile->addError( 'Trailing comment formatted incorrectly; // <comment>', $closeCurlyPtr );

      return;

    } // if empty comment or missing whitespace

    $this->_processDeclarationName( $closeCurlyPtr, $scopeOpenerPtr, $phpcsFile );

  } // process

  protected function _processDeclarationName( $closeCurlyPtr, $scopeOpenerPtr, PHP_CodeSniffer_File $phpcsFile ) {

    $tokens = $phpcsFile->getTokens();

    $commentPtr = $closeCurlyPtr + 2;

    $longTrailing = [
        T_WHILE     => 'while',
        T_FOR       => 'for',
        T_FOREACH   => 'foreach',
        T_IF        => 'if',
        T_ELSE      => 'else',
        T_ELSEIF    => 'elseif',
        T_DO        => 'do',
        T_TRY       => 'try',
        T_CATCH     => 'catch',
        T_SWITCH    => 'switch'
    ];

    // ensure declaration names match expected comments
    $codeValues   = array_merge( array_keys( $this->nameTrailing ), array_keys( $longTrailing ) );
    $nameValues   = array_merge( $this->nameTrailing, $longTrailing );
    $scopeTypeMap = array_combine( $codeValues, $nameValues );

    if ( !isset( $scopeTypeMap[ $tokens[ $scopeOpenerPtr ]['code'] ] ) ) {
      return;
    }

    $scopeCode = $tokens[ $scopeOpenerPtr ]['code'];
    $scopeType = $scopeTypeMap[ $scopeCode ];

    $declarationName = ( in_array( $scopeType, $this->nameTrailing ) )
                       ? $phpcsFile->getDeclarationName( $scopeOpenerPtr )
                       : $scopeType;
    $expectedComment = "// {$declarationName}";
    $actualComment   = trim( $tokens[ $commentPtr ]['content'] );

    if ( in_array( $scopeType, $this->nameTrailing ) && $expectedComment !== $actualComment ) {

      $error = 'Trailing comment for %s "%s" incorrect; expected "%s", found "%s"';
      $data  = [ $scopeType, $declarationName, $expectedComment, $actualComment ];

      $phpcsFile->addError( $error, $closeCurlyPtr, 'InvalidFunctionTrailingComment', $data );

      return;

    } // if nameTrailing and not matching

    elseif ( in_array( $scopeType, $longTrailing ) ) {

      $hasExpected = ( strpos( $actualComment, $expectedComment ) === 0 );
      $error       = 'Control structure trailing comment not structured properly; expected "%s", found "%s"';
      $data        = [ $expectedComment . ' <description>', $actualComment ];

      if ( !$hasExpected ) {
        $phpcsFile->addError( $error, $closeCurlyPtr, 'InvalidTrailingComment', $data );
        return;
      }

      if ( strlen( $actualComment ) <= strlen( $expectedComment ) && !in_array( $scopeCode, $this->descriptionNotRequired ) ) {
        $phpcsFile->addError( $error, $closeCurlyPtr, 'InvalidTrailingComment', $data );
        return;
      }

      if ( preg_match( '/\$/', $actualComment ) === 1 ) {
        $phpcsFile->addError( 'No PHP variable-like names in trailing comments', $closeCurlyPtr, 'InvalidTrailingComment' );
        return;
      }

    } // elseif is longtrailing scope

  } // _processDeclarationName

  /**
   * closures need a semicolon before the trailing comment, but only when it's an assignment
   *
   * @param   int   $curlyOpenerPtr
   * @param   PHP_CodeSniffer_File $phpcsFile
   * @return  boolean
   */
  protected function _isAssignedClosure( $curlyOpenerPtr, PHP_CodeSniffer_File $phpcsFile ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $curlyOpenerPtr ]['code'] !== T_CLOSURE ) {
      return false;
    }

    $assignmentPtr = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, $curlyOpenerPtr - 1, null, true );

    return in_array( $tokens[ $assignmentPtr ]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens );

  } // _isAssignedClosure

  /**
   * Starts from the *beginning* of the scope (ie: where '{' is)
   *
   * @param   array $tokens
   * @param   int   $scopeBeginPtr
   * @param   int   $scopeEndPtr
   * @param   PHP_CodeSniffer_File $phpcsFile
   * @return  int
   */
  protected function _numberOfLinesInScope( $scopeBeginPtr, $scopeEndPtr, PHP_CodeSniffer_File $phpcsFile ) {

    $tokens = $phpcsFile->getTokens();

    return max( 0, $tokens[ $scopeEndPtr ]['line'] - $tokens[ $scopeBeginPtr ]['line'] - 1 );

  } // _numberOfLinesInScope

} // Behance_Sniffs_Comments_TrailingCommentSniff
