<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "search");

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

setMoveLocSql("");

canMoveFromHere();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", "./view.htm");
    domAppendTemplateTo("mainForm", "./search.htm");
    domAppendTemplateTo("contentContainer", "./result.htm");

    if (
        (fromPOST("title") != null) ||
        (fromPOST("series") != null) ||
        (fromPOST("writer") != null)
    ) {
        $title = fromPOST("title");
        $series = fromPOST("series");
        $writer = fromPOST("writer");

        global $dom;
        // $dom = new DOMDocument();
        $dom->getElementById("title")->setAttribute("value", $title);
        $dom->getElementById("series")->setAttribute("value", $series);
        $dom->getElementById("writer")->setAttribute("value", $writer);

        $tLocation = PLACE_TABLE;
        $tBook = BOOK_TABLE;
        $tWriter = WRITER_TABLE;
        $tWrote = BOOK_AUTHOR_TABLE;

        $bookConditions = "";
        $sqlTypes = "";
        $sqlParams = [];

        $titleAssoc = interpretInput($title, "`$tBook`.`title`");
        $sqlTypes .= $titleAssoc["types"];
        array_push($sqlParams, ...$titleAssoc["params"]);

        $seriesAssoc = interpretInput($series, "`$tBook`.`series`");
        $sqlTypes .= $seriesAssoc["types"];
        array_push($sqlParams, ...$seriesAssoc["params"]);

        $writerAssoc = interpretInput($writer, "`authors`");
        $sqlTypes .= $writerAssoc["types"];
        array_push($sqlParams, ...$writerAssoc["params"]);

        $bookConditions = $titleAssoc["conditions"] . " AND " . $seriesAssoc["conditions"];
        $writerConditions = $writerAssoc["conditions"];

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
                    (`$tBook` LEFT JOIN `$tWrote` ON `$tBook`.`id`=`$tWrote`.`book`) 
                    LEFT JOIN 
                    `$tWriter` ON `$tWriter`.`id`=`$tWrote`.`author`
            )
            WHERE $bookConditions GROUP BY `$tBook`.`id`
            HAVING $writerConditions
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
            /* */
            "book", /*/"", /* */
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

    domSetTitle(pageToDisplayText(PAGE));

    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();
