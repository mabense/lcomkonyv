<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "session.php");


function sqlSetLocationPath($locationId)
{
    resetLocation();
    haveLocationPath();
    $reversePath = [];

    $id = $locationId;
    while ($id !== null) {
        array_push($reversePath, $id);
        $tLocation = PLACE_TABLE;
        $sql = "SELECT `where` FROM $tLocation WHERE `id`=?";
        $stmtParentId = sqlPrepareBindExecute(
            $sql,
            "i",
            [
                $id
            ],
            __FUNCTION__
        );
        if ($stmtParentId) {
            $result = $stmtParentId->get_result();
            $parent = $result->fetch_assoc();
            $id = $parent ? $parent["where"] : null;
        }
        else {
            $id = null;
        }
    }

    while (sizeof($reversePath) > 0) {
        pushLocation(array_pop($reversePath));
    }
}


function sqlGetLocationPathString()
{
    $path = getLocationPath();
    $string = "";
    if (sizeof($path) > 0) {
        $first = true;

        $tLocation = PLACE_TABLE;
        $sql = "SELECT `name` FROM $tLocation WHERE `id`=?";

        foreach ($path as $location) {
            $stmt = sqlPrepareBindExecute(
                $sql,
                "i",
                [
                    $location
                ],
                __FUNCTION__
            );
            // $stmtParentId = new mysqli_stmt($id, $id);
            $result = $stmt->get_result();
            if ($loc = $result->fetch_assoc()) {
                if ($first) {
                    $first = false;
                } else {
                    $string .= DisplayString::TITLE_SEPARATOR;
                }
                $string .= $loc["name"];
            }
        }
    } else {
        $string .= pageToDisplayText("locations");
    }
    return $string;
}
