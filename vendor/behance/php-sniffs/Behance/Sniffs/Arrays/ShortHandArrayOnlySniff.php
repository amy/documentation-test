<?php
/**
 * Adds an error to the PHPCS file whenever array( ... ) is encountered
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */

/**
 * Adds an error to the PHPCS file whenever array( ... ) is encountered
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */
class Behance_Sniffs_Arrays_ShortHandArrayOnlySniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_ARRAY ];

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

    $phpcsFile->addError( 'PHP 5.4 arrays (shorthand) only. eg: [ ... ]', $stackPtr, 'ShortHandArraysOnly' );

  } // process

} // Behance_Sniffs_Arrays_ShortHandArrayOnlySniff
