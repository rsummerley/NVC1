<?php 
session_start();
//require_once('../tryconnection.php');

function taxvalue($database_tryconnection, $tryconnection, $minvdte){
$minvdte=strtotime($minvdte);
mysql_select_db($database_tryconnection, $tryconnection);
$query_TAX = "SELECT HGST, HOGST, DATE_FORMAT(HGSTDATE,'%m/%d/%Y') AS HGSTDATE FROM CRITDATA LIMIT 1";
$TAX = mysql_query($query_TAX, $tryconnection) or die(mysql_error());
$row_TAX = mysqli_fetch_assoc($TAX);

$hgstdate=strtotime($row_TAX['HGSTDATE']);

if ($minvdte < $hgstdate){
$taxx=$row_TAX['HOGST'];
echo $row_TAX['HOGST'];
return $taxx;
}

else if ($minvdte >= $hgstdate){
$taxx=$row_TAX['HGST'];
echo $row_TAX['HGST'];
return $taxx;
}

else {echo "help!";}

}

////////////////////////////DISPLAY WITH GST NUMBER
function taxname($database_tryconnection, $tryconnection, $minvdte){
$minvdte=strtotime($minvdte);
mysql_select_db($database_tryconnection, $tryconnection);
$query_TAX = "SELECT HTAXNAME, HOTAXNAME, HGSTNO, DATE_FORMAT(HGSTDATE,'%m/%d/%Y') AS HGSTDATE FROM CRITDATA";
$TAX = mysql_query($query_TAX, $tryconnection) or die(mysql_error());
$row_TAX = mysqli_fetch_assoc($TAX);

$hgstdate=strtotime($row_TAX['HGSTDATE']);

if ($minvdte < $hgstdate){
$taxnamex=$row_TAX['HOTAXNAME']." (".$row_TAX['HGSTNO'].")";
echo $taxnamex;
return $taxnamex;
}

else if ($minvdte >= $hgstdate){
$taxnamex=$row_TAX['HTAXNAME']." (".$row_TAX['HGSTNO'].")";
//echo $taxnamex;
return $taxnamex;
}

else {echo "help!";}
}

//////////////////////////////////////////////////////////////////////////
mysql_select_db($database_tryconnection, $tryconnection);
$query_TAX = "SELECT HTAXNAME, HOTAXNAME, HGSTNO, DATE_FORMAT(HGSTDATE,'%m/%d/%Y') AS HGSTDATE FROM CRITDATA";
$TAX = mysql_query($query_TAX, $tryconnection) or die(mysql_error());
$row_TAX = mysqli_fetch_assoc($TAX);
$minvdte=strtotime($_SESSION['minvdte']);

if (isset($_SESSION['csminvdte'])){
$minvdte = strtotime($_SESSION['csminvdte']);
}

$hgstdate=strtotime($row_TAX['HGSTDATE']);
$taxnumber=$row_TAX['HGSTNO'];

if ($minvdte < $hgstdate){
$nametax=$row_TAX['HOTAXNAME'];
}

else if ($minvdte >= $hgstdate){
$nametax=$row_TAX['HTAXNAME'];
}

?>
