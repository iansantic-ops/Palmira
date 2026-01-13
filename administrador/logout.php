<?php
session_start();
if (isset($_SESSION['idAdmin'])) {
    unset($_SESSION['idAdmin']);
}

header("Location: index.php");
exit();
