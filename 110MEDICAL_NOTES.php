<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);
//$totalRows_PATIENT_CLIENT = mysql_num_rows($PATIENT_CLIENT);


////////////////////// PRESENTING PROBLEM ////////////////////////////////
$query_RECEP = "SELECT RECEPID, PROBLEM, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE RFPETID='$patient'";
$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
$row_RECEP = mysql_fetch_assoc($RECEP);


$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

//$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR ORDER BY DOCTOR ASC");
//$DOCTOR = mysql_query($query_DOCTOR, $tryconnection) or die(mysql_error());
//$row_DOCTOR = mysql_fetch_assoc($DOCTOR);



//include("../../ASSETS/history.php");

$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysql_query($select_MEDNOTE, $tryconnection) or die(mysql_error());
$row_MEDNOTE = mysql_fetch_assoc($select_MEDNOTE);


//INSERT INTO RECEP IF NOT INSERTED YET
if (isset($_POST['check']) && empty($row_RECEP['PROBLEM'])){
$insert_RECEP="INSERT INTO RECEP (CUSTNO, NAME, RFPETID, PETNAME, PSEX, RFPETTYPE, LOCATION, DESCRIP, FNAME, PROBLEM, AREA1, PH1, AREA2, PH2, AREA3, PH3, DATEIN, TIME, DATETIME) VALUES ('$client', '".mysql_real_escape_string($row_PATIENT_CLIENT['COMPANY'])."', '$patient', '".mysql_real_escape_string($row_PATIENT_CLIENT['PETNAME'])."', '$psex', '$row_PATIENT_CLIENT[PETTYPE]', '2', '$row_PATIENT_CLIENT[PETBREED]','$row_PATIENT_CLIENT[CONTACT]','".mysql_real_escape_string($_POST['nproblem'])."', '$row_PATIENT_CLIENT[AREA]','$row_PATIENT_CLIENT[PHONE]','$row_PATIENT_CLIENT[CAREA2]','$row_PATIENT_CLIENT[PHONE2]','$row_PATIENT_CLIENT[CAREA3]','$row_PATIENT_CLIENT[PHONE3]', NOW(), NOW(), NOW())";
$insRECEP=mysql_query($insert_RECEP,$tryconnection) or die(mysql_error());
}

else if (isset($_POST['check']) && !empty($row_RECEP['PROBLEM'])){
$query_insertSQL="UPDATE RECEP SET PROBLEM='".mysql_real_escape_string($_POST['nproblem'])."', DATEIN=NOW(), TIME=NOW() WHERE RFPETID='$patient'";
$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());
}


if (isset($_POST['check']) && empty($row_MEDNOTE)){
$insert_MEDNOTE="INSERT INTO MEDNOTES (NCUSTNO, NPET, NPROBLEM, NDIAGNOSIS, NPROCEDURES, NCLINSTR, NCASESUM) VALUES ('$client', '$patient', '".mysql_real_escape_string($_POST['nproblem'])."', '".mysql_real_escape_string($_POST['ndiagnosis'])."', '".mysql_real_escape_string($_POST['nprocedures'])."', '".mysql_real_escape_string($_POST['nclinstr'])."', '".mysql_real_escape_string($_POST['ncasesum'])."')";
$MEDNOTE = mysql_query($insert_MEDNOTE, $tryconnection) or die(mysql_error());

if (isset($_POST['save'])){
header("Location:PROCESSING_MENU.php");
}
else if (isset($_POST['preview'])){
$openpreview="window.open('../../IMAGES/CUSTOM_DOCUMENTS/DISCHARGE_SHEET.php?preview=preview&dischgpetid=".$_SESSION['patient']."','_blank'); document.location='MEDICAL_NOTES.php';";
}
}


