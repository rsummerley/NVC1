<?php
session_start();
unset($_SESSION['start']);
//if (!empty($_GET['refID'])){
//$_SESSION['refID']=$_GET['refID'];
//}

if (isset($_POST['check2'])){
unset($_SESSION['company']);
unset($_SESSION['contact']);
unset($_SESSION['searchpet']);
unset($_SESSION['invnumber']);
unset($_SESSION['phone']);
unset($_SESSION['sorting']);
header('Location:CLIENT_SEARCH_SCREEN.php');
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title id="title"></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
</style>

<script type="text/javascript">

if (!sessionStorage.refID){
document.getElementById('title').innerText="SEARCH SCREEN";
}
else if (sessionStorage.refID=='MOVE PATIENT'){
document.getElementById('title').innerText="SELECT ORIGINAL CLIENT";
}
else if (sessionStorage.refID=='MOVE PATIENT+INVOICES'){
document.getElementById('title').innerText="SELECT ORIGINAL CLIENT";
}
else if (sessionStorage.refID=='MOVE INVOICES'){
document.getElementById('title').innerText="SELECT ORIGINAL CLIENT";
}
else if (sessionStorage.refID=='MERGE HISTORY'){
document.getElementById('title').innerText="SELECT ORIGINAL CLIENT";
}
else if (sessionStorage.refID=='TARGET1' || sessionStorage.refID=='TARGET2' || sessionStorage.refID=='TARGET3'){
document.getElementById('title').innerText="SELECT TARGET CLIENT";
}
else if (sessionStorage.refID=='TARGET4'){
document.getElementById('title').innerText="SELECT TARGET CLIENT AND PATIENT";
}
else{
document.getElementById('title').innerText="SEARCH SCREEN FOR "+sessionStorage.refID;
}

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;

document.getElementById('company').focus();
sessionStorage.setItem('filetype','0');
document.searchclient.submit();
document.getElementById('<?php echo $_GET['id']; ?>').bgColor="#FF0099";
}

function isiPad() {
var test=navigator.userAgent ;
var itis = (test.search("iPad")) ;
}

function setsorting(x,y)
{
self.location='CLIENT_SEARCH_SCREEN.php?sorting=' + x + '&refID=<?php echo $_GET['refID']; ?>&id=' + y;
}

function displaynext()
{
window.frames[0].self.location='NEW_SEARCH_IFRAME.php?navigation=next';
}

function displayprevious()
{
window.frames[0].location.href='NEW_SEARCH_IFRAME.php?navigation=prev';
}

function displayfirst()
{
window.frames[0].self.location='NEW_SEARCH_IFRAME.php?navigation=first';
}

function displaylast()
{
window.frames[0].self.location='NEW_SEARCH_IFRAME.php?navigation=last';
}

function displaypatients(){
document.getElementById('phone').style.display="none";
document.getElementById('searchpet').style.display="";
document.getElementById('invnumber').style.display="none";
document.getElementById('address').style.display="none";
document.getElementById('patients').style.display="";
document.getElementById('phonenumber').style.display="none";
document.getElementById('invoice').style.display="none";
document.getElementById('searchpet').focus();
}
function displayaddress(){
document.getElementById('phone').style.display="none";
document.getElementById('searchpet').style.display="none";
document.getElementById('invnumber').style.display="none";
document.getElementById('address').style.display="";
document.getElementById('patients').style.display="none";
document.getElementById('phonenumber').style.display="none";
document.getElementById('invoice').style.display="none";
}
function displayphone(){
document.getElementById('phone').style.display="";
document.getElementById('searchpet').style.display="none";
document.getElementById('invnumber').style.display="none";
document.getElementById('address').style.display="none";
document.getElementById('patients').style.display="none";
document.getElementById('phonenumber').style.display="";
document.getElementById('invoice').style.display="none";
document.getElementById('phone').focus();
}
function displayinvoice(){
document.getElementById('phone').style.display="none";
document.getElementById('searchpet').style.display="none";
document.getElementById('invnumber').style.display="";
document.getElementById('address').style.display="none";
document.getElementById('patients').style.display="none";
document.getElementById('phonenumber').style.display="none";
document.getElementById('invoice').style.display="";
document.getElementById('invnumber').focus();
}


//function showPatients()
//{
//document.getElementById('address').style.display="none";
//document.getElementById('patients').style.display="";
////window.frames[0].document.getElementById().style.display="";
//}
//
//function showAddress()
//{
//document.getElementById('address').style.display="";
//document.getElementById('patients').style.display="none";
//}


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

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}


function bodyonunload(){
sessionStorage.setItem('cancel',document.location);
}

