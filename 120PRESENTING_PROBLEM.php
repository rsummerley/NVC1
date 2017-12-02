<?php
session_start();

//unset($_SESSION['']);
require_once('../tryconnection.php');
include("../ASSETS/age.php");
$timeformat=$_SESSION['timeformat'];
$recepid=$_GET['recepid'];

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$client=$_GET['client'];
}

mysql_select_db($database_tryconnection, $tryconnection);
$query_RECEP = "SELECT *, DATE_FORMAT(DATEIN, '%m/%d/%Y') AS DATEIN, DATE_FORMAT(TIME, '%H:%i') AS TIME FROM RECEP WHERE RECEPID='$recepid' OR RFPETID='$patient' ORDER BY DATETIME DESC LIMIT 1";
$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
$row_RECEP = mysqli_fetch_assoc($RECEP);

$patient=$row_RECEP['RFPETID'];
$client=$row_RECEP['CUSTNO'];

include("../ASSETS/photo_directory.php");







if ((isset($_POST['save']) || isset($_POST['record'])) && ($recepid=="0" || empty($row_RECEP))){
$query_insertSQL="INSERT INTO RECEP (CUSTNO, NAME, RFPETID, PETNAME, PSEX, RFPETTYPE, LOCATION, DESCRIP, FNAME, PROBLEM, AREA1, PH1, AREA2, PH2, AREA3, PH3, DATEIN, TIME, DATETIME, CLINICIAN) VALUES ('$row_PATIENT_CLIENT[CUSTNO]', '".mysql_real_escape_string($row_PATIENT_CLIENT['COMPANY'])."', '$row_PATIENT_CLIENT[PETID]', '".mysql_real_escape_string($row_PATIENT_CLIENT['PETNAME'])."', '$row_PATIENT_CLIENT[PSEX]', '$_POST[rfpettype]', '$_POST[location]', '".mysql_real_escape_string($row_PATIENT_CLIENT['PETBREED'])."','$row_PATIENT_CLIENT[CONTACT]','".mysql_real_escape_string($_POST['problem'])."','$row_PATIENT_CLIENT[AREA]','$row_PATIENT_CLIENT[PHONE]','$row_PATIENT_CLIENT[CAREA2]','$row_PATIENT_CLIENT[PHONE2]','$row_PATIENT_CLIENT[CAREA3]','$row_PATIENT_CLIENT[PHONE3]',STR_TO_DATE('$_POST[datein]','%m/%d/%Y'),STR_TO_DATE('$time','H:i'),NOW(), '".mysql_real_escape_string($_POST['clinician'])."')";
$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());

$updateSQL = "UPDATE PETMAST SET PWEIGHT='$_POST[pweight]' WHERE PETID='$patient' LIMIT 1";
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());

$winback="window.open('RECEPTION_FILE.php','_self');";
}

else if ((isset($_POST['save']) || isset($_POST['record'])) && $recepid!="0"){
$query_insertSQL="UPDATE RECEP SET LOCATION='2', PROBLEM='".mysql_real_escape_string($_POST['problem'])."', DATEIN=STR_TO_DATE('$_POST[datein]','%m/%d/%Y'), TIME=STR_TO_DATE('$time','H:i'), CLINICIAN='".mysql_real_escape_string($_POST['clinician'])."' WHERE RECEPID='$recepid' LIMIT 1";
$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());

$updateSQL = "UPDATE PETMAST SET PWEIGHT='$_POST[pweight]' WHERE PETID='$patient' LIMIT 1";
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());

$winback="history.go(-2);";
}

else if (!empty($row_RECEP) && (isset($_POST['save']) || isset($_POST['record']))) {
//move to discharged within the reception file
$query_admit="UPDATE RECEP SET PROBLEM='".mysql_real_escape_string($_POST['problem'])."' WHERE RFPETID='$patient' LIMIT 1";
$admit=mysql_query($query_admit,$tryconnection) or die(mysql_error());

$updateSQL = "UPDATE PETMAST SET PWEIGHT='$_POST[pweight]' WHERE PETID='$patient' LIMIT 1";
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());

}

