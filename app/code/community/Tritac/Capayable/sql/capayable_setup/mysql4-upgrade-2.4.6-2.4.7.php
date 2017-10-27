<?php
/**
 * Created by PhpStorm.
 * User: willemjan
 * Date: 13-7-15
 * Time: 16:40
 */


$this->startSetup();
$installer = $this;
$config_db = $installer->getTable('core/config_data');
$query =<<<SQL
UPDATE {$config_db} SET path = REPLACE(path,'payment/capayable/','capayable/capayable/')
WHERE path LIKE 'payment/capayable/%'
SQL;

$installer->run($query);
$installer->endSetup();