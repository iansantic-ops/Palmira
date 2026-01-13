<?php
session_start();
unset($_SESSION['USER']);

header("Location: index.php");
exit();
