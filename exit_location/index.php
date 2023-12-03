<?php

define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "exit_location");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_dom.php");

haveSession();

if (!auth(false, DEV_USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

handleAction();

$location = getLocation();

if ($location != false) {
    sqlConnect();

    // $tLocation = PLACE_TABLE;
    // $stmt = sqlPrepareBindExecute(
    //     "SELECT `where` FROM $tLocation WHERE `id`=?",
    //     "i",
    //     [
    //         $location
    //     ],
    //     __FUNCTION__
    // );
    // $result = $stmt->get_result();
    // if($row = $result->fetch_assoc()) {
    //     pushLocation($row["where"]);
    // }
    // else {
    //     resetLocation();
    // }


    $from = popLocation();
    $to = getLocation();

    sqlDisconnect();
    if ($from == $to) {
        pushFeedbackToLog(ErrorString::LOCATION_NO_EXIT, true);
    }
}
redirectTo(ROOT, "locations");