<?php 
session_start();
require_once('../tryconnection.php');
include("../ASSETS/age.php");


if (isset($_GET['client'])){
	if (empty($_GET['client'])){
	$client=$_SESSION['client'];
	}
	else {
	$client=$_GET['client'];
	$_SESSION['client']=$_GET['client'];
	}
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}


mysql_select_db($database_tryconnection, $tryconnection);

////////////////////////////////// CLIENT ///////////////////////////////////
$q_this = "SELECT SUM(ITOTAL) AS ITHIS FROM ARINVOI WHERE CUSTNO = '$client' " ;
$do_this = mysql_query($q_this, $tryconnection) or die(mysql_error()) ;
$g_this = mysqli_fetch_assoc($do_this) ;
$q_last = "SELECT SUM(ITOTAL) AS ILAST FROM INVLAST WHERE CUSTNO = '$client' " ;
$do_last = mysql_query($q_last, $tryconnection) or die(mysql_error()) ;
$g_last = mysqli_fetch_assoc($do_last) ;
$q_hx = "SELECT SUM(ITOTAL) AS IHX FROM ARYINVO WHERE INVDTE >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND CUSTNO = '$client' " ;
$do_hx = mysql_query($q_hx, $tryconnection) or die(mysql_error()) ;
$g_hx = mysqli_fetch_assoc($do_hx) ;
$thisX = $g_this['ITHIS'] ;
$lastX = $g_last['ILAST'] ;
$hxX   = $g_hx['IHX'] ;
if ( is_null($thisX)) {$thisX = 0 ;}
if ( is_null($lastX)) {$lastX = 0 ;}
if ( is_null($hxX))   {$hxX = 0 ;}
$yrsls = $thisX + $lastX + $hxX ;

$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client' LIMIT 1";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

/*if ($row_CLIENT['LOCKED']=='1'){
$_SESSION['lock']='2';
}

else if ($_GET['refID']=='REG' && !isset($_SESSION['lock'])){
	$query_LOCK = "UPDATE ARCUSTO SET LOCKED='1' WHERE CUSTNO = '$client'";
	$LOCK = mysql_query($query_LOCK, $tryconnection) or die(mysql_error());
	$_SESSION['lock']='1';
}
*/

$fileused="$row_CLIENT[TITLE] $row_CLIENT[CONTACT] $row_CLIENT[COMPANY]";
//$_SESSION['fileused']= $fileused;
//$_SESSION['filetype']= "C";

///////////////////////////////// PATIENTS ////////////////////////////////////
$pdead=" AND PDEAD=0 AND PMOVED=0";
if (isset($_GET['pdead'])){
$pdead='';
}
$query_PATIENTS = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB, DATE_FORMAT(PDEADATE,'%m/%d/%Y') AS PDEADATE, DATE_FORMAT(PRABDAT,'%m/%d/%Y') AS PRABDAT, DATE_FORMAT(POTHDAT,'%m/%d/%Y') AS POTHDAT, DATE_FORMAT(PLEUKDAT,'%m/%d/%Y') AS PLEUKDAT, DATE_FORMAT(POTHTWO,'%m/%d/%Y') AS POTHTWO, DATE_FORMAT(POTHTHR,'%m/%d/%Y') AS POTHTHR, DATE_FORMAT(POTHFOR,'%m/%d/%Y') AS POTHFOR, DATE_FORMAT(POTHFIV,'%m/%d/%Y') AS POTHFIV, DATE_FORMAT(POTHSIX,'%m/%d/%Y') AS POTHSIX, DATE_FORMAT(POTHSEV,'%m/%d/%Y') AS POTHSEV, DATE_FORMAT(POTH8,'%m/%d/%Y') AS POTH8, DATE_FORMAT(POTH9,'%m/%d/%Y') AS POTH9, DATE_FORMAT(POTH10,'%m/%d/%Y') AS POTH10, DATE_FORMAT(POTH11,'%m/%d/%Y') AS POTH11, DATE_FORMAT(POTH12,'%m/%d/%Y') AS POTH12, DATE_FORMAT(POTH13,'%m/%d/%Y') AS POTH13, DATE_FORMAT(POTH14,'%m/%d/%Y') AS POTH14, DATE_FORMAT(POTH15,'%m/%d/%Y') AS POTH15, DATE_FORMAT(PFIRSTDATE,'%m/%d/%Y') AS PFIRSTDATE, DATE_FORMAT(PLASTDATE,'%m/%d/%Y') AS PLASTDATE FROM PETMAST WHERE CUSTNO = '$client'".$pdead." ORDER BY PETNAME ASC";
$PATIENTS = mysql_query($query_PATIENTS, $tryconnection) or die(mysql_error());
$row_PATIENTS = mysqli_fetch_assoc($PATIENTS);

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

