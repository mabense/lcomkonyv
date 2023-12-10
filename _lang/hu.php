<?php

final class SubString
{
    const RENAMING = " átnevezése";
}

final class ErrorString
{
    const SESSION_NOT_RESET = "Session hiba. Töröld a sütiket, és frissíts.";
    const PASSWORD_WEAK = "Túl gyönge jelszó.";
    const PASSWORD_NOT_MATCH = "A jelszavak nem egyeznek.";
    const LOGIN_REJECTED = "Hibás jelszó.";
    const LOGIN_FAILED = "Nem sikerült belépni.";
    const LOGOUT_FAILED = "Nem sikerült kilépni.";
    const HANDLE_MISSING_NOT_FOUND = "\"%page%\" nem található.";
    const HANDLE_ACTION_FAILED = "\"%action%\" sikertelen.";
    const HANDLE_ACTION_NOT_FOUND = "\"%action%\" nem található.";
    const LOCATION_NO_EXIT = "Nem sikerült kijjebb lépni a helyről.";
    const CREATE_FAIL = "Nem sikerült létrehozni.";
    const NEW_AUTHOR_CLARIFY = "Már van ilyen nevű író! Esetleg egyértelműsíteni kell.";
    const NEW_AUTHOR_RECLARIFY = "Már van ilyen nevű író! Esetleg más egyértelműsítés kell.";
    const NEW_AUTHOR_NO_NAME = "Az író neve nincs megadva!";
    const NEW_BOOK_NO_AUTHORS = "Nincsenek írók!";
    const NEW_BOOK_NO_AUTHOR = "Nincs író megadva!";
    const NEW_BOOK_FAILED_TO_ADD_AUTHOR = "\"%author%\"t nem sikerül hozzáadni mint író!";
    const NEW_BOOK_NO_TITLE = "A könyvcím nincs megadva!";
    const NEW_LOCATION_NO_NAME = "A helynév nincs megadva!";
    const NO_LOCATION = "Hely nem található. Lehetséges, hogy szerverhiba történt.";
}

final class FeedbackString
{
    const LOGIN_ACCEPTED = "Sikeres belépés.";
    const LOGOUT_SUCCESS = "Sikeres kilépés.";
    const CREATE_SUCCESS = "Sikeres létrehozás.";
    const PLACE_EMPTY = "Ez a hely üres.";
}

final class ButtonString
{
    const CANCEL = "Mégse";
    const AUTHOR_NEW = "Új szerző";
    const LOCATION_NO_EXIT = "Nincs kijjebb";
    const LOCATION_EXIT = "Kijjebb lépés";
    const LOCATION_EDIT = "Hely" . SubString::RENAMING;
    const LOCATION_NEW = "Új hely ide";
    const BOOK_NEW = "Új könyv ide";
    const BOOK_NEW_LESS_AUTHORS = "Kevesebb szerző";
    const BOOK_NEW_MORE_AUTHORS = "Több szerző";
}

final class DisplayString
{
    const MAIN_TITLE = "Bense könyvtár";
    const TITLE_SEPARATOR = " - ";
    const LOGIN_GREETING = "Üdv!";
}

final class TableString
{
    const AUTHORS = "Szerzők";
    const AUTHOR_FULLNAME = "Teljes név";
    const PLACES = "HELYEK";
    const PLACE_ENTER = "Bejjebb lépés";
    const BOOKS = "KÖNYVEK";
    const BOOK_AUTHOR = "Szerző";
    const BOOK_TITLE = "Cím";
    const BOOK_SERIES = "Sorozat";
}

final class FormString
{
    const USERNAME = "Felhasználónév:";
    const PASSWORD = "Jelszó:";
    const REPASS = "Jelszó ismét:";
    const SIGNUP_SUBMIT = "Regisztráció";
    const LOGIN_SUBMIT = "Belépés";
    const WRITER_NAME = "Író neve";
    const WRITER_SURNAME = "Vezetéknév";
    const WRITER_GIVENNAME = "Keresztnév";
    const WRITER_CLERIFICATION = "Egyértelműsítő név";
    const PLACE_NAME = "Hely neve";
    const BOOK_AUTHOR = TableString::BOOK_AUTHOR . "k";
    const BOOK_AUTHOR_NEW = "Inkább új:";
    const BOOK_TITLE = TableString::BOOK_TITLE;
    const BOOK_SERIES = TableString::BOOK_SERIES;
    const CREATE_SUBMIT = "Létrehozás";
    const EDIT_SUBMIT = "Mentés";
    const SEARCH_SUBMIT = "Keresés";
}


$pageName = [
    "search" => "keresés",
    "locations" => "helyek",
    "authors" => "szerzők",

    "new_author" => "új_szerző",
    "new_location" => "új_hely",
    "new_book" => "új_könyv",

    "edit_location" => ButtonString::LOCATION_EDIT,

    "log_in" => "belépés",
    "sign_up" => "regisztráció",
    "log_out" => "kilépés",
    "home" => "kezdőlap"
];
