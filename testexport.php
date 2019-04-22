<?php

/*
 * the final version should allow the export of specific sql results to simple sql files (or even better compressed sql files)
 *
 */

require_once 'libs/MySQLExport.php';

require_once 'configuration.php';

$export = new MySQLExport(HOST, PORT, USERNAME, PASSWORD, DATABASE);

$export->testConnection();

$export->setExportFilename("testexport.sql");
$export->setExportPath("");
$export->setExportSQL("SELECT * FROM `errorlog` WHERE id < 300000 and id > 200000");

var_dump($export->doExport());
var_dump($export->testInsertHead());