/*

*/



$_SESSION['petids']=array();	  
do { $_SESSION['petids'][]=$row_PATIENTS['PETID'];} 
while ($row_PATIENTS = mysqli_fetch_assoc($PATIENTS)); 
	  
if (isset($_SESSION['patient']) && (!in_array($_SESSION['patient'],$_SESSION['petids']))){
$_SESSION['patient']=$_SESSION['petids'][0];
}

else if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}

else if (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

else {
$_SESSION['patient']=$_SESSION['petids'][0];
$patient=$_SESSION['patient'];
}

// define this in case it is undefined for the clinical logs.
$_SESSION['patient'] = $patient ;

//date and time stamp showing the last time the client file was modified in any way. It happens at invoicing, and during client file edits
$datetime=date("Y-m-d H:i:s");

///////////////////////////// COMMENT ////////////////////////////////////////

if (isset($_POST['comment'])) {
$updateSQL = sprintf("UPDATE ARCUSTO SET COMMENT='%s' WHERE CUSTNO='%s' LIMIT 1",
                       mysql_real_escape_string($_POST['commentwrite']),
                       $client);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
header("Location: CLIENT_PATIENT_FILE.php");
}

//////////////////////////// SECOND INDEX & SECOND ADDRESS /////////////////////////////////////////

$custno=$row_CLIENT['CUSTNO'];

$query_SECINDEX = "SELECT * FROM SECINDEX WHERE CUSTNO = '$custno'";
$SECINDEX = mysql_query($query_SECINDEX, $tryconnection) or die(mysql_error());
$row_SECINDEX = mysqli_fetch_assoc($SECINDEX);
$totalRows_SECINDEX = mysqli_num_rows($SECINDEX);

$query_SECADDRESS = "SELECT * FROM SECADDRESS WHERE CUSTNO = '$custno'";
$SECADDRESS = mysql_query($query_SECADDRESS, $tryconnection) or die(mysql_error());
$row_SECADDRESS = mysqli_fetch_assoc($SECADDRESS);
$totalRows_SECADDRESS = mysqli_num_rows($SECADDRESS);

//////////////////////////// WEIGHT UNIT FROM CRITDATA /////////////////////////////////////////

$query_CRITDATA = "SELECT CRITDATA.WEIGHTUNIT FROM CRITDATA LIMIT 1";
$CRITDATA = mysql_query($query_CRITDATA, $tryconnection) or die(mysql_error());
$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);
$totalRows_CRITDATA = mysqli_num_rows($CRITDATA);

////////////////////////////////////////////////////////////////////////////////////////

mysql_select_db($database_tryconnection, $tryconnection);
$query_DLOG = "SELECT DLPETID FROM TICKLER";
$DLOG = mysql_query($query_DLOG, $tryconnection) or die(mysql_error());
$row_DLOG = mysqli_fetch_assoc($DLOG);
$DLOGarray=array();
do {
$DLOGarray[]=$row_DLOG['DLPETID'];
}
while ($row_DLOG = mysqli_fetch_assoc($DLOG));

/////////////////////////////PAGING WITHIN CLIENT FILES/////////////////////////
$query_VIEW="CREATE OR REPLACE VIEW CLIENTS AS SELECT DISTINCT CUSTNO FROM ARCUSTO ORDER BY COMPANY,CONTACT,CUSTNO ASC";
$VIEW= mysql_query($query_VIEW, $tryconnection) or die(mysql_error());

$query_COMPANY="SELECT * FROM CLIENTS";
$COMPANY= mysql_query($query_COMPANY, $tryconnection) or die(mysql_error());
$row_COMPANY = mysqli_fetch_assoc($COMPANY);

$ids= array();
do {
$ids[]=$row_COMPANY['CUSTNO'];
}
while ($row_COMPANY = mysqli_fetch_assoc($COMPANY));

$key=array_search($row_CLIENT['CUSTNO'],$ids);

////////////////////////VIEW FROM DUTY LOG////////////////////////
$query_VIEWDL="CREATE OR REPLACE VIEW DLOG AS SELECT DLPETID FROM TICKLER";
$VIEWDL= mysql_query($query_VIEWDL, $tryconnection) or die(mysql_error());

$query_DLOG="SELECT * FROM DLOG";
$DLOG= mysql_query($query_DLOG, $tryconnection) or die(mysql_error());
$row_DLOG = mysqli_fetch_assoc($DLOG);

