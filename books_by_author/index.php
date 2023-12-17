<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "books_by_author");

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

setMoveLocSql("");

canMoveFromHere();
pushPreviousPage();

$page = PAGE;
$authorID = getAuthor();
$authorFullName = "";

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_result.htm");

    domAppendTemplateTo("contentContainer", "./view.htm");

    if ($authorID != null) {

        global $dom;
        // $dom = new DOMDocument();

        $tLocation = PLACE_TABLE;
        $tBook = BOOK_TABLE;
        $tWriter = WRITER_TABLE;
        $tWrote = BOOK_AUTHOR_TABLE;

        $bookConditions = "`$tWrote`.`author`=?";
        $writerConditions = "TRUE";
        $sqlTypes = "i";
        $sqlParams = [
            $authorID
        ];

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
            
        $stmt = sqlPrepareBindExecute(
            "SELECT $full_name AS 'fullname' FROM $tWriter WHERE `id`=?", 
            "i", 
            [
                $authorID
            ], 
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $authorFullName = $row["fullname"];
                $page = $authorFullName;
            }
        }

        $bookConditions = "TRUE";
        $writerConditions = "`authors` LIKE ?";
        $sqlTypes = "s";
        $sqlParams = [
            "%$authorFullName%"
        ];

        
        $authors = "GROUP_CONCAT(DISTINCT $full_name SEPARATOR ' - ')";
        $fields = "`$tBook`.`id`, `$tBook`.`title`, 
            IF(ISNULL($authors), '', $authors) AS 'authors', 
            IF(ISNULL(`series`), '', `series`) AS 'series', 
            IF(ISNULL(`number_in_series`), '', `number_in_series`) AS 'number'";

        $sql = "SELECT $fields 
            FROM (
                    (`$tBook` LEFT JOIN `$tWrote` ON `$tBook`.`id`=`$tWrote`.`book`) 
                    LEFT JOIN 
                    `$tWriter` ON `$tWriter`.`id`=`$tWrote`.`author`
            )
            WHERE $bookConditions GROUP BY `$tBook`.`id`
            HAVING $writerConditions
            ORDER BY `authors`, `series`, `number`, `$tBook`.`title`";
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
        domDeleteElementById("bookList");
        setMoveBookSql("");
    }

    domDeleteElementById("bookListHead");

    domSetStrings(
        new TargetedString("forWriter", FormString::BOOK_AUTHOR, StringTarget::TEXT_CONTENT),
        new TargetedString("forTitle", FormString::BOOK_TITLE, StringTarget::TEXT_CONTENT),
        new TargetedString("forSeries", FormString::BOOK_SERIES, StringTarget::TEXT_CONTENT),
        new TargetedString("ok", FormString::SEARCH_SUBMIT, StringTarget::VALUE)
    );

    $buttons = $dom->getElementById("contentButtons");

    // $dom = new DOMDocument();

    if (getMoveState() == MoveState::SELECTED) {
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

    domSetTitle(pageToDisplayText($page));

    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();