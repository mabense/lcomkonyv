<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "auth.php");

haveSession();
$success = false;
$page = PAGE;

$sur = fromPOST("sur");
$given = fromPOST("given");
$clar = fromPOST("clar");

$id = 0;

if (
    (!isset($sur) or $sur === "") and
    (!isset($given) or $given === "") and
    (!isset($clar) or $clar === "")
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

    // $success = new mysqli_stmt();
    $id = ($success != false) ? $success->insert_id : 0;

    sqlDisconnect();
}

if ($success != false) {
    pushFeedbackToLog(FeedbackString::CREATE_SUCCESS);
    setAuthor($id);
    $page = "authors";
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::CREATE_FAIL, true);
}
redirectToPreviousPage();
