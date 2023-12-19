<?php
define("ROOT", ".." . DIRECTORY_SEPARATOR);
define("PAGE", "book_move");

require_once(ROOT . "const.php");
require_once(ROOT . "requirements.php");

haveSession();

if (!is_null(getBook())) {

    resetMoveLocs();
    resetMoveBooks();

    moveBooksPush(["id" => getBook()]);
    setMoveState(MoveState::SELECTED);
}

redirectTo(ROOT, "locations");
