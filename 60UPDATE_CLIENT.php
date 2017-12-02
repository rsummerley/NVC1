<?php 
session_start();
require_once('../tryconnection.php');

mysql_select_db($database_tryconnection, $tryconnection);
$query_CLIENT = sprintf("SELECT * FROM ARCUSTO WHERE CUSTNO = '%s'", $_GET['client']);
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

if (!empty($_POST['phonea'])){$phone=$_POST['phonea'].'-'.$_POST['phoneb'];}
if (!empty($_POST['phone2a'])){$phone2=$_POST['phone2a'].'-'.$_POST['phone2b'];}
if (!empty($_POST['phone3a'])){$phone3=$_POST['phone3a'].'-'.$_POST['phone3b'];}
if (!empty($_POST['phone4a'])){$phone4=$_POST['phone4a'].'-'.$_POST['phone4b'];}
if (!empty($_POST['phone5a'])){$phone5=$_POST['phone5a'].'-'.$_POST['phone5b'];}
if (!empty($_POST['phone6a'])){$phone6=$_POST['phone6a'].'-'.$_POST['phone6b'];}
if (!empty($_POST['phone7a'])){$phone7=$_POST['phone7a'].'-'.$_POST['phone7b'];}
if (!empty($_POST['phone8a'])){$phone8=$_POST['phone8a'].'-'.$_POST['phone8b'];}


if (isset($_POST['save']) && $_GET['client']!="0") {
$updateSQL = sprintf("UPDATE ARCUSTO SET TITLE='%s', CONTACT='%s', COMPANY='%s', ADDRESS1='%s', ADDRESS2='%s', CITY='%s', `STATE`='%s', ZIP='%s', COUNTRY='%s', CAREA='%s', CAREA2='%s', CAREA3='%s', CAREA4='%s', CAREA5='%s', CAREA6='%s', CAREA7='%s', CAREA8='%s', PHONE='%s', PHONE2='%s', PHONE3='%s', PHONE4='%s', PHONE5='%s', PHONE6='%s', PHONE7='%s', PHONE8='%s', EMAIL='%s', CBEXT='%s', CBEXT2='%s', REFVET='%s', REFCLIN='%s', `COMMENT`='%s', PHMEMO='%s', PHMEMO2='%s', REMINDERS='%s', INACTIVE='%s' WHERE CUSTNO='%s' LIMIT 1",
                       $_POST['title'], 
                       mysql_real_escape_string($_POST['CONTACT']), 
                       mysql_real_escape_string($_POST['COMPANY']), 
                       mysql_real_escape_string($_POST['address1']), 
                       mysql_real_escape_string($_POST['address2']), 
                       mysql_real_escape_string($_POST['city']), 
                       mysql_real_escape_string($_POST['state']), 
                       strtoupper($_POST['zip']), 
                       $_POST['country'], 
                       $_POST['carea'], 
                       $_POST['carea2'], 
                       $_POST['carea3'], 
                       $_POST['carea4'], 
                       $_POST['carea5'], 
                       $_POST['carea6'], 
                       $_POST['carea7'], 
                       $_POST['carea8'], 
					   $phone,
					   $phone2,
					   $phone3,
					   $phone4,
					   $phone5,
					   $phone6,
					   $phone7,
					   $phone8,
                       mysql_real_escape_string(strtolower($_POST['email'])), 
                       $_POST['cbext'], 
                       $_POST['cbext2'], 
                       mysql_real_escape_string($_POST['refvet']), 
                       mysql_real_escape_string($_POST['refclin']), 
                       mysql_real_escape_string($_POST['comment']), 
                       mysql_real_escape_string($_POST['phmemo']), 
                       mysql_real_escape_string($_POST['phmemo2']), 
                       $_POST['reminders'],
					   $_POST['inactive'], 
                       $_GET['client']);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
//header("Location: CLIENT_PATIENT_FILE.php?client=".$_GET['client']);
$wingoback="history.go(-2);";
}

