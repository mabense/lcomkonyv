<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "edit_location");

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

$page = PAGE;
$loc = getLocation();

handleAction();

if (newDOMDocument(BASE_TEMPLATE)) {
    $tLocation = PLACE_TABLE;

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_form.htm");

    domAppendTemplateTo("mainForm", "./view.htm");

    if (isset($loc)) {
        $stmt = sqlPrepareBindExecute(
            "SELECT `name` FROM $tLocation WHERE `id`=?",
            "i",
            [
                $loc
            ],
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $page = $row["name"] . SubString::RENAMING;
            }
        }
    }

    $buttons = $dom->getElementById("contentButtons");

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("locations"));
    $buttons->appendChild($exitLoc);

    domSetStrings(
        new TargetedString("forName", FormString::PLACE_NAME, StringTarget::TEXT_CONTENT), 
        new TargetedString("ok", FormString::EDIT_SUBMIT, StringTarget::VALUE)
    );

    domSetTitle(pageToDisplayText($page));

    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();
