<?php 
session_start();
unset($_SESSION['client']);
unset($_SESSION['patient']);
unset($_SESSION['petname']);
unset($_SESSION['payments']);
unset($_SESSION['methods']);
unset($_SESSION['onaccount']);

if (isset($_POST['clearsessions'])){
//session_destroy();
$goindex="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');";
}

require_once('../tryconnection.php'); 

if (isset($_GET['refID'])){
$refID=$_GET['refID'];
$_SESSION['refID']=$_GET['refID'];
}
elseif (isset($_SESSION['refID'])){
$refID=$_SESSION['refID'];
}

$ref=$_GET['ref'];
if (!empty($_GET['id'])){
$_SESSION['id']=$_GET['id'];
}
// this added for testing purposes
else {
$_SESSION['id'] = 'waiting' ;
}

if (isset($_GET['filter']))
{
$_SESSION['filter'] = $_GET['filter'];
$filter = $_SESSION['filter'];
}
elseif (isset($_SESSION['filter'])){
$filter = $_SESSION['filter'];
}
else {
$filter='PROBLEM';
} 

if (!empty($_GET['sortingrf']))
{
$_SESSION['sortingrf'] = $_GET['sortingrf'];
$sortby = $_SESSION['sortingrf'];
}
elseif (!isset($_SESSION['sortingrf'])){
$sortby='PETNAME';
} 
else{
$sortby = $_SESSION['sortingrf'];
}

mysqli_select_db($tryconnection, $database_tryconnection);

// Check to see if auto rollover of appointments has happened. If not. do so...

$has_Auto = "SELECT AUTOROLL FROM CRITDATA LIMIT 1 " ;
$query_Auto = mysqli_query($tryconnection, $has_Auto) or die(mysqli_error($mysqli_link)) ;
$row_Auto = mysqli_fetch_array($query_Auto) ;
$autodate = $row_Auto['AUTOROLL'] ;

$today = "SELECT DATE(NOW()) AS DATE ";
$query_date = mysqli_query($tryconnection, $today) or die(mysqli_error($mysqli_link)) ;
$row_date = mysqli_fetch_array($query_date) ;
$date = $row_date['DATE'] ;

if ($autodate < $date ) {
 $anti_lock = "UPDATE ARCUSTO SET LOCKED = 0 " ;
 $query_lock = mysqli_query($tryconnection, $anti_lock) or die(mysqli_error($mysqli_link))  ;
 $update_CRITDATA = "UPDATE CRITDATA SET AUTOROLL = '$date' LIMIT 1" ;
 $query_CRITDATA = mysqli_query($tryconnection, $update_CRITDATA) or die(mysqli_error($mysqli_link)) ;
 
 $Auto_roll = "INSERT INTO RECEP (CUSTNO,NAME,RFPETID,PETNAME,RFPETTYPE,LOCATION,DESCRIP,PSEX,FNAME,PROBLEM,AREA1,PH1,AREA2,PH2,AREA3,PH3,BUSEXT,DATEIN,TIME,
                CLINICIAN) SELECT CUSTNO,NAME,PETID,PETNAME,RFPETTYPE,'1',DESCRIP,PSEX,CONTACT,PROBLEM,CAREA,PHONE1,CAREA2,PHONE2,CAREA3,PHONE3,BUSEXT,DATEOF,TIMEOF,
                SHORTDOC FROM APPTS WHERE DATEOF = '$date' AND CANCELLED <> 1 AND NOT EXISTS (SELECT RFPETID FROM RECEP WHERE RECEP.RFPETID = APPTS.PETID  AND RECEP.DATEIN = '$date' )" ;
                
 $update_it = mysqli_query($tryconnection, $Auto_roll) or die(mysqli_error($mysqli_link)) ;
}

$query_WAITING = "SELECT *, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE LOCATION=1 ORDER BY $sortby ASC";
$WAITING = mysqli_query($tryconnection, $query_WAITING) or die(mysqli_error($mysqli_link));
$row_WAITING = mysqli_fetch_assoc($WAITING);

