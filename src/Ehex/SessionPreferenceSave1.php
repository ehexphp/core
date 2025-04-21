<?php

/**
 * Save to File
 * Class SessionPreferenceSave1
 */
class SessionPreferenceSave1
{
    static function sec_session_start()
    {
        $secure = true;
        // This stops JavaScript being able to access the session id.
        $httponly = true;

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $secure,
            $httponly);
        // Sets the session name to the one set above.
        session_start();            // Start the PHP session
        //session_regenerate_id(true);    // regenerated the session, delete the old one.
    }
}