</script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../IMAGES/firsthover.jpg','../IMAGES/previoushover.jpg','../IMAGES/nexthover.jpg','../IMAGES/lasthover.jpg')" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
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
<form action="NEW_SEARCH_IFRAME.php" method="get" target="list" name="searchclient">
<input type="hidden" name="start" value="0" />
<input type="hidden" name="refID" value="<?php echo $_GET['refID']; ?>" />
<input type="hidden" name="sorting" id="sorting" value="<?php echo $_GET['sorting']; ?>" />
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  <td colspan="5" align="left" valign="top">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <tr>
    <td width="56" bgcolor="#000000" id="client" class="Verdana11Bwhite" onclick="setsorting('CUSTNO','client');" onmouseover="document.getElementById(this.id).style.cursor='pointer';">Client #</td>
    <td width="150" align="left" bgcolor="#000000" class="Verdana11Bwhite" id="lastname" onclick="setsorting('COMPANY,CONTACT',this.id);"onmouseover="document.getElementById(this.id).style.cursor='pointer';">Last Name</td>
    <td width="95" align="left" bgcolor="#000000" id="firstname" class="Verdana11Bwhite" onclick="setsorting('CONTACT',this.id);" onmouseover="document.getElementById(this.id).style.cursor='pointer';">First Name</td>
    <td width="263" bgcolor="#000000" class="Verdana11Bwhite">
    <span id="address" style="display:<?php if ($_SESSION['display']=="2"){echo "";} else {echo "none";} ?>">Address</span>
    <span id="patients" style="display:<?php if (!isset($_SESSION['display'])){echo "";}  elseif ($_SESSION['display']=="1"){echo "";} else {echo "none";} ?>">Patients</span>
    <span id="phonenumber" style="display:<?php if ($_SESSION['display']=="3"){echo "";} else {echo "none";} ?>">Phone number</span>
    <span id="invoice" style="display:<?php if ($_SESSION['display']=="4"){echo "";} else {echo "none";} ?>">Invoice</span>    </td>
    <td width="108" align="left" valign="middle" bgcolor="#000000" class="Verdana11Bwhite"></td>
    <td align="left" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" width="61">Balance</td>
  </tr>
<!--  <tr>
<td colspan="4">

 <table width="100%" cellpadding="0" cellspacing="0">-->  
 <tr>
      <td width="56" height="10"><input name="custno" type="text" class="Input" id="custno" size="6" maxlength="6"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['custno']; ?>"/></td>
	  <td width="150" height="10" align="left"><input name="company" type="text" class="Input" id="company" size="17"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['company']; ?>"/></td>      
      <td width="95" height="10" align="left"><input name="contact" type="text" class="Input" id="contact" size="10"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['contact']; ?>"/></td>
      <td height="10" align="left" class="Verdana10">
      <!-------------------- PHONE NUMBER------------------------------>
      <input type="text" name="phone" class="Input" id="phone" size="8"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['phone']; ?>" style="display:<?php if ($_SESSION['display']=="3"){echo "";} else {echo "none";} ?>" title="123-4567 (no area code)"/>
      <!----------------------- PETNAME-------------------------------->
      <input type="text" name="searchpet" class="Input" id="searchpet" size="12"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['searchpet']; ?>" style="display:<?php if (!isset($_SESSION['display'])){echo "";}  elseif ($_SESSION['display']=="1"){echo "";} else {echo "none";} ?>"/>
      <!----------------------- INVOICE NUMBER -------------------------->
      <input type="text" name="invnumber" class="Input" id="invnumber" size="10"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['invnumber']; ?>" style="display:<?php if ($_SESSION['display']=="4"){echo "";} else {echo "none";} ?>"/></td>
      <td align="right" class="Andale12noDecor"><input type="button" name="clear" value="Clear search" onclick="document.clearsessions.submit();" /></td>
      <td align="left" class="Andale12noDecor"></td>
    </tr>
  <!--</table>  </td>
  </tr>-->
  <tr>
  <td colspan="6"><iframe name="list" id="list" scrolling="auto" height="465" width="100%" frameborder="0" ></iframe></td>
  </tr>
  </table></td>
  </tr>


    <tr> 
    <td width="29" align="center" class="Verdana12BPink" id="first" onclick="displayfirst()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','../IMAGES/firsthover.jpg',1)" title="Click to display first set of results"><img src="../IMAGES/first.jpg" alt="first" name="Image1" width="12" height="18" border="0" id="Image1" /></a></td>
    <td width="117" align="left" class="Verdana12BPink" id="previous" onclick="displayprevious()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','../IMAGES/previoushover.jpg',1)" title="Click to display previous set of results"><img src="../IMAGES/previous.jpg" alt="previous" name="Image4" width="12" height="18" border="0" id="Image4" /></a></td>
    <td width="484" align="center" class="Verdana12BPink">    </td>
    <td width="73" align="right" class="Verdana12BPink" id="next" onclick="displaynext()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','../IMAGES/nexthover.jpg',1)" title="Click to display next set of results"><img src="../IMAGES/next.jpg" alt="next" name="Image3" width="12" height="18" border="0" id="Image3" /></a></td>
    <td width="30" align="center" class="Verdana12BPink" id="last" onclick="displaylast()" ><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','../IMAGES/lasthover.jpg',1)" title="Click to display last set of results"><img src="../IMAGES/last.jpg" alt="last" name="Image2" width="12" height="18" border="0" id="Image2" /></a></td>
  </tr>
  <tr>
  <td colspan="5" align="center" valign="middle"class="ButtonsTable">
  <input class="button" type="button" name="Submit" value="ADD" onclick="window.open('UPDATE_CLIENT.php?client=0','_self')" />
  <input class="button" type="submit" name="showpatients" value="PATIENTS" onclick="displaypatients()"/>
  <input class="button" type="submit" name="showaddress" value="ADDRESS" onclick="displayaddress()"/>
  <input class="button" type="submit" name="showphone" value="PHONE" onclick="displayphone()"/>
  <input class="button" type="submit" name="showinvoice" value="INVOICE" onclick="displayinvoice()"/>
  <input class="button" type="reset" name="cancel" value="CANCEL" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" />
  <!--- onclick="history.back();"  -->
  </td>
  </tr>	
</table>
<input type="hidden" name="check" value="1"  />
</form>

<form action="" method="post" name="clearsessions">
<input type="hidden" name="check2" value="1"  />
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>