$query_ADMITTED = "SELECT *, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE LOCATION=2 ORDER BY $sortby ASC";
$ADMITTED = mysqli_query($tryconnection, $query_ADMITTED) or die(mysqli_error($mysqli_link));
$row_ADMITTED = mysqli_fetch_assoc($ADMITTED);

$query_DISCHARGED = "SELECT *, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE LOCATION=3 ORDER BY $sortby ASC";
$DISCHARGED = mysqli_query($tryconnection, $query_DISCHARGED) or die(mysqli_error($mysqli_link));
$row_DISCHARGED = mysqli_fetch_assoc($DISCHARGED);

if (isset($_GET['recepid'])){
$query_delete="DELETE FROM RECEP WHERE RECEPID=".substr($_GET['recepid'],1);
$delete=mysqli_query($tryconnection, $query_delete) or die(mysqli_error($mysqli_link));
header("Location: RECEPTION_FILE.php?ref=DEL");
}

if (isset($_POST['check'])){
$client=$_POST['client'];
$patient=$_POST['patient'];
$rfpettype=$_POST['pettype'];

	if ($_POST['check']=='1'){
	$_SESSION['client']=$client;
	$_SESSION['patient']=$patient;
	
	include('../ASSETS/photo_directory.php');
	$fileused=mysqli_real_escape_string($mysqli_link, $custname);
	$openpage="openpage('$patient', '".mysqli_real_escape_string($mysqli_link, $custname)."', '$custphone', '".mysqli_real_escape_string($mysqli_link, $petname)."', '".mysqli_real_escape_string($mysqli_link, $desco)."', '".mysqli_real_escape_string($mysqli_link, $desct)."', '$custprevbal', '$custcurbal', '$custterm', '$psex', '$pdob', '$rfpettype','".mysqli_real_escape_string($mysqli_link, $address)."','".mysqli_real_escape_string($mysqli_link, $city)."');";	
	}//if ($_POST['check']=='1')
}//if (isset($_POST['check']))

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.60" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title"></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<link rel="stylesheet" type="text/css" href="../ASSETS/print.css" media="print"/>

<script type="text/javascript">
setInterval("self.location.reload()", 600000);
if (!sessionStorage.refID){
document.getElementById('title').innerText="RECEPTION SCREEN";
}
else
{
document.getElementById('title').innerText="RECEPTION SCREEN FOR "+sessionStorage.refID;
}

function bodyonload(){
<?php echo $goindex; ?>

sessionStorage.setItem('fileused','<?php echo mysqli_real_escape_string(0,25), substr($fileused); ?>');
sessionStorage.setItem('filetype','C');

document.getElementById('inuse').innerText=localStorage.xdatabase;

<?php echo $openpage; ?>
document.getElementById('waiting').bgColor="#"+localStorage.theme;
document.getElementById('admitted').bgColor="#"+localStorage.theme;
document.getElementById('discharged').bgColor="#"+localStorage.theme;
	if (localStorage.theme=='CC0033' || localStorage.theme=='FF0099' || localStorage.theme=='0000FF' || localStorage.theme=='330099' || localStorage.theme=='000000'){
	document.getElementById('waiting').style.color="#FFFFFF";
	document.getElementById('admitted').style.color="#FFFFFF";
	document.getElementById('discharged').style.color="#FFFFFF";
	}
	else {
	document.getElementById('<?php echo $_SESSION['id']; ?>').style.color="#000000";
	}
document.getElementById('<?php echo $_SESSION['id']; ?>').bgColor="#"+localStorage.theme;
}

function bodyonunload() {
}

function deletion(x)
{
self.location="RECEPTION_FILE.php?recepid=" + x + "&ref=DEL";
}

function setsorting(x,y)
{
//reloads the window with sorting criteria
self.location='RECEPTION_FILE.php?sortingrf=' + x + '&id=' + y;
}

