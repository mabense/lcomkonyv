<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "book");

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

resetNumberOfAuthors();

$page = PAGE;
$book = getBook();
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
        sqlTableParams(
            $sql,
            $sqlTypes,
            $sqlParams,
            [
                "authors" => TableString::BOOK_AUTHOR,
                // "title" => TableString::BOOK_TITLE,
                "series" => TableString::BOOK_SERIES,
                "number" => "#"
            ],
            /* * / "book", /*/
            "", /* */
            [
                // "id"
            ],
            "bookList"
        );
    }

    $buttons = $dom->getElementById("contentButtons");

    $jump = $dom->createElement("a", ButtonString::BOOK_JUMP);
    $jump->setAttribute("class", "a_button");
    $jump->setAttribute("href", "../" . findPage("locations") . "/?jump=$location");
    $buttons->appendChild($jump);

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
