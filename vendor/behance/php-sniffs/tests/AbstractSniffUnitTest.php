<?php
/**
 * An abstract class that all sniff unit tests must extend.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * An abstract class that all sniff unit tests must extend.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings that are not found, or
 * warnings and errors that are not expected, are considered test failures.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class AbstractSniffUnitTest extends PHPUnit_Framework_TestCase {

  /**
   * The PHP_CodeSniffer object used for testing.
   *
   * @var PHP_CodeSniffer
   */
  protected static $phpcs = null;

  /**
   * Extension of unit tests - can be overwritten by defining TEST_EXT
   * MUST include the file extension
   *
   * @var string
   */
  protected $testExtension = 'UnitTest.php';

  /**
   * Name of the standard being tested; is set based on this class name
   *
   * @var string
   */
  protected $testBaseName;

  protected function setUp() {

    if ( defined( 'TEST_EXT' ) ) {
      $this->testExtension = TEST_EXT;
    }

    self::$phpcs = new PHP_CodeSniffer();
    $this->testBaseName = preg_replace( "/{$this->testExtension}$/", '', get_class( $this ) );
    $this->codes = [];
    $this->fixableCodes = [];

  } // setUp


  /**
   * Tests the extending classes Sniff class.
   *
   * @test
   * @return void
   * @throws PHPUnit_Framework_Error
   */
  final public function runTest() {

    self::$phpcs->initStandard( $this->_getStandardName(), [ $this->_getSniffCode() ] );
    self::$phpcs->setIgnorePatterns( [] );
    self::$phpcs->cli->setCommandLineValues( [ '-s' ] );

    $failureMessages = [];

    $testClassFile   = ( new ReflectionClass( get_class( $this ) ) )->getFileName();
    $testClassFile   = realpath( $testClassFile );
    $testFileBase    = dirname( $testClassFile ) . '/' . basename( $testClassFile, 'php' );

    $testFiles       = [];

    $dir             = substr( $testFileBase, 0, strrpos( $testFileBase, DIRECTORY_SEPARATOR ) );
    $di              = new DirectoryIterator( $dir );

    foreach ( $di as $file ) {

      $path = $file->getPathname();

      if ( substr( $path, 0, strlen( $testFileBase ) ) === $testFileBase ) {
        if ( $path !== $testFileBase . 'php' && substr( $path, -5 ) !== 'fixed' ) {
          $testFiles[] = $path;
        }
      }

    } // foreach di

    sort( $testFiles );

    foreach ( $testFiles as $testFile ) {

      $filename        = basename( $testFile );
      $phpcsFile       = self::$phpcs->processFile( $testFile );
      $failures        = $this->generateFailureMessages( $phpcsFile );
      $failureMessages = array_merge( $failureMessages, $failures );

      if ( $phpcsFile->getFixableCount() > 0 ) {
        // Attempt to fix the errors.
        $phpcsFile->fixer->fixFile();
        $fixable = $phpcsFile->getFixableCount();
        if ( $fixable > 0 ) {
          $failureMessages[] = "Failed to fix {$fixable} fixable violations in {$filename}";
        }

        // Check for a .fixed file to check for accuracy of fixes.
        $fixedFile = $testFile . '.fixed';
        if ( file_exists( $fixedFile ) ) {
          $diff = $phpcsFile->fixer->generateDiff( $fixedFile );
          if ( trim( $diff ) !== '' ) {
            $fixedFilename     = basename( $fixedFile );
            $failureMessages[] = "Fixed version of {$filename} does not match expected version in {$fixedFilename}; the diff is\n{$diff}";
          }
        } // if file_exists
      } // if getFixableCount > 0
    } // foreach testFiles

    if ( empty( $failureMessages ) === false ) {
        $this->fail( implode( PHP_EOL, $failureMessages ) );
    }

  } // runTest

  /**
   * Generate a list of test failures for a given sniffed file.
   *
   * @param PHP_CodeSniffer_File $file The file being tested.
   *
   * @return array
   * @throws PHP_CodeSniffer_Exception
   */
  public function generateFailureMessages( PHP_CodeSniffer_File $file ) {

    $testFile = $file->getFilename();

    $foundErrors      = $file->getErrors();
    $foundWarnings    = $file->getWarnings();
    $expectedErrors   = $this->getErrorList( basename( $testFile ) );
    $expectedWarnings = $this->getWarningList( basename( $testFile ) );

    if ( is_array( $expectedErrors ) === false ) {
        throw new PHP_CodeSniffer_Exception( 'getErrorList() must return an array' );
    }

    if ( is_array( $expectedWarnings ) === false ) {
        throw new PHP_CodeSniffer_Exception( 'getWarningList() must return an array' );
    }

    /*
        We merge errors and warnings together to make it easier
        to iterate over them and produce the errors string. In this way,
        we can report on errors and warnings in the same line even though
        it's not really structured to allow that.
    */

    $allProblems     = [];
    $failureMessages = [];

    foreach ( $foundErrors as $line => $lineErrors ) {
      foreach ( $lineErrors as $column => $errors ) {
        if ( isset( $allProblems[ $line ] ) === false ) {
          $allProblems[ $line ] = [
              'expected_errors'   => 0,
              'expected_warnings' => 0,
              'found_errors'      => [],
              'found_warnings'    => [],
          ];
        } // if allProblems

        $foundErrorsTemp = [];

        foreach ( $allProblems[ $line ]['found_errors'] as $foundError ) {
          $foundErrorsTemp[] = $foundError;
        }

        $errorsTemp = [];
        foreach ( $errors as $foundError ) {
          $errorsTemp[] = $foundError['message'] . ' (' . $foundError['source'] . ')';

          $source = $foundError['source'];
          if ( in_array( $source, $this->codes ) === false ) {
            $this->codes[] = $source;
          }

          if ( $foundError['fixable'] === true && in_array( $source, $this->fixableCodes ) === false ) {
            $this->fixableCodes[] = $source;
          }
        } // foreach errors

        $allProblems[ $line ]['found_errors'] = array_merge( $foundErrorsTemp, $errorsTemp );
      } // foreach lineErrors

      if ( isset( $expectedErrors[ $line ] ) === true ) {
        $allProblems[ $line ]['expected_errors'] = $expectedErrors[ $line ];
      }
      else {
        $allProblems[ $line ]['expected_errors'] = 0;
      }

      unset( $expectedErrors[ $line ] );
    } // foreach foundErrors

    foreach ( $expectedErrors as $line => $numErrors ) {
      if ( isset( $allProblems[ $line ] ) === false ) {
        $allProblems[ $line ] = [
            'expected_errors'   => 0,
            'expected_warnings' => 0,
            'found_errors'      => [],
            'found_warnings'    => [],
        ];
      } // if allProblems

      $allProblems[ $line ]['expected_errors'] = $numErrors;
    } // foreach expectedErrors

    foreach ( $foundWarnings as $line => $lineWarnings ) {
      foreach ( $lineWarnings as $column => $warnings ) {
        if ( isset( $allProblems[ $line ] ) === false ) {
          $allProblems[ $line ] = [
              'expected_errors'   => 0,
              'expected_warnings' => 0,
              'found_errors'      => [],
              'found_warnings'    => [],
          ];
        } // if allProblems

        $foundWarningsTemp = [];
        foreach ( $allProblems[ $line ]['found_warnings'] as $foundWarning ) {
            $foundWarningsTemp[] = $foundWarning;
        }

        $warningsTemp = [];
        foreach ( $warnings as $warning ) {
            $warningsTemp[] = $warning['message'] . ' (' . $warning['source'] . ')';
        }

        $allProblems[ $line ]['found_warnings'] = array_merge( $foundWarningsTemp, $warningsTemp );
      } // foreach lineWarnings

      if ( isset( $expectedWarnings[ $line ] ) === true ) {
          $allProblems[ $line ]['expected_warnings'] = $expectedWarnings[ $line ];
      }
      else {
          $allProblems[ $line ]['expected_warnings'] = 0;
      }

      unset( $expectedWarnings[ $line ] );
    } // foreach foundWarnings

    foreach ( $expectedWarnings as $line => $numWarnings ) {
      if ( isset( $allProblems[ $line ] ) === false ) {
        $allProblems[ $line ] = [
            'expected_errors'   => 0,
            'expected_warnings' => 0,
            'found_errors'      => [],
            'found_warnings'    => [],
        ];
      } // if allProblems

        $allProblems[ $line ]['expected_warnings'] = $numWarnings;
    } // foreach expectedWarnings

    // Order the messages by line number.
    ksort( $allProblems );

    foreach ( $allProblems as $line => $problems ) {
      $numErrors        = count( $problems['found_errors'] );
      $numWarnings      = count( $problems['found_warnings'] );
      $expectedErrors   = $problems['expected_errors'];
      $expectedWarnings = $problems['expected_warnings'];

      $errors      = '';
      $foundString = '';

      if ( $expectedErrors !== $numErrors || $expectedWarnings !== $numWarnings ) {
        $lineMessage     = "[LINE $line]";
        $expectedMessage = 'Expected ';
        $foundMessage    = 'in ' . basename( $testFile ) . ' but found ';

        if ( $expectedErrors !== $numErrors ) {
          $expectedMessage .= "$expectedErrors error(s)";
          $foundMessage    .= "$numErrors error(s)";
          if ( $numErrors !== 0 ) {
            $foundString .= 'error(s)';
            $errors      .= implode( PHP_EOL . ' -> ', $problems['found_errors'] );
          }

          if ( $expectedWarnings !== $numWarnings ) {
            $expectedMessage .= ' and ';
            $foundMessage    .= ' and ';
            if ( $numWarnings !== 0 ) {
              if ( $foundString !== '' ) {
                $foundString .= ' and ';
              }
            }
          } // if expectedWarnings
        } // if expectedErrors

        if ( $expectedWarnings !== $numWarnings ) {
          $expectedMessage .= "$expectedWarnings warning(s)";
          $foundMessage    .= "$numWarnings warning(s)";
          if ( $numWarnings !== 0 ) {
            $foundString .= 'warning(s)';
            if ( empty( $errors ) === false ) {
              $errors .= PHP_EOL . ' -> ';
            }

            $errors .= implode( PHP_EOL . ' -> ', $problems['found_warnings'] );
          } // if numWarnings
        } // if expectedWarnings

        $fullMessage = "$lineMessage $expectedMessage $foundMessage.";
        if ( $errors !== '' ) {
            $fullMessage .= " The $foundString found were:" . PHP_EOL . " -> $errors";
        }

        $failureMessages[] = $fullMessage;
      } // if expectedErrors
    } // foreach allProblems

    return $failureMessages;

  } // generateFailureMessages

  /**
   * Gets the sniff code based on the implmenting class
   *
   * @return string
   */
  protected function _getSniffCode() {

    // The code of the sniff we are testing.
    $parts = explode( '_', $this->testBaseName );

    return $parts[0] . '.' . $parts[2] . '.' . $parts[3];

  } // _getSniffCode

  /**
   * Gets the standard name based on current class name
   *
   * @return string
   */
  protected function _getStandardName() {

      return ( defined( 'STANDARD_PATH' ) ) ? STANDARD_PATH : substr( $this->testBaseName, 0, strpos( $this->testBaseName, '_' ) );

  } // _getStandardName

  /**
   * Returns all files in a directory & its subdirs
   *
   * @param string Directory
   * @return array
   */
  protected function _getAllFiles( $dir ) {

    $dir = rtrim( $dir, DIRECTORY_SEPARATOR );
    $items = glob( $dir . DIRECTORY_SEPARATOR . '*' );
    $items = array_diff( $items, [ '.', '..' ] );

    $files = [];

    foreach ( $items as $key => $file ) {
      if ( is_dir( $file ) ) {
        $files = array_merge( $files, $this->_getAllFiles( $file ) );
        continue;
      }
      $files[] = $file;
    } // foreach items

    return $files;

  } // _getAllFiles

  /**
   * Returns the lines where errors should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of errors that should occur on that line.
   *
   * @param $testFile
   *
   * @return array(int => int)
   */
  abstract public function getErrorList( $testFile );


  /**
   * Returns the lines where warnings should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of warnings that should occur on that line.
   *
   * @param $testFile
   *
   * @return array(int => int)
   */
  abstract public function getWarningList( $testFile );

} // AbstractSniffUnitTest
