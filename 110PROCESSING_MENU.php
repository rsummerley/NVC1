<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");


//this was set to a condition to first check a session (due to paging within clients), but then it doesnt see the GET variables, since the SESSION is always set up in this screen...I changed it back to check for the GET first, but it might have some other implications, so if there is a problem somewhere else, it needs a new logic.
if (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}
else if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}

if (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}
else if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}


mysql_select_db($database_tryconnection, $tryconnection);

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysql_fetch_assoc($PREFER);
$treatmxx=$_SESSION['client']/$row_PREFER['TRTMCOUNT'];
$treatmxx="PHOTO".floor($treatmxx);
$uploaddir = "$treatmxx"."/";
$uploadfile = $uploaddir . $_SESSION['patient'].".jpg";
////////////////////////////////// CLIENT ///////////////////////////////////

$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client' LIMIT 1";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysql_fetch_assoc($CLIENT);

$fileused="$row_CLIENT[TITLE] $row_CLIENT[CONTACT] $row_CLIENT[COMPANY]";
//$_SESSION['fileused']= $fileused;
//$_SESSION['filetype']= "C";


///////////////////////////////// PATIENT ////////////////////////////////////
$query_PATIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB, DATE_FORMAT(PDEADATE,'%m/%d/%Y') AS PDEADATE FROM PETMAST WHERE PETID = '$patient' LIMIT 1";
$PATIENT = mysql_query($query_PATIENT, $tryconnection) or die(mysql_error());
$row_PATIENT = mysql_fetch_assoc($PATIENT);
$_SESSION['pettype'] = $row_PATIENT['PETTYPE'] ;

function validity($mydate,$interv){
	if ($interv=='1' || $interv=='2' || $interv=='3'){
	$interv=$interv." year";
	}
	else if ($interv=='4' || $interv=='8'){
	$interv=$interv." weeks";	
	}
	else if ($interv=='6'){
	$interv=$interv." months";	
	}
	else {
	$interv="1 year";	
	}
	$mydate = strtotime($mydate." + ".$interv);
	$mydate = date('m/d/Y',$mydate);
	return $mydate;
}

//date and time stamp showing the last time the client file was modified in any way. It happens at invoicing, and during client file edits
$datetime=date("Y-m-d H:i:s");


////////////////////// PRESENTING PROBLEM ////////////////////////////////
$query_RECEP = "SELECT RECEPID, PROBLEM, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE RFPETID='$patient' limit 1";
$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
$row_RECEP = mysql_fetch_assoc($RECEP);

///////////////////////////// COMMENT ////////////////////////////////////////

if (isset($_POST['comment'])) {
$updateSQL = sprintf("UPDATE ARCUSTO SET COMMENT='%s' WHERE CUSTNO='%s' LIMIT 1",
                       mysql_real_escape_string($_POST['commentwrite']),
                       $client);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
header("Location: PROCESSING_MENU.php");
}

else if (isset($_POST['waiting'])){
	if (empty($row_RECEP)){
	$query_insertSQL="INSERT INTO RECEP (CUSTNO, NAME, RFPETID, PETNAME, PSEX, RFPETTYPE, LOCATION, DESCRIP, FNAME, AREA1, PH1, AREA2, PH2, AREA3, PH3, DATEIN, TIME, DATETIME) VALUES ('$client', '".mysql_real_escape_string($row_CLIENT['COMPANY'])."', '$patient', '".mysql_real_escape_string($row_PATIENT['PETNAME'])."', '$row_PATIENT[PSEX]', '$row_PATIENT[PETTYPE]', '1', '".mysql_real_escape_string($row_PATIENT['PETBREED'])."','".mysql_real_escape_string($row_CLIENT[CONTACT])."','$row_CLIENT[AREA]','$row_CLIENT[PHONE]','$row_CLIENT[CAREA2]','$row_CLIENT[PHONE2]','$row_CLIENT[CAREA3]','$row_CLIENT[PHONE3]', NOW(), NOW(), NOW())";
	$insertSQL=mysql_query($query_insertSQL,$tryconnection) or die(mysql_error());
header("Location: ../../RECEPTION/RECEPTION_FILE.php");
	}
	else {
	$query_waiting="UPDATE RECEP SET LOCATION='1' WHERE RFPETID='$_SESSION[patient]' LIMIT 1";
	$waiting=mysql_query($query_waiting,$tryconnection) or die(mysql_error());
header("Location: ../../RECEPTION/RECEPTION_FILE.php");
	}
}

else if (isset($_POST['admit'])){
$query_admit="UPDATE RECEP SET LOCATION='2' WHERE RFPETID='$_SESSION[patient]' LIMIT 1";
$admit=mysql_query($query_admit,$tryconnection) or die(mysql_error());
header("Location: ../../RECEPTION/RECEPTION_FILE.php");
}

