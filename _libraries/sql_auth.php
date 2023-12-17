<?php
require_once(LIB_DIR . "sql.php");


function passwordStrong($password)
{
    $isStrong = false;
    if (
        strlen($password) > 0
    ) {
        $isStrong = true;
    }
    return $isStrong;
}


function passwordCompare($one, $other)
{
    return $one == $other;
}


function sqlLogout()
{
    if (!resetUser()) {
        pushFeedbackToLog(ErrorString::SESSION_NOT_RESET, true);
        return false;
    }
    return true;
}


function sqlLogin($name, $password)
{
    $tUser = USER_TABLE;
    $fields = "`name`, `password`";
    $sql = "SELECT $fields FROM $tUser WHERE `name`=?";
    
    $stmt = sqlPrepareBindExecute(
        $sql,
        "s",
        [$name],
        __FUNCTION__
    );
    $user = $stmt ? $stmt->get_result()->fetch_assoc() : null;

    $uExists = ($user !== null);
    $pwdMatch = $uExists ? password_verify($password, $user["password"]) : false;

    if (!$uExists || !$pwdMatch) {
        pushFeedbackToLog(ErrorString::LOGIN_REJECTED, true);
        return false;
    }
    return $user["name"];
}


function sqlSignup($name, $password, $passwordAgain)
{
    $tUser = USER_TABLE;
    if (!passwordStrong($password)) {
        pushFeedbackToLog(ErrorString::PASSWORD_WEAK, true);
        return false;
    }
    if (!passwordCompare($password, $passwordAgain)) {
        pushFeedbackToLog(ErrorString::PASSWORD_NOT_MATCH, true);
        return false;
    }
    $password = password_hash($password, PASSWORD_BCRYPT);
    $fields = "(`name`, `password`)";
    $sql = "INSERT INTO $tUser $fields VALUES (?, ?)";
    $stmt = sqlPrepareBindExecute(
        $sql,
        "ss",
        [$name, $password],
        __FUNCTION__
    );
    return $stmt;
}