else if (isset($_POST['check']) && !empty($row_MEDNOTE)){
$update_MEDNOTE="UPDATE MEDNOTES SET NPROBLEM='".mysql_real_escape_string($_POST['nproblem'])."', NDIAGNOSIS='".mysql_real_escape_string($_POST['ndiagnosis'])."', NPROCEDURES='".mysql_real_escape_string($_POST['nprocedures'])."', NCLINSTR='".mysql_real_escape_string($_POST['nclinstr'])."', NCASESUM='".mysql_real_escape_string($_POST['ncasesum'])."' WHERE NPET='$patient'";
$MEDNOTE = mysql_query($update_MEDNOTE, $tryconnection) or die(mysql_error());

if (isset($_POST['save'])){
header("Location:PROCESSING_MENU.php");
}
else if (isset($_POST['preview'])){
$openpreview="window.open('../../IMAGES/CUSTOM_DOCUMENTS/DISCHARGE_SHEET.php?preview=preview&dischgpetid=".$_SESSION['patient']."','_blank'); document.location='MEDICAL_NOTES.php';";
}
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>MEDICAL NOTES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
function bodyonload(){
<?php echo $openpreview; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
var hxpreview=document.getElementById('hxpreview');
hxpreview.scrollTop = hxpreview.scrollHeight;
var xiframe=document.getElementById('xiframe');
xiframe.scrollTop = xiframe.scrollHeight;
document.getElementById('custname').value=sessionStorage.custname;
document.getElementById('petname').value=sessionStorage.petname;
}
</script>
<style type="text/css">
<!--
#table2 {	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
#apDiv1 {
	position:absolute;
	width:172px;
	height:22px;
	z-index:32768;
	left: 567px;
	top: 1px;
}
#apDiv2 {	position:absolute;
	width:172px;
	height:22px;
	z-index:32768;
	left: 2px;
	top: 2px;
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
  <form action="" name="review_history" method="post">
  <input type="hidden" name="custname" id="custname" value=""  />
  <input type="hidden" name="petname" id="petname" value=""  />
  <div id="apDiv2"> <span class="Verdana12B" style="background-color:#FFFF00">
    <script type="text/javascript">document.write(sessionStorage.custname);</script>
  </span> </div>
  <div id="apDiv1">
    <span class="Verdana12B" style="background-color:#FFFF00;">
    <script type="text/javascript">document.write(sessionStorage.petname);</script></span> &nbsp;<span class="Verdana11">(<script type="text/javascript">document.write(sessionStorage.desco.substr(0,6)); </script>, <script type="text/javascript">var desct = sessionStorage.desct.substr(1,1); if (desct == "(") {document.write(sessionStorage.desct.substr(0,4));} else {document.write(sessionStorage.desct.substr(0,1));}</script>)</span>
  </div>
    
  <table width="100%" height="532" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="22" align="center" valign="middle" class="Verdana12B">PRESENTING PROBLEM      </td>
      </tr>
    <tr>
      <td height="18" align="center" valign="middle" class="Verdana12B"><textarea name="nproblem" cols="90" rows="4" class="commentarea" id="nproblem"><?php if (!empty($row_RECEP['PROBLEM'])){ echo $row_RECEP['PROBLEM'];} else {echo $row_MEDNOTE['NPROBLEM'];} ?></textarea></td>
    </tr>
    <tr>
      <td height="20" align="center" valign="middle" class="Verdana12B">DIAGNOSIS</td>
    </tr>
    <tr>
      <td height="18" align="center" valign="middle" class="Verdana12B"><textarea name="ndiagnosis" cols="90" rows="4" class="commentarea" id="ndiagnosis"><?php echo $row_MEDNOTE['NDIAGNOSIS']; ?></textarea></td>
    </tr>
    <tr>
      <td height="20" align="center" valign="middle" class="Verdana12B">PROCEDURES</td>
    </tr>
    <tr>
      <td height="18" align="center" valign="middle" class="Verdana12B"><textarea name="nprocedures" cols="90" rows="6" class="commentarea" id="nprocedures"><?php echo $row_MEDNOTE['NPROCEDURES']; ?></textarea></td>
    </tr>
    <tr>
      <td height="20" align="center" valign="middle" class="Verdana12B">CLIENT INSTRUCTIONS</td>
    </tr>
    <tr>
      <td height="18" align="center" valign="middle" class="Verdana12B"><textarea name="nclinstr" cols="90" rows="5" class="commentarea" id="nclinstr"><?php echo $row_MEDNOTE['NCLINSTR']; ?></textarea></td>
    </tr>
    <tr>
      <td height="20" align="center" valign="middle" class="Verdana12B">CASE SUMMARY</td>
    </tr>
    <tr>
      <td height="90" align="center" valign="middle" class="Verdana12B"><textarea name="ncasesum" cols="90" rows="5" class="commentarea" id="ncasesum"><?php echo $row_MEDNOTE['NCASESUM']; ?></textarea></td>
    </tr>
    <tr>
      <td height="35" colspan="3" align="center" valign="middle" bgcolor="#B1B4FF">
        <input name="save" type="submit" class="button" id="save" onclick="window.open('','_self')" value="SAVE"/>
        <input name="preview" type="submit" class="button" id="preview" value="PREVIEW"/>
        <input name="cancel" class="button" type="reset" value="CANCEL" onclick="window.open('PROCESSING_MENU.php','_self')"/>
        <input type="hidden" name="check" value="1"/>        </td>
    </tr>
  </table>
  </form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>