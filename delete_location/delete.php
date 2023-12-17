<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "delete_location");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_dom.php");
require_once(LIB_DIR . "sql_session.php");

haveSession();
$success = false;
$page = PAGE;

if (!auth(AuthLevel::USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

$tLocation = PLACE_TABLE;
$tBook = BOOK_TABLE;
// $tWriter = WRITER_TABLE;
// $tWrote = BOOK_AUTHOR_TABLE;

$locToDelete = popLocation(); // here
$locToGo = getLocation(); // parent location

$location = $locToDelete;

if (isset($location)) {
    $placeConditions = "`where`=?";
    $bookConditions = "`location`=?";
    $sqlTypes = "i";
    $sqlParams = [
        $location
    ];
} else {
    pushFeedbackToLog(ErrorString::LOCATION_DELETE_FAILED, true);
    redirectToPreviousPage();
    exit;
}


sqlConnect();


$locsToMove = sqlPrepareBindExecute(
    "SELECT `id` FROM $tLocation WHERE $placeConditions",
    $sqlTypes,
    $sqlParams,
    __FUNCTION__
)->get_result()->fetch_all(MYSQLI_ASSOC);

$booksToMove = sqlPrepareBindExecute(
    "SELECT `id` FROM $tBook WHERE $bookConditions",
    $sqlTypes,
    $sqlParams,
    __FUNCTION__
)->get_result()->fetch_all(MYSQLI_ASSOC);


$locChanges = "`where`=?";
$locConds = [];
$locTypes = "i";
$locParams = [
    $locToGo
];
foreach ($locsToMove as $key) {
    $keyConds = [];
    foreach ($key as $name => $value) {
        array_push($keyConds, "`$name`=?");
        $type = gettype($value);
        switch ($type) {
            case "boolean":
            case "integer":
                $locTypes .= "i";
                break;
            case "double":
                $locTypes .= "d";
                break;
            case "string":
                $locTypes .= "s";
                break;
            default:
                echo "No SQL type for \"$type\" PHP type.";
                exit;
        }
        array_push($locParams, $value);
    }
    array_push($locConds, "(" . implode(" AND ", $keyConds) . ")");
}
$locConds = implode(" OR ", $locConds);

$bookChanges = "`location`=?";
$bookConds = [];
$bookTypes = "i";
$bookParams = [
    $locToGo
];
foreach ($booksToMove as $key) {
    $keyConds = [];
    foreach ($key as $name => $value) {
        array_push($keyConds, "`$name`=?");
        $type = gettype($value);
        switch ($type) {
            case "boolean":
            case "integer":
                $bookTypes .= "i";
                break;
            case "double":
                $bookTypes .= "d";
                break;
            case "string":
                $bookTypes .= "s";
                break;
            default:
                echo "No SQL type for \"$type\" PHP type.";
                exit;
        }
        array_push($bookParams, $value);
    }
    array_push($bookConds, "(" . implode(" AND ", $keyConds) . ")");
}
$bookConds = implode(" OR ", $bookConds);

$locSQL = "UPDATE $tLocation SET $locChanges WHERE $locConds";
$bookSQL = "UPDATE $tBook SET $bookChanges WHERE $bookConds";
if (sizeof($locsToMove) > 0) {
    sqlPrepareBindExecute(
        $locSQL,
        $locTypes,
        $locParams,
        __FUNCTION__
    );
}
if (sizeof($booksToMove) > 0) {
    sqlPrepareBindExecute(
        $bookSQL,
        $bookTypes,
        $bookParams,
        __FUNCTION__
    );
}

if(!sqlPrepareExecute(
    "DELETE FROM $tLocation WHERE `id`=$locToDelete",
    __FUNCTION__
)) {
    pushFeedbackToLog(ErrorString::LOCATION_DELETE_FAILED, true);
    redirectToPreviousPage();
    exit;
}


sqlDisconnect();

// redirectTo(ROOT, "exit_location");
redirectToPreviousPage();
