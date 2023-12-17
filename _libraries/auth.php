<?php


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


function authLogout()
{
    if (!resetUser()) {
        pushFeedbackToLog(ErrorString::SESSION_NOT_RESET, true);
        return false;
    }
    return true;
}


function authLogin($password)
{
    $pwdMatch = passwordCompare($password, LOCAL_PWD);

    if (!$pwdMatch) {
        pushFeedbackToLog(ErrorString::LOGIN_REJECTED, true);
        return false;
    }
    return true;
}

