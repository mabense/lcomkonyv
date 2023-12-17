<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "sign_up");

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
        "log_in"
    ]);

    domAppendTemplateTo("content", "./view.htm");

    domSetStrings(
        new TargetedString("forUser", FormString::USERNAME, StringTarget::TEXT_CONTENT), 
        new TargetedString("forPassword", FormString::PASSWORD, StringTarget::TEXT_CONTENT), 
        new TargetedString("forRepass", FormString::REPASS, StringTarget::TEXT_CONTENT), 
        new TargetedString("ok", FormString::SIGNUP_SUBMIT, StringTarget::VALUE)
    );


    domSetTitle(toDisplayText(PAGE));
    
    domPopFeedback();
}

global $dom;
echo $dom->saveHTML();
