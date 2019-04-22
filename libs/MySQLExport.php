<?php


class MySQLExport {
    private $path = "";
    private $filename = "";
    private $exportSQL = "";

    private $host = "";
    private $port = 3306;
    private $username = "";
    private $password = "";
    private $database = "";
    private $link = null;

    public function __construct($host, $port, $username, $password, $database) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function setExportPath($path) {
        $this->path = $path;
    }

    public function setExportFilename($filename) {
        $this->filename = $filename;
    }

    public function setExportSQL($exportSQL) {
        $this->exportSQL = $exportSQL;
    }

    public function doExport() {
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);


        $q_use_database = mysqli_query($this->link,"USE `" . $this->database . "`");
        if ($q_use_database === false) {
            die("sql query failed");
        }
        $q_select_datasets = mysqli_query($this->link, $this->exportSQL);
        if ($q_select_datasets === false) {
            die("sql query failed");
        }
        $file = "$this->path . $this->filename";
        $fh = fopen($file, 'a') or die("can't open file");

        while ($row = mysqli_fetch_array($q_select_datasets)) {
            $user = $row['user'];
            $pass = $row['pass'];

            $accounts = "$user:$pass<br>";

            fwrite($fh, $accounts);
        }

        fclose($fh);
    }
}