<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "move_select");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_dom.php");
require_once(LIB_DIR . "sql_session.php");

haveSession();

if (!auth(false, DEV_USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

sqlConnect();
handleLocationJump();

handleAction();

handleTableRow();
resetTableAllKeys();

$page = PAGE;
$location = getLocation();

setMoveState(MoveState::SELECTING);

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_form.htm");

    domAppendTemplateTo("mainForm", TEMPLATE_DIR . "table_select.htm");

    $tLocation = PLACE_TABLE;
    $tBook = BOOK_TABLE;
    $tWriter = WRITER_TABLE;
    $tWrote = BOOK_AUTHOR_TABLE;

    $placeConditions = "ISNULL(`where`)";
    $bookConditions = "ISNULL(`location`)";
    $sqlTypes = "";
    $sqlParams = [];

    if (isset($location)) {
        $placeConditions = "`where`=?";
        $bookConditions = "`location`=?";
        $sqlTypes = "i";
        $sqlParams = [
            $location
        ];

        $stmt = sqlPrepareBindExecute(
            "SELECT `name` FROM $tLocation WHERE `id`=?",
            "i",
            [
                $location
            ],
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $page = $row["name"];
            }
        }
    }


    $locsql = getMoveLocSql();
    $sqlHasPlaces = strlen($locsql["sql"]) > 0;

    $booksql = getMoveBookSql();
    $sqlHasBooks = strlen($booksql["sql"]) > 0;

    if ($sqlHasPlaces) {
        domSetString("placeListHead", TableString::PLACES, StringTarget::TEXT_CONTENT);

        $stmt = sqlTableParams(
            $locsql["sql"], 
            $locsql["types"], 
            $locsql["params"], 
            [
                // "id" => "id", 
                "name" => "(" . TableString::PLACE_ENTER . ")"
            ],
            "locations",
            [
                "id"
            ],
            "placeList"
        );
    } else {
        domDeleteElementById("placeListHead");
        domDeleteElementById("placeList");
    }

    if ($sqlHasBooks) {
        domSetString("bookListHead", TableString::BOOKS, StringTarget::TEXT_CONTENT);

        $stmt = sqlTableParams(
            $booksql["sql"], 
            $booksql["types"], 
            $booksql["params"], 
            [
                "authors" => TableString::BOOK_AUTHOR,
                "title" => TableString::BOOK_TITLE,
                "series" => TableString::BOOK_SERIES,
                "number" => TableString::BOOK_NUMBER
            ],
            "book",
            [
                "id"
            ],
            "bookList"
        );
    } else {
        domDeleteElementById("bookListHead");
        domDeleteElementById("bookList");
    }

    if (!$sqlHasPlaces && !$sqlHasBooks) {
        domSetString("contentContainer", FeedbackString::PLACE_EMPTY, StringTarget::TEXT_CONTENT);
    }

    $locationPathString = sqlGetLocationPathString($page);

    $buttons = $dom->getElementById("contentButtons");

    // $dom = new DOMDocument();

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("cancel"));
    $buttons->appendChild($exitLoc);


    domSetTitle(
        pageToDisplayText($page),
        $locationPathString
    );


    // pushFeedbackToLog("path: " . implode(", ", getLocationPath()));
    // $_str = [];
    // $keys = is_array(getTableAllKeys("placeList")) ? getTableAllKeys("placeList") : [];
    // foreach($keys as $key) {
    //     array_push($_str, "[" . implode(", ", $key) . "]");
    // }
    // pushFeedbackToLog("keys: " . implode(" ", $_str));


    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();
