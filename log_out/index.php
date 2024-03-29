<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "log_out");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "auth.php");

haveSession();

if(!auth(AuthLevel::USER)){
    redirectTo(ROOT, "log_in");
}

$user = getUserName();
$page = "home";

if ($user != false) {
    sqlConnect();
    $out = authLogout();
    sqlDisconnect();
    if ($out) {
        pushFeedbackToLog(FeedbackString::LOGOUT_SUCCESS);
        $page = "log_in";
    } elseif (!isThereFeedback()) {
        pushFeedbackToLog(ErrorString::LOGOUT_FAILED, true);
    }
}
redirectTo(ROOT, $page);