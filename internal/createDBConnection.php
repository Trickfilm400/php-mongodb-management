<?php

require_once __DIR__ . '/../vendor/autoload.php';

$db_host = getenv('DB_HOST') ?: "localhost";
$db_port = getenv('DB_PORT') ?: "27017";
$db_user = getenv('DB_USER') ?: "root";
$db_password = getenv('DB_PASSWORD') ?: "example";
$db_authSource = getenv('DB_USER_AUTH_SOURCE') ?: "admin";

$connectionString = "mongodb://$db_user:$db_password@$db_host:$db_port/?authSource=$db_authSource";
//error_log($connectionString);

//var_dump($connectionString);

$client = null;
try {
    $client = new MongoDB\Client($connectionString);

} catch (Throwable $e) {
    error_log($e);
    //var_dump($e->getMessage());
}


//var_dump($client->listDatabases());
