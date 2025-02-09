<?php
session_start() ; 



if (isset($_SESSION['users'])) { 
    var_dump($_SESSION['users']) ; 
    unset($_SESSION['users']['specialist']) ; 
    unset($_SESSION['users']['client']) ; 

}

header("Location: index.html");



exit(); // إنهاء السكربت بعد التوجيه