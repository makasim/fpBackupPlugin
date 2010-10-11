<?php

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Test
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpDsnTestCase extends sfBasePhpunitTestCase
{ 
  /**
   * 
   * @dataProvider providerInvalidDsn
   */
  public function testConstructInvalidDsn($invalidDsn, $expectedExceptionMessage)
  {
    try {
      new fpDsn($invalidDsn);
    } catch (InvalidArgumentException $e) {
      
      $this->assertContains($expectedExceptionMessage, $e->getMessage());
      
      return;
    }
    
    $this->fail('The `InvalidArgumentException` is expected but was not thrown');
  }
  
  public function testConstructValidUrlDsn()
  {
    $dsn = new fpDsn('mysql://bar:foo@example.com/FooDb');
    
    $this->assertEquals('mysql', $dsn->driver());
    $this->assertEquals('bar', $dsn->user());
    $this->assertEquals('foo', $dsn->password());
    $this->assertEquals('example.com', $dsn->host());
    $this->assertEquals('FooDb', $dsn->database());
  }
  
  public function testConstructValidArrayDsn()
  {
    $dsn = new fpDsn(array(
      'dsn' => 'mysql:host=example.com;dbname=FooDb',
      'username' => 'bar',
      'password' => 'foo'));

    $this->assertEquals('mysql', $dsn->driver());
    $this->assertEquals('bar', $dsn->user());
    $this->assertEquals('foo', $dsn->password());
    $this->assertEquals('example.com', $dsn->host());
    $this->assertEquals('FooDb', $dsn->database());
  }
  
  public static function providerInvalidDsn()
  {
    return array(
      // invalid type 
      array(new stdClass(), 'The dsn has invalid type. can be either array or string'),
      
      // invalid url
      array('foo', 'The invalid or unsupported driver given'),
      array('mysql://example.com/db', 'The user is empty but it is not allowed'),
      array('mysql://bar:foo@example.com', 'The database name is empty but it is not allowed'),
      
      // invalid array
      array(array(), 'The dsn is invalid. Should contain driver name'),
      array(array('dsn' => 'foo:host'), 'A dsn option invaid is invalid. Should match the patter key=value'),
      array(array('dsn' => 'foo:host=foo'), 'The invalid or unsupported driver given'),
      array(array('dsn' => 'mysql:host=foo', 'username' => 'bar'), 'The database name is empty but it is not allowed'),
      array(array('dsn' => 'mysql:host=foo'), 'The user is empty but it is not allowed'));
  }
}