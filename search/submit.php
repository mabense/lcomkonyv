<?php
require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_auth.php");

echo "ennek soha nem kéne megjelenni!";
exit;

haveSession();

if (!auth(false, DEV_USER)) {
    redirectTo(ROOT, "log_in");
}

$success = false;
$page = PAGE;

$user = fromGET("user");
if (!isset($user)) {
    $user = DEV_USER;
}

$title = fromPOST("title");
$series = fromPOST("series");
$writer = fromPOST("writer");

if (!isset($title)) {
    $title = "";
}
if (!isset($series)) {
    $series = "";
}
if (!isset($writer)) {
    $writer = "";
}
$page .= "?title=" . $title . "&series=" . $series . "&writer=" . $writer;

redirectTo(ROOT, $page);
