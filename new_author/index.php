<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "new_author");

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

if (newDOMDocument(BASE_TEMPLATE)) {

    domAddStyle("../_styles/query_page.css");

    domMakeToolbarLoggedIn();

    domAppendTemplateTo("content", TEMPLATE_DIR . "sql_form.htm");

    domAppendTemplateTo("mainForm", "./view.htm");

    $buttons = $dom->getElementById("contentButtons");

    $exitLoc = $dom->createElement("a", ButtonString::CANCEL);
    $exitLoc->setAttribute("class", "a_button");
    $exitLoc->setAttribute("href", "../" . findPage("authors"));
    $buttons->appendChild($exitLoc);

    domSetStrings(
        new TargetedString("forSur", FormString::WRITER_SURNAME, StringTarget::TEXT_CONTENT),
        new TargetedString("forGiven", FormString::WRITER_GIVENNAME, StringTarget::TEXT_CONTENT),
        new TargetedString("forClar", FormString::WRITER_CLERIFICATION, StringTarget::TEXT_CONTENT),
        new TargetedString("ok", FormString::CREATE_SUBMIT, StringTarget::VALUE)
    );

    domSetTitle(pageToDisplayText(PAGE));

    domPopFeedback();
}

sqlDisconnect();
global $dom;
echo $dom->saveHTML();