$dls= array();
do {
$dls[]=$row_DLOG['DLPETID'];
}
while ($row_DLOG = mysqli_fetch_assoc($DLOG));

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.2" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title"></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<style type="text/css">
<!--
.CustomizedButton1 {
	font-family: Verdana;
	font-size: 14px;
	width: 100px;
	height: 27px;
	margin-left: 1px;
	margin-right: 1px;
	
}

.CustomizedButton2 {
	font-family: Verdana;
	font-size: 14px;
	width: 75px;
	height: 27px;
	margin-left: 1px;
	margin-right: 1px;
	
}

.CustomizedButton3 {
	font-family: Verdana;
	font-size: 14px;
	width: 100px;
	height: 27px;
	margin-left: 1px;
	margin-right: 1px;
}

#WindowBodyShadow {
	position:absolute;
	width:733px;
	height:553px;
	z-index:10;
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #000000;
	opacity: 0.3;
}
.CustomizedButton11 {font-family: Verdana;
	font-size: 14px;
	width: 80px;
	height: 27px;
	margin-left: 1px;
	margin-right: 1px;
}

-->
</style>

<script src="../../SpryAssets/SpryAccordion.js" type="text/javascript"></script>

<script type="text/javascript">
if (!sessionStorage.refID){
document.getElementById('title').innerText="FAMILY SCREEN";
}
else if (sessionStorage.refID=='MOVE PATIENT'){
document.getElementById('title').innerText="ORIGINAL CLIENT AND PATIENT";
}
else if (sessionStorage.refID=='MOVE PATIENT+INVOICES'){
document.getElementById('title').innerText="ORIGINAL CLIENT AND PATIENT";
}
else if (sessionStorage.refID=='MOVE INVOICES'){
document.getElementById('title').innerText="ORIGINAL CLIENT";
}
else if (sessionStorage.refID=='MERGE HISTORY'){
document.getElementById('title').innerText="ORIGINAL CLIENT AND PATIENT";
}
else if (sessionStorage.refID=='TARGET1' || sessionStorage.refID=='TARGET2' || sessionStorage.refID=='TARGET3'){
document.getElementById('title').innerText="TARGET CLIENT";
}
else if (sessionStorage.refID=='TARGET4'){
document.getElementById('title').innerText="TARGET CLIENT AND PATIENT";
}
else{
document.getElementById('title').innerText="FAMILY SCREEN FOR "+sessionStorage.refID;
}


function bodyonload()
{
setInterval("self.location.reload()", 6000000);
sessionStorage.setItem('fileused',"<?php echo substr($fileused,0,25); ?>");
sessionStorage.setItem('client','<?php echo $client; ?>');
document.getElementById('inuse').innerText=localStorage.xdatabase;

<?php if (isset($_SESSION['patient'])){
echo "window.open('../PATIENT/PHOTO_DIRECTORY.php?refID=".$_GET['refID']."&patient=".$_SESSION['patient']."&client=".$_SESSION['client']."','photodirectory');";
} 
else {
echo "window.open('../PATIENT/PHOTO_DIRECTORY.php?refID=".$_GET['refID']."&patient=".$row_PATIENTS['PETID']."&client=".$_SESSION['client']."','photodirectory');";
}?>
}

sessionStorage.setItem('filetype','C');

function IntextOnFocus(x) {
	x.className=(x.className=="Andale13noDecor")?"Andale13noDecor2":"Andale13noDecor2";
}

function IntextOnBlur(x) {
	x.className=(x.className=="Andale13noDecor2")?"Andale13noDecor":"Andale13noDecor";
}


function ClickOnPatient(patient,refID,client,petno,pettype,petname,psex)
{
//window.open('../PATIENT/PHOTO_DIRECTORY.php?patient=' + patient + '&client=' + client + '&refID=' + refID + '&pettype' + pettype + '&petname=' + petname + '&psex=' + psex + '&llocalid=' + localStorage.llocalid,'photodirectory');
window.open('../PATIENT/PHOTO_DIRECTORY.php?patient=' + patient + '&client=' + client + '&refID=' + refID + '&llocalid=' + localStorage.llocalid,'photodirectory');
document.getElementById(petno).style.display="";
sessionStorage.setItem('petname',petname) ;
}


function WriteComment() {
document.getElementById('commentdisplay').style.display="none";
document.getElementById('commentwrite').style.display="";
document.getElementById('comment').style.display="";
document.getElementById('commentwrite').focus();

//commentdisplay
}



