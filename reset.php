<?php
session_start();
unset($_SESSION['hasil'], $_SESSION['inputData']);
header('Location: index.php');
exit;
