<?php
  require_once '../const.php';



function createTable($name, $query) {
    try {
        queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
        echo "Table '$name' created or already exists.<br>";
    } catch (Exception $e) {
        echo "$e->getMessage()";
    }

}


function destroy_session() {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
}


function validate_username($user)
{
    if (strlen($user) < 3 || (preg_match("/[^a-zA-Z0-9_-]/", $user)))
    {
        return false;
    }
    else return true;
}

function validate_pass($pass)
{
    if (strlen($pass) < 3)
    {
        return false;
    }
    else return true;
}


    // Display Errors:
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


?>
