<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "log_in");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

if(!auth(AuthLevel::GUEST)){
    redirectTo(ROOT, "home");
}

handleMissingPage();

handleAction();

if (newDOMDocument(BASE_TEMPLATE)) {

    domMakeToolbar([
        "sign_up"
    ]);

    domAppendTemplateTo("content", "./view.htm");

    domSetStrings(
        new TargetedString("forUser", FormString::USERNAME, StringTarget::TEXT_CONTENT), 
        new TargetedString("forPassword", FormString::PASSWORD, StringTarget::TEXT_CONTENT), 
        new TargetedString("ok", FormString::LOGIN_SUBMIT, StringTarget::VALUE)
    );

    domSetTitle(pageToDisplayText(PAGE));
    
    domPopFeedback();
}

global $dom;
echo $dom->saveHTML();
