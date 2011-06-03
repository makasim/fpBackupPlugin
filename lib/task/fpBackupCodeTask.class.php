<?php

/**
 * 
 * @package fpBackupPlugin
 * @subpackage Task
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class fpBackupCodeTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_NONE, 'Verbose output'),
      new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'The file where backup will be stored')));

    $this->namespace        = 'fp';
    $this->name             = 'backup-code';
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
    $cmd = new fpCommandBackupCode(array(
      'file' => $options['file'], 
      'verbose' => $options['verbose']));
      
    $cmd->exec();
  }
}