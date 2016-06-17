<?php
if ( true ) {
} // if this is kay

if ( false ) {
  echo "dontcha wish yo sniffer were tested liek me?";
  // 2 lines, still ok
}

if ( false ) {
  echo "dontcha wish yo sniffer were tested liek me?";
  // 3 lines, still ok
  // OHHHHH MYYYY HEDONISM BOT YESSS
}

elseif ( true ) {
  // STUFF
  // STUFF
  // STUFF
  // STUFF
  // ONOES, moar than 4, need trailing
}

if ( 1 ) {
}// NOPE

if ( 2 ) {
}     // WHY ALL DA WHITESPACE MON

$thingy = function() {
} // NO SEMICOLON

while ( true ) {

} //need whitespace between this and the slashes

if ( true ) {
  // STUFF
  // STUFF
  // STUFF
  // STUFF
  // ONOES, wrong word
} // else


if ( true ) {
  // STUFF
  // STUFF
  // STUFF
  // STUFF
  // ONOES, need description
} // if

if ( true ) {
  // STUFF
  // STUFF
  // STUFF
  // STUFF
  // STUFF
} // if $YO DIS VAR

if ( true ) {
  // STUFF
  // STUFF
  // STUFF
  // STUFF
  // STUFF
} // if YO $DIS $VAR

$goodClosureAssignment = function() {
  $a++;
}; // goodClosureAssignment

$badClosureAssignmentNoTrailing = function() {
  $a++;
};

function badNonMatchingComment() {
  $a++;
} // doesntMatch

function goodSingleLineNoComment() {}

sort( function() {

} );

interface iFace {
  public function goodNoScope();
} // iFace

switch ( $a ) {
  case 'noCommentsNeeded':
    $b++;
}

function imLessThan4Lines() {

  return 'but I still need a comment';

}

trait harkATrait {

} // harkATrait

trait missingDescription {

}

trait wrongDescription {

} // wrongDesc

function {}