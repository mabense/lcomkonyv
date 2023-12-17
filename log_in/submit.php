<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$userName = false;
$page = PAGE;

$user = fromGET("user");
if(!isset($user)) {
    $user = DEV_USER;
}
$pwd = fromPOST("pwd");
if(!isset($pwd)) {
    $pwd = DEV_PWD;
}

if (
    isset($user)
    && isset($pwd)
) {
    sqlConnect();
    $userName = sqlLogin($user, $pwd);
    sqlDisconnect();
}

if ($userName != false) {
    setUser($userName);
    pushFeedbackToLog(FeedbackString::LOGIN_ACCEPTED);
    $page = /* * / "locations" /*/ PAGE /* */ ;
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::LOGIN_FAILED, true);
}
redirectTo(ROOT, $page);