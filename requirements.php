<?php

require_once(CONFIG_FILE);
require_once(LIB_DIR . "feedback_log.php");
require_once(LIB_DIR . "session.php");
haveSession();
require_once(LANG_DIR . getLang() . ".php");
require_once(LIB_DIR . "public_func.php");
require_once(LIB_DIR . "dom.php");

define("PRIVATE_FUNC", "." . DIRECTORY_SEPARATOR . "private_func.php");
if (file_exists(PRIVATE_FUNC)) {
    require_once(PRIVATE_FUNC);
}

if (
    PAGE == "cancel"
) {
    redirectTo(ROOT, popPreviousPage());
}

// echo getLang();
// exit;

// echo "previous page: ";
// echo var_dump(fromSESSION("prevPage")) . "<br />";

// echo "place keys: ";
// echo var_dump(moveLocsGetAll()) . "<br />";
// echo "book keys: ";
// echo  var_dump(moveBooksGetAll()) . "<br />";
// echo var_dump(getMoveState()) . "<br />";
// echo "<br />";
