<?php
session_start();
require_once('../tryconnection.php');


if (isset($_GET['refID'])){
$table=DOCTOR;
$order=DOCTOR;

mysql_select_db($database_tryconnection, $tryconnection);
$query_ORIGINALD = "SELECT PERSONID, SIGNEDIN, PRIORITY FROM DOCTOR ORDER BY PRIORITY";
$ORIGINALD = mysql_query($query_ORIGINALD, $tryconnection) or die(mysql_error());
$row_ORIGINALD = mysqli_fetch_assoc($ORIGINALD);

$query_ORIGINALS = "SELECT PERSONID, SIGNEDIN,PRIORITY FROM STAFF ORDER BY PRIORITY";
$ORIGINALS = mysql_query($query_ORIGINALS, $tryconnection) or die(mysql_error());
$row_ORIGINALS = mysqli_fetch_assoc($ORIGINALS);

do {
$key=$row_ORIGINALD['PERSONID'];
$_SESSION['originald'][$key]=$row_ORIGINALD['SIGNEDIN']; 
} 
while ($row_ORIGINALD=mysqli_fetch_assoc($ORIGINALD));

do {
$key=$row_ORIGINALS['PERSONID'];
$_SESSION['originals'][$key]=$row_ORIGINALS['SIGNEDIN']; 
} 
while ($row_ORIGINALS=mysqli_fetch_assoc($ORIGINALS));
}


elseif (isset($_GET['bstaff']))
{
$_SESSION['button']=STAFF;
}
elseif (isset($_GET['bdoctors']) || !isset($_SESSION['button']))
{
$_SESSION['button']=DOCTOR;
}

/////////////////////////////////////////////////////

if ($_SESSION['button']==STAFF)
{
$_SESSION['staffordoc']=STAFF;
$_SESSION['table']=STAFF;
$table=$_SESSION['table'];
$order=$_SESSION['table'];
}

elseif ($_SESSION['button']==DOCTOR)
{
$_SESSION['staffordoc']=DOCTOR;
$_SESSION['table']=DOCTOR;
$table=$_SESSION['table'];
$order=$_SESSION['table'];
}

/////////////////////////////////////////////////////
$person=$_SESSION['staffordoc'];
if(!isset($_SESSION['staffordoc']))
{
$person=DOCTOR;
}


mysql_select_db($database_tryconnection, $tryconnection);
$query_STAFF = "SELECT * FROM ".$table." ORDER BY PRIORITY ASC";
$STAFF = mysql_query($query_STAFF, $tryconnection) or die(mysql_error());
$row_STAFF = mysqli_fetch_assoc($STAFF);
$totalRows_STAFF = mysqli_num_rows($STAFF);


$query_SIGNEDIN = "SELECT * FROM ".$table." WHERE SIGNEDIN='1' ORDER BY PRIORITY ASC";
$SIGNEDIN= mysql_query($query_SIGNEDIN, $tryconnection) or die(mysql_error());
$row_SIGNEDIN = mysqli_fetch_assoc($SIGNEDIN);
$totalRows_SIGNEDIN = mysqli_num_rows($SIGNEDIN);

//SIGN IN
if (isset($_GET['add'])){
$id=$_GET['add'];
$query_ADD = "UPDATE ".$table." SET SIGNEDIN=1 WHERE PERSONID='$id'";
$ADD= mysql_query($query_ADD, $tryconnection) or die(mysql_error());
header("Location: STAFF_SIGN_IN_OUT.php");
}

//SIGN OUT
if (isset($_GET['remove'])){
$id=$_GET['remove']-100;
$query_REMOVE = "UPDATE ".$table." SET SIGNEDIN=0 WHERE PERSONID='$id'";
$REMOVE= mysql_query($query_REMOVE, $tryconnection) or die(mysql_error());
header("Location: STAFF_SIGN_IN_OUT.php");
}

