<?php
$goodbye = "Goodbye,";
$world = "world!";

echo $goodbye.' cruel '.$world;
echo $goodbye . ' cruel ' . $world;
echo $goodbye  .  ' cruel '  .  $world;
echo $goodbye
               .  ' cruel '
                  .  $world;

echo 1+2;
echo 1 + 2;
echo 1  +  2;
echo 1
       +
         2;

echo 1-2;
echo 1 - 2;
echo 1  -  2;
echo 1
       -
         2;

echo 1%2;
echo 1 % 2;
echo 1  %  2;
echo 1
       %
         2;

echo 1/2;
echo 1 / 2;
echo 1  /  2;
echo 1
       /
         2;

echo 1*2;
echo 1 * 2;
echo 1  *  2;
echo 1
       *
         2;

echo 1^2;
echo 1 ^ 2;
echo 1  ^  2;
echo 1
       ^
         2;

echo 1&2;
echo 1 & 2;
echo 1  &  2;
echo 1
       &
         2;

echo 1|2;
echo 1 | 2;
echo 1  |  2;
echo 1
       |
         2;

echo 1 and 2;
echo 1  and  2;
echo 1
       and
         2;

echo 1 or 2;
echo 1  or  2;
echo 1
       or
         2;

echo 1 xor 2;
echo 1  xor  2;
echo 1
       xor
         2;

$goodbye.=$world;
$goodbye .= $world;
$goodbye  .=  $world;
$goodbye
          .=
              $world;

$goodbye+=$world;
$goodbye += $world;
$goodbye  +=  $world;
$goodbye
          +=
              $world;

$goodbye-=$world;
$goodbye -= $world;
$goodbye  -=  $world;
$goodbye
          -=
              $world;

$goodbye/=$world;
$goodbye /= $world;
$goodbye  /=  $world;
$goodbye
          /=
              $world;

$goodbye%=$world;
$goodbye %= $world;
$goodbye  %=  $world;
$goodbye
          %=
              $world;

$goodbye*=$world;
$goodbye *= $world;
$goodbye  *=  $world;
$goodbye
          *=
              $world;

$goodbye<>$world;
$goodbye <> $world;
$goodbye  <>  $world;
$goodbye
          <>
              $world;

$goodbye&=$world;
$goodbye &= $world;
$goodbye  &=  $world;
$goodbye
          &=
              $world;

$arr = [
    'blah'=>'blah',
    'blah' => 'blah',
    'blah'     =>     'blah',
    'blah'
               =>
                      'blah'
];

$truthy = 1==2;
$truthy = 1 == 2;
$truthy = 1  ==  2;
$truthy = 1
              ==
                   2;

$truthy = 1===2;
$truthy = 1 === 2;
$truthy = 1  ===  2;
$truthy = 1
              ===
                   2;

$truthy = 1!=2;
$truthy = 1 != 2;
$truthy = 1  !=  2;
$truthy = 1
              !=
                   2;

$truthy = 1!==2;
$truthy = 1 !== 2;
$truthy = 1  !==  2;
$truthy = 1
              !==
                   2;

$truthy = 1<2;
$truthy = 1 < 2;
$truthy = 1  <  2;
$truthy = 1
              <
                   2;

$truthy = 1>2;
$truthy = 1 > 2;
$truthy = 1  >  2;
$truthy = 1
              >
                   2;

$truthy = 1>=2;
$truthy = 1 >= 2;
$truthy = 1  >=  2;
$truthy = 1
              >=
                   2;

$truthy = 1<=2;
$truthy = 1 <= 2;
$truthy = 1  <=  2;
$truthy = 1
              <=
                   2;

$foo++;
$foo ++;
++ $foo;

$foo--;
$foo --;
--$foo;
-- $foo;

!$foo;
! $foo; // BAD

$foo =& $bar;
$foo = & $bar;
$foo = &$bar;
$foo =&
        $bar;

// more cases for minus - unary too
$foo = - 5;           // no space for flat LNUMBER
$foo = -5;
$foo = $bar-$bleh;    // spaces required
$foo = $bar - $bleh;
$foo = -$bleh;
$foo = - $bleh;       // no space

// more cases for ampersand
foreach ( $values as &$val ) {}
foreach ( $values as & $val ) {}
$refArr = [
    'foo' => &$val,
    'bar' =>&$val,
    'bak' =>& $val,
    'bar' => & $val,
];

function blah1( &$things ) {}
function blah2(&$things ) {}
function blah3( $thing, &$things ) {}
function blah4( $thing, & $things ) {}
function blah5( $thing,&$things ) {}

$diff = microtime( true ) - microtime( true );
$diff = microtime( true ) -microtime( true );
$diff = microtime( true )- microtime( true );
$diff = microtime( true )-microtime( true );
$arr  = [
  'negval' => -1,
  'negval' => - 1,
  -1 => $foo,
  - 1 => $blah
];

$thing->passNegativeFirstKay( -1, $foo );
$thing->passNegativeFirstKay( - 1, $foo );
$thing->passNegativeFirstKay(- 1, $foo );

$arr  = [ -1 => 'REAL' ];
$arr = [ -1 ];
$arr = [ - 1 ];
$create_modules[ -1 ] = $good_modules[ -1 ];
$create_modules[ - 1 ] = $good_modules[ - 1 ];

    return [
        -1 => $this->_valid_image_module(),
        -2 => $this->_valid_video_module(),
        -3 => $this->_valid_audio_module(),
        -4 => $this->_valid_text_module(),
        -5 => $this->_valid_embed_module(),
    ];

$thing = ( false )
         ? -2
         : -1;

$thing = ( true )
         ? - 2
         : - 1;

$foo = $params['page'] - 1;
$foo = $params['page']- 1;
$foo = $params['page'] -1;

$foo = Foo::FOO - 1;
$foo = Foo::FOO- 1;
$foo = Foo::FOO -1;

$foo = Foo::$FOO - 1;
$foo = Foo::$FOO- 1;
$foo = Foo::$FOO -1;

return -1;
return - 1;

$foo == -1;
$foo == - 1;

$foo === -1;
$foo === - 1;

$foo != -1;
$foo != - 1;

$foo !== -1;
$foo !== - 1;

$foo > -1;
$foo > - 1;

$foo >= -1;
$foo >= - 1;

$foo < -1;
$foo < - 1;

$foo <= -1;
$foo <= - 1;

$badNot = $a &&!$b;
$goodNot = $a && !$b;

$doubleNegativeGood = !!$b;
$doubleNegativeBad = ! !$b;
