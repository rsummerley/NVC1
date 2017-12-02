<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");


//if these sessions are set from CANCEL in MPL_ADD, unset them
if (isset($_SESSION['categorization'])){
unset($_SESSION['categorization']);
}
if (isset($_SESSION['tdoctor'])){
unset($_SESSION['tdoctor']);
}
if (isset($_SESSION['treatdate'])){
unset($_SESSION['treatdate']);
}


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

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);
//$totalRows_PATIENT_CLIENT = mysql_num_rows($PATIENT_CLIENT);

$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR ORDER BY PRIORITY ASC");
$DOCTOR = mysql_query($query_DOCTOR, $tryconnection) or die(mysql_error());
$row_DOCTOR = mysql_fetch_assoc($DOCTOR);

$pharmacy1 = 10;
$pharmacy2 = 11;
$food1 = 14;
$food2 = 99;
$surgery1 = 4 ;
$surgery2 = 6 ;
$surgery3 = 7 ;
$surgery4 = 99 ;
$vaccines1 = 2 ;
$vaccines2 = 99 ;
$vaccines3 = 99 ;
$vaccines4 = 99 ;
$vaccines5 = 99 ;
$radiology1 = 5 ;
$radiology2 = 99 ;
$lab1 = 8 ;
$lab2 = 9 ;
$lab3 = 99 ;
$lab4 = 99 ;
$lab5 = 99 ;
$lab6 = 99 ;

$DROP_it = "DROP TEMPORARY TABLE IF EXISTS MPL1" ;
$DROP_it = mysql_query($DROP_it, $tryconnection) or die(mysql_error());

$GET_current = "CREATE TEMPORARY TABLE MPL1 (INVDATETIME DATE, CHRONO DATE, INVUNITS INT(6), INVPRICE FLOAT(8,2), INVTOT FLOAT(8,2), INVDESCR CHAR(30), INVCUST CHAR(7), INVPET INT(7), INVMAJ INT(2)) 
SELECT INVDATETIME, INVDATETIME AS CHRONO, INVUNITS, INVPRICE, INVTOT, INVDESCR, INVCUST, INVPET, INVMAJ FROM DVMINV WHERE INVCUST = '$client' AND INVPET = '$patient'" ;
$GET_current = mysql_query($GET_current, $tryconnection) or die(mysql_error());

$GET_Hx = "INSERT INTO MPL1 SELECT INVDATETIME,INVDATETIME AS CHRONO, INVUNITS, INVPRICE, INVTOT, INVDESCR, INVCUST, INVPET, INVMAJ FROM ARYDVMI WHERE INVCUST = '$client' AND invpet = '$patient'" ;
$GET_Hx = mysql_query($GET_Hx, $tryconnection) or die(mysql_error());

// Then get the problems out of the PROBLEMS table.

$REAL_problems ="SELECT DATE_FORMAT(TREATDATE, '%m/%d/%Y') AS TREATDATE, TREATDESC FROM PROBLEMS WHERE CUSTNO = '$client' AND PETID = '$patient' ORDER BY TREATDATE DESC" ;
$REAL_problems = mysql_query($REAL_problems, $tryconnection) or die(mysql_error());
$row_REAL_problems = mysql_fetch_assoc($REAL_problems);

// Then sort the temporary table into the six panels, in reverse chrono sequence.

$PHARMACY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, CONCAT('(',invtot - (invunits * invprice),')','(',invtot,') ',INVDESCR) AS INVDESCR FROM MPL1 WHERE INVMAJ = $pharmacy1 OR INVMAJ = $pharmacy2 ORDER BY CHRONO DESC " ;
$PHARMACY = mysql_query($PHARMACY, $tryconnection) or die(mysql_error());
$row_PHARMACY = mysql_fetch_assoc($PHARMACY);

$FOOD = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, INVDESCR FROM MPL1 WHERE INVMAJ = $food1 OR INVMAJ = $food2 ORDER BY CHRONO DESC " ;
$FOOD = mysql_query($FOOD, $tryconnection) or die(mysql_error());
$row_FOOD = mysql_fetch_assoc($FOOD);

$SVACCINES = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, INVDESCR FROM MPL1 WHERE INVMAJ = $vaccines1 OR INVMAJ = $vaccines2  OR INVMAJ = $vaccines3  OR INVMAJ = $vaccines4  OR INVMAJ = $vaccines5 ORDER BY CHRONO DESC " ;
$SVACCINES = mysql_query($SVACCINES, $tryconnection) or die(mysql_error());
$row_SVACCINES = mysql_fetch_assoc($SVACCINES);

$RADIOLOGY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, INVDESCR FROM MPL1 WHERE INVMAJ = $radiology1 OR INVMAJ = $radiology2 ORDER BY CHRONO DESC " ;
$RADIOLOGY = mysql_query($RADIOLOGY, $tryconnection) or die(mysql_error());
$row_RADIOLOGY = mysql_fetch_assoc($RADIOLOGY);

