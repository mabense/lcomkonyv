<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$user = false;
$page = PAGE;

$user = fromPOST("user");
$pwd = fromPOST("pwd");
$repwd = fromPOST("repwd");

if (
    isset($user)
    && isset($pwd)
    && isset($repwd)
) {
    sqlConnect();
    $result = sqlSignup($user, $pwd, $repwd, false);
    sqlDisconnect();
}

if ($result != false) {
    pushFeedbackToLog("Signed up successfully.");
} elseif(!isThereFeedback()) {
    pushFeedbackToLog("Failed to sign up.", true);
}
redirectTo(ROOT, $page);