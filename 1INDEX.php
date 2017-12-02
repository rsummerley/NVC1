<?php
 
/////////////
session_start() ;
session_destroy() ;
unset($_SESSION) ;
unset($_COOKIE) ;
//////////
session_start(); 
session_regenerate_id(TRUE) ;
$_SESSION=array() ;

date_default_timezone_set('America/Toronto');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.2" />
<title>DV MANAGER</title>
<link rel="stylesheet" type="text/css" href="ASSETS/styles.css" />

<style type="text/css">
#MenuBar {
	width:550px;
	height:24px;
	background-color: #DCF6DD;
	border: double;
	position:absolute;
	top:0px;
	left:55px;
	z-index:3;
}

#LogoHead {
	position:absolute;
	left:0px;
	top:0px;
	width:50px;
	height:24px;
	z-index:3;
	background-color: #DCF6DD;
	font-family: Verdana;
	font-size: large;
	color: #0C5C00;
	font-weight: bolder;
	text-align: center;
	border-style: double;
}

#inuse {
	position:absolute;
	right:0px;
	top:0px;
	width: 169px;
	z-index:3;
	background-color: #DCF6DD;
	font-family: Verdana;
	font-size:11px;
	color: #0C5C00;
	text-align: center;
	border-style: double;
	padding-top:6px;
	padding-bottom:5px;
}


.disabled {
color:#999999;
}

.SphereBg {
	color: #000000;
	font-size: 12px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-weight:400;
	background-image:url(IMAGES/lgreen.jpg);
	background-repeat: no-repeat;
}

.newSphereBg {
	color: #FFFFFF;
	font-size: 12px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-weight:400;
	background-image: url(IMAGES/dgreen.jpg);
	background-repeat: no-repeat;
	cursor:pointer;
}

#WindowBody {
	position:absolute;
	top:60px;
	width:733px;
	height:553px;
	z-index:2;
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	left: 25px;
	background:none;
}

#quickbuttons{
	position:absolute;
	left:0px;
	top:0px;
	z-index: 3;
	width: 100%;
}

#bgpicture {
background-repeat:no-repeat;
width:500px;
height:500px;
position:absolute;
left:160px;
top:45px;
z-index:2;
}

#whitebg{
background-color:#FFFFFF;
opacity:0.5;
width:732px;
height:553px;
z-index: 2;
position:absolute;
left:0px;
top:0px;
}

a {
color:#000000;
}


#apDiv1 {
	position:absolute;
	width:91px;
	height:24px;
	z-index:4;
	left: 649px;
	top: 66px;
}

.button{
width:100px;
}
.style8 {color: #FFFFFF}

#bubble {
	position:absolute;
	z-index:2;
	width:150px;
	left: 5px;
	top: 74px;
}
#divbubble {
	position:absolute;
	z-index:10;
	width:150px;
	height:200px;
	left: 10px;
	top: 55px;
	/*border:solid thin #000000*/;
}
</style>
<script type="text/JavaScript">
setInterval("self.location.reload()", 3000000);
sessionStorage.setItem('filetype','0');

function SphereBgOnMouseOver(x) {
	x.className=(x.className=="SphereBg")?"newSphereBg":"newSphereBg";
}
function SphereBgOnMouseOut(x) {
	x.className=(x.className=="newSphereBg")?"SphereBg":"SphereBg";
}

function bodyonload() {
 window.moveTo(450,0) ;

document.getElementById('bubble').style.display='none';
sessionStorage.setItem('filetype','0');
sessionStorage.removeItem('goto');
sessionStorage.removeItem('refID');
//document.getElementById('inuse').innerText=sessionStorage.fileused;
document.getElementById('bgpicture').style.backgroundImage='url(IMAGES/BGROUNDS/HOSPICT.jpg)';
}

var client=sessionStorage.client;
function openclient(){
window.open('CLIENT/CLIENT_PATIENT_FILE.php?client='+client,'_self');
}


function xopen(){
}


function showbubble(){
document.getElementById('bubble').style.display='';
document.getElementById('divbubble').style.cursor='pointer';
document.getElementById('divbubble').innerHTML="<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Need<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Help?";
}

function hidebubble(){
document.getElementById('bubble').style.display='none';
document.getElementById('divbubble').innerHTML="";
}

</script>
<script type="text/JavaScript" src="ASSETS/navigation.js"></script>
<script type="text/JavaScript" src="ASSETS/scripts.js"></script>
</head>
<body onload='bodyonload()' onunload='bodyonunload()'>

