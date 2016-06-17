<?php

class Behance_Sniffs_Keywords_AlphabeticalUseSniff implements PHP_CodeSniffer_Sniff {

  const TYPE_NAMESPACE = 'namespace';
  const TYPE_TRAIT     = 'trait';

  protected $_current_names = [];

  /**
   * Returns an array of tokens this test wants to listen for.
   * Taken from http://www.php.net/manual/en/reserved.keywords.php
   *
   * @return array
   */
  public function register() {

    return [ T_USE ];

  } // register

  /**
   * Returns whether the current use token is inside a closure.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the stack passed in $tokens.
   *
   * @return bool
   */
  private function _isClosureUse( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();
    $next   = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );

    return $tokens[ $next ]['code'] === T_OPEN_PARENTHESIS;

  } // _isClosureUse

  /**
   * Returns the class name, including namespace separators located at the provided pointer.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $namePtr   The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return string
   */
  private function _getClassName( PHP_CodeSniffer_File $phpcsFile, $namePtr ) {

    $find = [
        T_NS_SEPARATOR,
        T_STRING,
        T_WHITESPACE,
    ];

    $end  = $phpcsFile->findNext( $find, ( $namePtr + 1 ), null, true, null, true );
    $name = $phpcsFile->getTokensAsString( $namePtr, ( $end - $namePtr ) );

    return trim( $name );

  } // _getClassName

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

    if ( $this->_isClosureUse( $phpcsFile, $stackPtr ) ) {
      return;
    }

    $filename = $phpcsFile->getFilename();

    if ( !isset( $this->_current_names[ $filename ] ) ) {
      $this->_current_names[ $filename ] = [
          self::TYPE_NAMESPACE => '',
          self::TYPE_TRAIT     => '',
      ];
    } // if filename not set

    $list_type = ( $phpcsFile->hasCondition( $stackPtr, [ T_CLASS, T_TRAIT ] ) )
                 ? self::TYPE_TRAIT
                 : self::TYPE_NAMESPACE;

    $namePtr = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
    $name    = $this->_getClassName( $phpcsFile, $namePtr );

    if ( strcasecmp( $this->_current_names[ $filename ][ $list_type ], $name ) > 0 ) {
      $error = $name . ' is not in alphabetical order ';
      $phpcsFile->addError( $error, $namePtr, $list_type );
    }
    else {
      $this->_current_names[ $filename ][ $list_type ] = $name;
    }

  } // process

} // Behance_Sniffs_Keywords_AlphabeticalUseSniff
