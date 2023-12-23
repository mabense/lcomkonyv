<?php
// DON'T require: ANYTHING

// use function PHPSTORM_META\type;

function isThereFeedback()
{
    $log = fromSESSION("log");
    if (isset($log)) {
        return (is_array($log) && sizeof($log) > 0);
    }
    return false;
}


function getFeedbackLog()
{
    if (isset($_SESSION["bib_" . "log"])) {
        return $_SESSION["bib_" . "log"];
    } else {
        return false;
    }
}


function pushFeedbackToLog($message, $isError = false)
{
    // if(is_object($message) && enum_exists($message::class)){
    //     $message = $message->value;
    // }

    if (!isset($_SESSION["bib_" . "log"])) {
        $_SESSION["bib_" . "log"] = [];
    }
    array_push($_SESSION["bib_" . "log"], [$message, $isError]);
}


function resetFeedbackLog()
{
    if (isset($_SESSION["bib_" . "log"])) {
        $_SESSION["bib_" . "log"] = [];
        unset($_SESSION["bib_" . "log"]);
    }
}
