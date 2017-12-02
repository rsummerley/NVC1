<?php
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");
include("../../ASSETS/history.php");


if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

$client=$_SESSION['client'];

mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);
$totalRows_PATIENT_CLIENT = mysqli_num_rows($PATIENT_CLIENT);

$pdob=$row_PATIENT_CLIENT['PDOB'];

$query_HXBUFFER = sprintf("SELECT * FROM HXBUFFER WHERE HXPETID='$patient'");
$HXBUFFER = mysqli_query($tryconnection, $query_HXBUFFER) or die(mysqli_error($mysqli_link));
$row_HXBUFFER = mysqli_fetch_assoc($HXBUFFER);

$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR WHERE SIGNEDIN='1' ORDER BY DOCTOR ASC");
$DOCTOR = mysqli_query($tryconnection, $query_DOCTOR) or die(mysqli_error($mysqli_link));
$row_DOCTOR = mysqli_fetch_assoc($DOCTOR);

$query_STAFF = sprintf("SELECT STAFF FROM STAFF WHERE SIGNEDIN='1' ORDER BY STAFF ASC");
$STAFF = mysqli_query($tryconnection, $query_STAFF) or die(mysqli_error($mysqli_link));
$row_STAFF = mysqli_fetch_assoc($STAFF);

$query_HXFILTER = "SELECT * FROM HXFILTER WHERE HXGROUP='1'";
$HXFILTER = mysqli_query($tryconnection, $query_HXFILTER) or die(mysqli_error($mysqli_link));
$row_HXFILTER = mysqli_fetch_assoc($HXFILTER);

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysqli_query($tryconnection, $query_PREFER) or die(mysqli_error($mysqli_link));
$row_PREFER = mysqli_fetch_assoc($PREFER);

$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

//to identify DLOG -> have a GET variable + set the HSUBCAT into 31 & 32

if (isset($_POST['save'])){

$xfilter=implode(",",$_POST['filter']);

	if (!empty($row_HXBUFFER)) {
		$updateSQL = "UPDATE  HXBUFFER SET HXTEXT='".mysqli_real_escape_string($mysqli_link, $_POST['treatdesc'])."', HXHEADING='$_POST[heading]', HXFILTER='$xfilter', HXDOCTOR='".mysqli_real_escape_string($mysqli_link, $_POST['who'])."' WHERE HXPETID='$_SESSION[patient]'";
		mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	}
	else {
		$insertSQL = "INSERT INTO HXBUFFER (HXPETID, HXTEXT, HXHEADING, HXFILTER, HXDOCTOR) VALUE ('$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, $_POST['treatdesc'])."', '$_POST[heading]', '$xfilter', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
		mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	}
if ($_SESSION['path']=='3close'){
unset($_SESSION['path']);
$closewin="self.close();";
}
else if ($_GET['path']=='procmenu'){
header("Location:../PROCESSING_MENU/PROCESSING_MENU.php");
}
else if ($_GET['path']=='2close'){
$closewin="self.close();";
}
else {
header("Location:REVIEW_HISTORY.php?path=procmenu2");
}
}


else if (isset($_POST['finish'])){

	$query_CHECKTABLE="SELECT * FROM $treatmxx LIMIT 1";
	$CHECKTABLE= mysqli_query($tryconnection, $query_CHECKTABLE) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE TREATM0";
	$result=mysqli_query($tryconnection, $create_TREATMXX) or die(mysqli_error($mysqli_link));
	}


$filter=array_sum($_POST['filter']);
	
////////////////////////////////////////////////////////////////////
////////////////////////// DUTY LOG ///////////////////////////////
//////////////////////////////////////////////////////////////////
	if ($_POST['heading']=="31"){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'DUTY LOG', $filter, '31', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			//the HSUBCAT is 33 because 32 is reserved for the original duty log from the DL screen
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '33', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}
	
////////////////////////////////////////////////////////////////////
////////////////////////// PROCEDURES /////////////////////////////
//////////////////////////////////////////////////////////////////
	if ($_POST['heading']=="41"){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'PROCEDURES', $filter, '41', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '42', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}

////////////////////////////////////////////////////////////////////
////////////////////////// LABORATORY /////////////////////////////
//////////////////////////////////////////////////////////////////
	if ($_POST['heading']=="51"){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'LABORATORY', $filter, '51', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '52', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}

