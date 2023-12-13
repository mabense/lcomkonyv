<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$user = false;
$page = PAGE;
$success = false;

$user = fromPOST("user");
$pwd = fromPOST("pwd");
$repwd = fromPOST("repwd");

if (
    isset($user)
    && ($user == DEV_USER)
    && isset($pwd)
    && isset($repwd)
) {
    $tUser = USER_TABLE;
    $nameFree = false;
    sqlConnect();
    if (!passwordStrong($pwd)) {
        pushFeedbackToLog(ErrorString::PASSWORD_WEAK, true);
        redirectTo(ROOT, $page);
    }
    if (!passwordCompare($pwd, $repwd)) {
        pushFeedbackToLog(ErrorString::PASSWORD_NOT_MATCH, true);
        redirectTo(ROOT, $page);
    }
    $stmt = sqlPrepareBindExecute(
        "SELECT `name` FROM $tUser WHERE `name`=?",
        "s",
        [
            $user
        ],
        __FUNCTION__
    );
    $pass = password_hash($pwd, PASSWORD_BCRYPT);

    // echo $pass;
    // exit;

    if ($result = $stmt->get_result()) {
        if ($row = $result->fetch_assoc()) {
            $success = sqlPrepareBindExecute(
                "UPDATE $tUser SET `password`=? WHERE `name`=?",
                "ss",
                [
                    $pass,
                    $user
                ],
                __FUNCTION__
            );
        } else {
            $nameFree = true;
        }
    } else {
        $nameFree = true;
    }
    if ($nameFree) {
        $success = sqlSignup($user, $pwd, $repwd, false);
    }
    sqlDisconnect();
}

if ($success != false) {
    pushFeedbackToLog("Signed up successfully.");
} elseif (!isThereFeedback()) {
    pushFeedbackToLog("Failed to sign up.", true);
}
redirectTo(ROOT, $page);
