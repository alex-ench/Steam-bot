<?php
//Version 3.2
$steamauth['domainname'] = "http://localhost:8000/index.php"; // The main URL of your website displayed in the login page
$steamauth['logoutpage'] = "http://localhost:8000/index.php"; // Page to redirect to after a successfull logout (from the directory the SteamAuth-folder is located in) - NO slash at the beginning!
$steamauth['loginpage'] = "http://localhost:8000/index.php"; // Page to redirect to after a successfull login (from the directory the SteamAuth-folder is located in) - NO slash at the beginning!

// System stuff
if (empty($steamauth['domainname'])) {
    $steamauth['domainname'] = $_SERVER['SERVER_NAME'];
}
if (empty($steamauth['logoutpage'])) {
    $steamauth['logoutpage'] = $_SERVER['PHP_SELF'];
}
if (empty($steamauth['loginpage'])) {
    $steamauth['loginpage'] = $_SERVER['PHP_SELF'];
}
?>