////////////////////////////////////////////////////////////////////
////////////////////////// IMAGERY ////////////////////////////////
//////////////////////////////////////////////////////////////////

	else if ($_POST['heading']=="21"){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'IMAGERY', $filter, '21', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '22', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}


////////////////////////////////////////////////////////////////////
///////////////////// CLIENT COMMUNICATION ////////////////////////
//////////////////////////////////////////////////////////////////
	else if ($_POST['heading']=="81"){
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'CLIENT COMMUNICATION', $filter, '81', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '82', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}

////////////////////////////////////////////////////////////////////
//////////////////////// PROGRESS NOTES ///////////////////////////
//////////////////////////////////////////////////////////////////
	else if ($_POST['heading']=="91"){
	//create the OTHER heading
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'OTHER', $filter, '91', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	
			$note=array();
	
			if (strlen($_POST['treatdesc']) > 500){
			$howmany=ceil(strlen($_POST['treatdesc'])/500);
				for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
				$note[]=substr($_POST['treatdesc'],$i,500);
				}
			}
			else {
				$note[]=$_POST['treatdesc'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$_SESSION[client]', '$_SESSION[patient]','".mysqli_real_escape_string($mysqli_link, $note2)."', $filter, '92', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}//foreach (($note as $note2)
	}

	// and clear the buffer, as it is finished.
$DUMP_BUF = "DELETE FROM HXBUFFER WHERE HXPETID='$patient'" ;
$XITOUT = mysqli_query($tryconnection, $DUMP_BUF) or die(mysqli_error($mysqli_link)) ;

if ($_SESSION['path']=='3close'){
unset($_SESSION['path']);
$closewin="self.close();";
}
else if ($_GET['path']=='procmenu'){
header("Location:../PROCESSING_MENU/PROCESSING_MENU.php");
}
else if ($_GET['path']=='2close'){
$closewin="self.close();";
}
else {
header("Location:REVIEW_HISTORY.php?path=procmenu2");
}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ADD NEW NOTE INTO MEDICAL HISTORY</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
function bodyonload(){
<?php 
echo $closewin; 

if ($_GET['path']=='2close') {
echo "alert('DVManager has opened a new window for you to add new history. If you wish to exit, please click CLOSE and return to the FINISH INVOICE window.')";
} 
?>

document.getElementById('inuse').innerText=localStorage.xdatabase;

document.add_history.treatdesc.focus();

var hxpreview=document.getElementById('hxpreview');
hxpreview.scrollTop = hxpreview.scrollHeight;
}


function checknames()
{
valid = true;
var who=document.add_history.who;
var filterx=0;

for (i=0; i<document.add_history.checkbox.length; i++){
	  if (document.add_history.checkbox[i].checked){
	  filterx = 1;
	  }
}
	
	if (filterx == 0){
		alert ('Please select filter.');
		valid = false;
	}
	else if (document.add_history.who.options[0].selected==true){
		alert ('Please enter your name.');
		valid = false;
	}
return valid;
}

function clearbuffer(){
document.add_history.treatdesc.value='';
	for (i=0; i<document.forms[0].checkbox.length; i++){
	document.forms[0].checkbox[i].checked=false;
	}
}

</script>

<style type="text/css">
<!--
#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}

#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}


.SelectList {
	width: 100%;
	height: 100%;
	font-family: "Andale Mono";
	font-size: 13px;
	border-width: 1px;
	padding: 5 px;
	outline-width: 0px;
}
-->
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="">About DV Manager</span></a></li>
                <li><a onclick="utilities();">Utilities</a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self'/'+localStorage.xdatabase+'/INVOICE/CASUAL_SALE_INVOICING/STAFF.php?refID=SCI)"><span class="">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="nav0();">Regular Invoicing</a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Cancel Invoices</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/INVOICE/COMMENTS/COMMENTS_LIST.php?path=DIRECTORY','_blank','width=733,height=553,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')">Comments</a></li>
                <li><a href="#" onclick="tffdirectory()">Treatment and Fee File</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Worksheet File</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick="invreports();"><span class="">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')"><span class="">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick="reception();">Patient Registration</a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/RECEPTION/USING_REG_FILE.php','_blank','width=550,height=535')">Using Reception File</a></li>
                <li><a href="#" onclick="nav2();"><span class="hidden"></span>Examination Sheets</a></li>
                <li><a href="#" onclick="gexamsheets()"><span class="">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick="nav3();">Duty Log</a></li>
                <li><a href="#" onclick="staffsiso()">Staff Sign In &amp; Out</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="nav4();">Processing Menu</a> </li>
                <li><a href="#" onclick="nav5();">Review Patient Medical History</a></li>
                <li><a href="#" onclick="nav6();">Enter New Medical History</a></li>
                <li><a href="#" onclick="nav7();">Enter Patient Lab Results</a></li>
                <li><a href="#" onclick=""window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=ENTER SURG. TEMPLATES','_self')><span class="">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=CREATE NEW CLIENT','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');">Create New Client</a></li>
                <li><a href="#" onclick="movepatient();">Move Patient to a New Client</a></li>
                <li><a href="#" onclick="searchpatient()">Rabies Tags</a></li>
                <li><a href="#" onclick="searchpatient()">Tatoo Numbers</a></li>
                <li><a href="#" onclick="nav8();"><span class="">Certificates</span></a></li>
                <li><a href="#" onclick="nav9();"><span class="">Clinical Logs</span></a></li>
                <li><a href="#" onclick="nav10();"><span class="">Patient Categorization</span></a></li>
                <li><a href="#" onclick="">Laboratory Templates</a></li>
                <li><a href="#" onclick="nav1();"><span class="">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""accreports()>Accounting Reports</a></li>
                <li><a href="#" onclick="inventorydir();" id="inventory" name="inventory">Inventory</a></li>
                <li><a href="#" onclick="" id="busstatreport" name="busstatreport"><span class="">Business Status Report</span></a></li>
                <li><a href="#" onclick="" id="hospstatistics" name="hospstatistics"><span class="">Hospital Statistics</span></a></li>
                <li><a href="#" onclick="" id="monthend" name="monthend"><span class="">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')" ><span class="">Recalls and Searches</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Handouts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')MAILING/MAILING_LOG/MAILING_LOG.php?refID=">Mailing Log</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?refID=1','_blank','width=567,height=473')">Referring Clinics and Doctors</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Referral Adjustments</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<form action="" name="add_history" method="post" onsubmit="return checknames();">
    
  <table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="60" colspan="3" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center">
        <span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>        </td>
        <td width="19%" colspan="2" rowspan="4" align="center">
        <table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
      </tr>
    </table>    </td>
      </tr>
    <tr>
      <td height="" colspan="3" align="center" valign="top">
        
        
        <table class="table" width="733" height="457" border="1" cellpadding="0" cellspacing="0" >
          <tr>
            <td height="200" class="Verdana11">
              <div style="overflow:auto; height:100%;" id="hxpreview">             
 				<!--<iframe src="../../OLDHISTORY/<?php echo $treatmxx."/$patient"; ?>.pdf" height="900" width="700" id="xiframe" scrolling="no"></iframe>-->
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              
              <tr>
                  <td width="85"></td>
                  <td width='100'></td>
                  <td width='100'></td>
                  <td width='100'></td>
                  <td></td>
                  <td width="150"></td>
              </tr>
			  <?php 
			  $filter;
			  history($database_tryconnection, $tryconnection, $filter);			  
      		  
			  if (!empty($row_HXBUFFER)) {
			  echo '<tr class="Verdana11Grey">
                  <td width="50" height="20"></td>
                  <td width="155" colspan="4">HISTORY BUFFER</td>
                  <td width="150" align="center"></td>
              </tr>';
			  echo '<tr class="Verdana11Grey">
                  <td width="50"></td>
                  <td width="155" colspan="4">'.$row_HXBUFFER['HXTEXT'].'</td>
                  <td width="150" align="center">'.$row_HXBUFFER['HXDOCTOR'].'</td>
              </tr>';
			  }
			  ?>
			  </table>
      		  </div>            </td>
      </tr>
          <tr height="250">
            <td align="center" valign="middle">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="3" align="left" class="Labels2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Heading: 
                  
                  
                  <label><input type="radio" name="heading" value="41" <?php if (!empty($row_HXBUFFER) && $row_HXBUFFER['HXHEADING']=='41'){echo "checked='checked'";}?>/>
                  Procedures</label>&nbsp;&nbsp;
                  
                  <label><input name="heading" type="radio" value="21" <?php if (!empty($row_HXBUFFER) && $row_HXBUFFER['HXHEADING']=='21'){echo "checked='checked'";}?>/>
                  Imagery</label>&nbsp;&nbsp;

                  <label><input name="heading" type="radio" value="51" <?php if (!empty($row_HXBUFFER) && $row_HXBUFFER['HXHEADING']=='51'){echo "checked='checked'";}?>/>
                  Laboratory</label>&nbsp;&nbsp;

                  <label><input type="radio" name="heading" value="31" <?php if (!empty($row_HXBUFFER)) {if ($row_HXBUFFER['HXHEADING']=='31') {echo "checked='checked'";}} else if (isset($_SESSION['path'])){echo "checked='checked'";}?>/>
                  Duty Log</label>&nbsp;&nbsp;
                  
                  <label><input type="radio" name="heading" value="81" <?php if (!empty($row_HXBUFFER) && $row_HXBUFFER['HXHEADING']=='81'){echo "checked='checked'";}?>/>
                  Client Communication</label>
                                 
                  <label><input name="heading" type="radio" value="91" <?php if (!empty($row_HXBUFFER)) {if ($row_HXBUFFER['HXHEADING']=='91') {echo "checked='checked'";}} else if (!isset($_SESSION['path'])){echo "checked='checked'";}?>/>
                  Other</label>&nbsp;&nbsp;
                  
                  <input name="input" type="button" value="Use Template" class="hidden"/>                  </td>
                </tr> 
          <tr>
      <td colspan="3" align="center"><textarea name="treatdesc" cols="90" rows="10" class="commentarea" id="treatdesc"><?php if (!empty($row_HXBUFFER)) {echo $row_HXBUFFER['HXTEXT'];} ?></textarea></td>
    </tr>
                <tr height="30">
                  <td colspan="3" align="left" class="Labels2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Display in: 
                    <?php do{
			echo "<label style='white-space:nowrap;'><input name='filter[]' type='checkbox' id='checkbox' value='".$row_HXFILTER['HXCAT']."'";
			
			if (!empty($row_HXBUFFER)){
				$hxfilter = explode(",",$row_HXBUFFER['HXFILTER']);
				if (in_array($row_HXFILTER['HXCAT'], $hxfilter))
				{echo "checked";} 
			}
			
			else if ($row_HXFILTER['HXCNAME']=='Other') {echo "checked";}		
			echo " />".$row_HXFILTER['HXCNAME']."</label>&nbsp;";
					} while ($row_HXFILTER = mysqli_fetch_assoc($HXFILTER));
					?>                    </td>
    </tr>
                <tr height="20" style="white-space:nowrap;">
                  <td width="100" class="Labels2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Efective Date</td>
      <td width="" align="left" valign="middle" class="Labels2">
      <input name="treatdate" type="text" class="Input" id="treatdate" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo date("m/d/Y"); ?>" onclick="ds_sh(this)" /></td>
      <td align="right" class="Labels2">
        <select name="who">
        <option></option>
            <?php do { ?>
		<option value="<?php echo $row_DOCTOR['DOCTOR']; ?>"><?php echo $row_DOCTOR['DOCTOR']; ?></option>
<?php } while ($row_DOCTOR = mysqli_fetch_assoc($DOCTOR)); ?>
            <?php do { ?>
        <option value="<?php echo $row_STAFF['STAFF']; ?>"><?php echo $row_STAFF['STAFF']; ?></option>
            <?php } while ($row_STAFF = mysqli_fetch_assoc($STAFF)); ?>
        </select>        </td>
      </tr>
  </table>        </td>
      </tr>
        </table>    </td>
    </tr>
    <tr>
      <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
        <input name="save" class="button" type="submit" value="SAVE"/>
        <input name="finish" class="button" type="submit" value="FINISH"/>
        <input name="clear" class="button" type="button" value="CLEAR" title="Click to clear the blue box (the buffered history will NOT be deleted)" onclick="clearbuffer();"/>
        <input name="cancel" class="button" type="button" value="<?php if ($_GET['path']=='2close') {echo "CLOSE";} else {echo "CANCEL";} ?>" onclick="<?php if ($_GET['path']=='2close') {echo "self.close();";} else if ($_GET['path']=='procmenu') {echo "history.back()";} else if ($_GET['path']=='procmenu2') {echo "history.go(-2)";} else {echo "history.go(-3)";} ?>"/>
        <input type="hidden" name="check" value="1"/>
        <script type="text/javascript">//document.write(opener.document.location);</script>        </td>
    </tr>
  </table>
  </form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
