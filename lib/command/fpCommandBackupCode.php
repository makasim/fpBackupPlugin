<?php 

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Command
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpCommandBackupCode extends fpExecCommandBase
{
  /**
   * 
   * @var string
   */
  protected $_options = array(
    'file' => false,
    'verbose' => false);
  
  /**
   * 
   * @param fpDsn $dsn
   * @param string $backupFile
   * @param bool $isZip
   */
  protected function _initialize()
  {
    $opt = $this->_options;
    
    if (!($opt['file'])) {
      throw new Exception('The `file` option is required. But the option `' . $opt['file'] . '` you give is not valid or file is not writable');
    }
    
    $this->_doExec('gzip -h', false);
    $this->_doExec('du --help', false);
  }
  
  public function exec()
  {
    $file = $this->_options['file'];
    
    $stderr = '2>> log/fp:backup-err.log';
    $stdout = '>> log/fp:backup.log';

    $this->_doExecBackground(
      'tar -zcvf ' . $file . ' ' . sfConfig::get('sf_root_dir') . ' ' . $stdout . ' ' . $stderr, 
      $this->getOption('verbose'));
    
    $this->_doExecUntilChanging("du -sB M {$file} {$stderr}", $this->getOption('verbose'));
  }
}
