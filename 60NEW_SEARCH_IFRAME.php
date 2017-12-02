<?php
session_start();
require_once('../tryconnection.php'); 

if (!isset($_SESSION['display'])){
$_SESSION['display']="1";
}
elseif (isset($_GET['showpatients'])){
$_SESSION['display']="1";
}
elseif (isset($_GET['showaddress'])){
$_SESSION['display']=2;
}
elseif (isset($_GET['showphone'])){
$_SESSION['display']=3;
}
elseif (isset($_GET['showinvoice'])){
$_SESSION['display']=4;
}

/////////////////////////////////////
if (!empty($_GET['custno'])){
$_SESSION['custno'] = $_GET['custno'];
$custno = $_SESSION['custno'];
}
elseif (empty($_GET['custno']) && isset($_GET['custno']))
{
unset($_SESSION['custno']);
}
else{
$custno = $_SESSION['custno'];
}
$custno=$custno.'%';
/////////////////////////////////////
if (!empty($_GET['contact'])){
$_SESSION['contact'] = $_GET['contact'];
$firstname = $_SESSION['contact'];
}
elseif (empty($_GET['contact']) && isset($_GET['contact']))
{
unset($_SESSION['contact']);
}
else{
$firstname = $_SESSION['contact'];
}
/////////////////////////////////////
if (!empty($_GET['company'])){
$_SESSION['company'] = $_GET['company'];
$lastname = mysqli_real_escape_string($mysqli_link, $_SESSION['company']);
}
elseif (empty($_GET['company']) && isset($_GET['company']))
{
unset($_SESSION['company']);
}
else{
$lastname = mysqli_real_escape_string($mysqli_link, $_SESSION['company']);
}
/////////////////////////////////////
if (!empty($_GET['phone'])){
$_SESSION['phone'] = $_GET['phone'];
$phone = $_SESSION['phone'];
}
elseif (empty($_GET['phone']) && isset($_GET['phone']))
{
unset($_SESSION['phone']);
}
else{
$phone = $_SESSION['phone'];
}
/////////////////////////////////////
if (!empty($_GET['sorting']))
{
$_SESSION['sorting'] = $_GET['sorting'];
$sortby = $_SESSION['sorting'];
}
elseif (!isset($_SESSION['sorting'])){
$sortby='COMPANY,CONTACT';
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
$_SESSION['start']=$_SESSION['start']+30;
	if ($_SESSION['number']<=$_SESSION['start']){
	$start=$_SESSION['number']-30;
	}
	else{
	$start=$_SESSION['start'];
	}
}
else if ($_GET['navigation']=="prev") {
$_SESSION['start']=$_SESSION['start']-30;
$start=$_SESSION['start'];
}
else if ($_GET['navigation']=="first") {
$_SESSION['start']=0;
$start=$_SESSION['start'];
}
else if ($_GET['navigation']=="last") {
$_SESSION['start']=$_SESSION['number']-30;
$start=$_SESSION['start'];
}
if ($start<0){
$_SESSION['start']=0;
$start=$_SESSION['start'];
}




$joinpet="";
if (!empty($_GET['searchpet'])){
$_SESSION['searchpet'] = $_GET['searchpet'];
$searchpet = mysqli_real_escape_string($mysqli_link, $_SESSION['searchpet']);
$joinpet=" JOIN PETMAST ON (PETMAST.PETNAME LIKE '$searchpet%' AND PETMAST.CUSTNO=ARCUSTO.CUSTNO AND PETMAST.PDEAD='0')";
}
elseif (empty($_GET['searchpet']) && isset($_GET['searchpet']))
{
unset($_SESSION['searchpet']);
}


