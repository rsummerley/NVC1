<?php
session_start();
unset($_SESSION['start']);

if (isset($_POST['check2'])){
unset($_SESSION['pet2search']);
unset($_SESSION['prabtag']);
unset($_SESSION['ptatno']);
unset($_SESSION['number']);
unset($_SESSION['sorting']);
header('Location:PATIENT_SEARCH.php');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ADVANCED PATIENT SEARCH</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
</style>

<script type="text/javascript">
function bodyonload(){
document.searchpatient.pet2search.focus();
document.getElementById('inuse').innerText=localStorage.xdatabase;

document.searchpatient.submit();
document.getElementById('<?php echo $_GET['id']; ?>').bgColor="#FF0099";
}


function setsorting(x,y)
{
self.location='PATIENT_SEARCH.php?sorting=' + x + '&refID=<?php echo $_GET['refID']; ?>&id=' + y;
}

function displaynext()
{
window.frames[0].self.location='PATIENT_SEARCH_IFRAME.php?navigation=next';
}

function displayprevious()
{
window.frames[0].location.href='PATIENT_SEARCH_IFRAME.php?navigation=prev';
}

function displayfirst()
{
window.frames[0].self.location='PATIENT_SEARCH_IFRAME.php?navigation=first';
}

function displaylast()
{
window.frames[0].self.location='PATIENT_SEARCH_IFRAME.php?navigation=last';
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
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>

<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload();MM_preloadImages('../../IMAGES/firsthover.jpg','../../IMAGES/previoushover.jpg','../../IMAGES/nexthover.jpg','../../IMAGES/lasthover.jpg')" onunload="bodyonunload()">
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
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" --><!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<form action="PATIENT_SEARCH_IFRAME.php" method="get" target="list" name="searchpatient">
<input type="hidden" name="start" value="0" />
<input type="hidden" name="refID" value="<?php echo $_GET['refID']; ?>" />
<input type="hidden" name="sorting" id="sorting" value="<?php echo $_GET['sorting']; ?>" />
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  <td colspan="5" align="left" valign="top">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <tr>
    <td width="133" bgcolor="#000000" class="Verdana11Bwhite" id="pet" onclick="setsorting('PETNAME','pet');" onmouseover="document.getElementById(this.id).style.cursor='pointer';" title="Click to sort by patient name">Patient</td>
    <td width="87" align="left" bgcolor="#000000" class="Verdana11Bwhite" id="rabtag"  onclick="setsorting('PRABTAG','rabtag');" onmouseover="document.getElementById(this.id).style.cursor='pointer';" title="Click to sort by rabies tags">Rabies Tag</td>
    <td width="171" bgcolor="#000000" class="Verdana11Bwhite" id="tatno"  onclick="setsorting('PTATNO','tatno');" onmouseover="document.getElementById(this.id).style.cursor='pointer';" title="Click to sort by microchip number">Microchip</td>
    <td width="60" align="left" valign="middle" bgcolor="#000000" class="Verdana11Bwhite"  onclick="setsorting('','client');" onmouseover="document.getElementById(this.id).style.cursor='pointer';">File #</td>
    <td width="187" align="left" valign="middle" bgcolor="#000000" class="Verdana11Bwhite"  onclick="setsorting('','client');" onmouseover="document.getElementById(this.id).style.cursor='pointer';">Client (Phone #)</td>
    <td width="95" align="left" valign="middle" bgcolor="#000000" class="Verdana11Bwhite"  onclick="setsorting('','client');" onmouseover="document.getElementById(this.id).style.cursor='pointer';">&nbsp;</td>
  </tr>
<!--  <tr>
<td colspan="4">

 <table width="100%" cellpadding="0" cellspacing="0">-->  
 <tr>
      <td height="10"><input name="pet2search" type="text" class="Input" id="pet2search" size="15"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['pet2search']; ?>"/></td>
      <td width="77" height="10" align="left"><input name="prabtag" type="text" class="Input" id="prabtag" size="13"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['prabtag']; ?>"/></td>
      <td height="10" align="left"><span class="Andale12noDecor">
        <input name="ptatno" type="text" class="Input" id="ptatno" size="20"  onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="this.form.submit()" value="<?php echo $_SESSION['ptatno']; ?>"/>
      </span></td>
      <td height="10" align="left" valign="middle" class="Andale12noDecor">&nbsp;</td>
      <td align="left" valign="middle" class="Andale12noDecor">&nbsp;</td>
      <td align="left"><input type="button" name="clear" value="Clear search" onclick="document.clearsessions.submit();" /></td>
 </tr>
  <!--</table>  </td>
  </tr>-->
  <tr>
  <td colspan="6"><iframe name="list" id="list" scrolling="auto" height="465" width="100%" frameborder="0" ></iframe></td>
  </tr>
  </table></td>
  </tr>


  
  
  <tr>
    
    <td width="29" align="center" class="Verdana12BPink" id="first" onclick="displayfirst()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','../../IMAGES/firsthover.jpg',1)" title="Click to display first set of results"><img src="../../IMAGES/first.jpg" alt="first" name="Image1" width="12" height="18" border="0" id="Image1" /></a></td>
    <td width="117" align="left" class="Verdana12BPink" id="previous" onclick="displayprevious()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','../../IMAGES/previoushover.jpg',1)" title="Click to display previous set of results"><img src="../../IMAGES/previous.jpg" alt="previous" name="Image4" width="12" height="18" border="0" id="Image4" /></a></td>
    <td width="484" align="center" class="Verdana12BPink">    </td>
    <td width="73" align="right" class="Verdana12BPink" id="next" onclick="displaynext()"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','../../IMAGES/nexthover.jpg',1)" title="Click to display next set of results"><img src="../../IMAGES/next.jpg" alt="next" name="Image3" width="12" height="18" border="0" id="Image3" /></a></td>
    <td width="30" align="center" class="Verdana12BPink" id="last" onclick="displaylast()" ><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','../../IMAGES/lasthover.jpg',1)" title="Click to display last set of results"><img src="../../IMAGES/last.jpg" alt="last" name="Image2" width="12" height="18" border="0" id="Image2" /></a></td>
  </tr>
  <tr>
  <td colspan="5" align="center" valign="middle"class="ButtonsTable">
  <input class="button" type="button" name="ok" value="OK" onclick="window.open('../../INDEX.php','_self')" title="Click to go back to home page" />
  <input class="button" type="reset" name="cancel" value="CANCEL"  onclick="history.back();<?php //if ($_GET['path']=='2close') {echo "self.close();";} else {echo "history.back();";} ?>" />  </td>
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
