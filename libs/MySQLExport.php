<?php


class MySQLExport {
    private $path = "";
    private $filename = "";
    private $exportSQL = "";
    private $table = "newtable";
    private $insertHead = "";
    private $maxrowsperloop = 1000;

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

    /**
     * do not use any limit clause as the tool will use own settings
     * @param string $exportSQL the sql to be used for generation the export (without limit clause)
     */
    public function setExportSQL($exportSQL) {
        $this->exportSQL = $exportSQL;
    }

    public function setTable($table) {
        $this->table = $table;
    }

    private function getInsertStatementHead() {
        if ($this->insertHead == "") {
            $this->setInsertStatementHead();
        }
        return $this->insertHead;
    }

    private function setInsertStatementHead() {
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);
        $q_use_database = mysqli_query($this->link, "USE `" . $this->database . "`");
        if ($q_use_database === false) {
            die("sql query failed");
        }
        $q_select_datasets = mysqli_query($this->link, $this->exportSQL . " LIMIT 1");
        if ($q_select_datasets === false) {
            die("sql query failed");
        }

        $fields = mysqli_fetch_fields($q_select_datasets);
        $this->fields = $fields;
        $fields_count = count($fields);
        $this->fieldCount = $fields_count;
        $insert_head = "INSERT INTO `" . $this->table . "` (";
        for ($i = 0; $i < $fields_count; $i++) {
            $insert_head .= "`" . $fields[$i]->name . "`";
            if ($i < $fields_count - 1) {
                $insert_head .= ', ';
            }
        }
        $insert_head .= ")";
        $insert_head .= " VALUES\n";

        $this->insertHead = $insert_head;
    }


    public function doExport() {
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);


        $q_use_database = mysqli_query($this->link, "USE `" . $this->database . "`");
        if ($q_use_database === false) {
            die("sql query failed");
        }
        $q_select_datasets = mysqli_query($this->link, $this->exportSQL . " LIMIT 5");
        if ($q_select_datasets === false) {
            die("sql query failed");
        }

        $count_datasets = mysqli_num_rows($q_select_datasets);

        if ($count_datasets < 1) {
            // maybe generate empty statement ???
            die("no datasets to be fetched");
        }


        $file = "$this->path" . "$this->filename";
        $fh = fopen($file, 'a') or die("can't open file");
        $r = 0;
        $row_count = mysqli_num_rows($q_select_datasets);
        $content = "";
        while ($row = mysqli_fetch_row($q_select_datasets)) {
            $content .= $this->getInsertStatementHead();
            $content .= "(";
            for ($i = 0; $i < $this->fieldCount; $i++) {
                $row_content = str_replace("\n", "\\n", mysqli_real_escape_string($this->link, $row[$i]));

                switch ($this->fields[$i]->type) {
                    case 8:
                    case 3:
                        $content .= $row_content;
                        break;
                    default:
                        $content .= "'" . $row_content . "'";
                }
                if ($i < $this->fieldCount - 1) {
                    $content .= ', ';
                }
            }
            if (($r + 1) == $row_count) {
                $content .= ");\n\n";
            } else {
                $content .= "),\n";
            }
            $r++;
        }

        fwrite($fh, $content);
        fclose($fh);

        return true;
    }

    public function testConnection() {
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database, $this->port);
        var_dump($this->link);
    }

    public function testInsertHead() {
        var_dump($this->getInsertStatementHead());
    }
}