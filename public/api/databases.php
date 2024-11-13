<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
//header("Access-Control-Request-Methods: *");

global $client;
require_once __DIR__ . "/../../internal/createDBConnection.php";
require_once __DIR__ . "/../../internal/_methods.php";
require_once __DIR__ . "/../../internal/_methods.database.php";

auth();

//var_dump($client->listDatabases());
switch ($_SERVER["REQUEST_METHOD"]) {
    //list all databases
    case "GET":
        $arr = getDatabases($client);
        echo json_encode($arr);
        break;
    //create new database (or more specifically the collection)
    case "PUT":
        if (!checkParam("database") || !checkParam("collection")) {
            http_response_code(400);
            echo json_encode(array("message" => "Missing parameters"));
            break;
        }
        $res = $client->selectDatabase($_GET["database"])->createCollection($_GET["collection"]);
        echo json_encode($res);
        break;
    //delete one database
    case "DELETE":
        if (!checkParam("database")) {
            http_response_code(400);
            echo json_encode(array("message" => "Missing parameter"));
            break;
        }
        $res = $client->dropDatabase($_GET["database"]);
        echo json_encode($res);
        //echo json_encode(array("success" => true));
        break;
    case "HEAD":
    case "POST":
    case "PATCH":
        http_response_code(405);
        echo json_encode(array("error" => "Method not implemented"));
        break;
    default:
        http_response_code(200);
        echo json_encode(array());
        break;
}