function setpatient(pettype,petname,client,patient,check,recepid,psex){
	var tablename=document.getElementById('twaiting');
	var trname=tablename.getElementsByTagName('tr');
	for (var i=0; i<trname.length; i++){
	trname[i].style.backgroundColor="#FFFFFF";
	}
	var tablename=document.getElementById('tadmitted');
	var trname=tablename.getElementsByTagName('tr');
	for (var i=0; i<trname.length; i++){
	trname[i].style.backgroundColor="#FFFFFF";
	}
	var tablename=document.getElementById('tdischarged');
	var trname=tablename.getElementsByTagName('tr');
	for (var i=0; i<trname.length; i++){
	trname[i].style.backgroundColor="#FFFFFF";
	}

	sessionStorage.setItem('patient',patient);
	sessionStorage.setItem('petname',petname);
    sessionStorage.setItem('client',client);
    sessionStorage.setItem('recepid',recepid);
    sessionStorage.setItem('psex',psex);

document.reception.pettype.value=pettype;
document.reception.petname.value=petname;
document.reception.client.value=client;
document.reception.patient.value=patient;
document.reception.check.value=check;
document.reception.recepid.value=recepid;
document.reception.psex.value=psex;
	if (psex=='M'){bgcol='#2FC3F5';} else {bgcol='#FF99CC';}
document.getElementById(recepid).style.backgroundColor=bgcol;
}

function openquery(pettype,client,patient,check,recepid,psex){
document.reception.pettype.value=pettype;
document.reception.client.value=client;
document.reception.patient.value=patient;
document.reception.check.value=check;
document.reception.recepid.value=recepid;
document.reception.psex.value=psex;
	if (sessionStorage.refID!=undefined){
	document.reception.submit();
	}
}
function ipadquery() {
var pettype=sessionStorage.pettype ;
var client=sessionStorage.client ;
var patient=sessionStorage.patient ;
var check=1 ;
var recepid=sessionStorage.recepid ;
var psex=sessionStorage.psex ;
openquery(pettype,client,patient,check,recepid,psex) ;

}

function highliteline(x,y){
document.getElementById(x).style.cursor="pointer";
document.getElementById(x).style.backgroundColor=y;
document.getElementById(document.reception.recepid.value).style.backgroundColor=bgcol;
}

function whiteoutline(x){
document.getElementById(x).style.backgroundColor="#FFFFFF";
document.getElementById(document.reception.recepid.value).style.backgroundColor=bgcol;
}

function editrf(){
var recepid=document.reception.recepid.value;
window.open('PRESENTING_PROBLEM.php?recepid='+recepid,'_self');
}
function viewrf(){
var recepid=document.reception.recepid.value;
window.open('PROBLEM_ZOOM.php?recepid='+recepid,'_blank','width=600,height=360');
}
function qweight(){
var petname=document.reception.petname.value;
var patient=document.reception.patient.value;
window.open('../PATIENT/QUICK_WEIGHT.php?patient='+patient+'&petname='+petname,'_blank', 'width=400,height=200');
}

</script>

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
<form method="post" action="" name="reception">
<input type="hidden" name="pettype" value=""  />
<input type="hidden" name="petname" value=""  />
<input type="hidden" name="client" value=""  />
<input type="hidden" name="patient" value="" />
<input type="hidden" name="recepid" value="" />
<input type="hidden" name="psex" value="" />
<input type="hidden" name="check" value=""  />

<table width="733" border="0" cellpadding="0" cellspacing="0">
<tr height="15" bgcolor="#000000">
<td width="51" id="date" onclick="setsorting('DATEIN',this.id);" onmouseover="CursorToPointer(this.id)"  title="Click to sort chronologically by date"  class="Verdana11Bwhite">Date</td>
<td width="65" align="left"  class="Verdana11Bwhite">Doctor</td>
<td width="118" id="client" onclick="setsorting('NAME',this.id);" onmouseover="CursorToPointer(this.id)" title="Click to sort by client name"  class="Verdana11Bwhite">Client</td>
<td width="95" id="patient" onclick="setsorting('PETNAME',this.id);" onmouseover="CursorToPointer(this.id)" title="Click to sort by patient name" class="Verdana11Bwhite">Patient</td>
<td width="140"  class="Verdana11Bwhite">Description</td>
<td width="264"  class="Verdana11Bwhite"><span <?php if ($filter=="PHONE"){echo "class='hidden'";} ?>>Presenting Problem</span><span <?php if ($filter=="PROBLEM"){echo "class='hidden'";} ?>>Phone numbers</span></td>
</tr>
<tr>
<td colspan="6">