function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function bodyonunload(){
sessionStorage.setItem('cancel',document.location);
}
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../IMAGES/left_arrow_dark.JPG','../IMAGES/right_arrow_dark.JPG')" onunload="bodyonunload()">
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
                <li><a href="#" onclick="ledgerlist();">Accounting Reports</a></li>
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

<!--<div id="WindowBodyShadow">
</div>-->
<form action="" method="post" name="form">
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
<script type="text/javascript">

</script>
<input type="hidden" name="coffee" id="coffee" value="" />
  <tr>
<!--CLIENT INFO-->
    <td rowspan="2" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" >     
<tr>
                <td height="17" colspan="4" align="left" bgcolor="#FFFF00" class="Verdana12B">
					<?php echo $row_CLIENT['TITLE']; echo " ".$row_CLIENT['CONTACT']; echo " ".$row_CLIENT['COMPANY']; ?>
                	<?php if ($row_CLIENT['INACTIVE']=='1') {echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>INACTIVE</span>";} ?>
                	<?php if ($row_CLIENT['TERMS']=='4') {echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>COLLECTION</span>"; echo "<script language = javascript> alert('This client is in collection. Proceed at your own expense... '); </script>" ;}  else if ($row_CLIENT['TERMS']=='2'){echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>CASH ONLY";} else if ($row_CLIENT['TERMS']=='3'){echo "&nbsp;&nbsp;&nbsp;<span class='Verdana12BRed'>NO CREDIT";}?>
                <script type="text/javascript">//document.write(checkfamily);</script>
                    
                </td>
            	<td height="17" colspan="2" align="right" bgcolor="#FFFF00" class="Verdana12B">
                Client#: <?php echo $row_CLIENT['CUSTNO']; ?>                </td>
            </tr>
            <tr align="left">
                <td height="20" colspan="4" class="Verdana11">
				<?php echo $row_CLIENT['ADDRESS1']; echo " ".$row_CLIENT['ADDRESS2']; ?>                </td>
                <td rowspan="2" colspan="2" align="center" valign="middle"></td>
            </tr>
            <tr align="left">
                <td height="20" colspan="4" class="Verdana11">
				<?php echo $row_CLIENT['CITY']; echo ", ".$row_CLIENT['STATE']; echo ", ".$row_CLIENT['ZIP']; ?>                </td>
            </tr>
<tr align="left">
                <td width="63" height="17" align="left" class="Verdana11B">Home:</td>
              <td width="99" height="17" align="left" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA'].")".$row_CLIENT['PHONE']; ?></td>
              <td width="46" height="17" align="left" class="Verdana11B">Fax:</td>
              <td width="91" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA6'].")".$row_CLIENT['PHONE6']; ?></td>
              <td width="68" height="17" align="left" valign="middle" class="Verdana11B">Work1:</td>
              <td width="121" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA4'].")".$row_CLIENT['PHONE4'] . '-' . $row_CLIENT['CBEXT'] ; ?></td>
            </tr>
            <tr align="left" class="Andale12noDecor">
                <td height="17" align="left" class="Verdana11B">Cell1:</td>
                <td width="99" height="17" align="left"class="Verdana11"><?php echo "(".$row_CLIENT['CAREA2'].")".$row_CLIENT['PHONE2']; ?></td>
              <td height="17" align="left" class="Verdana11B">Other:</td>
                <td width="91" height="17" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA7'].")".$row_CLIENT['PHONE7']; ?></td>
              <td width="68" height="17" align="left" valign="middle" class="Verdana11B">Work2:</td>
              <td width="121" height="17" rowspan="2" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA5'].")".$row_CLIENT['PHONE5'] . '-' . $row_CLIENT['CBEXT2']; ?></td>
            </tr>
            <tr align="left">
                <td height="17" rowspan="2" align="left" class="Verdana11B">Cell2:</td>
                <td width="99" height="17" rowspan="2" align="left"class="Verdana11"><?php echo "(".$row_CLIENT['CAREA3'].")".$row_CLIENT['PHONE3']; ?></td>
              <td height="17" rowspan="2" align="left" class="Verdana11B">Barn:</td>
                <td width="91" height="17" rowspan="2" align="left" valign="middle" class="Verdana11"><?php echo "(".$row_CLIENT['CAREA8'].")".$row_CLIENT['PHONE8']; ?></td>
          	</tr>
          	<tr align="left">
                <td width="68" height="17" align="left"></td>
              <td colspan="2" rowspan="2" align="right">
                <a href="UPDATE_CLIENT.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>"><img src="../IMAGES/e3 copy.jpg" alt="e" width="30" height="30" title="Click to edit client" /></a>                </td>
            </tr>
            <tr>
                <td height="17" colspan="5" align="left" class="Verdana11"><strong>Email:&nbsp;</strong><?php echo $row_CLIENT['EMAIL']; //if($row_CLIENT['REMINDERS']=="1"){echo "&bull;";}?></td>
            </tr>
<!--PAGING-->              
            <tr>
                <td height="35" align="left" valign="middle" bgcolor="#B1B4FF"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image6','','../IMAGES/left_arrow_dark.JPG',1)" onclick="document.location='CLIENT_PATIENT_FILE.php?client=<?php echo $ids[$key-1]; ?>'"><img src="../IMAGES/left_arrow_light.JPG" alt="PREVIOUS" name="Image6" width="28" height="28" border="0" id="Image6" /></a><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image7','','../IMAGES/right_arrow_dark.JPG',1)" style="margin-left:2px;" onclick="document.location='CLIENT_PATIENT_FILE.php?client=<?php echo $ids[$key+1]; ?>'"><img src="../IMAGES/right_arrow_light.JPG" alt="NEXT" name="Image7" width="28" height="28" border="0" id="Image7" /></a></td>
