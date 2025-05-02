<?php
session_start();
session_destroy();
header('Location: download.php');
exit;
