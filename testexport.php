<?php

/*
 * the final version should allow the export of specific sql results to simple sql files (or even better compressed sql files)
 *
 */
$start = microtime(true);
require_once 'libs/MySQLExport.php';

require_once 'configuration.php';

$export = new MySQLExport(HOST, PORT, USERNAME, PASSWORD, DATABASE);

$export->testConnection();
var_dump(microtime(true) - $start);
$export->setExportFilename("testexport.sql");
$export->setExportPath("");
$export->setExportSQL("SELECT * FROM `zuege` WHERE datum='2018-06-12' ");
var_dump(microtime(true) - $start);
$export->setMaxrowsperinsert(500);
$export->setMaxrowsperloop(100000);
var_dump($export->doExport());
var_dump(microtime(true) - $start);

var_dump(memory_get_peak_usage());

var_dump($export->gzCompressFile("testexport.sql", 4));
var_dump(microtime(true) - $start);