//REVERT CHANGES
if (isset($_GET['revert']) || isset($_GET['cancel'])){
foreach ($_SESSION['originald'] as $key => $value){
$query_CANCELD = "UPDATE DOCTOR SET SIGNEDIN='$value' WHERE PERSONID='$key'";
$CANCELD= mysql_query($query_CANCELD, $tryconnection) or die(mysql_error());
}
foreach ($_SESSION['originals'] as $key => $values){
$query_CANCELS = "UPDATE STAFF SET SIGNEDIN='$values' WHERE PERSONID='$key'";
$CANCELS= mysql_query($query_CANCELS, $tryconnection) or die(mysql_error());
}
header("Location: STAFF_SIGN_IN_OUT.php");
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>STAFF SIGN IN/OUT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function RemovePerson(x)
{
self.location="STAFF_SIGN_IN_OUT.php?remove=" + x;
}

function AddPerson(x)
{
self.location="STAFF_SIGN_IN_OUT.php?add=" + x;
}



function OnMouseOver(x){
document.getElementById(x).style.cursor='pointer';
document.getElementById(x).bgColor='#DCF6DD';
}

function OnMouseOut(x){
document.getElementById(x).bgColor='#FFFFFF';
}

</script>


<style type="text/css">
<!--
.table {
	border-color: #FFFFFF;
	border-style: ridge;
	border-width: 3px;
	border-collapse: separate;
	border-spacing: 1px;
	background-color:#FFFFFF;
}
.SelectList {
	width: 100%;
	height: 100%;
	font-family: "Andale Mono";
	font-size: 13px;
	border-width: 0px;
	padding: 5 px;
	outline-width: 0px;
}

.CustomizedButton {
	font-family: Verdana;
	font-size: 20px;
	width: 150px;
	height: 27px;
	margin-left: 5px;
	margin-right: 5px;
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><?php // if (empty($_SESSION['fileused'])){echo"&nbsp;"; } else {echo substr($_SESSION['fileused'],0,25);}  ?>
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<?php 
?>



<form action="" target="list" method="get" name="staff_sign">
<table width="100%" height="553" border="0" cellpadding="0" cellspacing="0" bgcolor="#B1B4FF">
  <tr height="10">
    <td width="65" height="70" align="left" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
    <td width="250" height="70" align="left" valign="top" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
    <td width="100" rowspan="3" align="left" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
    <td width="250" height="70" align="right" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
    <td width="65" height="70" align="right" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
  </tr>
  <tr height="10">
    <td width="65" align="left" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
      <td width="250" align="center" bgcolor="#000000" class="Verdana11Bwhite"><?php if ($person=='DOCTOR'){echo "Doctors";} else {echo "Staff";} ?> to sign in</td>
      <td width="250" align="center" bgcolor="#000000" class="Verdana11Bwhite"><?php if ($person=='DOCTOR'){echo "Doctors";} else {echo "Staff";} ?> signed in </td>
      <td width="65" align="right" bgcolor="#B1B4FF" class="Verdana11Bwhite">&nbsp;</td>
  </tr>
      <tr>
        <td width="65" align="left" valign="middle">&nbsp;</td>
      <td width="250" align="left" valign="top">
      
      <table height="80%" width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
         <?php do { ?>
  <tr height="10" id="<?php echo $row_STAFF['PERSONID']; ?>" onmouseover="OnMouseOver(this.id)" onmouseout="document.getElementById(this.id).bgColor='#FFFFFF';" title="Click to sign in" onclick="AddPerson(this.id);">
    <td align="left" valign="bottom" class="Andale13noDecor"><?php echo $row_STAFF[$person]; ?></td>
    <td width="25" align="center" class="Andale14B" style="color: #446441;"><?php if ($row_STAFF['SIGNEDIN']=="0"){echo ">>";} ?></td>
  </tr>
          <?php } while ($row_STAFF = mysqli_fetch_assoc($STAFF)); ?>
<tr><td></td>
  <td></td>
</tr>
</table>
</td>
      <td width="250" align="right" valign="top">
      <table height="80%" width="100%" border="0" cellpadding="0" cellspacing="0" class="table">		
       
        <?php do { ?>
  <tr id="<?php echo ($row_SIGNEDIN['PERSONID']+100); ?>" onmouseover="OnMouseOver(this.id)" onmouseout="document.getElementById(this.id).bgColor='#FFFFFF';" title="Click to sign out" onclick="RemovePerson(this.id);">
    <td height="10" align="left" valign="bottom" class="Andale13noDecor"><?php echo $row_SIGNEDIN[$person]; ?></td>
    <td width="15" align="center" class="Andale14B" style="color:#CC0033;">X</td>
  </tr>
          <?php } while ($row_SIGNEDIN = mysqli_fetch_assoc($SIGNEDIN)); ?> 
<tr><td></td>
  <td></td>
</tr>
</table>      </td>
      <td width="65" align="right">&nbsp;</td>
      </tr>
    <tr>
     	<td height="35" align="center" valign="middle" class="ButtonsTable" colspan="6">
     	  <input name="save" type="button" class="button" id="button" value="SAVE" onclick="self.location='../INDEX.php'" title="Click to save" >
     	  <input name="bdoctors" type="button" class="button" id="button2" value="DOCTORS" onclick="self.location='STAFF_SIGN_IN_OUT.php?bdoctors='" title="Click to display doctors" />
     	  <input name="bstaff" type="button" class="button" id="button4" value="STAFF" onclick="self.location='STAFF_SIGN_IN_OUT.php?bstaff='" title="Click to display staff" />
         <input name="revert" type="button" class="button" id="revert" value="REVERT" onclick="self.location='STAFF_SIGN_IN_OUT.php?revert='" title="Click to revert changes" />
         <input name="cancel" type="button" class="button" id="cancel" value="CANCEL" onclick="self.location='../INDEX.php'"/>     	</td>
     </tr>
  </table>
</form>



<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
