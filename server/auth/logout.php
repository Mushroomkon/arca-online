<?php
session_start();
session_destroy();
header("Location: ../../client/auth/login.html");
exit();
?>