else if (isset($_POST['save']) && $_GET['client'] == '0') {
//get the CUSTNO from CRITDATA
$query_CUSTNO = "SELECT LASTCUST FROM CRITDATA LIMIT 1 ";
$CUSTNO = mysql_query($query_CUSTNO, $tryconnection) or die(mysql_error());
$row_CUSTNO = mysqli_fetch_assoc($CUSTNO);
$newcustno=$row_CUSTNO['LASTCUST']+1;
//update CRITDATA with the new custno
$update_CRITDATA="UPDATE CRITDATA SET LASTCUST='$newcustno'";
$CRITDATA=mysql_query($update_CRITDATA, $tryconnection) or die(mysql_error());
//insert the new patient into ARCUST
$insertSQL = sprintf("INSERT INTO ARCUSTO (CUSTNO, TITLE, CONTACT, COMPANY, ADDRESS1, ADDRESS2, CITY, `STATE`, ZIP, COUNTRY, CAREA, CAREA2, CAREA3, CAREA4, CAREA5, CAREA6, CAREA7, CAREA8, PHONE, PHONE2, PHONE3, PHONE4, PHONE5, PHONE6, PHONE7, PHONE8, EMAIL, CBEXT, CBEXT2, REFVET, REFCLIN, `COMMENT`, PHMEMO, PHMEMO2, REMINDERS, BALANCE, INACTIVE) VALUES ('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                       $newcustno, 
                       $_POST['title'], 
                       mysql_real_escape_string($_POST['CONTACT']), 
                       mysql_real_escape_string($_POST['COMPANY']), 
                       mysql_real_escape_string($_POST['address1']), 
                       mysql_real_escape_string($_POST['address2']), 
                       mysql_real_escape_string($_POST['city']), 
                       $_POST['state'], 
                       strtoupper($_POST['zip']), 
                       $_POST['country'], 
                       $_POST['carea'], 
                       $_POST['carea2'], 
                       $_POST['carea3'], 
                       $_POST['carea4'], 
                       $_POST['carea5'], 
                       $_POST['carea6'], 
                       $_POST['carea7'], 
                       $_POST['carea8'], 
					   $phone,
					   $phone2,
					   $phone3,
					   $phone4,
					   $phone5,
					   $phone6,
					   $phone7,
					   $phone8,
                       mysql_real_escape_string(strtolower($_POST['email'])),
                       $_POST['cbext'], 
                       $_POST['cbext2'], 
                       mysql_real_escape_string($_POST['refvet']), 
                       mysql_real_escape_string($_POST['refclin']), 
                       mysql_real_escape_string($_POST['comment']), 
                       mysql_real_escape_string($_POST['phmemo']), 
                       mysql_real_escape_string($_POST['phmemo2']), 
                       $_POST['reminders'],
					   "0.00",
                       $_POST['inactive']
					   );
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
header("Location: CLIENT_PATIENT_FILE.php?client=$newcustno");
//$wingoback="history.go(-2);";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CLIENT RECORD</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
//x=element, y=value
function skip(x,y){
	if (y.length==x.maxLength){
	next=x.tabIndex;
	document.forms[0].elements[next+2].focus();
	document.forms[0].elements[next+2].select();
	}
}


function bodyonload(){
<?php echo $wingoback; ?>
document.add_edit_client.CONTACT.focus();
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

function onMouseOver(x)
{
document.getElementByClass(x).style.cursor="pointer";
}

function OpenSecaddress(){
var client="<?php echo $_GET['client']; ?>";
if (client ==0){
window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_SECOND_ADDRESS.php?client=<?php echo $row_CUSTNO['CUSTNO']+1; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=445, height=345');
}
else {
window.open('UPDATE_ADDITIONAL_DATA_CLIENT/SECADDRESS_DIRECTORY.php?client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=445, height=345'); 
}
}

function OpenSecindex(){
var client="<?php echo $_GET['client']; ?>";
if (client ==0){
window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_SECOND_INDEX.php?client=<?php echo $row_CUSTNO['CUSTNO']+1; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=442, height=327');
}
else {
window.open('UPDATE_ADDITIONAL_DATA_CLIENT/SECINDEX_DIRECTORY.php?client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=442, height=327'); 
}
}

</script>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
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

<form action="" name="add_edit_client" class="FormDisplay" method="POST">
<!--this hidden element is here just to make the skip function work...it has not other function-->
<input type="hidden" />
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
<!--TITLE-->  
  <tr align="center">
    <td height="41" colspan="2" valign="middle" class="Verdana14B"><?php if ($_GET['client']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> CLIENT</td>
  </tr>
<!--CLIENT'S NAME--> 
  <tr align="center">
		<td height="54" colspan="2">
		
		  <table width="100%" height="33" border="0" align="center" cellpadding="0" cellspacing="0" >
<tr>
			<td width="19%" height="29" align="center" class="Labels">
<!-- CLIENT NUMBER!!!!!!!!! -->					  
            <select name="title" tabindex="1">
              <option value="<?php echo $row_CLIENT['TITLE']; ?>" selected="selected"><?php echo $row_CLIENT['TITLE']; ?></option>
              <option value="Ms." <?php if ($_GET['client']=="0"){echo "selected";}?>>Ms.</option>
              <option value="Miss.">Miss.</option>
              <option value="Mrs.">Mrs.</option>
              <option value="Mr.">Mr.</option>
              <option value="Dr.">Dr.</option>
              <option value="Reverend">Reverend</option>
              <option value="The Honourable">The Honourable</option>
              <option value=" ">Other</option>
            </select>				
			</td>
	    <td width="38%" align="center">
					
        <span class="Labels">First Name</span>
        <input name="CONTACT" type="text" class="Input" id="CONTACT" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['CONTACT']; ?>" size="20" maxlength="20" tabindex="2"/>
          </td>
		<td width="43%" align="left">
					
        <span class="RequiredItems">Last Name</span>
        <input name="COMPANY" type="text" class="Input" id="COMPANY" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['COMPANY']; ?>" size="20" maxlength="25" tabindex="3"/>
									
		</td>
		    </tr>
		  </table>
	  </td>
  </tr>

<!--PHONE NUMBERS-->
  <tr>
<td width="361" rowspan="2">
		
			<table  width="100%" border="0" cellpadding="0" cellspacing="0">
			  <!--DWLayoutTable-->
			  <tr>
				<td width="48" height="35" align="left" valign="middle" class="Labels">Home</td>
				<td height="35" colspan="3" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px; margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea" type="text" class="Input" id="carea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="5"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phonea" type="text" class="Input" id="phonea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="7"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phoneb" type="text" class="Input" id="phoneb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="15"/></td>
				<td height="35" colspan="2" align="left" valign="middle" class="Labels">Fax</td>
				<td height="35" colspan="2" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea6" type="text" class="Input" id="carea6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA6']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="11"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone6a" type="text" class="Input" id="phone6a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE6'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="13"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone6b" type="text" class="Input" id="phone6b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE6'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="21"/>                </td>
			  </tr>
			  <tr>
				<td height="35" align="left" valign="middle" class="Labels">Cell 1</td>
				<td height="35" colspan="3" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea2" type="text" class="Input" id="carea2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA2']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="17"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone2a" type="text" class="Input" id="phone2a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE2'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="19"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone2b" type="text" class="Input" id="phone2b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE2'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="27"/></td>
				<td height="35" colspan="2" align="left" valign="middle" class="Labels">Other</td>
				<td height="35" colspan="2" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea7" type="text" class="Input" id="carea7" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA7']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="23"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone7a" type="text" class="Input" id="phone7a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE7'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="25"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone7b" type="text" class="Input" id="phone7b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE7'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="33"/></td>			  
              </tr>
			  <tr>
				<td height="35" align="left" valign="middle" class="Labels">Cell 2</td>
				<td height="35" colspan="3" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea3" type="text" class="Input" id="carea3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA3']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="29"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone3a" type="text" class="Input" id="phone3a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE3'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="31"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone3b" type="text" class="Input" id="phone3b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE3'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="9"/></td>
				<td height="35" colspan="2" align="left" valign="middle" class="Labels">Barn</td>
				<td height="35" colspan="2" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea8" type="text" class="Input" id="carea8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA8']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="35"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone8a" type="text" class="Input" id="phone8a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE8'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="37"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone8b" type="text" class="Input" id="phone8b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE8'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="39"/></td>
			  </tr>
			  <tr>
				<td height="35" align="left" valign="middle" class="Labels">Work 1</td>
				<td height="35" colspan="3" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea4" type="text" class="Input" id="carea4" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA4']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="41"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="phone4a" type="text" class="Input" id="phone4a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE4'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="43"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone4b" type="text" class="Input" id="phone4b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE4'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="44"/></td>
   				<td height="35" align="left" valign="middle" class="Labels2">Ext</td>
				<td height="35" colspan="2" align="left" valign="middle"><input name="cbext" type="text" class="Input" id="cbext" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['CBEXT']; ?>" size="6" maxlength="6"/></td>
				<td width="106" height="35" align="left" valign="middle"><input name="phmemo" type="text" class="Input" id="phmemo" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="12" maxlength="20" value="<?php echo $row_CLIENT['PHMEMO']; ?>"  title="Type in a short memo" /></td>
			  </tr>
			  <tr>
				<td height="35" align="left" valign="middle" class="Labels">Work 2</td>
				<td height="35" colspan="3" align="left" valign="middle">
                <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="carea5" type="text" class="Input" id="carea5" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="3" maxlength="3" value="<?php echo $row_CLIENT['CAREA5']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="49"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:22px; width:5px;" value=")" disabled="disabled" /><input name="phone5a" type="text" class="Input" id="phone5a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE5'],0,3); ?>" size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="51"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="phone5b" type="text" class="Input" id="phone5b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_CLIENT['PHONE5'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="52"/></td>
				<td height="35" align="left" valign="middle" class="Labels2">Ext</td>
				<td height="35" colspan="2" align="left" valign="middle"><input name="cbext2" type="text" class="Input" id="cbext2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['CBEXT2']; ?>" size="6" maxlength="6" /></td>
				<td height="35" align="left" valign="middle"><input name="phmemo2" type="text" class="Input" id="phmemo2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="12" maxlength="20" value="<?php echo $row_CLIENT['PHMEMO2']; ?>" title="Type in a short memo"/></td>
			  </tr>
			  <tr>
			    <td height="0"></td>
			    <td width="126"></td>
			    <td width="3"></td>
			    <td width="8"></td>
			    <td width="25"></td>
			    <td width="15"></td>
			    <td width="28"></td>
			    <td></td>
		      </tr>
		  </table>
			
	  </td>
  	</tr>
  
<!--ADDRESS-->
  
  <tr>
    <td width="362" height="241">  
	    
	    <table  width="100%" border="0" cellpadding="0" cellspacing="0">
	      <tr>
	        <td width="15%" height="35" align="left" valign="middle" class="Labels">Street</td>
      <td height="35" colspan="5" align="left" valign="middle" class="Labels"><input name="address1" type="text" class="Input" id="address1" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['ADDRESS1']; ?>" size="30" maxlength="25"/></td>
      </tr>
	      <tr>
	        <td height="35" align="left" valign="middle" class="Labels">Apt/RR/Box</td>
      <td height="35" colspan="5" align="left" valign="middle" class="Labels"><input name="address2" type="text" class="Input" id="address2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['ADDRESS2']; ?>" size="30" maxlength="25"/></td>
	      </tr>
	      <tr>
	        <td height="35" align="left" valign="middle" class="Labels">City</td>
      		<td height="35" colspan="3" align="left" valign="middle" class="Labels"><input name="city" type="text" class="Input" id="city" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['CITY']; ?>" size="22" maxlength="25"/></td>
	      </tr>
	      <tr>
	        <td height="35" align="left" valign="middle" class="Labels">P.Code/Zip</td>
	        <td height="35" colspan="4" align="left" valign="middle" class="Labels"><input name="zip" type="text" class="Input" id="zip" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['ZIP']; ?>" size="8" maxlength="12"/>&nbsp;&nbsp;&nbsp;Prov/State
	          <input name="state" type="text" class="Input" id="state" size="3" maxlength="3" value="<?php if (!empty($row_CLIENT['STATE'])) {echo $row_CLIENT['STATE'];} else {echo "ON";} ?>" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
	        </tr>
	      <tr>
	        <td height="35" align="left" valign="middle" class="Labels">Country</td>
            <td width="59" height="35" align="left" valign="middle" class="Labels"><input name="country" type="text" class="Input" id="country" size="22" value="<?php if (!empty($row_CLIENT['COUNTRY'])) {echo $row_CLIENT['COUNTRY'];} else {echo "Canada";} ?>" maxlength="25" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
            <td height="35" colspan="2" align="center" valign="middle" class="<?php if ($row_CLIENT['INACTIVE']=='1'){echo "Verdana11BRed";} else {echo "Verdana11";} ?>"><label>
              <input type="checkbox" name="inactive" id="inactive" value="1" <?php if ($row_CLIENT['INACTIVE']=='1'){echo "checked";} ?> />
              Inactive</label></td>
            </tr>
  </table>

	  </td>
  </tr>
<!--COMMENT-->  
  <tr align="center">
    <td height="75" colspan="2">
	
	    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="69" height="50" align="left" valign="middle" class="Labels">Email</td>
        <td width="351" valign="middle"><input name="email" type="text" class="Input" id="email" value="<?php echo $row_CLIENT['EMAIL']; ?>" size="40" maxlength="50" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
        <td width="163" align="center" valign="middle" class="Labels2">Reminders via email:</td>
        <td width="51" align="left" valign="middle">
        <label><input type="radio" name="reminders" value="1" <?php if ($row_CLIENT['REMINDERS']=='1'){echo "CHECKED";}; ?> />&nbsp;Yes</label>
        </td>
        <td width="95" align="left" valign="middle">
        <label><input type="radio" name="reminders" value="0" <?php if ($row_CLIENT['REMINDERS']=='0'){echo "CHECKED";}; ?>/>&nbsp;No</label>
        </td>
        </tr>
      
      <tr>
        <td height="67" align="left" valign="top" class="Labels">Comment</td>
        <td colspan="4" valign="top"><textarea name="comment" cols="70" rows="4" wrap="virtual" id="comment" class="commentarea"><?php echo $row_CLIENT['COMMENT']; ?></textarea></td>
      </tr>
    </table>
	</td>
  </tr>
  
  
<!--LINKS-->  
  <tr align="center">
    <td height="50" colspan="2" align="center" class="Verdana11Grey">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="138" height="25" align="right" valign="middle"></td>
        <td width="24" height="25" align="right" valign="middle">
        	<img src="../IMAGES/koule.JPG" alt="koule" width="20" height="20" class="koule" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_SECOND_ADDRESS.php?client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=445, height=345');" id="koule1" onmouseover="CursorToPointer(this.id)" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>/>
        </td>
        <td width="213" height="25" align="left" valign="middle">
            <span class="Verdana11" onclick="OpenSecaddress()" onmouseover="CursorToPointer(this.id)"id="koule2" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>>SECOND ADDRESS</span><?php if ($_GET['client']=='0'){echo "<span title='To add second address, please save the client first.'>SECOND ADDRESS</span>";} ?></td>
        <td width="24" height="25" align="right" valign="middle">
        	<img alt="koule" src="../IMAGES/koule.JPG" height="20px" width="20px" class="koule" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_ACCOUNT_INFORMATION.php?client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=yes, width=465, height=328');" id="koule3" onmouseover="CursorToPointer(this.id)" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>/>
        </td>
        <td width="328" height="25" align="left" valign="middle">
            <span class="Verdana11" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_ACCOUNT_INFORMATION.php?client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=yes, width=445, height=310');" id="koule4" onmouseover="CursorToPointer(this.id)" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>>ACCOUNT INFORMATION</span><?php if ($_GET['client']=='0'){echo "<span title='To add account infromation, please save the client first.'>ACCOUNT INFORMATION</span>";} ?></td>
      </tr>
      <tr>
        <td height="25" align="right" valign="middle"></td>
        <td height="25" align="right" valign="middle">
        	<img alt="koule" src="../IMAGES/koule.JPG" height="20" width="20" class="koule" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_REFERRAL.php?tea=1&client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=650, height=222');" id="koule5" onmouseover="CursorToPointer(this.id)"/>
        </td>
        <td height="25" align="left" valign="middle">
        	<span class="Verdana11" onclick="window.open('UPDATE_ADDITIONAL_DATA_CLIENT/UPDATE_REFERRAL.php?tea=1&client=<?php echo $_GET['client']; ?>','_blank','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=620, height=190');" id="koule6" onmouseover="CursorToPointer(this.id)">REFERRAL</span></td>
        <td height="25" align="right" valign="middle">
        	<img alt="koule" src="../IMAGES/koule.JPG" height="20px" width="20px" class="koule" onclick="OpenSecindex()" id="koule7" onmouseover="CursorToPointer(this.id)" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>/>
        </td>
        <td height="25" align="left" valign="middle">
        	<span class="Verdana11" onclick="OpenSecindex()" id="koule8" onmouseover="CursorToPointer(this.id)" <?php if ($_GET['client']=='0'){echo "style='display:none'";} ?>>SECOND INDICES</span><?php if ($_GET['client']=='0'){echo "<span title='To add second index, please save the client first.'>SECOND INDICES</span>";} ?></td>
      </tr>
    </table>	</td>
  </tr>
  <tr>
  <td colspan="5">
  	
        <table class="ButtonsTable">
		<tr>
		<td align="center" valign="middle">
		<input name="save" type="submit" class="button" id="save" value="SAVE"/>
        <input name="CANCEL" type="reset" class="button" value="CANCEL" onclick="history.go(-1);" /> 
		</td>
		</tr>
	  </table>
	  </td>
	  </tr>
	</table>

<!--DATA FROM REFERRAL-->	
    <!--REF. CLINIC-->
    <input type="hidden" name="refclin" value="<?php echo $row_CLIENT['REFCLIN']; ?>"  />
    <!--REF. VET-->
    <input type="hidden" name="refvet" value="<?php echo $row_CLIENT['REFVET']; ?>"  />
    
</form>	
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>