<!--BUTTONS-->
                <td align="center" colspan="5" valign="middle" bgcolor="#B1B4FF">
             <input name="search"type="button" class="CustomizedButton1" id="search" value="CLIENTS" onclick="window.open('CLIENT_SEARCH_SCREEN.php?refID=<?php echo $_GET['refID']; ?>','_self')"/>
             <input name="LABEL2" type="button" class="CustomizedButton1" id="LABEL2" value="LABEL" onclick="window.open('../PATIENT/MAIL_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
              <input name="ENVELOPE"type="button" class="CustomizedButton1" id="ENVELOPE" value="ENVELOPE" disabled="disabled"/>
              <input name="CANCEL" type="button" class="CustomizedButton11" id="CANCEL" value="RECEP." onclick="sessionStorage.setItem('refID','PROCESSING MENU'); sessionStorage.setItem('filetype','0'); document.location='../RECEPTION/RECEPTION_FILE.php'" style="width:83px;"/></td>
            </tr>
            <tr>
            	<td colspan="6">
           		 <!--ACCORDION PANNELS-->
           		 <table width="100%" height="115" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td>                        
                  <div id="Accordion1" class="Accordion" tabindex="0">
                            <div class="AccordionPanel">
                              <div class="AccordionPanelTab">Sticky Notes</div>
                              <!--<div class="AccordionPanelContent" ondblclick="WriteAlert()" title="Doubleclick to type the alert.">-->
						      <div class="AccordionPanelContent">                              
							  <span id="alertdisplay">
							  <?php //echo $row_CLIENT['ALERTWRITE'];
							  	  $PATIENTS = mysql_query($query_PATIENTS, $tryconnection) or die(mysql_error());
								  $row_PATIENTS = mysqli_fetch_assoc($PATIENTS); 
							  do {
							  if (strlen(trim($row_PATIENTS['STICKIE'])) > 0){
							  echo "<span class='alerttext12'>".$row_PATIENTS['PETNAME'].":</span>";
							  echo "<span class='Verdana12'> ".$row_PATIENTS['STICKIE']."</span><br />";
							  	}
							  }
							  while ($row_PATIENTS = mysqli_fetch_assoc($PATIENTS));
							  
							  
							  $PATIENTS = mysql_query($query_PATIENTS, $tryconnection) or die(mysql_error());
							  $row_PATIENTS = mysqli_fetch_assoc($PATIENTS);

							  
							  ?>
                              </span>
                             </div>
                            </div>
                            <div class="AccordionPanel">
                              <div class="AccordionPanelTab">Comment</div>
                              <div class="AccordionPanelContent" ondblclick="WriteComment()" title="Doubleclick to type the comment."> <span class="Verdana12" id="commentdisplay"><?php echo $row_CLIENT['COMMENT']; ?></span>
                                  <textarea name="commentwrite" cols="62" rows="2" id="commentwrite" class="commentarea" style="display:none"><?php echo $row_CLIENT['COMMENT']; ?></textarea>
                                  <input type="submit" name="comment" id="comment" value="OK" style="display:none"/>
                              </div>
                            </div>
                            <div class="AccordionPanel">
                              <div class="AccordionPanelTab">Second Index</div>
                              <div class="AccordionPanelContent">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
                                  <?php do { ?>
                                    <!--ondblclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_SECOND_INDEX.php?secindex=<?php //if(empty($row_SECINDEX['SECINDEX'])){echo "0";} else {echo $row_SECINDEX['SECINDEX'];} ?>&custno=<?php //if (empty($row_SECINDEX['CUSTNO'])){echo $row_CLIENT['CUSTNO'];} else {echo $row_SECINDEX['CUSTNO'];} ?>','_blank','width=440, height=327')" title="Click to update this second index."-->
                                    <tr class="Verdana11" id="<?php echo $row_SECINDEX['SECINDEX']."a"; ?>" height="8" onmouseover="CursorToPointer(this.id)" <?php if (empty($row_SECINDEX['SECINDEX'])){echo "style='display:none'";} ?>>
                                      <td><?php echo $row_SECINDEX['FNAME']; echo " ".$row_SECINDEX['LNAME']; echo ", ".$row_SECINDEX['RELATION']; echo ", ".$row_SECINDEX['ADDRESS']; if ($row_SECINDEX['AUTHORIZED']=="1"){echo ", (Authorized)";}?></td>
                                    </tr>
                                    <?php } while ($row_SECINDEX = mysqli_fetch_assoc($SECINDEX)); ?>
                                </table>
                              </div>
                            </div>
                            <div class="AccordionPanel">
                              <div class="AccordionPanelTab">Second Address</div>
                              <div class="AccordionPanelContent">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#CCCCCC" frame="below" rules="rows">
                                  <?php do { ?>
                                    <!--      ondblclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_SECOND_ADDRESS.php?secaddress=<?php //if(empty($row_SECADDRESS['SECADDRESS'])){echo "0";} else {echo $row_SECADDRESS['SECADDRESS'];} ?>&custno=<?php //if (empty($row_SECINDEX['CUSTNO'])){echo $row_CLIENT['CUSTNO'];} else {echo $row_SECINDEX['CUSTNO'];} ?>','_blank','width=428, height=347')" title="Click to update this second address."
-->
                                    <tr class="Verdana11" id="<?php echo $row_SECADDRESS['SECADDRESS']."b"; ?>" onmouseover="CursorToPointer(this.id)" height="8" <?php if (empty($row_SECADDRESS['SECADDRESS'])){echo "style='display:none'";} ?>>
                                      <td><?php echo $row_SECADDRESS['SECNAME'].", ".$row_SECADDRESS['STREET']; echo ", ".$row_SECADDRESS['UNITNO']; echo ", ".$row_SECADDRESS['CITY2']; echo ", ".$row_SECADDRESS['PROV']; echo ", ".$row_SECADDRESS['ZIP2']." (".$row_SECADDRESS['AREA'].") ".$row_SECADDRESS['TEL']; if ($row_SECADDRESS['LEGAL']=="1"){echo ", (Legal address)";}?></td>
                                    </tr>
                                    <?php } while ($row_SECADDRESS = mysqli_fetch_assoc($SECADDRESS)); ?>
                                </table>
                              </div>
                            </div>
                            <div class="AccordionPanel">
                              <div class="AccordionPanelTab">Referrals</div>
                              <div class="AccordionPanelContent">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                                    <tr class="Verdana11" >
                                      <td>
									  <?php 
									  	$refvet=explode(',',$row_CLIENT['REFVET']);
										$query_REFERRAL = "SELECT * FROM REFER WHERE REFVET='".mysql_real_escape_string($refvet[0])."' AND REFCLIN='".mysql_real_escape_string($row_CLIENT['REFCLIN'])."'";
										$REFERRAL = mysql_query($query_REFERRAL, $tryconnection) or die(mysql_error());
										$row_REFERRAL = mysqli_fetch_assoc($REFERRAL);

									  
									  echo $row_CLIENT['REFVET'].", ".$row_CLIENT['REFCLIN']."<br />";
									  echo $row_REFERRAL['ADDRESS']."<br />".$row_REFERRAL['CITY'].", ".$row_REFERRAL['STATE'].", ";
									  echo $row_REFERRAL['ZIP']."<br />";
									  echo "Tel (".$row_REFERRAL['CAREA'].") ".$row_REFERRAL['PHONE']."&nbsp;&nbsp;&nbsp;";
									  echo "Fax (".$row_REFERRAL['CAREA2'].") ".$row_REFERRAL['PHONE2'];
									  
									  ?>
                                      </td>
                                    </tr>
                                </table>
                              </div>
                            </div>
                  </div>                </td>
              </tr>
              <tr>
                <td class="ButtonsTable">
            <span class="Verdana12B" style="background-color:#FFFF00; padding:2px;">Patients:</span>
            <input type="button" name="ADD" class="CustomizedButton2" id="ADD" value="ADD" onclick="window.open('../PATIENT/INSERT_PATIENT.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&species=0&patient=0','_self')" title="Click to add new patient"/>
            <input type="button" name="CLABEL" class="CustomizedButton2" id="CLABEL" value="CAGE L" onclick="window.open('../PATIENT/CAGE_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
            <input type="button" name="LABEL" class="CustomizedButton2" id="LABEL" value="BLOOD L" onclick="window.open('../PATIENT/BLOOD_LABEL.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/>
            <input type="button" name="ZOOM" class="CustomizedButton2" id="ZOOM" value="ZOOM" onclick="window.open('../PATIENT/ZOOM.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','status=no,scrolling=no,width=732,height=500')" title="Click to view whole family"/>
            <input type="button" name="ALL" class="<?php if($_GET['pdead']=='1'){echo "hidden";} else {echo "CustomizedButton2";} ?>" id="DISPLAY ALL" value="ALL" onclick="self.location='CLIENT_PATIENT_FILE.php?pdead=1&client=<?php echo $client; ?>'" title="Click to display all patients (deceased included)" /><input type="button" name="ALL" class="<?php if(!isset($_GET['pdead'])){echo "hidden";} else {echo "CustomizedButton2";} ?>" id="LIVE" value="LIVE" onclick="self.location='CLIENT_PATIENT_FILE.php?client=<?php echo $client; ?>'" title="Click to display alive patients only" />                </td>
              </tr>
            </table>           		</td>
         	</tr>
      </table>    
    </td>
