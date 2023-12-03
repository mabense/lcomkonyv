<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "home");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

if(!auth(false, DEV_USER)){
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

handleAction();

if (newDOMDocument(BASE_TEMPLATE)) {

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", "./view.htm");

    domSetTitle(pageToDisplayText(PAGE), DisplayString::LOGIN_GREETING);

    domPopFeedback();
}

global $dom;
echo $dom->saveHTML();
