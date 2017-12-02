<?php
session_start();
require_once('../../tryconnection.php'); 

/////////////////////////////////////
if (!empty($_GET['pet2search'])){
$_SESSION['pet2search'] = $_GET['pet2search'];
$pet2search = mysql_real_escape_string($_SESSION['pet2search']);
}
elseif (empty($_GET['pet2search']) && isset($_GET['pet2search']))
{
unset($_SESSION['pet2search']);
}
else{
$pet2search = mysql_real_escape_string($_SESSION['pet2search']);
}
/////////////////////////////////////
if (!empty($_GET['prabtag'])){
$_SESSION['prabtag'] = $_GET['prabtag'];
$prabtag = $_SESSION['prabtag'];
}
elseif (empty($_GET['prabtag']) && isset($_GET['prabtag']))
{
unset($_SESSION['prabtag']);
}
else{
$prabtag = $_SESSION['prabtag'];
}
/////////////////////////////////////
if (!empty($_GET['ptatno'])){
$_SESSION['ptatno'] = $_GET['ptatno'];
$ptatno = $_SESSION['ptatno'];
}
elseif (empty($_GET['ptatno']) && isset($_GET['ptatno']))
{
unset($_SESSION['ptatno']);
}
else{
$ptatno = $_SESSION['ptatno'];
}
/////////////////////////////////////
if (!empty($_GET['sorting']))
{
$_SESSION['sorting'] = $_GET['sorting'];
$sortby = $_SESSION['sorting'];
}
elseif (!isset($_SESSION['sorting'])){
$sortby=PETNAME;
} 
else{
$sortby = $_SESSION['sorting'];
}
/////////////////////////////////////
if (empty($_GET['navigation'])){
$start=0;
$_SESSION['start']=$start;
}
else if ($_GET['navigation']=="next") {
$_SESSION['start']=$_SESSION['start']+16;
$start=$_SESSION['start'];
}
else if ($_GET['navigation']=="prev") {
$_SESSION['start']=$_SESSION['start']-16;
$start=$_SESSION['start'];
}
else if ($_GET['navigation']=="first") {
$_SESSION['start']=0;
$start=$_SESSION['start'];
}
else if ($_GET['navigation']=="last") {
$_SESSION['start']=$totalRows_PET-16;
$start=$_SESSION['start'];
}
if ($start<0){
$start=0;
$_SESSION['start']=0;
}


mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENTS = "SELECT  SQL_CALC_FOUND_ROWS PETID, CUSTNO, PETNAME, PSEX, PRABTAG, PTATNO, PRABLAST, PFILENO, PETTYPE FROM PETMAST WHERE PDEAD='0' AND PETNAME LIKE '$pet2search%' AND (PRABTAG LIKE '$prabtag%' OR PRABLAST LIKE '$prabtag%') AND PTATNO LIKE '$ptatno%' ORDER BY ".$sortby." ASC LIMIT ".$start.", 16 ";
$PATIENTS = mysql_query($query_PATIENTS, $tryconnection) or die(mysql_error());
$totalRows_PATIENTS = mysqli_num_rows($PATIENTS);
$row_PATIENTS = mysqli_fetch_assoc($PATIENTS);

$query_NUMBER="SELECT FOUND_ROWS()";
$NUMBER=mysql_query($query_NUMBER, $tryconnection) or die(mysql_error());
$row_NUMBER = mysqli_fetch_array($NUMBER);
$_SESSION['number']=$row_NUMBER[0];

function displayclient($database_tryconnection, $tryconnection, $custno){
mysql_select_db($database_tryconnection, $tryconnection);
$query_CLIENT = sprintf("SELECT * FROM ARCUSTO WHERE CUSTNO = '%s' LIMIT 1", $custno);
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysqli_fetch_assoc($CLIENT);
$totalRows_CLIENT = mysqli_num_rows($CLIENT);
echo $row_CLIENT['TITLE']." ".$row_CLIENT['CONTACT']." ".$row_CLIENT['COMPANY']." (".$row_CLIENT['PHONE'].")";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />

<title>DV MANAGER MAC</title>

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<style type="text/css">
<!--
#WindowBody {
	position:absolute;
	top:0px;
	width:733px;
	height:553px;
	z-index:1;
	font-family: "Verdana";
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #FFFFFF;
	left: 0px;
	color: #000000;
	text-align: left;
}
-->
</style>

</head>
<!-- InstanceBeginEditable name="EditRegion2" -->

<script type="text/javascript">

function bodyonload()
{
}

function highliteline(x,y){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor=y;
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}

function ClickOnPatient(patient,refID,client,petno,pettype,pet2search,psex)
{
window.open('../../CLIENT/CLIENT_PATIENT_FILE.php?patient=' + patient + '&client=' + client + '&refID=' + refID + '&llocalid=' + localStorage.llocalid,'_parent');
document.getElementById(petno).style.display="";
}

</script>



<style type="text/css">
</style>
<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->

<div id="WindowBody" style="width:715px;">
<div style="height:100%;">
<form action="" method="post" name="patient_list">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows" bgcolor="#FFFFFF">
   
   <?php do { ?> 
   <tr class="Verdana11" id="<?php echo $row_PATIENTS['PETID']; ?>" onclick="ClickOnPatient('<?php echo $row_PATIENTS['PETID']; ?>','<?php echo $_GET['refID']; ?>','<?php echo $row_PATIENTS['CUSTNO']; ?>','<?php echo $row_PATIENTS['PETNO']; ?>','<?php echo $row_PATIENTS['PETTYPE']; ?>','<?php  echo $row_PATIENTS['PETNAME']; ?>','<?php echo $row_PATIENTS['PSEX']; ?>')" onmouseover="highliteline(this.id,'<?php if ($row_PATIENTS['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';} ?>');" onmouseout="whiteoutline(this.id);">
      <td width="135" height="10" title="Click to open this patient's detail"><?php echo $row_PATIENTS['PETNAME']; ?></td>
      <td width="77" height="10" align="left"><?php if ($row_PATIENTS['PRABTAG']==""){echo "-";} else {echo $row_PATIENTS['PRABTAG'];} echo "<br />"; if ($row_PATIENTS['PRABLAST']==""){echo "-";} else {echo $row_PATIENTS['PRABLAST'];} ?></td>
      <td width="181" height="10" align="left">&nbsp;<?php echo $row_PATIENTS['PTATNO']; ?></td>
      <td width="60" height="10"><span id="home"><?php echo $row_PATIENTS['PFILENO']; ?></span>
      </td>
      <td width="" height="10" align="left" valign="middle"><?php displayclient($database_tryconnection, $tryconnection, $row_PATIENTS['CUSTNO']); ?></td>
    </tr>
    
    <?php } while ($row_PATIENTS = mysqli_fetch_assoc($PATIENTS)); ?>
    
    <tr height="40" valign="middle">
    <td colspan="5" align="center" class="Verdana11Grey">Displaying records: 
    <?php echo ($start+1)." to "; if ($totalRows_PATIENTS<16){echo $totalRows_PATIENTS;} else {echo ($start+16);}	echo " of ".$row_NUMBER[0];?>
    </td>
    </tr>

</table>

</form>
</div>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>