if (!empty($_GET['invnumber'])){
$_SESSION['invnumber'] = $_GET['invnumber'];
$invn = $_SESSION['invnumber'];
$query_INVNO="SELECT CUSTNO FROM ARARECV WHERE INVNO='$invn'";
$INVNO=mysqli_query($tryconnection, $query_INVNO) or die(mysqli_error($mysqli_link));
$query_INVNO = mysqli_fetch_assoc($INVNO);
$totalRows_INVNO = mysqli_num_rows($INVNO);
	if (empty($query_INVNO['CUSTNO'])){
	$query_INVNO="SELECT CUSTNO FROM ARINVOI WHERE INVNO='$invn'";
	$INVNO=mysqli_query($tryconnection, $query_INVNO) or die(mysqli_error($mysqli_link));
	$query_INVNO = mysqli_fetch_assoc($INVNO);
	}
	if (empty($query_INVNO['CUSTNO'])){
	$query_INVNO="SELECT CUSTNO FROM INVLAST WHERE INVNO='$invn'";
	$INVNO=mysqli_query($tryconnection, $query_INVNO) or die(mysqli_error($mysqli_link));
	$query_INVNO = mysqli_fetch_assoc($INVNO);
	}
	if (empty($query_INVNO['CUSTNO'])){
	$query_INVNO="SELECT CUSTNO FROM ARYINVO WHERE INVNO='$invn'";
	$INVNO=mysqli_query($tryconnection, $query_INVNO) or die(mysqli_error($mysqli_link));
	$query_INVNO = mysqli_fetch_assoc($INVNO);
	}
$custno=$query_INVNO['CUSTNO'];
}
elseif (empty($_GET['invnumber']) && isset($_GET['invnumber']))
{
unset($_SESSION['invnumber']);
}




mysqli_select_db($tryconnection, $database_tryconnection);
$query_CLIENT = "SELECT SQL_CALC_FOUND_ROWS * FROM ARCUSTO ".$joinpet." WHERE ARCUSTO.CONTACT LIKE '$firstname%' AND ARCUSTO.CUSTNO LIKE '".$custno."' AND ARCUSTO.COMPANY LIKE '$lastname%' AND (ARCUSTO.PHONE LIKE '$phone%' OR ARCUSTO.PHONE2 LIKE '$phone%' OR ARCUSTO.PHONE3 LIKE '$phone%' OR ARCUSTO.PHONE4 LIKE '$phone%' OR ARCUSTO.PHONE5 LIKE '$phone%' OR ARCUSTO.PHONE6 LIKE '$phone%') ORDER BY ARCUSTO.".$sortby." ASC LIMIT ".$start.", 30 ";
$query_NUMBER="SELECT FOUND_ROWS()";
$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
$NUMBER=mysqli_query($tryconnection, $query_NUMBER) or die(mysqli_error($mysqli_link));
$row_NUMBER = mysqli_fetch_array($NUMBER);
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

	if (empty($row_CLIENT)){
	$query_CLIENT = "SELECT CUSTNO FROM SECINDEX WHERE FNAME LIKE '$firstname%' AND LNAME LIKE '$lastname%'";
	$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
	$row_CLIENT = mysqli_fetch_assoc($CLIENT);
		if (!empty($row_CLIENT)){
		$custnos=" OR CUSTNO='".$row_CLIENT['CUSTNO']."'";
		do { $custnos = $custnos." OR CUSTNO='".$row_CLIENT['CUSTNO']."'"; } while ($row_CLIENT = mysqli_fetch_assoc($CLIENT));
		$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO='$row_CLIENT[CUSTNO]'".$custnos;
		$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
		$row_CLIENT = mysqli_fetch_assoc($CLIENT);		
		}
	}
	
$totalRows_CLIENT = mysqli_num_rows($CLIENT);

$_SESSION['number']=$row_NUMBER[0];

