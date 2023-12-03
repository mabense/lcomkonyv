<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$success = false;
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
    $success = sqlLogin($user, $pwd);
    sqlDisconnect();
}

if ($success != false) {
    setUser($user);
    pushFeedbackToLog(FeedbackString::LOGIN_ACCEPTED);
    $page = /* * / "locations" /*/ PAGE /* */ ;
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::LOGIN_FAILED, true);
}
redirectTo(ROOT, $page);