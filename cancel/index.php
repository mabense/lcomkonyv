<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "cancel");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

redirectToPreviousPage();