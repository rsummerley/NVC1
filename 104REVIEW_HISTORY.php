<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}


if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}

if ($_GET['path']=='3close'){
$_SESSION['path']="3close";
}

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);
//$totalRows_PATIENT_CLIENT = mysql_num_rows($PATIENT_CLIENT);

$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

$query_HXFILTER = "SELECT * FROM HXFILTER WHERE HXGROUP='1'";
$HXFILTER = mysql_query($query_HXFILTER, $tryconnection) or die(mysql_error());
$row_HXFILTER = mysql_fetch_assoc($HXFILTER);

$query_HXFILTER2 = "SELECT * FROM HXFILTER WHERE HXGROUP='2'";
$HXFILTER2 = mysql_query($query_HXFILTER2, $tryconnection) or die(mysql_error());
$row_HXFILTER2 = mysql_fetch_assoc($HXFILTER2);

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysql_fetch_assoc($PREFER);

$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR ORDER BY DOCTOR ASC");
$DOCTOR = mysql_query($query_DOCTOR, $tryconnection) or die(mysql_error());
$row_DOCTOR = mysql_fetch_assoc($DOCTOR);

$query_HXBUFFER = sprintf("SELECT * FROM HXBUFFER WHERE HXPETID='$patient'");
$HXBUFFER = mysql_query($query_HXBUFFER, $tryconnection) or die(mysql_error());
$row_HXBUFFER = mysql_fetch_assoc($HXBUFFER);


$filter;
if (isset($_POST['submit']) && !empty($_POST['filter'])){
$filter=array_sum($_POST['filter']);
}

include("../../ASSETS/history.php");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>REVIEW MEDICAL HISTORY</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
<?php 
if ($_GET['path']=='2close') {
echo "alert('DVManager has opened a new window for you to review history. If you wish to exit, please click CLOSE and return to the previous window.');";
} 
?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
var hxpreview=document.getElementById('hxpreview');
hxpreview.scrollTop = hxpreview.scrollHeight;
var xiframe=document.getElementById('xiframe');
xiframe.scrollTop = xiframe.scrollHeight;
}

</script>