function listpets($database_tryconnection, $tryconnection, $custno){
mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT = sprintf("SELECT * FROM PETMAST WHERE CUSTNO = '%s' AND PDEAD='0' AND PMOVED='0' ORDER BY PETNAME ASC LIMIT 5", $custno);
$PATIENT = mysqli_query($tryconnection, $query_PATIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT = mysqli_fetch_assoc($PATIENT);
$petsarray=array();
do {
$petsarray[]=$row_PATIENT['PETNAME'];
} while ($row_PATIENT = mysqli_fetch_assoc($PATIENT));
echo implode(", ", $petsarray);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />

<title>DV MANAGER MAC</title>

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

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

function highliteline(x){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor="#DCF6DD";
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
}

function performonclick(client){
var refID="<?php echo $_GET['refID']; ?>";
if (refID=="MOVE"){
parent.window.document.getElementById('search_client').style.display="none";
parent.window.document.getElementById('move_to').style.display="";
parent.window.document.getElementById('move_to_client').src="MOVE_TO_IFRAME.php?client="+client;
}
else{
window.open('CLIENT_PATIENT_FILE.php?client=' + client + '&refID='+ refID,'_parent');
}
//if (refID=="DUTY LOG"){
//parent.window.searchclient.custno.value=client;
//parent.window.self.close();
//}
//else{
//window.open('CLIENT_PATIENT_FILE.php?client=' + client + '&refID='+ refID,'_parent');
//}
}



</script>

<style type="text/css">
<!--
.style1 {color: <?php if (empty($row_CLIENT)){echo "#FFFFFF";} else {echo "#999999";} ?>
}
-->
</style>
<!-- InstanceEndEditable -->



<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->

<div id="WindowBody" style="width:715px;">
<div style="height:100%;">
<form action="" method="post" name="client_list">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">

    
   <?php
   if (empty($row_CLIENT)){
   echo "<tr height='50' valign='middle'>
    <td colspan='7' align='center' class='Verdana12'>
	I'm sorry, no record found. It's not my fault.
    </td>
    </tr>";
   }
   
   else{
   
   do {  
   
 echo  '<tr class="Verdana11" id="'.$row_CLIENT['CUSTNO'].'" onclick="performonclick(\''.$row_CLIENT['CUSTNO'].'\')" onmouseover="highliteline(this.id);" onmouseout="whiteoutline(this.id);">
      <td width="52" height="15">'.$row_CLIENT['CUSTNO'].'</td>
      <td width="155" align="left">&nbsp;'.$row_CLIENT['COMPANY'].',&nbsp;'.$row_CLIENT['TITLE'].'</td>
      <td width="95" align="left">'.$row_CLIENT['CONTACT'].'</td>
      <td>';
	  
	  if ($_SESSION['display']=='1'){
	  listpets($database_tryconnection, $tryconnection, $row_CLIENT['CUSTNO']);
	  } 
	  
	  else if ($_SESSION['display']=='2'){
	  echo $row_CLIENT['ADDRESS1'].' '.$row_CLIENT['CITY'].' '.$row_CLIENT['STATE'].' '.$row_CLIENT['ZIP'];
	  }
	  
	  else if ($_SESSION['display']=='3'){
	  	$c1=(!empty($row_CLIENT['CAREA'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p1=(!empty($row_CLIENT['PHONE'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
	  	$c2=(!empty($row_CLIENT['CAREA2'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p2=(!empty($row_CLIENT['PHONE2'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
	  	$c3=(!empty($row_CLIENT['CAREA3'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p3=(!empty($row_CLIENT['PHONE3'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
	  	$c4=(!empty($row_CLIENT['CAREA4'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p4=(!empty($row_CLIENT['PHONE4'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
	  	$c5=(!empty($row_CLIENT['CAREA5'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p5=(!empty($row_CLIENT['PHONE5'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
	  	$c6=(!empty($row_CLIENT['CAREA6'])) ? "(".$row_CLIENT['CAREA'].")" : "";
		$p6=(!empty($row_CLIENT['PHONE6'])) ? $row_CLIENT['PHONE']."&nbsp;&nbsp;" : "";
		$myarray=array($c1.$p1.$c2.$p2.$c3.$p3.$c4.$p4.$c5.$p5.$c6.$p6);
		$imyarray=implode(" ", $myarray);
		echo $imyarray;
	  }
	  
	  else if ($_SESSION['display']=='4'){
		$query_INVOICE = "SELECT INVNO FROM INVHOLD WHERE INVCUST = '".$row_CLIENT['CUSTNO']."' AND INVNO LIKE '".$_GET['invnumber']."%' ORDER BY INVNO ASC LIMIT 1";
		$INVOICE = mysqli_query($tryconnection, $query_INVOICE) or die(mysqli_error($mysqli_link));
		$row_INVOICE = mysqli_fetch_assoc($INVOICE);
	  	echo $row_INVOICE['INVNO'];
	  }
	  
echo '</td>
      <td width="70" align="right" valign="middle">
      <span';
	  if ($row_CLIENT['BALANCE']<0){echo " class='Verdana11Red' ";} 
echo  '>'.number_format($row_CLIENT['BALANCE'],2).'</span>
      </td>
	</tr>';
	} while ($row_CLIENT = mysqli_fetch_assoc($CLIENT)); }
	?>
    
    <tr height="16" valign="middle">
    <td colspan="7" align="center" class="Verdana11 style1">Displaying records: 
    <?php echo ($start+1)." to "; if ($totalRows_CLIENT<30){echo $totalRows_CLIENT;} else {echo ($start+30);}	echo " of ".$row_NUMBER[0];?>
    </td>
    </tr>
</table>
</form>
</div>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>