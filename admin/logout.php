<?php
session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

session_destroy();
header("Location: /utrechtsarchief-module6.1/admin/login.php");
exit;
