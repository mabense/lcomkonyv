<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

haveSession();
$success = false;
$page = PAGE;

resetMoveLocs();
resetMoveBooks();

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

$debug = /* */ false /*/ true /* */;

if ($debug) {
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
}

if (!is_null($selectedLocs)) {
    foreach ($selectedLocs as $row => $on) {
        $locKey = $sessLocs[$row];
        moveLocsPush($locKey);
    }

    if ($debug) {
        echo "place keys: ";
        echo var_dump(moveLocsGetAll()) . "<br />";
    }
}

if ($debug) {
    echo "-------------------------------------------------------------<br />";
}

if (!is_null($selectedBooks)) {
    foreach ($selectedBooks as $row => $on) {
        $bookKey = $sessBooks[$row];
        moveBooksPush($bookKey);
    }

    if ($debug) {
        echo "book keys: ";
        echo var_dump(moveBooksGetAll()) . "<br />";
    }
}


if ($debug) {
    echo "-------------------------------------------------------------<br />";

    // echo $locSQL = "UPDATE $tLocation SET $locChanges WHERE $locConds";
    // echo $bookSQL = "UPDATE $tBook SET $bookChanges WHERE $bookConds";

    exit;
}

setMoveState(MoveState::SELECTED);

redirectToPreviousPage();
