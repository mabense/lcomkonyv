<?php

require_once(LANGUAGE_FILE);
require_once(CONFIG_FILE);
require_once(LIB_DIR . "feedback_log.php");
require_once(LIB_DIR . "session.php");
require_once(LIB_DIR . "public_func.php");
require_once(LIB_DIR . "dom.php");

define("PRIVATE_FUNC", "." . DIRECTORY_SEPARATOR . "private_func.php");
if(file_exists(PRIVATE_FUNC)) {
    require_once(PRIVATE_FUNC);
}