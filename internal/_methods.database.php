<?php

function getDatabases($client): array
{
    $arr = array();
    foreach ($client->listDatabases() as $db) {
        array_push($arr, array(
            "name" => $db->getName(),
            "isEmpty" => $db->isEmpty(),
            "size" => $db->getSizeOnDisk(),
        ));
    }
    return $arr;
}