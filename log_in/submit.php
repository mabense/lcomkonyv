<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "auth.php");

haveSession();
$success = false;
$page = PAGE;

$pwd = fromPOST("pwd");

if (
    isset($pwd)
) {
    sqlConnect();
    $success = authLogin(/* */ $pwd /*/ LOCAL_PWD /* */);
    sqlDisconnect();
}

if ($success != false) {
    setUser($success);
    pushFeedbackToLog(FeedbackString::LOGIN_ACCEPTED);
    $page = /* * / "locations" /*/ PAGE /* */;
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::LOGIN_FAILED, true);
}
redirectTo(ROOT, $page);
