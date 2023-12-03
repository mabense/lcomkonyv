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
$name = fromPOST("name");

if (!isset($name) or $name === "") {
    pushFeedbackToLog(ErrorString::NEW_LOCATION_NO_NAME, true);
} else {
    $tLocation = PLACE_TABLE;
    $fields = "`name`, `where`";

    $sqlVals = "?, NULL";
    $sqlTypes = "s";
    $sqlParams = [
        $name
    ];
    if (isset($loc)) {
        $sqlVals = "?, ?";
        $sqlTypes = "si";
        $sqlParams = [
            $name, 
            $loc
        ];
    }
    sqlConnect();
    $success = sqlPrepareBindExecute(
        "INSERT INTO $tLocation ($fields) VALUES ($sqlVals)",
        $sqlTypes,
        $sqlParams,
        __FUNCTION__
    );
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
