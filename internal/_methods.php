<?php
function checkParam(string $name)
{
    if (isset($_GET[$name]) && !empty($_GET[$name])) return true;
    return false;
}

function validateToken(string | null $token): bool
{
    $split = explode(' ', $token);
    if ($split[0] !== "Bearer") return false;
    $config_token = getenv('AUTH_TOKEN') ?: "";
    if ($config_token === "") return true;
    else if ($config_token !== $split[1]) return false;
    else return true;
}

function auth(): void
{
    $token = $_SERVER["HTTP_AUTHORIZATION"];
    $res = validateToken($token);
    if ($res !== true) {
        header("HTTP/1.1 401 Unauthorized");
        die();
    };
}