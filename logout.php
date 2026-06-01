<?php

session_start();

session_destroy();

header("Location: /SISTEMALL/login.php");
exit;

?>