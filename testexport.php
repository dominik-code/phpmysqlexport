<?php

/*
 * the final version should allow the export of specific sql results to simple sql files (or even better compressed sql files)
 *
 */
$start = microtime(true);
require_once 'libs/MySQLExport.php';

require_once 'configuration.php';

$tablename = "zuege";
//$datum = date("Y-m-d", time());
$datum = "2017-12-03";
$filename = $datum."_export_".$tablename.".sql";



$export = new MySQLExport(HOST, PORT, USERNAME, PASSWORD, DATABASE);

$export->testConnection();
var_dump(microtime(true) - $start);
$export->setTable($tablename);
$export->setExportFilename($filename);
$export->setExportPath("dump/");
$export->setExportSQL("SELECT * FROM `zuege` WHERE datum='$datum' ");
var_dump(microtime(true) - $start);
$export->setMaxrowsperinsert(500);
$export->setMaxrowsperloop(100000);
var_dump($export->doExport());
var_dump(microtime(true) - $start);

var_dump(memory_get_peak_usage());

var_dump($export->gzCompressFile("dump/".$filename, 4));
var_dump(microtime(true) - $start);
