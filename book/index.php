<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "book");

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
resetTableAllKeys();

$page = PAGE;
$book = getBook();
$authors = "";
$series = "";
$number = 0;

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_result.htm");

    domAppendTemplateTo("contentContainer", "./view.htm");

    $tLocation = PLACE_TABLE;
    $tBook = BOOK_TABLE;
    $tWriter = WRITER_TABLE;
    $tWrote = BOOK_AUTHOR_TABLE;

    $bookConditions = "ISNULL(`location`)";
    $sqlTypes = "";
    $sqlParams = [];

    if (isset($book)) {
        $bookConditions = "`$tBook`.`id`=?";
        $sqlTypes = "i";
        $sqlParams = [
            $book
        ];

        $stmt = sqlPrepareBindExecute(
            "SELECT `title`, `location` FROM $tBook WHERE $bookConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $page = $row["title"];
                $location = $row["location"];
            }
        }

        if (isset($location)) {
            sqlSetLocationPath(($location !== "") ? $location : null);
        }

        $locationFull = sqlGetLocationPathString();

        $full_name = "
        CONCAT(
            `$tWriter`.`surname`, ',', `$tWriter`.`givenname`, 
            IF(
                `$tWriter`.`clarification` = '', 
                '', 
                CONCAT(
                    '(', `$tWriter`.`clarification`, ')'
                )
            )
        )";
        // $authors = "GROUP_CONCAT($full_name)";
        $authors = "GROUP_CONCAT(DISTINCT $full_name SEPARATOR ' - ')";
        $fields = "`$tBook`.`id`, 
        IF(ISNULL($authors), '', $authors) AS 'authors', 
        IF(ISNULL(`series`), '', `series`) AS 'series', 
        IF(ISNULL(`number_in_series`), '', `number_in_series`) AS 'number'";
        $sql = "SELECT $fields 
        FROM (
            (
                `$tBook` LEFT JOIN `$tWrote` ON `$tBook`.`id`=`$tWrote`.`book`
            ) LEFT JOIN `$tWriter` ON `$tWriter`.`id`=`$tWrote`.`author`
        )
        WHERE $bookConditions GROUP BY `$tBook`.`id` ORDER BY `authors`, `series`, `number`";
        $stmt = sqlPrepareBindExecute(
            $sql,
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $authors = $row["authors"];
                $series = $row["series"];
                $number = $row["number"];
            }
        }
    }

    $sep = ": ";
    domSetStrings(
        new TargetedString("label-where", TableString::BOOK_PLACE . $sep), 
        new TargetedString("book-where", $locationFull), 
        new TargetedString("label-by", TableString::BOOK_AUTHOR . $sep), 
        new TargetedString("book-by", $authors), 
        // new TargetedString("label-title", TableString::BOOK_TITLE . $sep), 
        // new TargetedString("book-title", $page), 
        new TargetedString("label-series", TableString::BOOK_SERIES . $sep), 
        new TargetedString("book-series", $series), 
        // new TargetedString("label-number", TableString::BOOK_NUMBER . $sep), 
        new TargetedString("book-number", $number)
    );

    $buttons = $dom->getElementById("contentButtons");

    $jump = $dom->createElement("a", ButtonString::BOOK_JUMP);
    $jump->setAttribute("class", "a_button");
    $jump->setAttribute("href", "../" . findPage("locations") . "/?jump=$location");
    $buttons->appendChild($jump);

    $move = $dom->createElement("a", ButtonString::BOOK_MOVE);
    $move->setAttribute("class", "a_button");
    $move->setAttribute("href", "../" . findPage("book_move") . "/?jump=$location");
    $buttons->appendChild($move);

    $edit = $dom->createElement("a", ButtonString::BOOK_EDIT);
    $edit->setAttribute("class", "a_button");
    $edit->setAttribute("href", "../" . findPage("edit_book"));
    $buttons->appendChild($edit);

    domSetTitle(pageToDisplayText($page));


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
