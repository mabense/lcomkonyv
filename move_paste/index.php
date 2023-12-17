<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "move_paste");

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

$where_to = getLocation();
$locs = moveLocsGetAll();
$books = moveBooksGetAll();

$locChanges = "`where`=?";
$locConds = [];
$locTypes = "i";
$locParams = [
    $where_to
];
foreach ($locs as $key) {
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
    $where_to
];
foreach ($books as $key) {
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

sqlConnect();
if (sizeof($locs) > 0) {
    sqlPrepareBindExecute(
        $locSQL,
        $locTypes,
        $locParams,
        __FUNCTION__
    );
}
if (sizeof($books) > 0) {
    sqlPrepareBindExecute(
        $bookSQL,
        $bookTypes,
        $bookParams,
        __FUNCTION__
    );
}
sqlDisconnect();


$debug = /* */ false /*/ true /* */;
if ($debug) {
    echo "-------------------------------------------------------------<br />";
    echo '$locSQL = ';
    if (sizeof($locs) > 0) {
        echo $locSQL . "<br />";
    }
    echo '$bookSQL = ';
    if (sizeof($books) > 0) {
        echo $bookSQL . "<br />";
    }

    exit;
}

resetMoveState();

redirectToPreviousPage();
