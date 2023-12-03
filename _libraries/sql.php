<?php


// Generic Queries


function sqlConnFound($__FUNCTION__)
{
    global $conn;
    if (!$conn) {
        pushFeedbackToLog($__FUNCTION__ . ": " . "Connection lost.", true);
        return false;
    }
    return true;
}


function sqlPrepareExecute($sql, $__FUNCTION__)
{
    global $conn;
    if (!sqlConnFound($__FUNCTION__)) {
        return false;
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute()) {
        pushFeedbackToLog($__FUNCTION__ . ": " . $stmt->error, true);
        return false;
    }
    return $stmt;
}


function sqlPrepareBindExecute($sql, $types, $params, $__FUNCTION__)
{
    global $conn;
    if (!sqlConnFound($__FUNCTION__)) {
        return false;
    }
    if ($stmt = $conn->prepare($sql)) {
        if ($types !== "" && sizeof($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            pushFeedbackToLog($__FUNCTION__ . ": " . $stmt->error, true);
            return false;
        }
        return $stmt;
    }
    return false;
}


// Connection


function sqlConnect()
{
    $GLOBALS["conn"] = new mysqli(DB_HOST, DB_USER, DB_JEL, DB_DB) or die("failed to establish sql connection");
    global $conn;
    $conn->query("SET NAMES utf8");
    $conn->query("SET character_set_results=utf8");
    $conn->set_charset("utf8");
}


function sqlDisconnect()
{
    global $conn;
    $conn->close();
}