//$query_RECEP = "SELECT * FROM RECEP WHERE RFPETID='$patient'";
//$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
//$row_RECEP = mysql_fetch_assoc($RECEP);

//if (empty($row_RECEP) && (isset($_POST['save']) || isset($_POST['record']))){
//$query_insertSQL="INSERT INTO RECEP (CUSTNO, NAME, RFPETID, PETNAME, PSEX, RFPETTYPE, LOCATION, DESCRIP, FNAME, PROBLEM, DATEIN, TIME, DATETIME, CLINICIAN) VALUES ('$client', '".mysql_real_escape_string($row_PATIENT_CLIENT['COMPANY'])."', '$patient', '$row_PATIENT_CLIENT[PETNAME]', '$row_PATIENT_CLIENT[PSEX]', '$row_PATIENT_CLIENT[PETTYPE]', '$_POST[location]', '$row_PATIENT_CLIENT[PETBREED]','$row_PATIENT_CLIENT[CONTACT]','".mysql_real_escape_string($_POST['problem'])."',STR_TO_DATE('$_POST[datein]','%m/%d/%Y'),STR_TO_DATE('$time','H:i'),NOW(), '".mysql_real_escape_string($_POST['clinician'])."')";
//$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());
//}


if (isset($_POST['record'])){

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysqli_fetch_assoc($PREFER);

$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

	$query_CHECKTABLE="SELECT * FROM $treatmxx limit 1";
	$CHECKTABLE= mysql_query($query_CHECKTABLE, $tryconnection) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE TREATM0";
	$result=mysql_query($create_TREATMXX, $tryconnection) or die(mysql_error());
	}
	//create the CLIENT COMMUNICATION heading
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$client', '$patient', 'EXAM', 16384, '81', NOW(), '".mysql_real_escape_string($_POST['clinician'])."')";
	mysql_query($insertSQL, $tryconnection) or die(mysql_error());
	
			$note=array();
	
			if (strlen($_POST['problem']) > 230){
			$howmany=ceil(strlen($_POST['problem'])/230);
				for ($i=0; $i<($howmany*230); $i=($i/230+1)*230){
				$note[]=substr($_POST['problem'],$i,230);
				}
			}
			else {
				$note[]=$_POST['problem'];
			}
			
			foreach ($note as $note2){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client', '$patient', '".mysql_real_escape_string($note2)."', 16384, '82', '".mysql_real_escape_string($_POST['clinician'])."', NOW())";
			mysql_query($insertSQL, $tryconnection) or die(mysql_error());
			}//foreach (($note as $note2)

}


$query_STAFF = sprintf("SELECT STAFF, STAFFINIT FROM STAFF WHERE SIGNEDIN=1 ORDER BY STAFF ASC");
$STAFF = mysql_query($query_STAFF, $tryconnection) or die(mysql_error());
$row_STAFF = mysqli_fetch_assoc($STAFF);

$query_Doctor = "SELECT * FROM DOCTOR WHERE SIGNEDIN=1 AND INSTR(DOCTOR,'DVM') <> 0";
$Doctor = mysql_query($query_Doctor, $tryconnection) or die(mysql_error());
$row_Doctor = mysqli_fetch_assoc($Doctor);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PRESENTING PROBLEM</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
function bodyonload(){
<?php echo $winback; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
document.pres_problem.problem.focus();	
}

function bodyonunload(){
//sessionStorage.removeItem('refID');
sessionStorage.setItem('refID','PROCESSING MENU');
}


function checknames()
{
valid = true;
var clinician=document.pres_problem.clinician;
if (document.pres_problem.clinician.options[0].selected==true){
alert ('Please enter your name.');
valid = false;
}
return valid;
}