<div id="LogoHead">DVM</div>

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="">About DV Manager</span></a></li>
                <li><a onclick="utilities();">Utilities</a></li>
                <li><a onclick="window.open('TRACKER/TRACKER.php','_self');"></a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="">Casual Sale Invoicing</span></a></li>
                <li><a href="#" onclick="nav0();">Regular Invoicing</a></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="cancelinvoices()"><span class="">Cancel Invoices</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/INVOICE/COMMENTS/COMMENTS_LIST.php?path=DIRECTORY','_blank','width=733,height=553,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');">Comments</a></li>
                <li><a href="#" onclick="tffdirectory()">Treatment and Fee File</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Worksheet File</span></a></li>
                <li><a href="#" onclick="procdirectory()"><span class="">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick="invreports();"><span class="">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('APPOINTMENTS/MONTH.php?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>','_blank', 'width=850,height=880''toolbar=no, status=no')"><span class="">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick="reception();">Patient Registration</a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/RECEPTION/USING_REG_FILE.php','_blank','width=560,height=545')">Using Reception File</a></li>
                <li><a href="#" onclick="nav2();"><span class="hidden"></span>Examination Sheets</a></li>
                <li><a href="#" onclick="gexamsheets()"><span class="">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick="nav3();">Duty Log</a></li>
                <li><a href="#" onclick="staffsiso();">Staff Sign In &amp; Out</a></li>
                <li><a href="#" onclick="window.open('INVOICE/INVOICING_REPORTS/END_OF_DAY_DIR.php','_self')"><span class="">End of Day Accounting Reports</span></a></li>
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
                <li><a href="#" onclick="createnewclient()">Create New Client</a></li>
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
                <li><a href="#" onclick="accreports()">Accounting Reports</a></li>
                <li><a href="#" onclick="inventorydir();" id="inventory" name="inventory">Inventory</a></li>
                <li><a href="#" onclick="busstat();" id="busstatreport" name="busstatreport"><span class="">Business Status Report</span></a></li>
                <li><a href="#" onclick="hospitalstat();" id="hospstatistics" name="hospstatistics"><span class="">Hospital Statistics</span></a></li>
                <li><a href="#" onclick="monthend()" id="monthend" name="monthend"><span class="">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('MAILING/RECALLS/RECALLS_DIRECTORY.php','_self')" ><span class="">Recalls and Searches</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Handouts</span></a></li>
                <li><a href="#" onclick="window.open('MAILING/MAILING_LOG/MAILING_LOG_DIRECTORY.php?refID=','_self')">Mailing Log</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?refID=1','_blank','width=700,height=473')">Referring Clinics and Doctors</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Referral Adjustments</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse"><?php echo date('m/d/Y'); ?></div>



<div id="WindowBody">
<div id="bgpicture"></div>


  <img src="IMAGES/BGROUNDS/bubble.jpg" id="bubble" alt="bubble" width="150"/>
  <div id="divbubble" onmouseover="showbubble()" onmouseout="hidebubble()" class="Verdana13BRed" onclick="window.open('UTILITIES/HINTS.php','_blank','width=620, height=700')"></div>

<div id="quickbuttons">
<table width="100%" border="1" cellpadding="0" cellspacing="0" class="table">
  <tr>
    <td height="40" align="center" valign="middle" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td height="40" align="center" valign="middle" bgcolor="#0000FF" class="Verdana20B style8"><?php //echo date('m/d/Y'); ?>
        <script type="text/javascript">document.write(localStorage.hospname);</script>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="269" align="center" valign="middle" class="Verdana12">

<div style="display:none;">    
    
    <br />
    
		<script type="text/javascript">
		document.write(localStorage.xdatabase);
		sessionStorage.clear();
        </script>
</div>    



</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="center" bgcolor="#FFFFFF">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
	
    <td width="171" align="center" valign="middle" class="SphereBg" id="a8" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="window.open('APPOINTMENTS/MONTH.php?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>','_blank', 'toolbar=no, status=no, width=910, height=820')"><span class="">APPOINTMENTS</span></td>    
    
    <td width="10" align="center" valign="middle" bgcolor="#FFFFFF" ></td>
	
    <td width="171" height="60" align="center" valign="middle" class="SphereBg" id="a7" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="nav0();">REGULAR INVOICING</td>
 
    <td width="10" align="center" valign="middle" bgcolor="#FFFFFF"></td>

	<td width="171" align="center" valign="middle" class="SphereBg" id="a9" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="nav11();">ESTIMATE</td>
 
    <td align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>    </td>
  </tr>
  <tr>
    <td height="20"></td>
    </tr>
  <tr>
    <td bgcolor="#FFFFFF">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
	
    <td width="171" height="60" align="center" valign="middle" class="SphereBg" id="a" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="nav4();">PROCESSING MENU</td>    
    
    <td width="10" align="center" valign="middle" bgcolor="#FFFFFF"></td>
    
    <td width="171" height="60" align="center" valign="middle" class="SphereBg" id="a4" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="nav2();">EXAM SHEETS</td>
    
    <td width="10" align="center" valign="middle" bgcolor="#FFFFFF"></td>
	
    <td width="171" align="center" valign="middle" class="SphereBg" id="a3" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="signin();">SIGN IN PATIENT</td>    
    <td align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>    </td>
  </tr>
    <tr>
    <td height="20">&nbsp;</td>
    </tr>
  <tr>
    <td bgcolor="#FFFFFF">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
	
    <td width="171" align="center" valign="middle" class="SphereBg" id="a2" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="window.open('/'+localStorage.xdatabase+'/INVENTORY/INVENTORY_MAINTENANCE/PRICE_INQUIRIES.php?ref=Lookup','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')">PRICE INQUIRIES</td>   
    
    <td width="10" height="60" align="center" valign="middle" bgcolor="#FFFFFF"></td>
    
	<td width="171" align="center" valign="middle" class="SphereBg" id="a6" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);"  onclick="inventorydir();">INVENTORY<br />
      MAINTENANCE</td>
   
   	<td width="10" align="center" valign="middle" bgcolor="#FFFFFF"></td>
    
	<td width="171" align="center" valign="middle" class="SphereBg" id="a5" onmouseover="SphereBgOnMouseOver(this);" onmouseout="SphereBgOnMouseOut(this);" onclick="nav5();">REVIEW MEDICAL<br />
    HISTORY</td>         
    
    <td align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>    </td>
  </tr>
</table>
</div>

<div id="whitebg"></div>

</div>


<div id="apDiv1">
  <input name="button" type="button" class="hidden" id="button" value="TIMECLOCK" />
</div>
</body>
</html>
