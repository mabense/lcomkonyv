<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "delete_page");

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

$page = PAGE;
$location = getLocation();

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_result.htm");

    domAppendTemplateTo("contentContainer", "./view.htm");

    $tLocation = PLACE_TABLE;

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
                $placeName = $row["name"];
            }
        }
    }

    domSetString("message", FeedbackString::R_U_SURE_DELETE_LOCATION, StringTarget::TEXT_CONTENT);
    domSetString("message", $placeName, StringTarget::TEXT_CONTENT, "%place%");

    $locationPathString = sqlGetLocationPathString();

    $buttons = $dom->getElementById("contentButtons");

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("cancel"));
    $buttons->appendChild($exitLoc);

    $delLoc = $dom->createElement("a", ButtonString::LOCATION_DELETE);
    $delLoc->setAttribute("class", "a_button");
    $delLoc->setAttribute("style", "background-color: red;");
    $delLoc->setAttribute("href", "./delete.php");
    $buttons->appendChild($delLoc);


    domSetTitle(
        pageToDisplayText($page),
        $locationPathString
    );

    domPopFeedback();
}
sqlDisconnect();

global $dom;
echo $dom->saveHTML();
