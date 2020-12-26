<?php
require_once "./Database.php";

Database::setDatabase('test')->setUsername('root')->connect();
$query = Database::where('price', '>', 100)->from('books')->get();
echo '<pre>' , var_dump($query) , '</pre>';