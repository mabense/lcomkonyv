<?php
// DON'T require: dom.php

function haveSession()
{
    if (!session_id()) {
        session_start();
    }
}

function fromSESSION($nameInSESSION)
{
    if (isset($_SESSION[$nameInSESSION])) {
        return $_SESSION[$nameInSESSION];
    }
    return null;
}


function auth($acceptGuest, ...$acceptedUsers)
{
    $user = getUserName();
    $isGuest = !$user;
    if (!$acceptGuest && $isGuest) {
        return false;
    }
    if (!$isGuest) {
        if (!is_string($user)) {
            return false;
        }
        if (!is_array($acceptedUsers)) {
            return false;
        }
        if (!in_array($user, $acceptedUsers)) {
            return false;
        }
    }
    return true;
}

function getUserName()
{
    return fromSESSION("uName");
}

function setUser($userName)
{
    $_SESSION["uName"] = $userName;
}

function resetUser()
{
    // resetLocation();
    // resetNumberOfAuthors();
    // unset($_SESSION["uName"]);
    // unset($_SESSION["sGET"]);
    session_destroy();
    haveSession();
    return !isset($_SESSION["uName"]);
}


function getTableAllKeys($tableName)
{
    return fromSESSION($tableName . "AllKeys");
}


function setTableAllKeys($tableName, $keys)
{
    $_SESSION[$tableName . "AllKeys"] = $keys;
}

function resetTableAllKeys($tableName = null)
{
    setTableAllKeys($tableName, false);
    unset($_SESSION[$tableName . "AllKeys"]);
    return !isset($_SESSION[$tableName . "AllKeys"]);
}


function haveLocationPath()
{
    if (!isset($_SESSION["location"]) || !is_array($_SESSION["location"])) {
        $_SESSION["location"] = [];
    }
}


function getLocationPath()
{
    haveLocationPath();
    return $_SESSION["location"];
}


function pushLocation($id)
{
    haveLocationPath();
    array_push($_SESSION["location"], $id);
}


function popLocation()
{
    haveLocationPath();
    return array_pop($_SESSION["location"]);
}


function getLocation()
{
    $path = getLocationPath();
    $top = sizeof($path) - 1;
    if ($top < 0) {
        return null;
    }
    return $path[$top];
}


function resetLocation()
{
    $_SESSION["location"] = [];
    unset($_SESSION["location"]);
    return !isset($_SESSION["location"]);
}

function getNumberOfAuthors()
{
    haveSession();
    if (isset($_SESSION["noa"])) {
        return $_SESSION["noa"];
    }
    $_SESSION["noa"] = 1;
    return $_SESSION["noa"];
}

function lessAuthors()
{
    haveSession();
    if ($_SESSION["noa"] > 1) {
        $_SESSION["noa"]--;
    }
}

function moreAuthors()
{
    haveSession();
    $_SESSION["noa"]++;
}

function resetNumberOfAuthors()
{
    haveSession();
    unset($_SESSION["noa"]);
    return !isset($_SESSION["noa"]);
}
