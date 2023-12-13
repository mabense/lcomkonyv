<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "move_cancel");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

require_once(LIB_DIR . "sql.php");
require_once(LIB_DIR . "sql_dom.php");
require_once(LIB_DIR . "sql_session.php");

haveSession();
$success = false;
$page = PAGE;

if (!auth(false, DEV_USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

$user = fromGET("user");
if (!isset($user)) {
    $user = DEV_USER;
}
resetMoveState();

redirectToPreviousPage();
