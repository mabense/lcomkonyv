<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "authors");

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

pushPreviousPage();

$page = PAGE;
$location = getLocation();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_result.htm");

    domAppendTemplateTo("contentContainer", "./view.htm");

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
    $fields = "`id`, $full_name AS 'author'";

    $sql = "SELECT $fields FROM $tAuthor ORDER BY `author` ASC";

    sqlTable(
        $sql,
        [
            // "id" => "id", 
            "author" => TableString::AUTHOR_FULLNAME
            // "surname" => "surname", 
            // "givenname" => "givenname", 
            // "clarification" => "clarification"
        ],
        "books_by_author",
        [
            "id"
        ],
        "authorList"
    );

    $buttons = $dom->getElementById("contentButtons");

    $new = $dom->createElement("a", ButtonString::AUTHOR_NEW);
    $new->setAttribute("class", "a_button");
    $new->setAttribute("href", "../" . findPage("new_author"));
    $buttons->appendChild($new);

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
