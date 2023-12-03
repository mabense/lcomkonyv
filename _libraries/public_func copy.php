<?php
// DON'T require: dom.php


function pageToDisplayText($page)
{
    global $pageName;
    if (array_key_exists($page, $pageName)) {
        return toDisplayText($pageName[$page]);
    }
    return toDisplayText($page);
}


function toDisplayText($page)
{
    return ucfirst(str_replace("_", " ", $page));
}


function findPage($nextPage)
{
    // TODO: GET params
    $route = ROOT . $nextPage . DIRECTORY_SEPARATOR;
    if (file_exists($route . "index.php")) {
        return $nextPage;
    }
    return PAGE . "?missing=" . $nextPage;
}


function redirectTo($root, $pageRoute)
{
    header("Location: " . $root . findPage($pageRoute));
    exit;
}

function fromGET($nameInGET)
{
    $valueInGET = null;
    // if (!isset($_SESSION["sGET"]) or !is_array($_SESSION["sGET"])) {
    //     $_SESSION["sGET"] = [];
    // }
    // foreach ($_GET as $key => $value) {
    //     if ($key != $nameInGET && $key != 'user') {
    //         $_SESSION["sGET"][$key] = $value;
    //     }
    // }

    if (isset($_GET[$nameInGET])) {
        $valueInGET = $_GET[$nameInGET];
    }
    // elseif (isset($_SESSION["sGET"][$nameInGET])){
    //     $valueInGET = $_SESSION["sGET"][$nameInGET];
    // }
    // unset($_SESSION["sGET"][$nameInGET]);
    return $valueInGET;
}

function fromPOST($nameInPOST)
{
    if (isset($_POST[$nameInPOST])) {
        return $_POST[$nameInPOST];
    }
    return null;
}

function handleMissingPage()
{
    haveSession();
    $missingPage = fromGET("missing");
    if (isset($missingPage)) {
        pushFeedbackToLog(
            str_replace("%page%", pageToDisplayText($missingPage), ErrorString::HANDLE_MISSING_NOT_FOUND/*->value*/),
            true
        );
        redirectTo(ROOT, PAGE);
    }
}

function handleAction()
{
    haveSession();
    $action = fromGET("action");
    if (isset($action)) {
        $actionPath = "./" . $action . ".php";
        if (file_exists($actionPath)) {
            include_once($actionPath);
            pushFeedbackToLog(
                str_replace("%action%", pageToDisplayText($action), ErrorString::HANDLE_ACTION_FAILED/*->value*/),
                true
            );
            redirectTo(ROOT, PAGE);
        } else {
            pushFeedbackToLog(
                str_replace("%action%", pageToDisplayText($action), ErrorString::HANDLE_ACTION_NOT_FOUND/*->value*/),
                true
            );
            redirectTo(ROOT, PAGE);
        }
    }
}


function handleLocationJump()
{
    haveSession();
    $location = fromGET("jump");
    if (isset($location)) {
        sqlSetLocationPath(($location !== "") ? $location : null);
        redirectTo(ROOT, PAGE);
    }
}


function handleTableRow()
{
    haveSession();
    $tableName = fromGET("table");
    $rowIndex = fromGET("row");
    if (isset($rowIndex)) {
        $allKeys = getTableAllKeys($tableName);
        $keys = $allKeys[$rowIndex];
        switch (PAGE) {
            case 'locations':
                pushLocation($keys["id"]);
                break;
            case 'milestone':
                // setMilestone($keys["number"]);
                break;
            case 'document':
                // setDocument($keys["requirement"]);
                break;
            default:
                break;
        }

        resetTableAllKeys($tableName);

        redirectTo(ROOT, PAGE);
    }
}