$SURGERY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, INVDESCR FROM MPL1 WHERE INVMAJ = $surgery1 OR INVMAJ = $surgery2 OR INVMAJ = $surgery3 OR INVMAJ = $surgery4 ORDER BY CHRONO DESC " ;
$SURGERY = mysql_query($SURGERY, $tryconnection) or die(mysql_error());
$row_SURGERY = mysql_fetch_assoc($SURGERY);

$LAB = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS WHENWAS, INVDATETIME AS CHRONO, INVUNITS, INVDESCR FROM MPL1 WHERE INVMAJ = $lab1 OR INVMAJ = $lab2  OR INVMAJ = $lab3  OR INVMAJ = $lab4  OR INVMAJ = $lab5  OR INVMAJ = $lab6 ORDER BY CHRONO DESC " ;
$LAB = mysql_query($LAB, $tryconnection) or die(mysql_error());
$row_LAB = mysql_fetch_assoc($LAB);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CASE HISTORY SUMMARY</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;
}

</script>

<style type="text/css">
<!--
.table2 {	
	border-color: #CCCCCC;
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
        <td width="19%" colspan="2" rowspan="4" align="center">
        <table width="100%" border="1" cellspacing="0" cellpadding="0" class="table2">
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
      
      <table width="100%" border="1" cellspacing="0" cellpadding="0" class="table2">
        <tr bgcolor="#000000" class="Verdana11Bwhite">
          <td colspan="2" align="center">MASTER PROBLEM LIST</td>
          </tr>
        <tr>
          <td height="80" colspan="2" align="left" class="Verdana11" valign="top">
          
          <div style="height:78px; overflow:auto;">
          
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">&nbsp;'.$row_REAL_problems['TREATDATE'].'</td>
					<td>&nbsp;'.$row_REAL_problems['TREATDESC'].'</td>
					<td>&nbsp;</td>
				</tr>';
		  } while ($row_REAL_problems = mysql_fetch_assoc($REAL_problems));
		  ?>
            </table>
          </div>
          
          </td>
          </tr>
        <tr bgcolor="#0000FF" class="Verdana11Bwhite">
          <td width="50%" align="center">Immunization</td>
          <td width="50%" align="center">Surgery</td>
        </tr>
        <tr>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="98%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_SVACCINES['WHENWAS'].'</td>
					<td>'.$row_SVACCINES['INVDESCR'].'</td>
				</tr>';
		  } while ($row_SVACCINES = mysql_fetch_assoc($SVACCINES));
		  ?>
            </table>
          </div>
          </td>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_SURGERY['WHENWAS'].'</td>
					<td>'.$row_SURGERY['INVDESCR'].'</td>
				</tr>';
		  } while ($row_SURGERY = mysql_fetch_assoc($SURGERY));
		  ?>
            </table>
          </div>
          </td>
        </tr>
        <tr bgcolor="#00FF00" class="Verdana11Bwhite">
          <td align="center">Radiology</td>
          <td align="center">Pharmacy</td>
        </tr>
        <tr>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_RADIOLOGY['WHENWAS'].'</td>
					<td>'.$row_RADIOLOGY['INVDESCR'].'</td>
				</tr>';
		  } while ($row_RADIOLOGY = mysql_fetch_assoc($RADIOLOGY));
		  ?>
            </table>
          </div>
          </td>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_PHARMACY['WHENWAS'].'</td>
					<td align="right" width="25">'.$row_PHARMACY['INVUNITS'].'&nbsp;</td>
					<td>'.$row_PHARMACY['INVDESCR'].'</td>
				</tr>';
		  } while ($row_PHARMACY = mysql_fetch_assoc($PHARMACY));
		  ?>
            </table>
          </div>
          </td>
        </tr>
        <tr bgcolor="#FF0000" class="Verdana11Bwhite">
          <td align="center">Laboratory</td>
          <td align="center">Food/Diets</td>
        </tr>
        <tr>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_LAB['WHENWAS'].'</td>
					<td>'.$row_LAB['INVDESCR'].'</td>
				</tr>';
		  } while ($row_LAB = mysql_fetch_assoc($LAB));
		  ?>
            </table>
          </div>
          </td>
          <td height="100" align="center" class="Verdana11" valign="top">
          <div style="height:98px; overflow:auto;">
          
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
          <?php 
		  do {
		  echo '<tr>
					<td align="left" width="75">'.$row_FOOD['WHENWAS'].'</td>
					<td align="right" width="25">'.$row_FOOD['INVUNITS'].'&nbsp;</td>
					<td>'.$row_FOOD['INVDESCR'].'</td>
				</tr>';
		  } while ($row_FOOD = mysql_fetch_assoc($FOOD));
		  ?>
            </table>
          </div>
          </td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
      <input name="add" class="button" type="button" value="ADD" onclick="window.open('MPL_DOCTOR.php','_self');"/>
      <input name="print" class="button" type="button" value="PRINT" onclick="window.open('PRINT_MPL.php','_blank');"/>
      <input name="family" class="button" type="button" value="FAMILY" onclick="document.location='../../CLIENT/CLIENT_PATIENT_FILE.php';"/>
      <input name="procmenu" class="button" type="button" value="FINISHED" onclick="history.back()"/>
      
      </td>
    </tr>
  </table>
    </form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>