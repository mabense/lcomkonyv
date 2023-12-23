<?php

final class ErrorString
{
    const SESSION_NOT_RESET = "Session error. Clear cookies, and refresh.";
    const PASSWORD_WEAK = "The password is too weak.";
    const PASSWORD_NOT_MATCH = "The passwords don't match.";
    const LOGIN_REJECTED = "Incorrect password.";
    const LOGIN_FAILED = "Failed to log in.";
    const LOGOUT_FAILED = "Failed to log out.";
    const HANDLE_MISSING_NOT_FOUND = "\"%page%\" not found.";
    const HANDLE_ACTION_FAILED = "\"%action%\" failed.";
    const HANDLE_ACTION_NOT_FOUND = "\"%action%\" not found.";
    const LOCATION_NO_EXIT = "Failed to step out of the location.";
    const LOCATION_DELETE_FAILED = "Failed to delete the location.";
    const CREATE_FAIL = "Failed to create.";
    const NEW_AUTHOR_CLARIFY = "An author like that already exists! Clarification may be needed.";
    const NEW_AUTHOR_RECLARIFY = "An author like that already exists! Another clarification may be needed.";
    const NEW_AUTHOR_NO_NAME = "The author's name is missing!";
    const NEW_BOOK_NO_AUTHORS = "There are no authors!";
    const NEW_BOOK_NO_AUTHOR = "No author is selected!";
    const NEW_BOOK_FAILED_TO_ADD_AUTHOR = "Failed to add \"%author%\" as author!";
    const NEW_BOOK_NO_TITLE = "The book's title is missing!";
    const NEW_LOCATION_NO_NAME = "The location's name is missing!";
    const NO_LOCATION = "Location not found. A server error may have occurred.";
    const EDIT_FAIL = "The changes have failed.";
}

final class FeedbackString
{
    const LOGIN_ACCEPTED = "Logged in successfully.";
    const LOGOUT_SUCCESS = "Logged out successfully.";
    const CREATE_SUCCESS = "Added successfully.";
    const EDIT_SUCCESS = "Changes successful.";
    const PLACE_EMPTY = "There's nothing here.";
    const R_U_SURE_DELETE_LOCATION = "Are you sure you want to delete \"%place%\"?";
}

final class ButtonString
{
    const CANCEL = "Cancel";
    const AUTHOR_NEW = "New author";
    const LOCATION_NO_EXIT = "Already outside";
    const LOCATION_EXIT = "Step outside";
    const LOCATION_EDIT = "Rename this location";
    const LOCATION_NEW = "New location here";
    const LOCATION_DELETE = "Delete location, move contents outside";
    const BOOK_NEW = "New book here";
    const BOOK_NEW_LESS_AUTHORS = "Fewer authors";
    const BOOK_NEW_MORE_AUTHORS = "More authors";
    const BOOK_JUMP = "Jump to the location of this book";
    const BOOK_MOVE = "Move this book";
    const BOOK_EDIT = "Edit data";
    const MOVE_SELECT = "Move from here";
    const MOVE_PASTE = "Move here";
    const MOVE_CANCEL = "Cancel move";
}

final class DisplayString
{
    const MAIN_TITLE = "Bense library";
    const TITLE_SEPARATOR = " - ";
    const LOGIN_GREETING = "Hello there!";
    const RENAMING = "Rename %what%";
}

final class TableString
{
    const AUTHORS = "Authors";
    const AUTHOR_FULLNAME = "Full name";
    const PLACES = "LOCATIONS";
    const PLACE_ENTER = "Step inside";
    const BOOKS = "BOOKS";
    const BOOK_PLACE = "Location";
    const BOOK_AUTHOR = "Authors";
    const BOOK_TITLE = "Title";
    const BOOK_SERIES = "Series";
    const BOOK_NUMBER = "#";
}

final class FormString
{
    const USERNAME = "Username:";
    const PASSWORD = "Password:";
    const REPASS = "Password again:";
    const SIGNUP_SUBMIT = "Sign up";
    const LOGIN_SUBMIT = "Log in";
    const WRITER_NAME = "Name of author";
    const WRITER_SURNAME = "Surname";
    const WRITER_GIVENNAME = "Givenname";
    const WRITER_CLERIFICATION = "Clarifying name";
    const PLACE_NAME = "Name of location";
    const BOOK_AUTHOR = TableString::BOOK_AUTHOR;
    const BOOK_AUTHOR_NEW = "New one instead:";
    const BOOK_TITLE = TableString::BOOK_TITLE;
    const BOOK_SERIES = TableString::BOOK_SERIES;
    const CREATE_SUBMIT = "Create";
    const EDIT_SUBMIT = "Save";
    const SEARCH_SUBMIT = "Search";
    const SELECT_SUBMIT = "Confirm selection";
}


$pageName = [
    "move_select" => "select locations and books", 

    "search" => "search",
    "locations" => "locations",
    "authors" => "authors",

    "new_author" => "new_author",
    "new_location" => "new_location",
    "new_book" => "new_book",

    "edit_location" => ButtonString::LOCATION_EDIT,
    "delete_location" => "delete_location",

    "log_in" => "log_in",
    "sign_up" => "sign_up",
    "log_out" => "log_out",
    "home" => "home"
];
