<?php
    class db{
        //Properties
        private $dbhost = 'labmm.clients.ua.pt';
        private $dbuser = 'deca_17L4_41_dbo';
        private $port = '3306';
        private $char = 'utf8';
        private $dbpass = '9j6c69';
        private $dbname = 'deca_17L4_41';

        //Connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;mysql:charset=$this->char;port=$this->port;dbname=$this->dbname;";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            $dbConnection->exec("SET NAMES utf8");
            $dbConnection ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
    }