<?php

final class ErrorString
{
    const SESSION_NOT_RESET = "Sessionfehler. Cookies löschen, und Seite neu laden.";
    const PASSWORD_WEAK = "Das Passwort ist nicht stark genug.";
    const PASSWORD_NOT_MATCH = "Die Passwörter stimmen nicht überein.";
    const LOGIN_REJECTED = "Das Passwort stimmt nicht.";
    const LOGIN_FAILED = "Anmeldefehler.";
    const LOGOUT_FAILED = "Abmeldefehler.";
    const HANDLE_MISSING_NOT_FOUND = "\"%page%\" nicht gefunden.";
    const HANDLE_ACTION_FAILED = "\"%action%\" ist durchgefallen.";
    const HANDLE_ACTION_NOT_FOUND = "\"%action%\" nicht gefunden.";
    const LOCATION_NO_EXIT = "Heraustreten ist durchgefallen.";
    const LOCATION_DELETE_FAILED = "Platzlöshen ist durchgefallen.";
    const CREATE_FAIL = "Erstellen ist durchgefallen.";
    const NEW_AUTHOR_CLARIFY = "Dieser Autorname ist bereits vorhanden!";
    const NEW_AUTHOR_RECLARIFY = "Dieser Autorname ist bereits vorhanden!";
    const NEW_AUTHOR_NO_NAME = "Der Autorname fehlt!";
    const NEW_BOOK_NO_AUTHORS = "Es gibt kein Autoren!";
    const NEW_BOOK_NO_AUTHOR = "Autoren fehlen!";
    const NEW_BOOK_FAILED_TO_ADD_AUTHOR = "Der Autor \"%author%\" hat nicht hinzugesetzt!";
    const NEW_BOOK_NO_TITLE = "Der Titel fehlt!";
    const NEW_LOCATION_NO_NAME = "Der Platzname fehlt!";
    const NO_LOCATION = "Platz nicht gefunden. Ein serverfehler ist möglich.";
    const EDIT_FAIL = "Einstellungen sind durchgefallen.";
}

final class FeedbackString
{
    const LOGIN_ACCEPTED = "Ihre Anmeldung ist erfolgreich.";
    const LOGOUT_SUCCESS = "Ihre Abmeldung ist erfolgreich.";
    const CREATE_SUCCESS = "Erstellung ist erfolgreich.";
    const EDIT_SUCCESS = "Einstellungen sind erfolgreich.";
    const PLACE_EMPTY = "Es gibt here nichts.";
    const R_U_SURE_DELETE_LOCATION = "Der Platz \"%place%\" löschen?";
}

final class ButtonString
{
    const CANCEL = "abbrechen";
    const AUTHOR_NEW = "neuer Autor";
    const LOCATION_NO_EXIT = "schon draußen";
    const LOCATION_EXIT = "hinaustreten";
    const LOCATION_EDIT = "Platz umbenennen";
    const LOCATION_NEW = "neu Platz hier";
    const LOCATION_DELETE = "Platz löschen, Inhalte nach draußen verschieben";
    const BOOK_NEW = "neu Buch hier";
    const BOOK_NEW_LESS_AUTHORS = "weniger Autoren";
    const BOOK_NEW_MORE_AUTHORS = "mehr Autoren";
    const BOOK_JUMP = "zur Platz des Buches";
    const BOOK_MOVE = "Buch verschieben";
    const BOOK_EDIT = "Datai ändern";
    const MOVE_SELECT = "hinausziehen";
    const MOVE_PASTE = "hereinschieben";
    const MOVE_CANCEL = "Verschieben abbrechen";
}

final class DisplayString
{
    const MAIN_TITLE = "Bibliothek";
    const TITLE_SEPARATOR = " - ";
    const LOGIN_GREETING = "Willkommen!";
    const RENAMING = "%what% umbenennen";
}

final class TableString
{
    const AUTHORS = "Autoren";
    const AUTHOR_FULLNAME = "gantzer Name";
    const PLACES = "PLÄTZE";
    const PLACE_ENTER = "hineintreten";
    const BOOKS = "BÜCHER";
    const BOOK_PLACE = "Plätze";
    const BOOK_AUTHOR = "Autoren";
    const BOOK_TITLE = "Titel";
    const BOOK_SERIES = "Reihe";
    const BOOK_NUMBER = "#";
}

final class FormString
{
    const USERNAME = "Benutzername:";
    const PASSWORD = "Passwort:";
    const REPASS = "Bestätigen:";
    const SIGNUP_SUBMIT = "erstellen";
    const LOGIN_SUBMIT = "Anmelden";
    const WRITER_NAME = "Autorname";
    const WRITER_SURNAME = "Familienname";
    const WRITER_GIVENNAME = "Rufname";
    const WRITER_CLERIFICATION = "Klarstellung";
    const PLACE_NAME = "Platzname";
    const BOOK_AUTHOR = TableString::BOOK_AUTHOR;
    const BOOK_AUTHOR_NEW = "Neu:";
    const BOOK_TITLE = TableString::BOOK_TITLE;
    const BOOK_SERIES = TableString::BOOK_SERIES;
    const CREATE_SUBMIT = "erstellen";
    const EDIT_SUBMIT = "speichern";
    const SEARCH_SUBMIT = "suchen";
    const SELECT_SUBMIT = "diese Auswahl benutzen";
}


$pageName = [
    "move_select" => "Plätze und Bücher auswählen", 

    "search" => "suchen",
    "locations" => "Plätze",
    "authors" => "Autoren",

    "new_author" => "neuer_Autor",
    "new_location" => "neuer_Platz",
    "new_book" => "neues_Buch",

    "edit_location" => ButtonString::LOCATION_EDIT,
    "delete_location" => "Platz_löschen",

    "log_in" => "anmelden",
    "sign_up" => "neues Konto erstellen",
    "log_out" => "abmelden",
    "home" => "Startseite"
];
