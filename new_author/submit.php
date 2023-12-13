<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$success = false;
$page = PAGE;

$user = fromGET("user");
if (!isset($user)) {
    $user = DEV_USER;
}
$sur = fromPOST("sur");
$given = fromPOST("given");
$clar = fromPOST("clar");

if (
    !isset($sur) or $sur === "" or
    !isset($given) or $given === ""
) {
    pushFeedbackToLog(ErrorString::NEW_AUTHOR_NO_NAME, true);
} else {
    $tAuthor = WRITER_TABLE;
    $fields = "`surname`, `givenname`, `clarification`";
    if (!isset($clar)) {
        $clar = "";
    }

    sqlConnect();

    $nameTaken = sqlPrepareBindExecute(
        "SELECT `id` FROM $tAuthor WHERE ($fields) = (?, ?, ?)",
        "sss",
        [
            $sur,
            $given,
            $clar
        ],
        __FUNCTION__
    )->get_result()->num_rows > 0 ? true : false;;

    if ($nameTaken) {
        if ($clar === "") {
            pushFeedbackToLog(ErrorString::NEW_AUTHOR_CLARIFY, true);
        } else {
            pushFeedbackToLog(ErrorString::NEW_AUTHOR_RECLARIFY, true);
        }
    } else {
        $success = sqlPrepareBindExecute(
            "INSERT INTO $tAuthor ($fields) VALUES (?, ?, ?)",
            "sss",
            [
                $sur,
                $given,
                $clar
            ],
            __FUNCTION__
        );
    }

    sqlDisconnect();
}

if ($success != false) {
    setUser($user);
    pushFeedbackToLog(FeedbackString::CREATE_SUCCESS);
    $page = "authors";
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::CREATE_FAIL, true);
}
redirectToPreviousPage();