else if (isset($_POST['discharge'])){
$query_discharge="UPDATE RECEP SET LOCATION='3' WHERE RFPETID='$_SESSION[patient]' LIMIT 1";
$discharge=mysql_query($query_discharge,$tryconnection) or die(mysql_error());
header("Location: ../../RECEPTION/RECEPTION_FILE.php");
}

//////////////////////////// SECOND INDEX & SECOND ADDRESS /////////////////////////////////////////

$custno=$row_CLIENT['CUSTNO'];

$query_SECINDEX = "SELECT * FROM SECINDEX WHERE CUSTNO = '$custno'";
$SECINDEX = mysql_query($query_SECINDEX, $tryconnection) or die(mysql_error());
$row_SECINDEX = mysql_fetch_assoc($SECINDEX);
$totalRows_SECINDEX = mysql_num_rows($SECINDEX);

$query_SECADDRESS = "SELECT * FROM SECADDRESS WHERE CUSTNO = '$custno'";
$SECADDRESS = mysql_query($query_SECADDRESS, $tryconnection) or die(mysql_error());
$row_SECADDRESS = mysql_fetch_assoc($SECADDRESS);
$totalRows_SECADDRESS = mysql_num_rows($SECADDRESS);

//////////////////////////// WEIGHT UNIT FROM CRITDATA /////////////////////////////////////////

$query_CRITDATA = "SELECT CRITDATA.WEIGHTUNIT FROM CRITDATA LIMIT 1";
$CRITDATA = mysql_query($query_CRITDATA, $tryconnection) or die(mysql_error());
$row_CRITDATA = mysql_fetch_assoc($CRITDATA);
$totalRows_CRITDATA = mysql_num_rows($CRITDATA);

//////////////////////// MEDNOTES //////////////////////////////

$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysql_query($select_MEDNOTE, $tryconnection) or die(mysql_error());
$row_MEDNOTE = mysql_fetch_assoc($select_MEDNOTE);

if (empty($row_MEDNOTE) && !empty($row_RECEP)) {
$setup_MEDNOTE = "INSERT INTO MEDNOTES (NCUSTNO,NPET,NDATE,NPROBLEM) VALUES ('$client','$patient', NOW(),'".mysql_real_escape_string($row_RECEP[PROBLEM])."')" ;
$fill_MEDNOTE = mysql_query($setup_MEDNOTE) or die(mysql_error()) ;
// and redo the mednote query.
$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysql_query($select_MEDNOTE, $tryconnection) or die(mysql_error());
$row_MEDNOTE = mysql_fetch_assoc($select_MEDNOTE);}
//////////////////////// INVOICE //////////////////////////////
$query_INVHOLD = "SELECT * FROM INVHOLD WHERE INVCUST='$client' AND (INVDESCR='GST' OR INVDESCR='HST' OR INVDESCR='PST' OR INVDESCR='TOTAL')";
$INVHOLD = mysql_query($query_INVHOLD, $tryconnection) or die(mysql_error());
$row_INVHOLD = mysql_fetch_assoc($INVHOLD);
$invhold=array();
$invhold[]=$row_INVHOLD['INVNO'];
do {
$invhold[]=$row_INVHOLD['INVTOT'];
} while ($row_INVHOLD = mysql_fetch_assoc($INVHOLD));

////////////////////////////////////////////////////////////////////////////////////////

mysql_select_db($database_tryconnection, $tryconnection);
$query_DLOG = "SELECT DLPETID FROM TICKLER";
$DLOG = mysql_query($query_DLOG, $tryconnection) or die(mysql_error());
$row_DLOG = mysql_fetch_assoc($DLOG);
$DLOGarray=array();
do {
$DLOGarray[]=$row_DLOG['DLPETID'];
}
while ($row_DLOG = mysql_fetch_assoc($DLOG));

/////////////////////////////PAGING WITHIN CLIENT FILES/////////////////////////
$query_VIEW="CREATE OR REPLACE VIEW CLIENTS AS SELECT DISTINCT CUSTNO FROM ARCUSTO ORDER BY COMPANY ASC";
$VIEW= mysql_query($query_VIEW, $tryconnection) or die(mysql_error());

$query_COMPANY="SELECT * FROM CLIENTS";
$COMPANY= mysql_query($query_COMPANY, $tryconnection) or die(mysql_error());
$row_COMPANY = mysql_fetch_assoc($COMPANY);

$ids= array();
do {
$ids[]=$row_COMPANY['CUSTNO'];
}
while ($row_COMPANY = mysql_fetch_assoc($COMPANY));

$key=array_search($row_CLIENT['CUSTNO'],$ids);

////////////////////////VIEW FROM DUTY LOG////////////////////////
$query_VIEWDL="CREATE OR REPLACE VIEW DLOG AS SELECT DLPETID FROM TICKLER";
$VIEWDL= mysql_query($query_VIEWDL, $tryconnection) or die(mysql_error());

