<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "locations");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_dom.php");
require_once(LIB_DIR . "sql_session.php");

haveSession();

if (!auth(AuthLevel::USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

sqlConnect();
handleLocationJump();

handleAction();

handleTableRow();

canMoveFromHere();
pushPreviousPage();

$page = PAGE;
$location = getLocation();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_result.htm");

    domAppendTemplateTo("contentContainer", "./view.htm");

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

    $sqlHasPlaces = sqlPrepareBindExecute(
        "SELECT `id` FROM $tLocation WHERE $placeConditions",
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    )->get_result()->num_rows > 0 ? true : false;

    $sqlHasBooks = sqlPrepareBindExecute(
        "SELECT `id` FROM $tBook WHERE $bookConditions",
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    )->get_result()->num_rows > 0 ? true : false;

    if ($sqlHasPlaces) {
        domSetString("placeListHead", TableString::PLACES, StringTarget::TEXT_CONTENT);

        $fields = "`id`, `name`";
        $stmt = sqlTableParams(
            "SELECT $fields FROM $tLocation WHERE $placeConditions
            ORDER BY `name` DESC",
            $sqlTypes,
            $sqlParams,
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

        $full_name = "CONCAT(
            `$tWriter`.`surname`, ',', `$tWriter`.`givenname`, 
            IF(`$tWriter`.`clarification` = '', 
                '', 
                CONCAT(
                    '(', `$tWriter`.`clarification`, ')'
                )
            )
        )";
        // $authors = "GROUP_CONCAT($full_name)";
        $authors = "GROUP_CONCAT(DISTINCT $full_name SEPARATOR ' - ')";
        $fields = "`$tBook`.`id`, `$tBook`.`title`, 
        IF(ISNULL($authors), '', $authors) AS 'authors', 
        IF(ISNULL(`series`), '', `series`) AS 'series', 
        IF(ISNULL(`number_in_series`), '', `number_in_series`) AS 'number'";
        $sql = "SELECT $fields 
        FROM (
            (
                `$tBook` LEFT JOIN `$tWrote` ON `$tBook`.`id`=`$tWrote`.`book`
            ) LEFT JOIN `$tWriter` ON `$tWriter`.`id`=`$tWrote`.`author`
        )
        WHERE $bookConditions GROUP BY `$tBook`.`id` 
        ORDER BY `series`, `authors`, `number`, `$tBook`.`title`";
        sqlTableParams(
            $sql,
            $sqlTypes,
            $sqlParams,
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

    $locationPathString = sqlGetLocationPathString();

    $buttons = $dom->getElementById("contentButtons");

    // $dom = new DOMDocument();
    if (isset($location)) {
        $exitLoc = $dom->createElement("a", ButtonString::LOCATION_EXIT);
        $exitLoc->setAttribute("class", "a_button");
        $exitLoc->setAttribute("href", "../" . findPage("exit_location"));
        $buttons->appendChild($exitLoc);

        $editLoc = $dom->createElement("a", ButtonString::LOCATION_EDIT);
        $editLoc->setAttribute("class", "a_button");
        $editLoc->setAttribute("href", "../" . findPage("edit_location"));
        $buttons->appendChild($editLoc);

        $delLoc = $dom->createElement("a", ButtonString::LOCATION_DELETE);
        $delLoc->setAttribute("class", "a_button");
        $delLoc->setAttribute("href", "../" . findPage("delete_location"));
        $buttons->appendChild($delLoc);
    } else {
        $exitLoc = $dom->createElement("a", ButtonString::LOCATION_NO_EXIT);
        $exitLoc->setAttribute("class", "a_button a_disabled");
        $buttons->appendChild($exitLoc);
    }
    $newLoc = $dom->createElement("a", ButtonString::LOCATION_NEW);
    $newLoc->setAttribute("class", "a_button");
    $newLoc->setAttribute("href", "../" . findPage("new_location"));
    $buttons->appendChild($newLoc);

    $newBook = $dom->createElement("a", ButtonString::BOOK_NEW);
    $newBook->setAttribute("class", "a_button");
    $newBook->setAttribute("href", "../" . findPage("new_book"));
    $buttons->appendChild($newBook);

    if(getMoveState() == MoveState::SELECTED) {
        $paste = $dom->createElement("a", ButtonString::MOVE_PASTE);
        $paste->setAttribute("class", "a_button");
        $paste->setAttribute("href", "../" . findPage("move_paste"));
        $buttons->appendChild($paste);
        
        $unmove = $dom->createElement("a", ButtonString::MOVE_CANCEL);
        $unmove->setAttribute("class", "a_button");
        $unmove->setAttribute("href", "../" . findPage("move_cancel"));
        $buttons->appendChild($unmove);
    } else {
        $sele = $dom->createElement("a", ButtonString::MOVE_SELECT);
        $sele->setAttribute("class", "a_button");
        $sele->setAttribute("href", "../" . findPage("move_select"));
        $buttons->appendChild($sele);
    }


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
