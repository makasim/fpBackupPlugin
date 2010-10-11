<?php

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Command
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
abstract class fpBaseCommand
{
  /**
   * 
   * @var array
   */
  protected $_options = array();
  
  public function __construct(array $options = array())
  {
    $options = array_intersect_key($options, $this->_options);
    
    $this->_options = array_merge($this->_options, $options);
    
    $this->_initialize();
  }
  
  protected function _initialize()
  {
    
  }
  
  abstract public function exec();
  
  protected function _doExec($command)
  {
    $command = trim($command);

    $result = 0;
    
    passthru($command, $result);

    if((int)$result > 0) {
      throw new Exception('Command: `' . $command . '`. Exit with error code: `'.$result.'`');
    }
  }
  
  protected function _doExecBackground($command)
  {
    $this->_doExec("nohup $command &");
  }
  
  protected function _doExecWhileChanging($command)
  {
    $current = 'curr';
    $preview = 'prev';
    while ($preview != $current) {
      ob_start();
      
      $this->_doExec($command);
      
      $preview = $current;
      $current = ob_get_flush();

      sleep(5);
    }
  }
  
  /**
   * 
   * @param string $name
   * @throws InvalidArgumentException
   * 
   * @return mixed
   */
  public function getOption($name)
  {
    if (!array_key_exists($this->_options[$name])) {
      throw new InvalidArgumentException('The option with a given name `'.$name.'` does not exist');
    }
    
    return $this->_options[$name];
  }
}