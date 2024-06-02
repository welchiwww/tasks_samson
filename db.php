<?php
$server = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'test_samson';

$connection = new mysqli($server, $username, $password, $dbname);

if ($connection->connect_error){
    throw new Exception("Ошибка подключения к базе данных");
}