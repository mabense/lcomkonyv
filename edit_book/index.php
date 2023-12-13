<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "edit_book");

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

$page = PAGE;
$book = getBook();
$location = getLocation();
$writers = [];
$title = "";
$series = "";
$number = 0;

if (newDOMDocument(BASE_TEMPLATE)) {
    global $dom;

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_form.htm");

    domAppendTemplateTo("mainForm", TEMPLATE_DIR . "edit_book.htm");

    $tLocation = PLACE_TABLE;
    $tBook = BOOK_TABLE;
    $tWriter = WRITER_TABLE;
    $tWrote = BOOK_AUTHOR_TABLE;

    $authorConditions = "TRUE";
    $bookConditions = "TRUE";
    $sqlTypes = "";
    $sqlParams = [];

    if (isset($book)) {
        $authorConditions = "`book`=?";
        $bookConditions = "`$tBook`.`id`=?";

        $sqlTypes = "i";
        $sqlParams = [
            $book
        ];

        $stmt = sqlPrepareBindExecute(
            "SELECT `title`, `location`, `series`, `number_in_series` FROM $tBook WHERE $bookConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $page = $row["title"];
                $title = $row["title"];
                $location = $row["location"];
                $series = $row["series"];
                $number = $row["number_in_series"];
            }
        }
        
        $sqlTypes = "i";
        $sqlParams = [
            $book
        ];

        $byWho = sqlPrepareBindExecute(
            "SELECT `author` FROM $tWrote WHERE $authorConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($result = $byWho->get_result()) {
            while ($row = $result->fetch_assoc()) {
                array_push($writers, $row["author"]);
            }
        }
    }

    $numOfAuthors = setNumberOfAuthors(max(getNumberOfAuthors(), sizeof($writers)));

    // $dom = new DOMDocument();
    // pushFeedbackToLog($numOfAuthors, true);
    for ($i = 1; $i <= $numOfAuthors; $i++) {
        domAppendTemplateTo("writerRows", TEMPLATE_DIR . "sql_select_author.htm");
        // $dom->getElementById("%id%")->setAttribute("id", "writer" . $i);
        $substr = "%%";
        domSetStrings(
            new TargetedString("wSelect" . $substr, $i, StringTarget::ID, $substr),
            new TargetedString("wNew" . $substr, $i, StringTarget::ID, $substr),
            new TargetedString("forNew" . $substr, $i, StringTarget::ID, $substr)
        );
        domSetStrings(
            new TargetedString("forNew" . $i, FormString::BOOK_AUTHOR_NEW, StringTarget::TEXT_CONTENT),
            new TargetedString("forSur" . $i, FormString::WRITER_SURNAME, StringTarget::TEXT_CONTENT),
            new TargetedString("forGiv" . $i, FormString::WRITER_GIVENNAME, StringTarget::TEXT_CONTENT),
            new TargetedString("forClar" . $i, FormString::WRITER_CLERIFICATION, StringTarget::TEXT_CONTENT)
        );
        domSetStrings(
            new TargetedString("wSelect" . $i, $i, StringTarget::NAME, $substr),
            new TargetedString("wNew" . $i, $i, StringTarget::NAME, $substr),
            new TargetedString("forNew" . $i, $i, StringTarget::FOR, $substr)
        );
    }
    // domSetString("authorListHead", TableString::AUTHORS, StringTarget::TEXT_CONTENT);

    $full_name = "CONCAT(
        `$tWriter`.`surname`, ', ', `$tWriter`.`givenname`, 
        IF(`$tWriter`.`clarification` = '', 
            '', 
            CONCAT(
                ' (', `$tWriter`.`clarification`, ')'
            )
        )
    )";
    $fields = "`id`, $full_name AS 'name'";
    $sql = "SELECT $fields FROM $tWriter ORDER BY `name` ASC";

    // Fill Writer Select
    $i = 1;
    $selected = 0;
    while ($writerSelect = $dom->getElementById("wSelect" . $i)) {
        $stmt = sqlPrepareExecute(
            $sql,
            __FUNCTION__
        );
        $writerResult = $stmt->get_result();
        if (!$writerResult) {
            pushFeedbackToLog(ErrorString::NEW_BOOK_NO_AUTHORS, true);
        } else {
            while ($writer = $writerResult->fetch_assoc()) {
                $writer_id = $writer["id"];
                $writer_name = $writer["name"];
                $opt = $dom->createElement("option");
                $opt->setAttribute("value", $writer_id);
                $opt->textContent = $writer_name;
                if (
                    ($selected < sizeof($writers))
                    && ($writers[$selected] == $writer_id)
                ) {
                    $opt->setAttribute("selected", "selected");
                }
                $writerSelect->appendChild($opt);
            }
        }
        $i++;
        $selected++;
    }

    $buttons = $dom->getElementById("contentButtons");

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("cancel"));
    $buttons->appendChild($exitLoc);

    $lessA = $dom->createElement("a", ButtonString::BOOK_NEW_LESS_AUTHORS);
    $lessA->setAttribute("class", "a_button");
    $lessA->setAttribute("href", "../" . findPage("book_author_less"));
    $buttons->appendChild($lessA);

    $moreA = $dom->createElement("a", ButtonString::BOOK_NEW_MORE_AUTHORS);
    $moreA->setAttribute("class", "a_button");
    $moreA->setAttribute("href", "../" . findPage("book_author_more"));
    $buttons->appendChild($moreA);

    domSetStrings(
        new TargetedString("forWriter", FormString::BOOK_AUTHOR, StringTarget::TEXT_CONTENT),
        new TargetedString("forTitle", FormString::BOOK_TITLE, StringTarget::TEXT_CONTENT),
        new TargetedString("forSeries", FormString::BOOK_SERIES, StringTarget::TEXT_CONTENT),
        new TargetedString("ok", FormString::EDIT_SUBMIT, StringTarget::VALUE)
    );

    domSetStrings(
        new TargetedString("title", $title, StringTarget::VALUE),
        new TargetedString("series", $series, StringTarget::VALUE)
    );
    $numInput = $dom->getElementById("number")->setAttribute("value", "$number");

    domSetTitle(pageToDisplayText($page));

    domPopFeedback();
}

sqlDisconnect();
echo $dom->saveHTML();
