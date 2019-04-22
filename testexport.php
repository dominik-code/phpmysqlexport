<?php

/*
 * the final version should allow the export of specific sql results to simple sql files (or even better compressed sql files)
 *
 */
$start = microtime(true);
require_once 'libs/MySQLExport.php';

require_once 'configuration.php';

$export = new MySQLExport(HOST, PORT, USERNAME, PASSWORD, DATABASE);

$tablename = "zuege";
if(isset($_REQUEST['archive'])) {
    $datum = $export->getOldestDate();

} else {

    $datum = date('Y-m-d', strtotime('-1 year -7 days'));
}



$filename = $datum."_export_".$tablename.".sql";




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

unlink("dump/".$filename);

if(file_exists("dump/$filename".".gz")) {
    if(filesize("dump/$filename".".gz") > 1024*1024) {
        $export->removeDatasets("DELETE FROM `zuege` WHERE datum='$datum'");
    } else {
        die("manual control needed for : ". $filename);
    }
}
