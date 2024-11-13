<?php
/*
 * PUT & POST: create user
 * DELETE:
 * PATCH: password update
 * GET: all user list / single user list
 */

/*
 * authDatabase
 * userName
 * Password
 */


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");


global $client;
require_once __DIR__ . "/../../internal/createDBConnection.php";
require_once __DIR__ . "/../../internal/_methods.php";
require_once __DIR__ . "/../../internal/_methods.database.php";

auth();

//var_dump($client->listDatabases());
switch ($_SERVER["REQUEST_METHOD"]) {
    //list all databases
    case "GET":
        $dbs = getDatabases($client);
        $return_array = [];
        foreach ($dbs as $db) {
            //get users
            $systemUsers = $client->selectCollection($db["name"], "system.users")->find();
            $usersInfo = $client->selectDatabase($db["name"])->command(['usersInfo' => 1]);
            $final_array = ["system_user" => [], "db_user" => []];
            foreach ($systemUsers as $systemUser) {
                array_push($final_array["system_user"], ($systemUser));
            }
            foreach ($usersInfo as $user) {
                if ($user["ok"] == 1 && count($user["users"]) > 0) {
//                    var_dump($user["ok"]);
//                    array_push($final_array["db_user"], ($user["users"]));
                    $final_array["db_user"] = $user["users"];
                }
            }
            $return_array[$db["name"]] = $final_array;
//            array_push($return_array, array($db["name"] => $final_array));
        }
        echo json_encode(array("data" => $return_array));
        break;
    //create new database (or more specifically the collection)
    case "PUT":
        if (!checkParam("username") || !checkParam("password") || !checkParam("authSource")) {
            http_response_code(400);
            echo json_encode(array("message" => "Missing parameters"));
            break;
        }
        $command = array(
            "createUser" => $_GET["username"],
            "pwd" => $_GET["password"],
            "roles" => array()
        );
        $result = $client->getManager()->executeCommand( // (4)
            $_GET["authSource"], new MongoDB\Driver\Command($command)
        );
        echo json_encode($result);
        break;
    //delete one database
    case "DELETE":
        if (!checkParam("userId")) {
            http_response_code(400);
            echo json_encode(array("message" => "Missing parameter"));
            break;
        }
        $user = explode(".", $_GET["userId"]);
        $command = array(
            "dropUser" => $user[1],
        );
        $result = $client->getManager()->executeCommand( // (4)
            $user[0], new MongoDB\Driver\Command($command)
        );
        echo json_encode($result);
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
