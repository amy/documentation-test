<?php
class Behance_Sniffs_Arrays_ArrayDeclarationSniff implements PHP_CodeSniffer_Sniff {

  /**
   * The number of spaces code should be indented.
   *
   * @var int
   */
  public $indent = 2;

  /**
   * The number of indents array elements should have.
   *
   * @var int
   */
  public $elementIndentLevel = 2;


  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_ARRAY, T_OPEN_SHORT_ARRAY ];

  } // register

  protected function _isArrayOpener( $token ) {

    return $token['code'] === T_ARRAY || $token['code'] === T_OPEN_SHORT_ARRAY;

  } // _isArrayOpener


  /**
   * Processes this sniff, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
   * @param int          $stackPtr  The position of the current token in the
   *                    stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    $isShortArray = $tokens[ $stackPtr ]['code'] !== T_ARRAY;

    // Array keyword should be lower case.
    if ( !$isShortArray && strtolower( $tokens[ $stackPtr ]['content'] ) !== $tokens[ $stackPtr ]['content'] ) {
      $error = 'Array keyword should be lower case; expected "array" but found "%s"';
      $data  = [ $tokens[ $stackPtr ]['content'] ];
      $phpcsFile->addError( $error, $stackPtr, 'NotLowerCase', $data );
    }

    if ( $isShortArray ) {
      $arrayStart = $tokens[ $stackPtr ]['bracket_opener'];
      $arrayEnd   = $tokens[ $arrayStart ]['bracket_closer'];
    }
    else {
      $arrayStart = $tokens[ $stackPtr ]['parenthesis_opener'];
      $arrayEnd   = $tokens[ $arrayStart ]['parenthesis_closer'];
    }

    $keywordStart = $tokens[ $stackPtr ]['column'];
    $indentPtr    = $phpcsFile->findFirstOnLine( PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr, true );
    $indentStart  = $tokens[ $indentPtr ]['column'];
    $indentSpaces = $this->indent * $this->elementIndentLevel;
    $isEmpty      = false;

    if ( !$isShortArray && $arrayStart != ($stackPtr + 1) ) {
      $error = 'There must be no space between the Array keyword and the opening parenthesis';
      $phpcsFile->addError( $error, $stackPtr, 'SpaceAfterKeyword' );
    }

    // Check for empty arrays.
    $content = $phpcsFile->findNext( [ T_WHITESPACE ], ($arrayStart + 1), ($arrayEnd + 1), true );
    if ( $content === $arrayEnd ) {
      $isEmpty = true;
      // Empty array, but if the brackets aren't together, there's a problem.
      if ( ($arrayEnd - $arrayStart) !== 1 ) {
        $error = 'Empty array declaration must have no spaces';
        $phpcsFile->addError( $error, $stackPtr, 'SpaceInEmptyArray' );

        // We can return here because there is nothing else to check. All code
        // below can assume that the array is not empty.
        return;
      } // if arrayEnd - arrayStart
    } // if content = arrayEnd

    if ( $tokens[ $arrayStart ]['line'] === $tokens[ $arrayEnd ]['line'] ) {

      if ( !$isEmpty ) {
        // ensure whitespace after start and before close
        if ( $tokens[ $arrayStart + 1 ]['content'] !== ' ' ) {
          $error = 'Expected exactly 1 space after array open';
          $phpcsFile->addError( $error, $stackPtr, 'NoSpaceAfterArrayOpen' );
        }

        if ( $tokens[ $arrayEnd - 1 ]['content'] !== ' ' ) {
          $error = 'Expected exactly 1 space before array close';
          $phpcsFile->addError( $error, $arrayEnd - 1, 'NoSpaceBeforeArrayClose' );
        }
      } // if !isEmpty

      // Single line array.
      // Check if there are multiple values. If so, then it has to be multiple lines
      // unless it is contained inside a function call or condition.
      $valueCount = 0;
      $commas   = [];
      for ( $i = ($arrayStart + 1); $i < $arrayEnd; $i++ ) {
        // Skip bracketed statements, like function calls.
        if ( $tokens[ $i ]['code'] === T_OPEN_PARENTHESIS ) {
          $i = $tokens[ $i ]['parenthesis_closer'];
          continue;
        }

        if ( $tokens[ $i ]['code'] === T_COMMA ) {
          // Before counting this comma, make sure we are not
          // at the end of the array.
          $next = $phpcsFile->findNext( T_WHITESPACE, ($i + 1), $arrayEnd, true );
          if ( $next !== false ) {
            $valueCount++;
            $commas[] = $i;
          }
          else {
            // There is a comma at the end of a single line array.
            $error = 'Comma not allowed after last value in single-line array declaration';
            $phpcsFile->addError( $error, $i, 'CommaAfterLast' );
          }
        } // if COMMA
      } // for arrayStart -> arrayEnd

      // Now check each of the double arrows (if any).
      $nextArrow = $arrayStart;
      while ( ( $nextArrow = $phpcsFile->findNext( T_DOUBLE_ARROW, ( $nextArrow + 1 ), $arrayEnd ) ) !== false ) {
        if ( $tokens[ $nextArrow - 1 ]['code'] !== T_WHITESPACE ) {
          $content = $tokens[ $nextArrow - 1 ]['content'];
          $error   = 'Expected at least 1 space between "%s" and double arrow; 0 found';
          $data  = [ $content ];
          $phpcsFile->addError( $error, $nextArrow, 'NoSpaceBeforeDoubleArrow', $data );
        } // if nextArrow - 1 !== T_WHITESPACE

        if ( $tokens[ ($nextArrow + 1) ]['code'] !== T_WHITESPACE ) {
          $content = $tokens[ ($nextArrow + 1) ]['content'];
          $error   = 'Expected at least 1 space between double arrow and "%s"; 0 found';
          $data  = [ $content ];
          $phpcsFile->addError( $error, $nextArrow, 'NoSpaceAfterDoubleArrow', $data );
        } // if nextArrow + 1 !== T_WHITESPACE
      } // while nextArrow

      if ( $valueCount > 0 ) {
        $conditionCheck = $phpcsFile->findPrevious( [ T_OPEN_PARENTHESIS, T_SEMICOLON ], ($stackPtr - 1), null, false );

        //TODO: potentially re-enable this
        // if (($conditionCheck === false) || ($tokens[ $conditionCheck ]['line'] !== $tokens[ $stackPtr ]['line'])) {
        //   $error = 'Array with multiple values cannot be declared on a single line';
        //   $phpcsFile->addError($error, $stackPtr, 'SingleLineNotAllowed');
        //   return;
        // }

        // We have a multiple value array that is inside a condition or
        // function. Check its spacing is correct.
        foreach ( $commas as $comma ) {
          if ( $tokens[ $comma + 1 ]['code'] !== T_WHITESPACE ) {
            $content = $tokens[ $comma + 1 ]['content'];
            $error = 'Expected at least 1 space between comma and "%s"; 0 found';
            $data = [ $content ];
            $phpcsFile->addError( $error, $comma, 'NoSpaceAfterComma', $data );
          } // if comma + 1 !== T_WHITESPACE

          if ( $tokens[ $comma - 1 ]['code'] === T_WHITESPACE ) {
            $content   = $tokens[ $comma - 2 ]['content'];
            $spaceLength = strlen( $tokens[ $comma - 1 ]['content'] );
            $error     = 'Expected 0 spaces between "%s" and comma; %s found';
            $data = [
                $content,
                $spaceLength,
            ];
            $phpcsFile->addError( $error, $comma, 'SpaceBeforeComma', $data );
          } // if tokens WHITESPACE
        } // foreach commas
      } // if valueCount

      return;
    } // if arrayStart line === arrayEnd line

    // Check the closing bracket is on a new line.
    $lastContent = $phpcsFile->findPrevious( T_WHITESPACE, ($arrayEnd - 1), $arrayStart, true );
    if ( $tokens[ $lastContent ]['line'] == ($tokens[ $arrayEnd ]['line']) ) {
      $error = 'Closer of array declaration must be on a new line';
      $phpcsFile->addError( $error, $arrayEnd, 'CloseBraceNewLine' );
    }
    elseif ( $tokens[ $arrayEnd ]['column'] !== $indentStart ) {
      // Check the closing bracket is lined up under the [ of the array opener.
      $expected = $indentStart;
      $found  = $tokens[ $arrayEnd ]['column'];
      $error  = 'Closer of array not aligned correctly; expected %s space(s) but found %s';
      $data   = [
          $expected,
          $found,
      ];
      $phpcsFile->addError( $error, $arrayEnd, 'CloseBraceNotAligned', $data );
    } // elseif arrayEnd column !== indentStart

    $nextToken  = $stackPtr;
    $lastComma  = $stackPtr;
    $keyUsed  = false;
    $singleUsed = false;
    $lastToken  = '';
    $indices  = [];
    $maxLength  = 0;

    // Find all the double arrows that reside in this scope.
    while ( ($nextToken = $phpcsFile->findNext( [ T_DOUBLE_ARROW, T_COMMA, T_ARRAY, T_OPEN_SHORT_ARRAY ], ($nextToken + 1), $arrayEnd )) !== false ) {
      $currentEntry = [];

      if ( $this->_isArrayOpener( $tokens[ $nextToken ] ) ) {
        // Let subsequent calls of this test handle nested arrays.
        $nextTokenString = $tokens[ $nextToken ]['code'] === T_ARRAY ? 'parenthesis' : 'bracket';
        $nextToken = $tokens[ $tokens[ $nextToken ][ $nextTokenString . '_opener' ] ][ $nextTokenString . '_closer' ];
        continue;
      } // if isArrayOpener

      if ( $tokens[ $nextToken ]['code'] === T_COMMA ) {
        $lastComma  = $nextToken;
        $stackPtrCount = isset( $tokens[ $stackPtr ]['nested_parenthesis'] )
          ? count( $tokens[ $stackPtr ]['nested_parenthesis'] )
          : 0;

        if ( !$isShortArray ) {
          $stackPtrCount++;
        }

        $nextPtrCount = isset( $tokens[ $nextToken ]['nested_parenthesis'] )
          ? count( $tokens[ $nextToken ]['nested_parenthesis'] )
          : 0;

        // This comma is inside more parenthesis than the ARRAY keyword,
        // then there it is actually a comma used to separate arguments
        // in a function call.
        if ( $nextPtrCount > $stackPtrCount ) {
          continue;
        }

        if ( $keyUsed === true && $lastToken === T_COMMA ) {
          $error = 'No key specified for array entry; first entry specifies key';
          $phpcsFile->addError( $error, $nextToken, 'NoKeySpecified' );
          return;
        }

        if ( $keyUsed === false ) {
          if ( $tokens[ ($nextToken - 1) ]['code'] === T_WHITESPACE ) {
            $content   = $tokens[ ($nextToken - 2) ]['content'];
            $spaceLength = strlen( $tokens[ ($nextToken - 1) ]['content'] );
            $error     = 'Expected 0 spaces between "%s" and comma; %s found';
            $data    = [
                $content,
                $spaceLength,
            ];
            $phpcsFile->addError( $error, $nextToken, 'SpaceBeforeComma', $data );
          } // if nextToken === T_WHITESPACE

          // Find the value, which will be the first token on the line,
          // excluding the leading whitespace.
          $valueContent = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ($nextToken - 1), null, true );
          while ( $tokens[ $valueContent ]['line'] === $tokens[ $nextToken ]['line'] ) {
            if ( $valueContent === $arrayStart ) {
              // Value must have been on the same line as the array
              // parenthesis, so we have reached the start of the value.
              break;
            }

            $valueContent--;
          } // while valueContent === nextToken

          $valueContent = $phpcsFile->findNext( T_WHITESPACE, ($valueContent + 1), $nextToken, true );
          $indices[]  = [ 'value' => $valueContent ];
          $singleUsed   = true;
        } // if !keyUsed

        $lastToken = T_COMMA;
        continue;
      } // if code T_COMMA

      if ( $tokens[ $nextToken ]['code'] === T_DOUBLE_ARROW ) {
        if ( $singleUsed === true ) {
          $error = 'Key specified for array entry; first entry has no key';
          $phpcsFile->addError( $error, $nextToken, 'KeySpecified' );
          return;
        }

        $currentEntry['arrow'] = $nextToken;
        $keyUsed         = true;

        // Find the start of index that uses this double arrow.
        $indexEnd   = $phpcsFile->findPrevious( T_WHITESPACE, ($nextToken - 1), $arrayStart, true );
        $index = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $lastComma + 1, $arrayEnd, true );

        $currentEntry['index']     = $index;
        $currentEntry['index_content'] = $phpcsFile->getTokensAsString( $index, ($indexEnd - $index + 1) );

        $indexLength = strlen( $currentEntry['index_content'] );
        if ( $maxLength < $indexLength ) {
          $maxLength = $indexLength;
        }

        // Find the value of this index.
        $nextContent       = $phpcsFile->findNext( [ T_WHITESPACE ], ($nextToken + 1), $arrayEnd, true );
        $currentEntry['value'] = $nextContent;
        $indices[]       = $currentEntry;
        $lastToken       = T_DOUBLE_ARROW;
      } // if code = T_DOUBLE_ARROW
    } // while nextToken

    // Check for mutli-line arrays that should be single-line.
    $singleValue = false;

    if ( empty( $indices ) ) {
      $singleValue = true;
    }
    elseif ( count( $indices ) === 1 && $lastToken === T_COMMA ) {
      // There may be another array value without a comma.
      $exclude   = PHP_CodeSniffer_Tokens::$emptyTokens;
      $exclude[]   = T_COMMA;
      $nextContent = $phpcsFile->findNext( $exclude, ($indices[0]['value'] + 1), $arrayEnd, true );
      if ( $nextContent === false ) {
        $singleValue = true;
      }
    } // elseif indices 1 and lastToken T_COMMA

    //TODO: potentially re-enable this
    // if ($singleValue === true) {
    //   // Array cannot be empty, so this is a multi-line array with
    //   // a single value. It should be defined on single line.
    //   $error = 'Multi-line array contains a single value; use single-line array instead';
    //   $phpcsFile->addError($error, $stackPtr, 'MultiLineNotAllowed');
    //   return;
    // }

    if ( $keyUsed === false && !empty( $indices ) ) {
      $count   = count( $indices );
      $lastIndex = $indices[ ($count - 1) ]['value'];

      $trailingContent = $phpcsFile->findPrevious( T_WHITESPACE, ($arrayEnd - 1), $lastIndex, true );
      //TODO: potentially re-enable this
      // if ($tokens[ $trailingContent ]['code'] !== T_COMMA) {
      //   $error = 'Comma required after last value in array declaration';
      //   $phpcsFile->addError($error, $trailingContent, 'NoCommaAfterLast');
      // }

      foreach ( $indices as $value ) {
        // ignore malformed arrays
        if ( empty( $value['value'] ) ) {
          continue;
        }

        if ( $tokens[ ($value['value'] - 1) ]['code'] === T_WHITESPACE ) {
          if ( $tokens[ $value['value'] ]['column'] !== ($indentStart + $indentSpaces) ) {
            $error = 'Array value not aligned correctly; expected %s spaces but found %s';
            $data  = [
                ($indentStart + $indentSpaces),
                $tokens[ $value['value'] ]['column'],
            ];
            $phpcsFile->addError( $error, $value['value'], 'ValueNotAligned', $data );
          } // if value column isnt indented
        } // if token value value is T_WHITESPACE
      } // foreach indices
    } // if !keyUsed and !empty indices

    $numValues = count( $indices );

    $indicesStart = ($indentStart + $indentSpaces);
    $arrowStart   = ($indicesStart + $maxLength + 1);
    $valueStart   = ($arrowStart + 3);
    foreach ( $indices as $index ) {
      if ( !isset( $index['index'] ) ) {
        // Array value only.
        if ( ($tokens[ $index['value'] ]['line'] === $tokens[ $stackPtr ]['line']) && ($numValues > 1) ) {
          $error = 'The first value in a multi-value array must be on a new line';
          $phpcsFile->addError( $error, $stackPtr, 'FirstValueNoNewline' );
        }

        continue;
      } // if index[index ]

      if ( $tokens[ $index['index'] ]['line'] === $tokens[ $stackPtr ]['line'] ) {
        $error = 'The first index in a multi-value array must be on a new line';
        $phpcsFile->addError( $error, $stackPtr, 'FirstIndexNoNewline' );
        continue;
      }

      if ( $tokens[ $index['index'] ]['column'] !== $indicesStart ) {
        $error = 'Array key not aligned correctly; expected %s spaces but found %s';
        $data  = [
            ($indicesStart - 1),
            ($tokens[ $index['index'] ]['column'] - 1),
        ];
        $phpcsFile->addError( $error, $index['index'], 'KeyNotAligned', $data );
        continue;
      } // if column !- indicesStart

      //TODO: re-enable this
      // if ($tokens[ $index['arrow']]['column'] !== $arrowStart) {
      //   $expected = ($arrowStart - (strlen($index['index_content']) + $tokens[ $index['index']]['column']));
      //   $found  = ($tokens[ $index['arrow']]['column'] - (strlen($index['index_content']) + $tokens[ $index['index']]['column']));

      //   $error = 'Array double arrow not aligned correctly; expected %s space(s) but found %s';
      //   $data  = [
      //         $expected,
      //         $found,
      //        ];
      //   $phpcsFile->addError($error, $index['arrow'], 'DoubleArrowNotAligned', $data);
      //   continue;
      // }

      // if ($tokens[ $index['value']]['column'] !== $valueStart) {
      //   $expected = ($valueStart - (strlen($tokens[ $index['arrow']]['content']) + $tokens[ $index['arrow']]['column']));
      //   $found  = ($tokens[ $index['value']]['column'] - (strlen($tokens[ $index['arrow']]['content']) + $tokens[ $index['arrow']]['column']));

      //   $error = 'Array value not aligned correctly; expected %s space(s) but found %s';
      //   $data  = [
      //         $expected,
      //         $found,
      //        ];
      //   $phpcsFile->addError($error, $index['arrow'], 'ValueNotAligned', $data);
      // }

      // Check each line ends in a comma.
      if ( !$this->_isArrayOpener( $tokens[ $index['value'] ] ) ) {
        $valueLine = $tokens[ $index['value'] ]['line'];
        $nextComma = false;
        for ( $i = ($index['value'] + 1); $i < $arrayEnd; $i++ ) {
          // Skip bracketed statements, like function calls.
          if ( $tokens[ $i ]['code'] === T_OPEN_PARENTHESIS ) {
            $i     = $tokens[ $i ]['parenthesis_closer'];
            $valueLine = $tokens[ $i ]['line'];
            continue;
          }

          if ( $tokens[ $i ]['code'] === T_COMMA ) {
            $nextComma = $i;
            break;
          }
        } // for value -> arrayEnd

        //TODO: potentially re-enable this
        // if (($nextComma === false) || ($tokens[ $nextComma ]['line'] !== $valueLine)) {
        //   $error = 'Each line in an array declaration must end in a comma';
        //   $phpcsFile->addError($error, $index['value'], 'NoComma');
        // }

        // Check that there is no space before the comma.
        if ( $nextComma !== false && $tokens[ ($nextComma - 1) ]['code'] === T_WHITESPACE ) {
          $content   = $tokens[ ($nextComma - 2) ]['content'];
          $spaceLength = strlen( $tokens[ ($nextComma - 1) ]['content'] );
          $error  = 'Expected 0 spaces between "%s" and comma; %s found';
          $data  = [
              $content,
              $spaceLength,
          ];
          $phpcsFile->addError( $error, $nextComma, 'SpaceBeforeComma', $data );
        } // if nextComma !false
      } // if !isArrayOpener
    } // foreach indices

  } // process


} // Behance_Sniffs_Arrays_ArrayDeclarationSniff
