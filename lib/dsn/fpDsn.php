<?php

/**
 *
 * @package fpBackupPlugin
 * @subpackage Dsn
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpDsn
{
  /**
   *
   * @var string
   */
  protected $_user;

  /**
   *
   * @var string
   */
  protected $_password;

  /**
   *
   * @var string
   */
  protected $_host;

  /**
   *
   * @var string
   */
  protected $_database;

  /**
   *
   * @var string
   */
  protected $_driver;
   
  /**
   *
   * @param array|string $dsn
   *
   * @throws InvalidArgumentException if dsn is invalid
   */
  public function __construct($dsn)
  {
    $this->_parse($dsn);
  }

  /**
   *
   * @return string
   */
  public function password()
  {
    return $this->_password;
  }

  /**
   *
   * @return string
   */
  public function user()
  {
    return $this->_user;
  }

  /**
   *
   * @return string
   */
  public function host()
  {
    return $this->_host;
  }

  /**
   *
   * @return string
   */
  public function database()
  {
    return $this->_database;
  }

  /**
   *
   * @return string
   */
  public function driver()
  {
    return $this->_driver;
  }
  
  /**
   * 
   * @param string|array $dsn
   */
  protected function _parse($dsn)
  {//var_dump($dsn);
    if (is_array($dsn)) {
      $this->_parseArray($dsn);
    } else if (is_string($dsn)) {
      $this->_parseString($dsn);
    } else {
      throw new InvalidArgumentException('The dsn has invalid type. can be either array or string but given type is `'.gettype($dsn).'`');
    }
  }
  
  /**
   * 
   * @param array $dns
   */
  protected function _parseArray(array $dsn)
  {
    $dsn = array_merge(
      array('dsn' => '', 'host' => '', 'dbname' => '', 'username' => '', 'password' => ''),
      $dsn);
    
    $parse = explode(':', $dsn['dsn']);
    if (count($parse) != 2) {
      throw new InvalidArgumentException('The dsn is invalid. Should contain driver name and after `:` other dsn options');
    }
    list($driver, $options) = $parse;
    
    foreach (explode(';', $options) as $option) {
      if (empty($option)) continue;
      
      $parse = explode('=', $option);
      if (count($parse) != 2) {
        throw new InvalidArgumentException('A dsn option invaid is invalid. Should match the patter key=value but given `'.$option.'`');
      }
      
      list($key, $value) = $parse;
      $dsn[$key] = $value;
    }

    $this->_setDriver($driver);
    $this->_setUser($dsn['username']);
    $this->_setPassword($dsn['password']);
    $this->_setHost($dsn['host']);
    $this->_setDatabase($dsn['dbname']);
  }
  
  /**
   * 
   * @param string $dsn
   * 
   * @throws InvalidArgumentException
   */
  protected function _parseString($dsn)
  {
    $options = parse_url($dsn);
    if (!is_array($options)) {
      throw new InvalidArgumentException('The parse_url function return false. It means that the dsn `'.$dsn.'` given is seriously malformed');
    }
    //var_dump($options);
    $options = array_merge(
      array('scheme' => '', 'user' => '', 'pass' => '', 'path' => '', 'host' => ''),
      $options);

    //cleaning starting slash for path (see parse_url behaviour)
    if ($options['path']&& $options['path'][0] == '/') {
      $options['path'] = substr($options['path'], 1);
    }
//    var_dump($options);
//    die;
    $this->_setDriver($options['scheme']);
    $this->_setUser($options['user']);
    $this->_setPassword($options['pass']);
    $this->_setHost($options['host']);
    $this->_setDatabase($options['path']);
  }
  
  /**
   * 
   * @param string $driver
   * 
   * @throws InvalidArgumentException if the driver invalid or unsupported
   */
  protected function _setDriver($driver)
  {
    $drivers = array('mysql');
    if (!in_array($driver, $drivers)) {
      throw new InvalidArgumentException('The invalid or unsupported driver given `'.$driver.'`. Only supported `'.implode('`, `', $drivers).'`');
    }
    
    $this->_driver = $driver;
  }
  
  /**
   * 
   * @param string $user
   * 
   * @throws InvalidArgumentException if the user empty
   */
  protected function _setUser($user)
  {
    if (empty($user)) {
      throw new InvalidArgumentException('The user is empty but it is not allowed');
    }
    
    $this->_user = $user;
  }
  
  /**
   * 
   * @param string $password
   */
  protected function _setPassword($password)
  {
    $this->_password = $password;
  }
  
  /**
   * 
   * @param string $host
   */
  protected function _setHost($host)
  {
    $this->_host = $host;
  }
  
  /**
   * 
   * @param string $database
   */
  protected function _setDatabase($database)
  {
    if (empty($database)) {
      throw new InvalidArgumentException('The database name is empty but it is not allowed');
    }
    
    $this->_database = $database;
  }
}