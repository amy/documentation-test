<?php
class Behance_Sniffs_Formatting_BlankLineSniff implements PHP_CodeSniffer_Sniff {

  private $_phpcsFile;
  private $_tokens;
  private $_limit;
  private $_numTokens;


  /**
   * @return array(int)
   */
  public function register() {

    return [ T_OPEN_TAG ];

  } // register


  /**
   * @param  PHP_CodeSniffer_File  $phpcsFile  The file being scanned.
   * @param  int                   $stackPtr   The position of the current token in the stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $this->_limit     = 2;
    $this->_phpcsFile = $phpcsFile;
    $this->_tokens    = $phpcsFile->getTokens();
    $this->_numTokens = $phpcsFile->numTokens;

    for ( $i = 1; $i < $this->_numTokens - $this->_limit - 1; $i++ ) {

      $this->_checkForExcessiveBlankLines( $i );

    } // for each token minus the limit

  } // process


  /**
   * @param   int  $index
   *
   * @return  void
   */
  protected function _checkForExcessiveBlankLines( $index ) {

    if ( $this->_isLastTokenBeforeBlankLine( $index ) && $this->_exceedsConsecutiveBlankLineLimit( $index + 1 ) ) {

      $error              = 'More than ' . $this->_limit . ' consecutive blank lines are not allowed';

      $fix_option_enabled = $this->_phpcsFile->addFixableError( $error, $index, 'MaxConsecutiveBlankLinesExceeded' );

      if ( $fix_option_enabled === true ) {
        $this->_removeExtraLines( $index );
      }

    } // if token is followed by a blank line

  } // _checkForExcessiveBlankLines


  /**
   * @param  int   $index
   *
   * @return bool
   */
  private function _isBlankLine( $index ) {

    return ( ( $this->_tokens[ $index ]['column'] === 1 ) && ( $this->_tokens[ $index ]['content'] === $this->_phpcsFile->eolChar ) );

  } // _isBlankLine


  /**
   * @param  int   $index
   *
   * @return bool
   */
  private function _exceedsConsecutiveBlankLineLimit( $index ) {

    for ( $i = 1; $i <= $this->_limit; $i++ ) {
      if ( !$this->_isBlankLine( $index + $i ) ) {
        return false;
      }
    }

    return true;

  } // _exceedsConsecutiveBlankLineLimit


  /**
   * @param  int   $index
   *
   * @return bool
   */
  private function _isFollowedByBlankLine( $index ) {

    return $this->_isBlankLine( $index + 1 );

  } // _isFollowedByBlankLine


  /**
   * @param  int   $index
   *
   * @return void
   */
  private function _removeExtraLines( $index ) {

    for ( $i = $index + $this->_limit - 1; $i < $this->_numTokens - $this->_limit; $i++ ) {

      if ( !$this->_isBlankLine( $i + $this->_limit ) ) {
        return;
      }

      $this->_phpcsFile->fixer->replaceToken( $i, '' );

    } // for each token, check if it's an extra EOL character and remove it

  } // _removeExtraLines


  /**
   * @param  int   $index
   *
   * @return bool
   */
  private function _isLastTokenBeforeBlankLine( $index ) {

    return $this->_isFollowedByBlankLine( $index ) && !$this->_isBlankLine( $index );

  } // _isLastTokenBeforeBlankLine


} // Behance_Sniffs_Formatting_BlankLineSniff