<!--ACCOUNT INFO-->
    <td width="33%" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
                <td height="14" class="Verdana11">&nbsp;</td>
                <td height="14" class="Verdana11">12 Month Sales:</td>
                <td height="14" align="right" valign="middle" class="Verdana11"><?php echo number_format($yrsls,2) ; ?></td>
                <td height="14" colspan="2" align="left" valign="middle" class="Verdana11">&nbsp;CAD</td>
              </tr>
              <tr>
                <td height="13" colspan="3" align="center" <?php if ($row_CLIENT['TERMS']!='1') {echo "class='Verdana12BRed'";} else {echo "class='Verdana12'" ;}?>><?php if ($row_CLIENT['TERMS']=='1'){echo "NORMAL CREDIT";} else if ($row_CLIENT['TERMS']=='2'){echo "CASH ONLY";} else if ($row_CLIENT['TERMS']=='3'){echo "NO CREDIT";} else if ($row_CLIENT['TERMS']=='4'){echo "COLLECTION";} else if ($row_CLIENT['TERMS']=='5'){echo "POST DATED CHEQUE";} else if ($row_CLIENT['TERMS']=='6'){echo "ACCEPT CHEQUE";}; ?></td>
                <td width="39" height="13" align="center" valign="middle"><img src="../IMAGES/e3 copy.jpg" alt="e" width="30" height="30" class="hidden" id="e" title="Click to edit the account information" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_ACCOUNT_INFORMATION.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, width=445, height=310')" onmouseover="CursorToPointer(this.id);"/></td>
                <td width="37" height="13" align="left" valign="middle"><img src="../IMAGES/v copy.jpg" alt="v" id="v" width="30" height="30" onclick="window.open('ACCOUNTING_VIEW.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>','_blank','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=600,height=377')" onmouseover="CursorToPointer(this.id);" title="Click to view the account information" /></td>
              </tr>
        </table>    </td>
  </tr>
  <tr>
    <td height="240">
	  <iframe name="photodirectory" width="100%" height="100%" scrolling="no" frameborder="0" <?php if (empty($row_PATIENTS)) {echo "class='hidden'";} ?>>
      </iframe>
    </td>
  </tr>