<table width="100%" border="1" cellpadding="0" cellspacing="0" class="table">
<tr>
<td id="waiting" height="15" align="center" class="Verdana11B">&nbsp;IN WAITING ROOM&nbsp;</td>
</tr>
<tr>
<td valign="top">

<!--WAITING--> 

<div class="receptionfilediv">
<table id="twaiting" width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
<?php 
	  if (!empty($row_WAITING)){

	  do {
	  
echo  '<tr heigth"15" class="Verdana11" id="'.$row_WAITING['RECEPID'].'"  onmouseover="highliteline(this.id,\'';
		if ($row_WAITING['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}
// that highlights the gender of the patient

echo  '\')" onmouseout="whiteoutline(this.id);" ondblclick="openquery(\''.$row_WAITING['RFPETTYPE'].'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'1\',\''.$row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')" >
<td class="Courier11" width="50" onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''.$row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')">'.$row_WAITING['DATEIN'].'</td>
<td width="60" align="left"';
		
		if ($ref=="DEL"){echo "style='display:none'";}
echo	'onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''.$row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')">';

if (substr($row_WAITING['CLINICIAN'],0,3)=='Dr.'){echo substr($row_WAITING['CLINICIAN'],4,8);} else {echo substr($row_WAITING['CLINICIAN'],0,9);}

echo 	'</td>
<td width="60" class="Verdana13BRed" id="a'.$row_WAITING['RECEPID'].'" align="center"';

// This section just for sign outs

		if ($ref!="DEL"){echo "style='display:none'";} 
		
echo	'onclick="deletion(\'a'. $row_WAITING['RECEPID'].'\')" onmouseover="CursorToPointer(this.id);">
X
</td>
<td width="120" onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''. $row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')" >'.$row_WAITING['NAME'].",&nbsp;".$row_WAITING['FNAME'].'</td>
<td width="100" onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''.$row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')">'.$row_WAITING['PETNAME'].'</td>
<td width="140" onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''. $row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')">'.$row_WAITING['DESCRIP'].'</td>
<td onclick="setpatient(\''.$row_WAITING['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_WAITING['PETNAME']).'\',\''.$row_WAITING['CUSTNO'].'\',\''.$row_WAITING['RFPETID'].'\',\'2\',\''.$row_WAITING['RECEPID'].'\',\''.$row_WAITING['PSEX'].'\')"><span ';
		if ($filter=="PHONE"){echo "class='hidden'";} 
echo	'>'.substr($row_WAITING['PROBLEM'],0,30).'</span><span ';
		if ($filter=="PROBLEM"){echo "class='hidden'";}
echo	'>('.$row_WAITING['AREA1'].')'.$row_WAITING['PH1'].', ('.$row_WAITING['AREA2'].')'.$row_WAITING['PH2'].', ('.$row_WAITING['AREA3'].')'.$row_WAITING['PH3'].'</span></td>
</tr>';

// end of sign out section.
	  
} while ($row_WAITING = mysqli_fetch_assoc($WAITING));
	  }
	  ?>
</table>
</div></td>
</tr>
<tr align="center" class="Verdana11BPink">
<td id="admitted" height="15" align="center" class="Verdana11B">&nbsp;ADMITTED / IN EXAM&nbsp; </td>
</tr>
<tr>
<td valign="top">

<div class="receptionfilediv">
<table id="tadmitted" width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">
<?php 
		if (!empty($row_ADMITTED)){

		do {
	  
echo  '<tr heigth"15" class="Verdana11" id="'.$row_ADMITTED['RECEPID'].'"  onmouseover="highliteline(this.id,\'';
		if ($row_ADMITTED['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}
echo  '\')" onmouseout="whiteoutline(this.id);" ondblclick="openquery(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'1\',\''.$row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')" >
<td class="Courier11" width="50" onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''.$row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')">'.$row_ADMITTED['DATEIN'].'</td>
<td width="60" align="center"';
		
		if ($ref=="DEL"){echo "style='display:none'";}
echo	'onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''.$row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')">';

if (substr($row_ADMITTED['CLINICIAN'],0,3)=='Dr.'){echo substr($row_ADMITTED['CLINICIAN'],4,8);} else {echo substr($row_ADMITTED['CLINICIAN'],0,8);}

echo 	'</td>
<td width="60" class="Verdana13BRed" id="a'.$row_ADMITTED['RECEPID'].'" align="center"';
		if ($ref!="DEL"){echo "style='display:none'";} 
		
echo	'onclick="deletion(\'a'. $row_ADMITTED['RECEPID'].'\')" onmouseover="CursorToPointer(this.id);">
X
</td>
<td width="120" onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''. $row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')" >'.$row_ADMITTED['NAME'].",&nbsp;".$row_ADMITTED['FNAME'].'</td>
<td width="100" onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''.$row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')">'.$row_ADMITTED['PETNAME'].'</td>
<td width="140" onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''. $row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')">'.$row_ADMITTED['DESCRIP'].'</td>
<td onclick="setpatient(\''.$row_ADMITTED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_ADMITTED['PETNAME']).'\',\''.$row_ADMITTED['CUSTNO'].'\',\''.$row_ADMITTED['RFPETID'].'\',\'2\',\''.$row_ADMITTED['RECEPID'].'\',\''.$row_ADMITTED['PSEX'].'\')"><span ';
		if ($filter=="PHONE"){echo "class='hidden'";} 
echo	'>'.substr($row_ADMITTED['PROBLEM'],0,30).'</span><span ';
		if ($filter=="PROBLEM"){echo "class='hidden'";}
echo	'>('.$row_ADMITTED['AREA1'].')'.$row_ADMITTED['PH1'].', ('.$row_ADMITTED['AREA2'].')'.$row_ADMITTED['PH2'].', ('.$row_ADMITTED['AREA3'].')'.$row_ADMITTED['PH3'].'</span></td>
</tr>';

	  
} while ($row_ADMITTED = mysqli_fetch_assoc($ADMITTED));
	 } 
	  ?>
</table>
</div>    </td>
</tr>
<tr align="center" class="Verdana11BPink">
<td id="discharged" height="15" align="center" class="Verdana11B">&nbsp;READY FOR DISCHARGE&nbsp;</td>
</tr>
<tr>
<td valign="top">

<div class="receptionfilediv">
<table id="tdischarged" width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#CCCCCC" frame="below" rules="rows">

	  <?php
	  if (!empty($row_DISCHARGED)){
	   do {
	  
echo  '<tr heigth"15" class="Verdana11" id="'.$row_DISCHARGED['RECEPID'].'"  onmouseover="highliteline(this.id,\'';
		if ($row_DISCHARGED['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}
echo  '\')" onmouseout="whiteoutline(this.id);" ondblclick="openquery(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'1\',\''.$row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')" >
<td class="Courier11" width="50" onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''.$row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')">'.$row_DISCHARGED['DATEIN'].'</td>
<td width="60" align="center"';

if (substr($row_DISCHARGED['CLINICIAN'],0,3)=='Dr.'){echo substr($row_DISCHARGED['CLINICIAN'],4,8);} else {echo substr($row_DISCHARGED['CLINICIAN'],0,8);}
		
		if ($ref=="DEL"){echo "style='display:none'";}
echo	'onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''.$row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')">';

echo 	'</td>
<td width="60" class="Verdana13BRed" id="a'.$row_DISCHARGED['RECEPID'].'" align="center"';
		if ($ref!="DEL"){echo "style='display:none'";} 
		
echo	'onclick="deletion(\'a'. $row_DISCHARGED['RECEPID'].'\')" onmouseover="CursorToPointer(this.id);">
X
</td>
<td width="120" onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''. $row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')" >'.$row_DISCHARGED['NAME'].",&nbsp;".$row_DISCHARGED['FNAME'].'</td>
<td width="100" onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''.$row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')">'.$row_DISCHARGED['PETNAME'].'</td>
<td width="140" onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''. $row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')">'.$row_DISCHARGED['DESCRIP'].'</td>
<td onclick="setpatient(\''.$row_DISCHARGED['RFPETTYPE'].'\',\''.mysqli_real_escape_string($mysqli_link, $row_DISCHARGED['PETNAME']).'\',\''.$row_DISCHARGED['CUSTNO'].'\',\''.$row_DISCHARGED['RFPETID'].'\',\'2\',\''.$row_DISCHARGED['RECEPID'].'\',\''.$row_DISCHARGED['PSEX'].'\')"><span ';
		if ($filter=="PHONE"){echo "class='hidden'";} 
echo	'>'.substr($row_DISCHARGED['PROBLEM'],0,30).'</span><span ';
		if ($filter=="PROBLEM"){echo "class='hidden'";}
echo	'>('.$row_DISCHARGED['AREA1'].')'.$row_DISCHARGED['PH1'].', ('.$row_DISCHARGED['AREA2'].')'.$row_DISCHARGED['PH2'].', ('.$row_DISCHARGED['AREA3'].')'.$row_DISCHARGED['PH3'].'</span></td>
</tr>';

	  
} while ($row_DISCHARGED = mysqli_fetch_assoc($DISCHARGED));
	  }
	  ?>
</table>
</div>    </td>
</tr>
</table>    </td>
</tr>
<tr>
<td colspan="6" align="center" class="ButtonsTable"><input name="gopro" type="button" class="button" value="OK"  onclick="ipadquery();"  <?php if ($ref=="DEL"){echo "style='display:none'";} ?> /><input name="signin" id="signin" type="button" class="button" value="SIGN IN" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> onclick="sessionStorage.setItem('refID','SIGN IN'); window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php','_self');" title="Click to sign in a patient"/><input name="signout" type="button" class="button" value="SIGN OUT" onclick="self.location='RECEPTION_FILE.php?ref=DEL';" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> title="Click to sign out a patient or delete entire reception file"/><input name="edit" type="button" class="button" value="EDIT" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> onclick="editrf();"/><input name="view" type="button" class="button" value="VIEW" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> onclick="viewrf();"  /><input name="finished" type="button" class="button" value="FINISHED" <?php if ($ref!="DEL"){echo "style='display:none'";} ?> onclick="self.location='RECEPTION_FILE.php';"/><input name="input2" type="button" class="button" value="PROBLEM" <?php if ($filter=="PROBLEM"){echo "style='display:none'";} ?> onclick="self.location='RECEPTION_FILE.php?filter=PROBLEM'" title="Click to display presenting problem"/><input name="input3" type="button" class="button" value="PHONE #" <?php if ($filter=="PHONE"){echo "style='display:none'";} ?> onclick="self.location='RECEPTION_FILE.php?filter=PHONE'" title="Click to display client's phone numbers"/><input name="input" type="button" class="button" value="CLIENTS" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> title="Click to go to search screen" onclick="window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=<?php echo $_GET['refID']; ?>','_self')"/>
<input name="signin4" type="button" class="button" value="WEIGHT" <?php if ($ref=="DEL"){echo "style='display:none'";} ?> onclick="qweight();" /><input name="cancel" type="button" class="button" value="CANCEL" <?php if ($ref=="DEL"){echo "style='display:none'";} ?>  title="Click to go back to home page" onclick="self.location='/'+localStorage.xdatabase+'/INDEX.php'"/><input name="input4" type="button" class="button" value="CANCEL" <?php if ($ref!="DEL"){echo "style='display:none'";} ?> onclick="self.location='RECEPTION_FILE.php'" title="Click to exit signing out" />
<!--<script type="text/javascript">document.write(sessionStorage.filetype);</script>-->
</td>
</tr>
</table>

</form>
<span class="Verdana10"><?php //print_r($_SESSION); ?></span>

<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>