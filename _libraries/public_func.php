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
    $route = ROOT . $nextPage . DIRECTORY_SEPARATOR;
    if (file_exists($route . "index.php")) {
        return $nextPage;
    }
    return PAGE . "?missing=" . $nextPage;
}


function redirectTo($root, $pageRoute)
{
    if (
        $pageRoute != "edit_book" &&
        $pageRoute != "new_book"
    ) {
        resetNumberOfAuthors();
    }
    if (
        getMoveState() == MoveState::SELECTING
    ) {
        resetMoveState();
    }
    header("Location: " . $root . findPage($pageRoute));
    exit;
}


function redirectToPreviousPage()
{
    redirectTo(
        ROOT,
        PAGE == "cancel" ? getPreviousPage() : popPreviousPage()
    );
}


function fromGET($nameInGET)
{
    if (isset($_GET[$nameInGET])) {
        return $_GET[$nameInGET];
    }
    return null;
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
            case 'book':
                setBook($keys["id"]);
                break;
            case 'books_by_author':
                setAuthor($keys["id"]);
                break;
            default:
                break;
        }

        resetTableAllKeys($tableName);

        redirectTo(ROOT, PAGE);
    }
}


function pushPreviousPage()
{
    haveSession();
    if (
        !isset($_SESSION["prevPage"])
    ) {
        $_SESSION["prevPage"] = [];
    }
    if (in_array(PAGE, $_SESSION["prevPage"], true)) {
        $keep = [];
        foreach ($_SESSION["prevPage"] as $page) {
            if ($page == PAGE) {
                break;
            }
            array_push($keep, $page);
        }
        $_SESSION["prevPage"] = $keep;
    }
    array_push($_SESSION["prevPage"], PAGE);
}


function popPreviousPage()
{
    haveSession();
    if (
        is_null(fromSESSION("prevPage"))
    ) {
        return PAGE;
    }
    return array_pop($_SESSION["prevPage"]);
}


function getPreviousPage()
{
    haveSession();
    if (
        is_null(fromSESSION("prevPage"))
        or sizeof(fromSESSION("prevPage")) == 0
    ) {
        return PAGE;
    }
    return $_SESSION["prevPage"][sizeof($_SESSION["prevPage"])];
}


function canMoveFromHere()
{
    resetTableAllKeys();

    $GLOBALS["moveStt"] = getMoveState();
    global $moveStt;

    switch ($moveStt) {
        case MoveState::NOT_SELECTED:
            resetMoveSqls();
            break;
        case MoveState::SELECTING:
            setMoveState(MoveState::NOT_SELECTED);
            $moveStt = getMoveState();
            break;
        case MoveState::SELECTED:
            break;
        default:
            break;
    }
}
