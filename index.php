<?php
define("ROOT", "." . DIRECTORY_SEPARATOR);
define("PAGE", "");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

handleMissingPage();

redirectTo(ROOT,  DEFAULT_PAGE);
