<?php

class ViolatingClass1 {
  const LOG_CHANNEL_DEFAULT = 'log_channel';
} // ViolatingClass1

class ViolatingClass2 {
  const LOG_CHANNEL_DEFAULT    = 'log_channel';
} // ViolatingClass2

class ViolatingClass3 {
  const LOG_CHANNEL_DEFAULT = 0;
} // ViolatingClass3

class ViolatingClass4 {
  const LOG_CHANNEL_DEFAULT = true;
} // ViolatingClass4

class ViolatingClass5 {
  const THING = TestClass::SOMETHING, LOG_CHANNEL_DEFAULT = 'String';
} // ViolatingClass5

class ViolatingClass6 {
  const LOG_CHANNEL_DEFAULT = TestClass::SOMETHING . 'FAIL';
} // ViolatingClass6

class OkayClass1 {
  const SOME_OTHER_CONSTANT = 'log_channel';
} // OkayClass1

class OkayClass2 {
  const LOG_CHANNEL_DEFAULT = TestClass::LOG_CHANNEL;
  const SOME_OTHER_CONSTANT = 'log_channel';
} // OkayClass2

class OkayClass3 {
  const LOG_CHANNEL_DEFAULT                 = TestClass::LOG_CHANNEL_DEFAULT;
} // OkayClass3

class OkayClass4 {
  const LOG_CHANNEL_DEFAULT
    = TestClass::LOG_CHANNEL;
} // OkayClass4

class OkayClass5 {
  const LOG_CHANNEL_DEFAULT = TestClass::LOG_CHANNEL; const SOME_OTHER_CONSTANT = 12345;
} // OkayClass5

class OkayClass6 {
  const LOG_CHANNEL_DEFAULT /** Comment here */ = /** Another comment here */ TestClass::SOMETHING;
} // OkayClass6

class OkayClass7 {
  const    THING = 'String', THING_2 = 'String2', LOG_CHANNEL_DEFAULT = TestClass::SOMETHING;
} // OkayClass7

class OkayClass8 {
  protected $_thing;
} // OkayClass8
