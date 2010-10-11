<?php

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Task
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpBackupTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name')));

    $this->namespace        = 'fp';
    $this->name             = 'backup';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
    
EOF;
  }

  /**
   * (non-PHPdoc)
   * @see lib/vendor/diem/symfony/lib/task/sfTask::execute()
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager  = new sfDatabaseManager($this->configuration);
    $database         = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null);
    $connection       = $database->getConnection();
    
    $dsn = $this->_getDsn($database);
    
    $d = new fpCommandBackup(array('dsn' => $dsn, 'file' => sfConfig::get('sf_root_dir').'/test.sql.gz'));
    $d->exec();
  }
  
  /**
   * 
   * @param unknown_type $database
   * 
   * @return fpDsn
   */
  protected function _getDsn($database)
  {
    $dsn  =  $database->getParameter('dsn');
    if (!strpos($dsn, '://')) {
      $dsn = new fpDsn(array(
        'dsn' => $dsn,
        'username' => $database->getParameter('username'), 
        'password' => $database->getParameter('password')));
    } else {
      $dsn = new fpDsn($dsn);
    }
    
    return $dsn;
  }
}