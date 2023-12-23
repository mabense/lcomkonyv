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

handleLetter();

$page = PAGE;
$location = getLocation();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", "./view.htm");
    domAppendTemplateTo("mainForm", "./search.htm");
    domAppendTemplateTo("contentContainer", "./result.htm");

    domSetString("ok", FormString::SEARCH_SUBMIT, StringTarget::VALUE);

    $tAuthor = WRITER_TABLE;

    $letters = [];
    $letterFields = "SUBSTRING(`surname`, 1, 1)";
    $lettersStmt = sqlPrepareExecute(
        "SELECT $letterFields AS 'letter' FROM $tAuthor GROUP BY `letter`",
        __FUNCTION__
    );
    // $lettersStmt = new mysqli_stmt();
    if ($result = $lettersStmt->get_result()) {
        while ($row = $result->fetch_assoc()) {
            array_push($letters, $row["letter"]);
        }
    }

    global $dom;
    // $dom = new DOMDocument();
    $letterSelect = $dom->getElementById("letter");
    $letterSelect->setAttribute("class", "select-js select-js-large");
    $route = "./?letter=";
    $letterSelect->setAttribute("onchange", "window.location='$route' + this.value;");
    foreach ($letters as $letter) {
        $opt = $dom->createElement("option", $letter);
        $opt->setAttribute("id", "letter-" . $letter);
        domSetString("letter-" . $letter, $letter);
        $letterSelect->appendChild($opt);
    }
    if (fromSESSION("letter") == null) {
        $letter = "A";
    } else {
        $letter = fromSESSION("letter");
    }
    $selectedLetter = $dom->getElementById("letter-$letter");
    $selectedLetter->setAttribute("selected", "selected");
    

    $conditions = "`author` LIKE ?";
    $types = "s";
    $params = [
        "$letter%"
    ];

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

    $sql = "SELECT $fields FROM $tAuthor HAVING $conditions ORDER BY `author` ASC";

    sqlTableParams(
        $sql,
        $types,
        $params,
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
