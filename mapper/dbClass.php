<?php

require_once '../const.php';

class DB
{
    private $dbhost;
    private $dbname;
    private $dbuser;
    private $dbpass;

    private $connection;

    function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->connect();

    }

    public function connect()
    {
        $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
    }

    function __get($connection)
    {
        return $this->connection;
    }
}


?>