<!--PATIENTS LIST-->
  <tr>
    <td colspan="2" align="left" valign="top">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr class="Andale11Bwhite">
        <td width="180" height="10" bgcolor="#000000" class="Verdana11Bwhite">Name</td>
        <td width="60" height="10" align="left" bgcolor="#000000" class="Verdana11Bwhite">Species</td>
        <td width="206" height="10" bgcolor="#000000" class="Verdana11Bwhite">Breed</td>
        <td width="28" height="10" align="center" bgcolor="#000000" class="Verdana11Bwhite">Sex</td>
        <td width="128" height="10" align="center" bgcolor="#000000" class="Verdana11Bwhite">Age</td>
        <td width="79" height="10" align="center" bgcolor="#000000" class="Verdana11Bwhite">Weight</td>
        <td bgcolor="#000000" class="Verdana11Bwhite">Notes</td>
      </tr>
     </table>
    	<div style="height:210px;overflow:auto;">

    <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
      <tr>
        <td width="188" height="0"></td>
        <td width="55" ></td>
        <td width="202"></td>
        <td width="28"></td>
        <td width="128"></td>
        <td width="79"></td>
        <td></td>
      </tr>
      <?php 
	  do { 
		  include('../ASSETS/photo_directory.php');

	  ?>
	  <input type="hidden" name=patientname value = <?php echo $row_PATIENTS['PETNAME'];?> >
	  <input type="hidden" name=clientname value = <?php echo $row_CLIENT['COMPANY'];?> >
	  <input type="hidden" name=clientname value = <?php echo $row_CLIENT['COMPANY'];?> >
        <tr <?php if (empty($row_PATIENTS)){echo "style='display:none'";}?> class="Verdana11" id="<?php echo $row_PATIENTS['PETID']; ?>" onclick="ClickOnPatient('<?php echo $row_PATIENTS['PETID']; ?>','<?php echo $_GET['refID']; ?>','<?php echo $row_PATIENTS['CUSTNO']; ?>','<?php echo $row_PATIENTS['PETNO']; ?>','<?php echo $row_PATIENTS['PETTYPE']; ?>','<?php  echo mysql_real_escape_string($row_PATIENTS['PETNAME']); ?>','<?php echo $row_PATIENTS['PSEX']; ?>')" onmouseover="highliteline(this.id,'<?php if ($row_PATIENTS['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';} ?>');" onmouseout="whiteoutline(this.id);"  ondblclick="openpage('<?php echo $patient; ?>','<?php echo mysql_real_escape_string($custname); ?>', '<?php echo $custphone; ?>','<?php echo mysql_real_escape_string($petname); ?>','<?php echo $desco; ?>','<?php echo $desct; ?>','<?php echo $custprevbal; ?>','<?php echo $custcurbal; ?>','<?php echo $custterm; ?>','<?php echo $psex; ?>','<?php echo $pdob; ?>','<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>','<?php echo mysql_real_escape_string($address); ?>','<?php echo mysql_real_escape_string($city); ?>');" >
        <td height="15" class="Verdana12B" title="Pet ID:<?php echo $row_PATIENTS['PETID']; ?>"><?php echo $row_PATIENTS['PETNAME']; ?></td>
          <td height="15"><?php if ($row_PATIENTS['PETTYPE']=='1'){echo "Can";} else if ($row_PATIENTS['PETTYPE']=='2'){echo "Fel";} else if ($row_PATIENTS['PETTYPE']=='3'){echo "Equ";}else if ($row_PATIENTS['PETTYPE']=='4'){echo "Bov";}else if ($row_PATIENTS['PETTYPE']=='5'){echo "Cap";}else if ($row_PATIENTS['PETTYPE']=='6'){echo "Por";}else if ($row_PATIENTS['PETTYPE']=='7'){echo "Avi";}else if ($row_PATIENTS['PETTYPE']=='8'){echo "Oth";}; ?></td>
          <td height="15"><?php echo $row_PATIENTS['PETBREED'];  //echo $patient;?></td>
          <td height="15" align="center"><?php echo $psex; if ($row_PATIENTS['PNEUTER'] == 1) {if ($row_PATIENTS['PSEX'] == 'M') {echo '(N)';} else { echo '(S)' ;}} ?></td>
          <td height="15" align="center"><?php agecalculation($tryconnection,$row_PATIENTS['PDOB']); ?></td>
          <td height="15" align="center"><?php if($row_PATIENTS['PWEIGHT']<'10'){echo '&nbsp;'.$row_PATIENTS['PWEIGHT'];} else {echo $row_PATIENTS['PWEIGHT'];}; ?> <?php echo $row_CRITDATA['WEIGHTUNIT']; ?></td>
          <td height="15" align="center"><span class="Verdana11B"><?php if ($row_PATIENTS['PDEAD']=='1'){echo "Dec.";} else if ($row_PATIENTS['PMOVED']=='1'){echo "<span class='Verdana11Blue'>Moved</span>";}?></span><?php if (in_array($row_PATIENTS['PETID'], $dls)){echo "<span class=\"alerttext12\" onclick=\"window.open('../RECEPTION/DUTY_LOG/DUTY_LOG.php','_self')\" title='Click to open the Duty Log'>DL</span>";}  else if (strtotime(validity($row_PATIENTS['POTH8'],'1')) < time() && ($row_PATIENTS['POTH8'] != '00/00/0000')) {echo "<span class=\"alerttext12\" title=\"Exam Overdue\">OVD.</span>";} ?></td>
       </tr>
        
        <?php } while ($row_PATIENTS = mysqli_fetch_assoc($PATIENTS)); 		?>
    </table>
    </div>    
    </td>
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