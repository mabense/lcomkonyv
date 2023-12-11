<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "new_book");

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

$numOfAuthors = getNumberOfAuthors();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_form.htm");

    domAppendTemplateTo("mainForm", TEMPLATE_DIR . "edit_book.htm");
    // $dom = new DOMDocument();
    global $dom;
    // pushFeedbackToLog($numOfAuthors, true);
    for ($i = 1; $i <= $numOfAuthors; $i++) {
        domAppendTemplateTo("writerRows", TEMPLATE_DIR . "sql_select_author.htm");
        $dom->getElementById("%id%")->setAttribute("id", "writer" . $i);
    }

    $tAuthor = WRITER_TABLE;
    // domSetString("authorListHead", TableString::AUTHORS, StringTarget::TEXT_CONTENT);
    
    $full_name = "CONCAT(
        `$tAuthor`.`surname`, ', ', `$tAuthor`.`givenname`, 
        IF(`$tAuthor`.`clarification` = '', 
            '', 
            CONCAT(
                ' (', `$tAuthor`.`clarification`, ')'
            )
        )
    )";
    $fields = "`id`, $full_name AS 'name'";
    $sql = "SELECT $fields FROM $tAuthor ORDER BY `name` ASC";

    // Fill Writer Select
    // $dom = new DOMDocument();
    $i = 1;
    while ($writerSelect = $dom->getElementById("writer" . $i)) {
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
                $writerSelect->appendChild($opt);
            }
        }
        $i++;
    }

    $buttons = $dom->getElementById("contentButtons");

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("locations"));
    $buttons->appendChild($exitLoc);

    $lessA = $dom->createElement("a", ButtonString::BOOK_NEW_LESS_AUTHORS);
    $lessA->setAttribute("class", "a_button");
    $lessA->setAttribute("href", "../" . findPage("new_book_by_less"));
    $buttons->appendChild($lessA);

    $moreA = $dom->createElement("a", ButtonString::BOOK_NEW_MORE_AUTHORS);
    $moreA->setAttribute("class", "a_button");
    $moreA->setAttribute("href", "../" . findPage("new_book_by_more"));
    $buttons->appendChild($moreA);

    domSetStrings(
        new TargetedString("forWriter", FormString::BOOK_AUTHOR, StringTarget::TEXT_CONTENT),
        new TargetedString("forTitle", FormString::BOOK_TITLE, StringTarget::TEXT_CONTENT),
        new TargetedString("forSeries", FormString::BOOK_SERIES, StringTarget::TEXT_CONTENT),
        new TargetedString("ok", FormString::CREATE_SUBMIT, StringTarget::VALUE)
    );

    domSetTitle(pageToDisplayText(PAGE));

    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();
