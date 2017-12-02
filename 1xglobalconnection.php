<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_tryconnection = "localhost";
$database_tryconnection = "XGLOBAL";
$username_tryconnection = "root";
$password_tryconnection = "";
$tryconnection = mysql_pconnect($hostname_tryconnection, $username_tryconnection, $password_tryconnection) or trigger_error(mysql_error(),E_USER_ERROR); 
date_default_timezone_set('America/Toronto') ;
?>