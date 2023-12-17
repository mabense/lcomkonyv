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

if (!auth(AuthLevel::USER)) {
    redirectTo(ROOT, "log_in");
}

handleMissingPage();

resetMoveState();

redirectToPreviousPage();
