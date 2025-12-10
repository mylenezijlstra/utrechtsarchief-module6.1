<?php

include "db.php";


session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

session_destroy();
header("Location: ".WEBSITEROOT."/admin/login.php");
exit;

?>