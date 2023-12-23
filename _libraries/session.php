<?php
// DON'T require: dom.php


final class AuthLevel
{
    const GUEST = 0;
    const USER = 1;
}


final class MoveState
{
    const NOT_SELECTED = 0;
    const SELECTING = 1;
    const SELECTED = 2;
}


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


function auth(...$AuthLevels)
{
    $isGuest = is_null(getUserName());
    $isUser = !$isGuest;
    if ($isGuest && !in_array(AuthLevel::GUEST, $AuthLevels, true)) {
        return false;
    }
    if ($isUser && !in_array(AuthLevel::USER, $AuthLevels, true)) {
        return false;
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
    $lang = getLang();
    session_destroy();
    haveSession();
    setLang($lang);
    return !isset($_SESSION["uName"]);
}


function getTableAllKeys($tableName)
{
    if (fromSESSION($tableName . "AllKeys") == null) {
        setTableAllKeys($tableName, []);
    }
    return fromSESSION($tableName . "AllKeys");
}


function setTableAllKeys($tableName, $keys)
{
    $_SESSION[$tableName . "AllKeys"] = $keys;
}


function resetTableAllKeys($tableName = null)
{
    if ($tableName == null) {
        resetTableAllKeys("placeList");
        resetTableAllKeys("bookList");
    } else {
        setTableAllKeys($tableName, []);
        unset($_SESSION[$tableName . "AllKeys"]);
    }
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


function setNumberOfAuthors($number)
{
    haveSession();
    $_SESSION["noa"] = ($number >= 1) ? $number : 1;
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


function getAuthor()
{
    return fromSESSION("author");
}


function setAuthor($authorID)
{
    $_SESSION["author"] = $authorID;
}


function getBook()
{
    return fromSESSION("book");
}


function setBook($book)
{
    $_SESSION["book"] = $book;
}


function resetBook()
{
    haveSession();
    unset($_SESSION["book"]);
    return !isset($_SESSION["book"]);
}


function getMoveState()
{
    haveSession();
    if (is_null(fromSESSION("moveState"))) {
        $_SESSION["moveState"] = MoveState::NOT_SELECTED;
    }
    return fromSESSION("moveState");
}


function setMoveState($MoveState)
{
    $_SESSION["moveState"] = $MoveState;
}


function resetMoveState()
{
    haveSession();
    // resetMoveSqls();
    resetMoveLocs();
    resetMoveBooks();
    setMoveState(MoveState::NOT_SELECTED);
    return $_SESSION["moveState"] == MoveState::NOT_SELECTED;
}


function getMoveLocSql()
{
    if (is_null(fromSESSION("locsql"))) {
        $_SESSION["locsql"] = [
            "sql" => "",
            "types" => "",
            "params" => []
        ];
    }
    return fromSESSION("locsql");
}


function setMoveLocSql($sql, $types = "", $params = [])
{
    $_SESSION["locsql"] = [
        "sql" => $sql,
        "types" => $types,
        "params" => $params
    ];
}


function getMoveBookSql()
{
    if (is_null(fromSESSION("booksql"))) {
        $_SESSION["booksql"] = [
            "sql" => "",
            "types" => "",
            "params" => []
        ];
    }
    return fromSESSION("booksql");
}


function setMoveBookSql($sql, $types = "", $params = [])
{
    $_SESSION["booksql"] = [
        "sql" => $sql,
        "types" => $types,
        "params" => $params
    ];
}


function resetMoveSqls()
{
    $_SESSION["locsql"] = [
        "sql" => "",
        "types" => "",
        "params" => []
    ];
    $_SESSION["booksql"] = [
        "sql" => "",
        "types" => "",
        "params" => []
    ];
}

///////////////////               

function moveLocsPush($loc)
{
    haveSession();
    if (is_array(moveLocsGetAll())) {
        array_push($_SESSION["moveLocs"], $loc);
    }
}


function moveBooksPush($boo)
{
    haveSession();
    if (is_array(moveBooksGetAll())) {
        array_push($_SESSION["moveBooks"], $boo);
    }
}


function moveLocsGetAll()
{
    if (is_null(fromSESSION("moveLocs"))) {
        $_SESSION["moveLocs"] = [];
    }
    return fromSESSION("moveLocs");
}


function moveBooksGetAll()
{
    if (is_null(fromSESSION("moveBooks"))) {
        $_SESSION["moveBooks"] = [];
    }
    return fromSESSION("moveBooks");
}


function resetMoveLocs()
{
    $_SESSION["moveLocs"] = [];
    unset($_SESSION["moveLocs"]);
}


function resetMoveBooks()
{
    $_SESSION["moveBooks"] = [];
    unset($_SESSION["moveBooks"]);
}

function setLang($langCode)
{
    $langAssoc = LANG_ASSOC;
    if (key_exists($langCode, $langAssoc)) {
        return $_SESSION["lang"] = $langCode;
    }
    return setLang(DEFAULT_LANG);
}

function getLang()
{
    $lang = fromSESSION("lang");
    if (!is_null($lang) && key_exists($lang, LANG_ASSOC)) {
        if(file_exists(LANG_DIR . $lang . ".php")) {
            return $lang;
        }
        else{
            pushFeedbackToLog("Language \"" . LANG_ASSOC[$lang] . "\" not found.", true);
        }
    }
    setLang(DEFAULT_LANG);
    return DEFAULT_LANG;
}
