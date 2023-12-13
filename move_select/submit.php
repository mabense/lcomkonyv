<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$success = false;
$page = PAGE;

setMoveState(MoveState::SELECTED);

$user = fromGET("user");
if (!isset($user)) {
    $user = DEV_USER;
}

$tLocation = PLACE_TABLE;
$tBook = BOOK_TABLE;
// $tWriter = WRITER_TABLE;
// $tWrote = BOOK_AUTHOR_TABLE;

$selectedLocs = fromPOST("placeList");
$selectedBooks = fromPOST("bookList");

$sessLocs = getTableAllKeys("placeList");
$sessBooks = getTableAllKeys("bookList");

echo "place checkboxes: ";
echo var_dump($selectedLocs) . "<br />";
// place checkboxes: array(1) { [0]=> string(2) "on" }
echo "book checkboxes: ";
echo var_dump($selectedBooks) . "<br />";
// book checkboxes: array(1) { [1]=> string(2) "on" }

echo "-------------------------------------------------------------<br />";

echo "place keys: ";
echo var_dump($sessLocs) . "<br />";
// place keys: array(1) { [0]=> array(1) { ["id"]=> int(72) } }
echo "book keys: ";
echo var_dump($sessBooks) . "<br />";
// book keys: array(2) { [0]=> array(1) { ["id"]=> int(16) } [1]=> array(1) { ["id"]=> int(19) } }

echo "-------------------------------------------------------------<br />";

$locFields = [];
$noFields = true;
$locConds = [];
$locTypes = "";
$locParams = [];
foreach ($selectedLocs as $row => $on) {
    $locKey = $sessLocs[$row];
    foreach ($locKey as $attr => $value) {
        if ($noFields) {
            array_push($locFields, $attr);
        }
        array_push($locConds, "`$attr`=?");
        switch (gettype($value)) {
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
                echo "No SQL type for the \"" . gettype($value) . "\" PHP type.";
        }
        array_push($locParams, $value);
    }
    $noFields = false;
}
$locFields = implode(", ", $locFields);
$locConds = implode(", ", $locConds);

echo "place fields: ";
echo $locFields . "<br />";

echo "place conditions: ";
echo $locConds . "<br />";
// 
echo "place types: ";
echo $locTypes . "<br />";
// 
echo "place parameters: ";
echo var_dump($locParams) . "<br />";
// 

echo "-------------------------------------------------------------<br />";

$bookFields = [];
$noFields = true;
$bookConds = [];
$bookTypes = "";
$bookParams = [];
foreach ($selectedBooks as $row => $on) {
    $bookKey = $sessBooks[$row];
    foreach ($bookKey as $attr => $value) {
        if ($noFields) {
            array_push($bookFields, $attr);
        }
        array_push($bookConds, "`$attr`=?");
        switch (gettype($value)) {
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
                echo "No SQL type for the \"" . gettype($value) . "\" PHP type.";
        }
        array_push($bookParams, $value);
    }
    $noFields = false;
}
$bookFields = implode(", ", $bookFields);
$bookConds = implode(", ", $bookConds);

echo "book fields: ";
echo $bookFields . "<br />";

echo "book conditions: ";
echo $bookConds . "<br />";
// 
echo "book types: ";
echo $bookTypes . "<br />";
// 
echo "book parameters: ";
echo var_dump($bookParams) . "<br />";
// 

echo "-------------------------------------------------------------<br />";

echo $locSQL = "UPDATE $tLocation SET $locChanges WHERE $locConds";
echo $bookSQL = "UPDATE $tBook SET $bookChanges WHERE $bookConds";

exit;

// sqlConnect();

// $nameTaken = sqlPrepareBindExecute(
//     "SELECT `id` FROM $tAuthor WHERE ($fields) = (?, ?, ?)",
//     "sss",
//     [
//         $sur,
//         $given,
//         $clar
//     ],
//     __FUNCTION__
// )->get_result()->num_rows > 0 ? true : false;;

// if ($nameTaken) {
//     if ($clar === "") {
//         pushFeedbackToLog(ErrorString::NEW_AUTHOR_CLARIFY, true);
//     } else {
//         pushFeedbackToLog(ErrorString::NEW_AUTHOR_RECLARIFY, true);
//     }
// } else {
//     $success = sqlPrepareBindExecute(
//         "INSERT INTO $tAuthor ($fields) VALUES (?, ?, ?)",
//         "sss",
//         [
//             $sur,
//             $given,
//             $clar
//         ],
//         __FUNCTION__
//     );
// }

// sqlDisconnect();

if ($success != false) {
    setUser($user);
    pushFeedbackToLog(FeedbackString::CREATE_SUCCESS);
    $page = "authors";
} elseif (!isThereFeedback()) {
    pushFeedbackToLog(ErrorString::CREATE_FAIL, true);
}
redirectTo(ROOT, $page);
