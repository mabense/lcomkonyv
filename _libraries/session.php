<?php
// DON'T require: dom.php


final class AuthLevel
{
    const GUEST = "g";
    const USER = "u";
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
    if (isset($_SESSION["bib_" . $nameInSESSION])) {
        return $_SESSION["bib_" . $nameInSESSION];
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
    $_SESSION["bib_" . "uName"] = $userName;
}


function resetUser()
{
    $lang = getLang();
    session_destroy();
    haveSession();
    setLang($lang);
    return !isset($_SESSION["bib_" . "uName"]);
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
    $_SESSION["bib_" . $tableName . "AllKeys"] = $keys;
}


function resetTableAllKeys($tableName = null)
{
    if ($tableName == null) {
        resetTableAllKeys("placeList");
        resetTableAllKeys("bookList");
    } else {
        setTableAllKeys($tableName, []);
        unset($_SESSION["bib_" . $tableName . "AllKeys"]);
    }
}


function haveLocationPath()
{
    if (!isset($_SESSION["bib_" . "location"]) || !is_array($_SESSION["bib_" . "location"])) {
        $_SESSION["bib_" . "location"] = [];
    }
}


function getLocationPath()
{
    haveLocationPath();
    return $_SESSION["bib_" . "location"];
}


function pushLocation($id)
{
    haveLocationPath();
    array_push($_SESSION["bib_" . "location"], $id);
}


function popLocation()
{
    haveLocationPath();
    return array_pop($_SESSION["bib_" . "location"]);
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
    $_SESSION["bib_" . "location"] = [];
    unset($_SESSION["bib_" . "location"]);
    return !isset($_SESSION["bib_" . "location"]);
}


function getNumberOfAuthors()
{
    haveSession();
    if (isset($_SESSION["bib_" . "noa"])) {
        return $_SESSION["bib_" . "noa"];
    }
    $_SESSION["bib_" . "noa"] = 1;
    return $_SESSION["bib_" . "noa"];
}


function setNumberOfAuthors($number)
{
    haveSession();
    $_SESSION["bib_" . "noa"] = ($number >= 1) ? $number : 1;
    return $_SESSION["bib_" . "noa"];
}


function lessAuthors()
{
    haveSession();
    if ($_SESSION["bib_" . "noa"] > 1) {
        $_SESSION["bib_" . "noa"]--;
    }
}


function moreAuthors()
{
    haveSession();
    $_SESSION["bib_" . "noa"]++;
}


function resetNumberOfAuthors()
{
    haveSession();
    unset($_SESSION["bib_" . "noa"]);
    return !isset($_SESSION["bib_" . "noa"]);
}


function getAuthor()
{
    return fromSESSION("author");
}


function setAuthor($authorID)
{
    $_SESSION["bib_" . "author"] = $authorID;
}


function getBook()
{
    return fromSESSION("book");
}


function setBook($book)
{
    $_SESSION["bib_" . "book"] = $book;
}


function resetBook()
{
    haveSession();
    unset($_SESSION["bib_" . "book"]);
    return !isset($_SESSION["bib_" . "book"]);
}


function getMoveState()
{
    haveSession();
    if (is_null(fromSESSION("moveState"))) {
        $_SESSION["bib_" . "moveState"] = MoveState::NOT_SELECTED;
    }
    return fromSESSION("moveState");
}


function setMoveState($MoveState)
{
    $_SESSION["bib_" . "moveState"] = $MoveState;
}


function resetMoveState()
{
    haveSession();
    // resetMoveSqls();
    resetMoveLocs();
    resetMoveBooks();
    setMoveState(MoveState::NOT_SELECTED);
    return $_SESSION["bib_" . "moveState"] == MoveState::NOT_SELECTED;
}


function getMoveLocSql()
{
    if (is_null(fromSESSION("locsql"))) {
        $_SESSION["bib_" . "locsql"] = [
            "sql" => "",
            "types" => "",
            "params" => []
        ];
    }
    return fromSESSION("locsql");
}


function setMoveLocSql($sql, $types = "", $params = [])
{
    $_SESSION["bib_" . "locsql"] = [
        "sql" => $sql,
        "types" => $types,
        "params" => $params
    ];
}


function getMoveBookSql()
{
    if (is_null(fromSESSION("booksql"))) {
        $_SESSION["bib_" . "booksql"] = [
            "sql" => "",
            "types" => "",
            "params" => []
        ];
    }
    return fromSESSION("booksql");
}


function setMoveBookSql($sql, $types = "", $params = [])
{
    $_SESSION["bib_" . "booksql"] = [
        "sql" => $sql,
        "types" => $types,
        "params" => $params
    ];
}


function resetMoveSqls()
{
    $_SESSION["bib_" . "locsql"] = [
        "sql" => "",
        "types" => "",
        "params" => []
    ];
    $_SESSION["bib_" . "booksql"] = [
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
        array_push($_SESSION["bib_" . "moveLocs"], $loc);
    }
}


function moveBooksPush($boo)
{
    haveSession();
    if (is_array(moveBooksGetAll())) {
        array_push($_SESSION["bib_" . "moveBooks"], $boo);
    }
}


function moveLocsGetAll()
{
    if (is_null(fromSESSION("moveLocs"))) {
        $_SESSION["bib_" . "moveLocs"] = [];
    }
    return fromSESSION("moveLocs");
}


function moveBooksGetAll()
{
    if (is_null(fromSESSION("moveBooks"))) {
        $_SESSION["bib_" . "moveBooks"] = [];
    }
    return fromSESSION("moveBooks");
}


function resetMoveLocs()
{
    $_SESSION["bib_" . "moveLocs"] = [];
    unset($_SESSION["bib_" . "moveLocs"]);
}


function resetMoveBooks()
{
    $_SESSION["bib_" . "moveBooks"] = [];
    unset($_SESSION["bib_" . "moveBooks"]);
}

function setLang($langCode)
{
    $langAssoc = LANG_ASSOC;
    if (key_exists($langCode, $langAssoc)) {
        return $_SESSION["bib_" . "lang"] = $langCode;
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
