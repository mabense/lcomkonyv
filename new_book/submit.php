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
$loc = getLocation();
$author = fromPOST("writer");
$title = fromPOST("title");
$series = fromPOST("series");
$number = fromPOST("number");

if (!isset($loc)) {
    pushFeedbackToLog(ErrorString::NO_LOCATION, true);
    // } else if (!isset($author) or $author === "") {
    //     pushFeedbackToLog(ErrorString::NEW_BOOK_NO_AUTHOR, true);
} else if (!isset($title) or $title === "") {
    pushFeedbackToLog(ErrorString::NEW_BOOK_NO_TITLE, true);
} else {
    $tBook = BOOK_TABLE;
    $tAuthor = BOOK_AUTHOR_TABLE;
    $fields = "`title`, `location`";
    $sqlVals = "?, ?";
    $sqlTypes = "si";
    $sqlParams = [
        $title,
        $loc
    ];

    if (isset($series) && $series != "") {
        $fields .= ", `series`";
        $sqlVals .= ", ?";
        $sqlTypes .= "s";
        array_push($sqlParams, $series);

        if (isset($number) && $number != "") {
            $fields .= ", `number_in_series`";
            $sqlVals .= ", ?";
            $sqlTypes .= "i";
            array_push($sqlParams, $number);
        }
    }

    sqlConnect();
    $success = sqlPrepareBindExecute(
        "INSERT INTO $tBook ($fields) VALUES ($sqlVals)",
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    );

    // $success = (new mysqli())->prepare("");

    // if ($bResult = $success->get_result()) {
    //     if ($book = $bResult->fetch_assoc()) {
    if ($book = $success->insert_id) {
        if (is_array($author) && (sizeof($author) > 0)) {
            $fields = "`book`, `author`";
            foreach ($author as $key => $authorId) {
                if(!is_nan($authorId) && $authorId > 0) {
                    $authorAdded = sqlPrepareBindExecute(
                        "INSERT INTO $tAuthor ($fields) VALUES (?, ?)",
                        "ii",
                        [
                            $book,
                            $authorId
                        ],
                        __FUNCTION__
                    );
                }
            }
        }
    }
    sqlDisconnect();
}

if ($success != false) {
    setUser($user);
    pushFeedbackToLog(FeedbackString::CREATE_SUCCESS);
    $page = "locations";
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::CREATE_FAIL, true);
}
redirectTo(ROOT, $page);