$query_DLOG="SELECT * FROM DLOG";
$DLOG= mysql_query($query_DLOG, $tryconnection) or die(mysql_error());
$row_DLOG = mysql_fetch_assoc($DLOG);

$dls= array();
do {
$dls[]=$row_DLOG['DLPETID'];
}
while ($row_DLOG = mysql_fetch_assoc($DLOG));




////////////////OUTPATIENT SCREEN////////////////
mysql_select_db($database_tryconnection, $tryconnection);
$query_EXAM = "SELECT * FROM REPORTCD WHERE TSPECIES = '$row_PATIENT[PETTYPE]' ORDER BY TCATGRY,TNO";
$EXAM = mysql_query($query_EXAM, $tryconnection) or die(mysql_error());
$row_EXAM = mysql_fetch_assoc($EXAM);
$totalRows_EXAM = mysql_num_rows($EXAM);

if (isset($_POST['exambegin'])){

//check if there is a record in EXAMHOLDs
$query_EXAMHOLD2 = "SELECT * FROM EXAMHOLD2 WHERE PETNO = $patient";
$EXAMHOLD2 = mysql_query($query_EXAMHOLD2, $tryconnection) or die(mysql_error());
$row_EXAMHOLD2 = mysql_fetch_assoc($EXAMHOLD2);

	if (empty($row_EXAMHOLD2)){
	$insertEX = sprintf("INSERT INTO EXAMHOLD2 (CUSTNO, PETNO, EXAMTIME, TSPECIES) VALUES ('%s', '%s', NOW(), '%s')", mysql_real_escape_string($row_PATIENT['CUSTNO']), $patient, $_GET['ref'], mysql_real_escape_string($row_PATIENT['PETTYPE']));
	$Result2 = mysql_query($insertEX, $tryconnection) or die(mysql_error());
	
	$insertSQL = sprintf("INSERT INTO EXAMHOLD (TID, CUSTNO, PETNO, EXAMTIME, EXAMTYPE, TREATDESC, TSPECIES, TCATGRY, TTYPE, TNO, TDESCR, TVAR1, TVAR, TMEMO, WEIGHT, TEMP, PULSE, RESPRATE, RESPCHAR, MUCOUSM, CRT, TNEWCOL, PULSENORM, ATTITUDE, HYDRATION, HYDRPC, BODYLIFE, BLSTATUS, BCS, PAS, DENTAL, TARTAR, GINGIVITIS, PD, NEEDSDENT, DACCEPTS, BOOK, DIET, DCURAMT, DCHANGE, DCHAMT, DIETACC) SELECT TID, '%s', '%s',  NOW(), EXAMTYPE, TREATDESC, '%s', TCATGRY, TTYPE, TNO, TDESCR, TVAR1, TVAR, TMEMO, WEIGHT, TEMP, PULSE, RESPRATE, RESPCHAR, MUCOUSM, CRT, TNEWCOL, PULSENORM, ATTITUDE, HYDRATION, HYDRPC, BODYLIFE, BLSTATUS, BCS, PAS, DENTAL, TARTAR, GINGIVITIS, PD, NEEDSDENT, DACCEPTS, BOOK, DIET, DCURAMT, DCHANGE, DCHAMT, DIETACC FROM REPORTCD WHERE TSPECIES = '$row_PATIENT[PETTYPE]' ORDER BY TCATGRY, TNO ASC", mysql_real_escape_string($row_PATIENT['CUSTNO']), $patient, $_GET['ref'],  mysql_real_escape_string($row_PATIENT['PETTYPE']));
	$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
	}

header("Location:OUT_PATIENT/OUT_PATIENT.php?ref=".$row_PATIENT['PETID']);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PROCESSING MENU</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.CustomizedButton1 {
	font-family: Verdana;
	font-size: 14px;
	width: 78px;
	height: 27px;
	margin:1px;
}

.CustomizedButton2 {
	font-family: Verdana;
	font-size: 14px;
	width: 70px;
	height: 27px;
	margin-left:1px;
	margin-right:1px;
}

.CustomizedButton3 {
	font-family: Verdana;
	font-size:14px;
	width: 100px;
}
.CustomizedButton11 {	font-family: Verdana;
	font-size: 14px;
	width: 80px;
	height: 27px;
}
.CustomizedButton21 {	font-family: Verdana;
	font-size: 14px;
	width: 60px;
	height: 27px;
}
.style1 {font-weight: bold}

-->
</style>


<script type="text/javascript">
setInterval("self.location.reload()", 6000000);
sessionStorage.setItem('filetype','P');

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
sessionStorage.setItem('filetype','P');
sessionStorage.removeItem('nproblem');
sessionStorage.removeItem('nplans');
}


function IntextOnFocus(x) {
	x.className=(x.className=="Andale13noDecor")?"Andale13noDecor2":"Andale13noDecor2";
}

