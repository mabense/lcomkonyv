<?php

define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

lessAuthors();

handleMissingPage();

$book = getBook();

if (isset($book)) {
    redirectTo(ROOT, "edit_book");
} else {
    redirectTo(ROOT,  "new_book");
}
