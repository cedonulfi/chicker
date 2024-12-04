<?php
session_start();
unset($_SESSION['conversation']);
header('Location: index.php');
exit();