<script src="../../../SpryAssets/SpryCollapsiblePanel.js" type="text/javascript"></script>
<link href="../../../SpryAssets/SpryCollapsiblePanel.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
#table2 {	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
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
  <form action="" name="review_history" method="post">
    
  <table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="60" colspan="3" valign="top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>
        <?php //echo $treatmxx; ?>        </td>
        <td width="19%" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
      </tr>
    </table></td>
      </tr>
    <tr height="457">
      <td height="457" colspan="3" align="center" valign="top">
        
        
        <table class="table" width="733" height="457" border="1" cellpadding="0" cellspacing="0" >
          <tr height="5%">
            <td valign="top" class="Verdana11">    
              <div id="CollapsiblePanel1" class="CollapsiblePanel">
                <div class="CollapsiblePanelTab"><span class="Verdana11B">Filter</span></div>
                <div class="CollapsiblePanelContent">
                  
                  <table width="732" border="0" cellpadding="0" cellspacing="0" class="Labels2">
                    <tr>
                      <td width="36" height="20" align="right">From</td>
              <td width="89" height="20">
              <input type="text" size="10" class="Input" value="<?php echo $_POST['from']; ?>" id="from" name="from" onclick="ds_sh(this)" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>              </td>
              <td width="89" height="20" align="right"><span class="hidden">Doctor</span></td>
              <td width="130" height="20">
        <select name="who" class="hidden">
        <option></option>
            <?php do { ?>
		<option value="<?php echo $row_DOCTOR['DOCTOR']; ?>"><?php echo $row_DOCTOR['DOCTOR']; ?></option>
<?php } while ($row_DOCTOR = mysql_fetch_assoc($DOCTOR)); ?>
		</select>              </td>
              <td width="217" height="20" align="center">&nbsp;</td>
              <td width="171" height="20"><label><input name="" type="checkbox" value="" />Farm/Family History</label></td>
            </tr>
                    <tr>
                      <td height="20" align="right">To</td>
              <td height="20">
              <input type="text" size="10" class="Input" id="to" name="to" onclick="ds_sh(this)" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $_POST['to']; ?>"/>              </td>
              <td height="20" align="right">Search Phrase</td>
              <td height="20" class="Verdana11Grey">&nbsp;Press Command+F</td>
              <td height="20" align="center"><input name="clear" type="button" value="Clear Search" onclick="document.location='REVIEW_HISTORY.php?patient=<?php echo $patient; ?>&client=<?php echo $client; ?>';"/>
              <input name="button" type="button" id="button" onclick="window.open('MORE_FILTER.php','_blank','width=350,height=400')" value="Advanced Search" /></td>
              <td height="20"><label><input name="supress" type="checkbox" value="1" <?php if ($_POST['supress']==1) {echo "checked";} ?> />Suppress Prices</label></td>
            </tr>
                    <tr>
                      <td height="20" colspan="6" align="left" class="Verdana12">
            <!--<label><input name='filter[]' type='checkbox' value='' />All</label>-->
            <?php do{
			echo "<label><input name='filter[]' type='checkbox' value='".$row_HXFILTER['HXCAT']."'";
				
				if (isset($_POST['filter'])) {if (in_array($row_HXFILTER['HXCAT'], $_POST['filter'])){echo "checked";}}
			
			echo " />".$row_HXFILTER['HXCNAME']."</label>&nbsp;&nbsp;";
					} while ($row_HXFILTER = mysql_fetch_assoc($HXFILTER));
			
			do{
			echo "<label class='hidden'><input name='filter[]' id='".substr($row_HXFILTER2['HXCNAME'],0,3)."' type='checkbox' value='".$row_HXFILTER2['HXCAT']."'";
				
				//if (isset($_POST['filter'])) {if (in_array($row_HXFILTER2['HXCAT'], $_POST['filter'])){echo "checked";}}
			
			echo " />".$row_HXFILTER2['HXCNAME']."</label>";
					} while ($row_HXFILTER2 = mysql_fetch_assoc($HXFILTER2));
			
			?>
            <label class='hidden'><input name='filter[]' id='Sub' type='checkbox' value='' <?php //if (isset($_POST['filter'])) {if (in_array($row_HXFILTER2['HXCAT'], $_POST['filter'])){echo "checked";}} ?>/>Subjective</label>
            <label class='hidden'><input name='filter[]' id='Obj' type='checkbox' value='' <?php //if (isset($_POST['filter'])) {if (in_array($row_HXFILTER2['HXCAT'], $_POST['filter'])){echo "checked";}} ?>/>Objective</label>
            <input type="submit" value="SUBMIT" name="submit"/></td>
              </tr>
          </table>
                </div>
              </div>

      <div id="CollapsiblePanel2" class="CollapsiblePanel">
      <div class="CollapsiblePanelTab"><span class="Verdana11B">Presenting Problem</span></div>
        <div class="CollapsiblePanelContent" style="height:40px;">
        <?php 
		////////////////////// PRESENTING PROBLEM ////////////////////////////////
		$query_RECEP = "SELECT RECEPID, PROBLEM, DATE_FORMAT(DATEIN, '%a %e') AS DATEIN FROM RECEP WHERE RFPETID='$patient'";
		$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
		$row_RECEP = mysql_fetch_assoc($RECEP);

		echo $row_RECEP['PROBLEM'];
		
		?>
        </div>
      </div>      </td>
      </tr>
          
          <tr>
            <td height="auto" valign="top" align="center" class="Verdana11">
              <?php
              $filename1 =  "$treatmxx/$patient.pdf" ;
              $filename = "../../OLDHISTORY/".$filename1 ;
              if (file_exists($filename)) {
				echo '<a href="'.$filename.'" target="_blank"><input name="openold" type="button" class="button" value="VIEW OLD HISTORY"  style="width:150px; margin:10px;"/></a>';
				
				//echo '<iframe src="'.$filename.'" height="300" width="700" id="xiframe" scrolling="no"></iframe>' ;
				}
				?>
              <div id="hxpreview" style="overflow:auto; height:400px;" > 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td width="50"></td>
                  <td width="155"></td>
                  <td width="100"></td>
                  <td width="100"></td>
                  <td></td>
                  <td width="150"></td>
              </tr>
			  <?php 			  
			  history($database_tryconnection, $tryconnection, $filter);			  
	
			  if (!empty($row_HXBUFFER)) {
			  echo '<tr class="Verdana11Grey">
                  <td width="50" height="20"></td>
                  <td width="155" colspan="4">HISTORY BUFFER</td>
                  <td width="150" align="center"></td>
              </tr>';
			  echo '<tr class="Verdana11Grey">
                  <td width="50"></td>
                  <td width="155" colspan="4">'.$row_HXBUFFER['HXTEXT'].'</td>
                  <td width="150" align="center">'.$row_HXBUFFER['HXDOCTOR'].'</td>
              </tr>';
			  }
	
			  ?>
			  </table>
      		  </div>            
            </td>
      </tr>
        </table>    </td>
    </tr>
    <tr>
      <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
        <input name="add" class="<?php if ($_GET['path']=='2close') {echo "hidden";} else {echo "button";} ?>" type="button" value="ADD NEW" onclick="window.open('ADD_NEW_HISTORY.php?path=procmenu','_self')"/>
        <input name="add2" class="<?php if ($_GET['path']=='2close') {echo "hidden";} else {echo "button";} ?>" type="button" value="ADD SCAN" onclick="window.open('../../../SC'+localStorage.xdatabase+'/ADD_SCAN.php?xdatabase='+localStorage.xdatabase,'_blank','width=500,height=310')" style="width:85px;"/>
        <input name="printhx" class="button" type="button" value="PRINT" onclick="document.print_history.submit();"/>
        <input name="add" class="hidden" type="button" value="SURG.LOG" onclick="window.open('ADD_NEW_HISTORY.php?path=procmenu','_self')"/>
        <input name="add" class="hidden" type="button" value="LAB RES." onclick="window.open('ADD_NEW_HISTORY.php?path=procmenu','_self')"/>
        <input name="family" class="<?php if ($_GET['path']=='2close') {echo "hidden";} else {echo "button";} ?>" type="button" value="FAMILY" onclick="window.open('../../CLIENT/CLIENT_PATIENT_FILE.php','_self');" />
        <input name="cancel" class="button" type="button" value="<?php if ($_GET['path']=='2close' || $_GET['path']=='3close') {echo "CLOSE";} else {echo "CANCEL";} ?>" onclick="<?php if ($_GET['path']=='2close' || $_GET['path']=='3close') {echo "self.close();";} else if ($_GET['path']=='procmenu') {echo "history.back()";} else if ($_GET['path']=='procmenu2') {echo "document.location='../PROCESSING_MENU/PROCESSING_MENU.php'";} else {echo "history.go(-2)";} ?>"/>
        <input type="hidden" name="check" value="1"/> 
        <input type="hidden" value="<?php echo $treatmxx; ?>" id="treatmxx" name="treatmxx"/>       </td>
    </tr>
  </table>
    </form>

<form action="../../IMAGES/CUSTOM_DOCUMENTS/HISTORY_PRINTOUT.php" name="print_history" method="post" target="_blank">
<input type="hidden" value="<?php echo $_POST['from']; ?>" id="from" name="from" />
<input type="hidden" value="<?php echo $_POST['to']; ?>" id="to" name="to"/>
<input type="hidden" value="<?php echo $_POST['supress']; ?>" id="supress" name="supress"/>
<input type="hidden" value="<?php echo $filter; ?>" id="filter" name="filter[]"/>
</form>

<script type="text/javascript">
<!--
var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1", {contentIsOpen:false, enableAnimation:false});
var CollapsiblePanel2 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel2", {enableAnimation:false, contentIsOpen:false});
//-->
</script>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>