function IntextOnBlur(x) {
	x.className=(x.className=="Andale13noDecor2")?"Andale13noDecor":"Andale13noDecor";
}

function outpatient()
{
//document.exam.submit();
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../../IMAGES/left_arrow_dark.JPG','../../IMAGES/right_arrow_dark.JPG')" onunload="bodyonunload()">
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
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="<?php echo "nav0();";//if ($row_CLIENT['LOCKED']=='1'){ echo "regnotallowed();";} else {echo "nav0();";} ?>">Regular Invoicing</a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="cancelinvoices()"><span class="">Cancel Invoices</span></a></li>
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
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/APPOINTMENTS/MONTH.php?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>','_blank', 'width=900, height=797, toolbar=no, status=no')"><span class="">Appointment Scheduling</span></a></li>
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
<form name="exam" method="post" action="" >

<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
  
  
  <tr>
    <td width="495" rowspan="2" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" >
      <tr>
        <td height="17" colspan="4" align="left" bgcolor="#FFFF00" class="Verdana12B"><?php echo $row_CLIENT['TITLE']; echo " ".$row_CLIENT['CONTACT']; echo " ".$row_CLIENT['COMPANY']; ?>
        <?php if ($row_CLIENT['INACTIVE']=='1') {echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>INACTIVE</span>";} ?>
                	<?php if ($row_CLIENT['TERMS']=='4') {echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>COLLECTION</span>";}  else if ($row_CLIENT['TERMS']=='2'){echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>CASH ONLY";} else if ($row_CLIENT['TERMS']=='3'){echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>NO CREDIT";}?> </td>
        <td height="17" colspan="2" align="right" bgcolor="#FFFF00" class="Verdana12B"> Client#: <?php echo $row_CLIENT['CUSTNO']; ?> </td>
      </tr>
      <tr align="left">
        <td height="20" colspan="4" class="Verdana11"><?php echo $row_CLIENT['ADDRESS1']; echo " ".$row_CLIENT['ADDRESS2']; ?> </td>
        <td rowspan="2" colspan="2" align="center" valign="middle"></td>
      </tr>
      <tr align="left">
        <td height="20" colspan="4" class="Verdana11"><?php echo $row_CLIENT['CITY']; echo ", ".$row_CLIENT['STATE']; echo ", ".$row_CLIENT['ZIP']; ?> <script type="text/javascript">//document.write(sessionStorage.filetype);</script></td>
      </tr>
      <tr align="left">
        <td width="63" height="17" align="left" class="Verdana11B">Home:</td>
        <td width="99" height="17" align="left" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA'].")".$row_CLIENT['PHONE']; ?></td>
        <td width="46" height="17" align="left" class="Verdana11B">Fax:</td>
        <td width="91" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA6'].")".$row_CLIENT['PHONE6']; ?></td>
        <td width="68" height="17" align="left" valign="middle" class="Verdana11B">Work1:</td>
        <td width="121" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA4'].")".$row_CLIENT['PHONE4']; ?></td>
      </tr>
      <tr align="left" class="Andale12noDecor">
        <td height="17" align="left" class="Verdana11B">Cell1:</td>
        <td width="99" height="17" align="left"class="Verdana11"><?php echo "(".$row_CLIENT['CAREA2'].")".$row_CLIENT['PHONE2']; ?></td>
        <td height="17" align="left" class="Verdana11B">Other:</td>
        <td width="91" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA7'].")".$row_CLIENT['PHONE7']; ?></td>
        <td width="68" height="17" align="left" valign="middle" class="Verdana11B">Work2:</td>
        <td width="121" height="17" rowspan="2" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA5'].")".$row_CLIENT['PHONE5']; ?></td>
      </tr>
      <tr align="left">
        <td height="17" rowspan="2" align="left" class="Verdana11B">Cell2:</td>
        <td width="99" height="17" rowspan="2" align="left"class="Verdana11"><?php echo "(".$row_CLIENT['CAREA3'].")".$row_CLIENT['PHONE3']; ?></td>
        <td height="17" rowspan="2" align="left" class="Verdana11B">Barn:</td>
        <td width="91" height="17" rowspan="2" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA8'].")".$row_CLIENT['PHONE8']; ?></td>
      </tr>
      <tr align="left">
        <td width="68" height="17" align="left"></td>
        <td colspan="2" rowspan="2" align="right"><a href="../../CLIENT/UPDATE_CLIENT.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>"><img src="../../IMAGES/e3 copy.jpg" alt="e" width="30" height="30" title="Click to edit client" /></a></td>
      </tr>
      <tr>
        <td height="17" colspan="5" align="left" class="Verdana11"><strong>Email:&nbsp;</strong><?php echo $row_CLIENT['EMAIL']; //if($row_CLIENT['REMINDERS']=="1"){echo "&bull;";}?></td>
      </tr>
      <!--PAGING-->
      <tr>
        <td height="35" align="left" valign="middle" bgcolor="#B1B4FF"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image6','','../../IMAGES/left_arrow_dark.JPG',1)" onclick="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php?client=<?php echo $ids[$key-1]; ?>'"><img src="../../IMAGES/left_arrow_light.JPG" alt="PREVIOUS" name="Image6" width="28" height="28" border="0" id="Image6" /></a><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image7','','../../IMAGES/right_arrow_dark.JPG',1)" style="margin-left:2px;" onclick="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php?client=<?php echo $ids[$key+1]; ?>'"><img src="../../IMAGES/right_arrow_light.JPG" alt="NEXT" name="Image7" width="28" height="28" border="0" id="Image7" /></a></td>
        <!--BUTTONS-->
        <td align="center" colspan="5" valign="middle" bgcolor="#B1B4FF"><input name="search"type="button" class="CustomizedButton11" id="search" value="CLIENTS" onclick="window.open('../../CLIENT/CLIENT_SEARCH_SCREEN.php?refID=<?php echo $_GET['refID']; ?>','_self')"/>
            <input name="LABEL2" type="button" class="CustomizedButton11" id="LABEL2" value="LABEL" onclick="window.open('../MAIL_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
            <input name="ENVELOPE"type="button" class="CustomizedButton11" id="ENVELOPE" value="ENVELOPE" disabled="disabled"/>
            <input name="CANCEL" type="button" class="CustomizedButton11" id="CANCEL" value="RECEP." onclick="sessionStorage.setItem('refID','PROCESSING MENU'); sessionStorage.setItem('filetype','0'); document.location='../../RECEPTION/RECEPTION_FILE.php'" style="width:83px;"/></td>
      </tr>
      <tr>
        <td colspan="6"><!--ACCORDION PANNELS-->
            <table width="100%" height="115" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><div id="Accordion1" class="Accordion" tabindex="0">
                    <div class="AccordionPanel">
                      <div class="AccordionPanelTab">Sticky Notes</div>
                      <!--<div class="AccordionPanelContent" ondblclick="WriteAlert()" title="Doubleclick to type the alert.">-->
                      <div class="AccordionPanelContent">
                        <?php
							echo "&nbsp;<span class='alerttext12'> ".$row_PATIENT['STICKIE']."&nbsp;</span>";
						?>
                      </div>
                    </div>
                  <div class="AccordionPanel">
                      <div class="AccordionPanelTab">Comment</div>
                    <div class="AccordionPanelContent" ondblclick="WriteComment()" title="Doubleclick to type the comment."> <span class="Verdana12" id="commentdisplay"><?php echo $row_CLIENT['COMMENT']; ?></span>
                          <textarea name="commentwrite" cols="65" rows="3" id="commentwrite" style="display:none"><?php echo $row_CLIENT['COMMENT']; ?></textarea>
                          <input type="submit" name="comment" id="comment" value="OK" style="display:none"/>
                      </div>
                  </div>
                  <div class="AccordionPanel">
                      <div class="AccordionPanelTab">Second Index</div>
                    <div class="AccordionPanelContent">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
                          <?php do { ?>
                          <tr class="Verdana11" id="<?php echo $row_SECINDEX['SECINDEX']."a"; ?>" height="8" onmouseover="CursorToPointer(this.id)" <?php if (empty($row_SECINDEX['SECINDEX'])){echo "style='display:none'";} ?>>
                            <td><?php echo $row_SECINDEX['FNAME']; echo " ".$row_SECINDEX['LNAME']; echo ", ".$row_SECINDEX['RELATION']; echo ", ".$row_SECINDEX['ADDRESS']; if ($row_SECINDEX['AUTHORIZED']=="1"){echo ", (Authorized)";}?></td>
                          </tr>
                          <?php } while ($row_SECINDEX = mysql_fetch_assoc($SECINDEX)); ?>
                        </table>
                    </div>
                  </div>
                  <div class="AccordionPanel">
                    <div class="AccordionPanelTab">Second Address</div>
                    <div class="AccordionPanelContent">
                      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
                        <?php do { ?>
                          <tr class="Verdana11" id="<?php echo $row_SECADDRESS['SECADDRESS']."b"; ?>" onmouseover="CursorToPointer(this.id)" height="8" <?php if (empty($row_SECADDRESS['SECADDRESS'])){echo "style='display:none'";} ?>>
                            <td><?php echo $row_SECADDRESS['STREET']; echo ", ".$row_SECADDRESS['UNITNO']; echo ", ".$row_SECADDRESS['CITY2']; echo ", ".$row_SECADDRESS['PROV']; echo ", ".$row_SECADDRESS['ZIP2']; if ($row_SECADDRESS['LEGAL']=="1"){echo ", (Legal address)";}?></td>
                          </tr>
                          <?php } while ($row_SECADDRESS = mysql_fetch_assoc($SECADDRESS)); ?>
                      </table>
                    </div>
                  </div>
                  <div class="AccordionPanel">
                    <div class="AccordionPanelTab">Referral</div>
                    <div class="AccordionPanelContent">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                                    <tr class="Verdana11" >
                                      <td>
									  <?php 
									  	$refvet=explode(',',$row_CLIENT['REFVET']);
										$query_REFERRAL = "SELECT * FROM REFER WHERE REFVET='".mysql_real_escape_string($refvet[0])."' AND REFCLIN='".mysql_real_escape_string($row_CLIENT['REFCLIN'])."'";
										$REFERRAL = mysql_query($query_REFERRAL, $tryconnection) or die(mysql_error());
										$row_REFERRAL = mysql_fetch_assoc($REFERRAL);

									  
									  echo $row_CLIENT['REFVET'].", ".$row_CLIENT['REFCLIN']."<br />";
									  echo $row_REFERRAL['ADDRESS']."<br />".$row_REFERRAL['CITY'].", ".$row_REFERRAL['STATE'].", ";
									  echo $row_REFERRAL['ZIP']."<br />";
									  echo "Tel (".$row_REFERRAL['CAREA'].") ".$row_REFERRAL['PHONE']."&nbsp;&nbsp;&nbsp;";
									  echo "Fax (".$row_REFERRAL['CAREA2'].") ".$row_REFERRAL['PHONE2'];
									  
									  ?>                                      </td>
                                    </tr>
                                </table>
                    </div>
                  </div>
                </div>                </td>
              </tr>
          </table></td>
      </tr>
    </table></td>
    
    
    <td width="238"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="17" colspan="5" align="center" bgcolor="#FFFF00" class="Verdana12B">Account Info</td>
      </tr>
              <tr>
                <td width="9" height="14" class="Verdana11">&nbsp;</td>
                <td width="64" height="14" class="Verdana11"> Balance:</td>
                <td width="77" height="14" align="right" valign="middle" class="Verdana11"><?php echo $row_CLIENT['BALANCE']; ?></td>
                <td height="14" colspan="2" align="left" valign="middle" class="Verdana11">&nbsp;CAD</td>
              </tr>
              <tr>
                <td height="14" class="Verdana11">&nbsp;</td>
                <td height="14" class="Verdana11">Deposit:</td>
                <td height="14" align="right" valign="middle" class="Verdana11"><?php echo $row_CLIENT['CREDIT']; ?></td>
                <td height="14" colspan="2" align="left" valign="middle" class="Verdana11">&nbsp;CAD</td>
              </tr>
              <tr>
                <td height="14" class="Verdana11">&nbsp;</td>
                <td height="14" class="Verdana11">Last Month:</td>
                <td height="14" align="right" valign="middle" class="Verdana11"><?php echo $row_CLIENT['LASTMON']; ?></td>
                <td height="14" colspan="2" align="left" valign="middle" class="Verdana11">&nbsp;CAD</td>
              </tr>
              <tr>
                <td height="13" colspan="3" align="center" <?php if ($row_CLIENT['TERMS']!='1') {echo "class='Verdana12BRed'";} else {echo "class='Verdana12'" ;}?>><?php if ($row_CLIENT['TERMS']=='1'){echo "NORMAL CREDIT";} else if ($row_CLIENT['TERMS']=='2'){echo "CASH ONLY";} else if ($row_CLIENT['TERMS']=='3'){echo "NO CREDIT";} else if ($row_CLIENT['TERMS']=='4'){echo "COLLECTION";} else if ($row_CLIENT['TERMS']=='5'){echo "POST DATED CHEQUE";} else if ($row_CLIENT['TERMS']=='6'){echo "ACCEPT CHEQUE";}; ?></td>
                <td width="39" height="13" align="center" valign="middle"><img src="../../IMAGES/e3 copy.jpg" alt="e" width="30" height="30" class="hidden" id="e" title="Click to edit the account information" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_ACCOUNT_INFORMATION.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, width=445, height=310')" onmouseover="CursorToPointer(this.id);"/></td>
                <td width="37" height="13" align="left" valign="middle"><img src="../../IMAGES/v copy.jpg" alt="v" id="v" width="30" height="30" onclick="window.open('../../CLIENT/ACCOUNTING_VIEW.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=610,height=387')" onmouseover="CursorToPointer(this.id);" title="Click to view the account information" /></td>
              </tr>
    </table></td>
  </tr>
  
  
  <tr>
  
    <td rowspan="2" align="left" valign="top">
    
    <table width="238" border="0" cellspacing="0" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="none">
      <tr>
        <td height="183" align="center" valign="middle" class="Verdana12B">
        <img src='../../../MSDVManager/<?php echo $uploadfile ; ?>' id="mug_shot" alt="patient picture" name="mugshot" width="170" height="170" id="mugshot" />
         </td>
        </tr>
      <tr>
            <td width="80" height="20" align="center"><a href="../INSERT_PATIENT.php?patient=<?php echo $patient; ?>&client=<?php echo $client; ?>&species=<?php echo $row_PATIENT['PETTYPE']; ?>"><img src="../../IMAGES/e3 copy.jpg" alt="e" width="30" height="30" title="Click to edit patient." /></a>
            <img src="../../IMAGES/v copy.jpg" alt="v" id="v2" width="30" height="30" class="style1" onclick="window.open('../PATIENT_DETAIL.php?patient=<?php echo $patient; ?>','_blank','width=660,height=540')"  onmouseover="CursorToPointer(this.id)" title="Click to open patient detail"/>
            <img src="../../IMAGES/h copy.jpg" alt="h" id="h" width="30" height="30" onclick="window.open('../HISTORY/REVIEW_HISTORY.php?path=procmenu&patient=<?php echo $patient; ?>&client=<?php echo $client; ?>','_parent')"  onmouseover="CursorToPointer(this.id)" title="Click to review medical history"/>
            <img src="../../IMAGES/m.jpg" alt="m" id="m" width="30" height="30" onmouseover="CursorToPointer(this.id)" onclick="window.open('../MPL/MPL.php?path=procmenu&patient=<?php echo $patient; ?>&client=<?php echo $client; ?>','_parent')"/>
            <input type="button" name="PHOTO" class="CustomizedButton2" id="PHOTO" value="PHOTO" onclick="window.open('../../../MSDVManager/ADD_SCAN.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&petid=<?php echo $_SESSION['patient']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
            </td>

      </tr>
      <tr>
        <td height="18" colspan="4" align="center" class="Verdana11BRed" title="File is locked for invoicing."><?php if ($row_CLIENT['LOCKED']=='1'){ echo "INVOICE OPEN ELSEWHERE";} ?></td>
        <!--<td height="18" colspan="2" align="left" class="Andale12noDecor">&nbsp;</td>-->
        </tr>
    </table>    </td>
  </tr>
  
  
  
  <tr>
    
    <td>
    	<table width="495" height="" border="0" cellpadding="0" cellspacing="0" >
      <tr>
        <td height="26" colspan="2" align="center" bgcolor="#B1B4FF" class="Andale13B">
          <input type="button" name="APCARD" class="CustomizedButton2" id="APCARD" value="AP.CARD" onclick="window.open('../MPL/PRINT_MPL.php?','_blank')"/>
          <input type="button" name="LABEL" class="CustomizedButton2" id="LABEL" value="FAMILY" onclick="window.open('../../CLIENT/CLIENT_PATIENT_FILE.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_self','status=no,scrolling=no,width=732,height=500')"/>
          <input type="button" name="LABEL3" class="CustomizedButton2" id="LABEL3" value="CAGE L" onclick="window.open('../CAGE_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
          <input type="button" name="LABEL4" class="CustomizedButton2" id="LABEL4" value="BLOOD L" onclick="window.open('../BLOOD_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
          <input type="submit" name="waiting" class="CustomizedButton2" id="waiting" value="WAITING" />          
          <input type="submit" name="admit" class="CustomizedButton2" id="admit" value="ADMIT" />          
          <input type="submit" name="discharge" class="CustomizedButton2" id="discharge" value="DISCHG" />          </td>
        </tr>
    </table>    </td>
  </tr>
  <tr>
    <td colspan="2" align="left" valign="top">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
  <tr>
    <td height="19" colspan="2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="19" bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" class="Verdana11"><span style="background-color:#FFFF00; padding:2px;" class="Verdana13B"> <?php echo $row_PATIENT['PETNAME']; ?></span>
           <?php if ($row_PATIENT['PETTYPE']=='1'){echo "Canine";} else if ($row_PATIENT['PETTYPE']=='2'){echo "Feline";} else if ($row_PATIENT['PETTYPE']=='3'){echo "Equine";}else if ($row_PATIENT['PETTYPE']=='4'){echo "Bovine";}else if ($row_PATIENT['PETTYPE']=='5'){echo "Caprine";}else if ($row_PATIENT['PETTYPE']=='6'){echo "Porcine";}else if ($row_PATIENT['PETTYPE']=='7'){echo "Avian";}else if ($row_PATIENT['PETTYPE']=='8'){echo "Other";}; ?>
          , <?php echo $row_PATIENT['PETBREED']; ?>, <?php echo $row_PATIENT['PCOLOUR']; ?>,&nbsp; <?php echo $row_PATIENT['PSEX']; if ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='M'){echo "(N)";} elseif ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='F'){echo "(S)";} ?>,&nbsp;&nbsp;<?php echo $row_PATIENT['PWEIGHT']; ?> <script type="text/javascript">document.write(localStorage.weightunit);</script>, Born: <?php echo $row_PATIENT['PDOB']; ?>, Age: <?php agecalculation($tryconnection,$row_PATIENT['PDOB']); ?>
          <?php if ((strtotime(validity($row_PATIENT['POTH8'],'1')) < time() && $row_PATIENT['POTH8']!='0000-00-00') ) {echo "<span class=\"alerttext12\" title=\"Exam Overdue\">OVD.</span>";} ?>          </td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td width="104" align="center" valign="middle" bgcolor="#B1B4FF" class="Verdana11Bwhite" id="presprob"><input type="button" name="button" id="button" value="PRES. PROBLEM" class="button" style="width:120px;" onclick="window.open('../../RECEPTION/PRESENTING_PROBLEM.php?recepid=<?php echo $row_RECEP['RECEPID']; ?>','_self')"/></td>
    <td width="704" height="50" align="left" valign="top" bgcolor="#B1B4FF" class="Labels2">
    <div style="height:45px; border:solid black thin; background-color:#FFFFFF; margin-top:2px; padding:2px; overflow:auto;" class="Verdana12">
    <?php echo $row_RECEP['PROBLEM']; ?>    </div>    </td>
  </tr>
  
  <tr>
    <td align="center" valign="middle" bgcolor="#B1B4FF" class="Verdana11Bwhite" id="outp"><input type="submit" name="exambegin" id="exambegin" value="OUT PATIENT" class="button" style="width:120px;"  onclick="" /></td>
    <td height="53" align="left" valign="top" bgcolor="#B1B4FF" class="Labels2">
    <div style="height:49px; border:solid black thin; background-color:#FFFFFF; margin-top:2px;"><?php //print_r($_SESSION); ?></div>	</td>
  </tr>
  
  <tr>
    <td align="center" valign="middle" bgcolor="#B1B4FF" id="mednot" onclick="" onmouseover=""document.getElementById(this.id).style.cursor='pointer'window.open('HISTORY/MEDICAL_NOTES.php?ref=<?php echo $row_PATIENT['PETID']; ?>','_self')><input type="button" name="button3" id="button3" class="button" value="MEDICAL NOTES" style="width:120px;" onclick="window.open('MEDICAL_NOTES.php','_self');" /></td>
    <td height="53" align="left" valign="top" bgcolor="#B1B4FF" class="Labels2">
    <div style="height:49px; width:600px; border:solid black thin; background-color:#FFFFFF; margin-top:2px; overflow:auto; white-space:pre;"><?php
		echo $row_MEDNOTE['NDIAGNOSIS']."<br />";
		echo $row_MEDNOTE['NPROCEDURES']."<br />";
		echo $row_MEDNOTE['NCLINSTR']."<br />";
		echo $row_MEDNOTE['NCASESUM']."<br />";
	?>
	<script type="text/javascript">//document.write(sessionStorage.filetype);</script></div>	</td>
  </tr>
  
  <tr>
    <td align="center" valign="middle" bgcolor="#B1B4FF" class="Verdana11Bwhite" id="invoice"><input name="button4" type="button" id="button4" class="button" style="width:120px;" value="INVOICE" <?php if ($row_CLIENT['LOCKED']=='1'){ echo "disabled";} ?> onclick="nav0()"/></td>
    <td height="53" align="left" valign="top" bgcolor="#B1B4FF"  class="Labels2">
    <div style="height:48px; border:solid black thin; background-color:#FFFFFF; margin-top:2px;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" <?php if (empty($invhold[0])){echo "style='display:none'";} ?>>
  <tr>
    <td width="28%" align="center" class="Verdana12">Current Invoice <strong>#<?php echo $invhold[0]; ?>:</strong></td>
    <td width="10%" align="left" class="Verdana12">Subtotal</td>
    <td width="15%" align="right" class="Verdana12"><?php echo number_format($invhold[3]-$invhold[2]-$invhold[1],2); ?></td>
    <td width="47%" class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td width="28%" class="Verdana12">&nbsp;</td>
    <td align="left" class="Verdana12">Taxes</td>
    <td align="right" class="Verdana12"><?php echo number_format($invhold[1]+$invhold[2],2); ?></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
  <tr>
    <td width="28%" class="Verdana12">&nbsp;</td>
    <td align="left" class="Verdana12"><strong>TOTAL</strong></td>
    <td align="right" class="Verdana12"><strong><?php echo number_format($invhold[3],2); ?></strong></td>
    <td class="Verdana12">&nbsp;</td>
  </tr>
</table> 
</div></td>
  </tr>
</table>    </td>
    </tr>
</table>      </td>
    </tr>
</table>
</form>

<script type="text/javascript">
<!--
var Accordion1 = new Spry.Widget.Accordion("Accordion1");
//-->
  </script>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>