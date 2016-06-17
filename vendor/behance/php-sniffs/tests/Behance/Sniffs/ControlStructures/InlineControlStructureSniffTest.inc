<?php
if ( true ) {
  $ok;
}
elseif ( true ) {
  $ok;
}
else {
  $ok;
}

if ( true )
  $bad;
elseif ( true )
  $bad;
else
  $bad;

while ( true ) {
  $ok;
}
while ( true )
  $bad;

foreach ( [] as $empty ) {
  $ok;
}
foreach ( [] as $empty )
  $bad;

for ( $x; $x; $x ) {
  $ok;
}
for ( $x; $x; $x )
  $bad;

switch ( $ok ) {
  case 'ok':
    break;
}
switch ( $bad )
  case 'bad':
    break;

if ( true )
  $bad;
else if ( true )
  $bad;

do {
  $ok;
} while ( true );
