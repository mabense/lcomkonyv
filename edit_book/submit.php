<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "auth.php");

haveSession();
$success = false;
$page = PAGE;

$book = getBook();
$loc = getLocation();
$author = fromPOST("writer");
$title = fromPOST("title");
$series = fromPOST("series");
$number = fromPOST("number");

if (!isset($loc)) {
    pushFeedbackToLog(ErrorString::NO_LOCATION, true);
    // } else if (!isset($author) or $author === "") {
    //     pushFeedbackToLog(ErrorString::NEW_BOOK_NO_AUTHOR, true);
} else {
    $tBook = BOOK_TABLE;
    $tAuthor = BOOK_AUTHOR_TABLE;

    $bookConditions = "`id`=?";
    $authorConditions = "`book`=?";
    $inputsSet = false;

    $changeArr = [];
    $sqlTypes = "";
    $sqlParams = [];

    if (isset($book) && $book != "") {
        $sqlTypes = "i";
        $sqlParams = [
            $book
        ];

        $stmt = sqlPrepareBindExecute(
            "SELECT `title`, `location` FROM $tBook WHERE $bookConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_assoc()) {
                $page = $row["title"];
                $location = $row["location"];
            }
        }
    }

    $changeArr = [];
    $sqlTypes = "";
    $sqlParams = [];

    if (isset($title)) {
        array_push($changeArr, "`title`=?");
        $sqlTypes .= "s";
        array_push($sqlParams, $title);
        if ($title != "") {
            $inputsSet = true;
        }
    }
    if (isset($series)) {
        array_push($changeArr, "`series`=?");
        $sqlTypes .= "s";
        array_push($sqlParams, $series);
        if ($series != "") {
            $inputsSet = true;
        }
    }
    if (isset($number)) {
        if((int) $number == 0) {
            array_push($changeArr, "`number_in_series`=NULL");
        }
        else{
            array_push($changeArr, "`number_in_series`=?");
            $sqlTypes .= "i";
            array_push($sqlParams, (int) $number);
            if ($number > 0) {
                $inputsSet = true;
            }
        }
    }

    if (is_array($author)) {
        foreach ($author as $key => $authorId) {
            if (!is_nan((float) $authorId) && $authorId > 0) {
                $inputsSet = true;
            }
        }
    }

    $changes = implode(", ", $changeArr);
    $sqlTypes .= "i";
    array_push($sqlParams, $book);

    sqlConnect();

    if ($inputsSet) {
        $success = sqlPrepareBindExecute(
            "UPDATE $tBook SET $changes WHERE $bookConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );

        $changeArr = [];
        $sqlTypes = "";
        $sqlParams = [];

        $sqlTypes = "i";
        array_push($sqlParams, $book);

        $deleteFail = false;
        $authorErrors = false;
        if ($success) {
            $deletion = sqlPrepareBindExecute(
                "DELETE FROM $tAuthor WHERE $authorConditions",
                $sqlTypes,
                $sqlParams,
                __FUNCTION__
            );
            if (!$deletion) {
                $deleteFail = true;
            }
            if (is_array($author) && (sizeof($author) > 0)) {
                $fields = "`book`, `author`";
                foreach ($author as $key => $authorId) {
                    if (!is_nan((float) $authorId) && $authorId > 0) {
                        $authorAdded = sqlPrepareBindExecute(
                            "INSERT INTO $tAuthor ($fields) VALUES (?, ?)",
                            "ii",
                            [
                                $book,
                                $authorId
                            ],
                            __FUNCTION__
                        );
                        if (!$authorAdded) {
                            $authorErrors = true;
                        }
                    }
                }
            }
            if ($deleteFail || $authorErrors) {
                $success = false;
            }
        }
        $page = "book";
    } else {
        $sqlTypes = "i";
        $sqlParams = [
            $book
        ];

        $deletion = sqlPrepareBindExecute(
            "DELETE FROM $tBook WHERE $bookConditions",
            $sqlTypes,
            $sqlParams,
            __FUNCTION__
        );
        if ($deletion) {
            $success = true;
        }
        $page = "locations";
    }

    sqlDisconnect();
}

if ($success != false) {
    pushFeedbackToLog(FeedbackString::EDIT_SUCCESS);
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::EDIT_FAIL, true);
}
redirectTo(ROOT, $page);