function countchar(){
var chars=document.forms[0].problem.value.length;
document.getElementById('maxnum').innerText=chars;
	if (chars>500){
	alert('I am sorry, but your text is too long. It\'s not my fault.');
	document.forms[0].problem.value=document.forms[0].problem.value.substr(0,499);	
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
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../ASSETS/calendar.js"></script>
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
                <li><a href="#" onclick="searchpatient()">Tattoo Numbers</a></li>
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

<form action="" class="FormDisplay" name="pres_problem" method="post" onsubmit="return checknames();">
<input type="hidden" name="rfpettype" value="<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>" />
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td height="60" colspan="3" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <?php echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY']; ?>
        <!--        <script type="text/javascript">document.write(sessionStorage.custname);</script>-->        
		</span>
        </td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <?php echo $custterm; ?>
        <!--<script type="text/javascript">document.write(sessionStorage.custterm);</script>-->          
        </span>
        </td>
        <td width="19%" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2"> 
                    <?php echo $row_PATIENT_CLIENT['BALANCE']; ?>       
					<!--<script type="text/javascript">document.write(sessionStorage.custprevbal);</script>--></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <?php echo $row_PATIENT_CLIENT['BALANCE']; ?>
                    <!--<script type="text/javascript">document.write(sessionStorage.custcurbal);</script>--></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<?php echo $row_PATIENT_CLIENT['AREA'].'-'.$row_PATIENT_CLIENT['PHONE'].', '.$row_PATIENT_CLIENT['CAREA2'].'-'.$row_PATIENT_CLIENT['PHONE2'].', '.$row_PATIENT_CLIENT['CAREA3'].'-'.$row_PATIENT_CLIENT['PHONE3'].', '.$row_PATIENT_CLIENT['CAREA4'].'-'.$row_PATIENT_CLIENT['PHONE4']; ?>
		<!--<script type="text/javascript">document.write(sessionStorage.custphone);</script>--></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        
<?php  echo $pettype.', '.$row_PATIENT_CLIENT['PETBREED'];?>
<!--<script type="text/javascript">document.write(sessionStorage.desco);</script>-->
         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <?php echo  $desct; ?>
        <!--<script type="text/javascript">document.write(sessionStorage.desct);</script>--> (<?php agecalculation($tryconnection,$pdob); ?>)
		</td>
      </tr>
    </table>    </td>
    </tr>
  <tr>
    <td height="" colspan="3" align="center" valign="top">
    
    
    <table class="table" width="733" height="457" border="1" cellpadding="0" cellspacing="0" >
    <tr>
    <td align="center"><table width="85%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="#FFFFFF" frame="box" rules="none">
      <tr>
        <td width="2" align="left" class="Verdana11B">&nbsp;</td>
        <td width="256" height="40" align="left" valign="middle" class="Verdana11B">Please enter the admitting information:</td>
        <td width="97" height="40" align="right" valign="middle">Date:</td>
        <td width="101" height="40" align="center" valign="middle"><input name="datein" type="text" class="Input" id="datein" size="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if (!empty($row_RECEP['DATEIN'])) {echo $row_RECEP['DATEIN'];} else {echo date('m/d/Y');} ?>" onclick="ds_sh(this <?php if ($recepid!="0"){echo ",'".substr($row_RECEP['DATEIN'],0,2)."','".substr($row_RECEP['DATEIN'],3,2)."','".substr($row_RECEP['DATEIN'],6,4)."'";}?>);"/></td>
        <td width="69" height="40" align="right" valign="middle">Time:</td>
        <td width="74" height="40" align="center" valign="middle"><input name="time" type="text" class="Input" id="time" size="5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if ($recepid=="0") {echo date('H:i');} else {echo $row_RECEP['TIME'];} ?>"/></td>
        <td width="51" height="40" valign="middle">&nbsp;</td>
      </tr>
      <tr>
        <td width="2" height="120" align="left">&nbsp;</td>
		<td height="120" colspan="6" align="center" class="Verdana11Grey"><textarea name="problem" cols="80" rows="5" class="commentarea" id="textarea" onkeyup="countchar()"><?php echo $row_RECEP['PROBLEM']; ?></textarea><br  /># of characters: <span id="maxnum"></span> (max 500)</td>
      </tr>
      <tr>
        <td width="2" align="center">&nbsp;</td>
		<td height="80" colspan="6" align="center">
        
        <table width="95%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333" frame="box" rules="none">
          <tr <?php if (!empty($row_RECEP)) {echo "class='hidden'";} ?>>
            <td class="Verdana11B">&nbsp;</td>
            <td height="30" class="Verdana11B">Location:</td>
            <td height="30"><label>
              <input type="radio" name="location" value="1" <?php if($row_RECEP['LOCATION']=="1" || empty($row_RECEP['LOCATION'])) {echo "checked";} ?> />Waiting
            </label></td>
            <td height="30"><label>
            <input type="radio" name="location" value="2" <?php if($row_RECEP['LOCATION']=="2") {echo "checked";} ?>/>Exam </label></td>
            <td height="30"><label class="hidden">
            <input type="radio" name="location" value="" />Treatment</label>              <label class="hidden">
            <input type="radio" name="location" value="" />Surgery</label>            <label class="hidden">
            <input type="radio" name="location" value="" />Recovery</label><label>
              <input type="radio" name="location" id="checkbox" <?php if($row_RECEP['LOCATION']=="3") {echo "checked";} ?> value="3"/>
              Ready for Discharge</label>            <label class="hidden">
            <input type="radio" name="location" value="" />Ward</label></td>
            </tr>
          <tr class="hidden">
            <td class="Verdana11B">&nbsp;</td>
            <td height="30" class="Verdana11B">Triage:</td>
            <td height="30"><label>
            <input type="radio" name="triage" value="1" <?php if($row_RECEP['TRIAGE']=="1") {echo "checked";} ?>/>Normal </label></td>
            <td height="30"><label>
            <input type="radio" name="triage" value="2" <?php if($row_RECEP['TRIAGE']=="2") {echo "checked";} ?>/>Serious</label></td>
            <td height="30"><label>
            <input type="radio" name="triage" value="3" <?php if($row_RECEP['TRIAGE']=="3") {echo "checked";} ?>/>Crisis</label></td>
            </tr>
        </table>        </td>
      </tr>
      <tr>
        <td width="2">&nbsp;</td>
        <td height="30" align="right"><label class="hidden">
        <input type="checkbox" name="checkbox2" id="checkbox2" /> 
        Add another patient for this client
</label>
          Enter weight:&nbsp;</td>
        <td height="30" align="left">
        <input name="pweight" type="text" class="Inputright" id="pweight" size="5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT_CLIENT['PWEIGHT']; ?>"/>
       <script type="text/javascript">document.write(localStorage.weightunit);</script>
        </td>
        <td height="30" align="right">Staff&nbsp;&nbsp;</td>
        <td height="30" colspan="3">
<select name="clinician" id="clinician">
			<option value="&nbsp;"></option>
<?php do {  
      echo '<option value="'.$row_Doctor['DOCTOR'].'">'.$row_Doctor['DOCTOR'].'</option>';
} while ($row_Doctor = mysqli_fetch_assoc($Doctor)); ?>
			<?php 
            do { ?>
            <option value="<?php echo $row_STAFF['STAFF']; ?>"><?php echo $row_STAFF['STAFF']; ?></option>
            <?php } while ($row_STAFF = mysqli_fetch_assoc($STAFF)); ?>
        </select>        </td>
        </tr>
    </table></td>
    </tr>
    </table>    </td>
  </tr>
  <tr>
    <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
     <input name="save" class="button" type="submit" value="SAVE"/>
     <input name="record" class="button" type="submit" value="RECORD" title="Record this into medical history."/>
     <input name="label" class="hidden" type="button" value="LABEL"/>
     <input name="cancel" class="button" type="reset" value="CANCEL" onclick="history.back();"/>
    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>