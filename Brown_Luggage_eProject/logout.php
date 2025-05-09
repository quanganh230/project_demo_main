<?php
session_start();
require 'db_connect.php';

session_unset();
session_destroy();
header('Location: index.php');
exit;
