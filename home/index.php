<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "home");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

if(!auth(AuthLevel::USER)){
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

handleAction();

pushPreviousPage("search");

if (newDOMDocument(BASE_TEMPLATE)) {

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", "./view.htm");

    domSetTitle(pageToDisplayText(PAGE), DisplayString::LOGIN_GREETING);

    domPopFeedback();
}

global $dom;
echo $dom->saveHTML();
