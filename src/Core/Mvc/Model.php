<?php namespace Core\Mvc;

use Core\Db\MySql\Database;

abstract class Model {

    protected $database;

    public function __construct() {
        $this->database = new Database();
    }
}