<?php

$this->startSetup();
$installer = $this;
$config_db = $installer->getTable('core/config_data');
$query =<<<SQL
UPDATE {$config_db} SET path = REPLACE(path,'capayable/capayable/','payment/capayable/')
WHERE path LIKE 'capayable/capayable/%'
SQL;

$installer->run($query);
$installer->endSetup();