<?php
/**
 * This file is part of the symfony package.
 * (c) Cedric Sadai <cedric@seedweb-agency.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * Performs an automatic backup of the database. Automatic archiving system.
 *
 * FEATURES:
 *   - Supports multiple snapshots per day
 *     - Keeps all snapshots generated during the current and the previous month.
 *   - Keeps the most recent snapshot of each week among those saved two months ago.
 *   - Keeps the most recent snapshot of the month among the weekly backups saved three months ago.
 *
 * @author Cedric Sadai <cedric@seedweb-agency.com>
 * @link http://www.seedweb-agency.com
 * @version 0.1
 * @license MIT
 *
 *
 */
class fcDbBackupTask extends sfBaseTask
{
  protected $workingDirectory;
  protected $pathToExecutable;

  protected function configure()
  {
    $this->addOptions(array(
    new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'front'),
    new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    // add your own options here
    ));

    $this->namespace        = 'fc';
    $this->name             = 'dbBackup';
    $this->briefDescription = 'Makes a snapshot of the database. Triggers the automatic archiving system.';
    $this->detailedDescription = <<<EOF
        The [fc:dbBackup|INFO] task makes snapshots of the database, for backup purposes.
          - Supports multiple snapshots per day
          - Keeps all snapshots generated during the current and the previous month.
          - Keeps the most recent snapshot of each week among those saved two months ago.
          - Keeps the most recent snapshot of the month among the weekly backups saved three months ago.
        Call it with:

        [php symfony fc:dbBackup|INFO]
EOF;
  }

  /**
   * Main callable. Gets the config, does the snapshot, makes the cleaning.
   *
   * @todo make it work not only with DSN, but with all kinds of configs put in database.yml
   * @todo Make it work with more RDBMS
   * @todo Automatic syncing with distant storage services (Amazon S3, etc.)
   *
   *
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $database = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null);
    $connection = $database->getConnection();
    
    $dsn = $this->_handleDsn($database);

    

    //-- preparing the backup command
    $command = $this->getDumpCommand(
    $parts['scheme'],
    $parts['host'],
    $parts['user'],
    $parts['pass'],
    $parts['path']
    );

    //-- Creating the snapshot
    exec($command);
    $this->logSection('backup', sprintf('Backup done for %s', date('d M Y')));


    //-- let's do some cleanup
    $this->cleanup();
  }
  
  /**
   * 
   * @param mixed $database
   * 
   * @return fpDsn
   */
  protected function _handleDsn($database)
  {
    $dsn = $database->getParameter('dsn');
    
    if (!strpos($dsn, '://')) {
      $dsn = array(
        'dsn' => $dsn,
        'username' => $database->getParameter('username'),
        'password' => $database->getParameter('password'));
    }
    
    return new fpDsn($dsn);
  }
}