<?php
// -------------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. Review the manual
// http://se.php.net/manual/en/function.session-destroy.php
//

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finally, destroy the session.
session_destroy();

// Create a new named session
session_name(preg_replace('/[:\.\/-_]/', '',  $pp->sessionName));
session_start();
session_regenerate_id();     // To avoid problems 

