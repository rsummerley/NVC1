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

$pres_prob = "SELECT PROBLEM FROM RECEP WHERE RFPETID = '$patient' LIMIT 1" ;
$query_prob = mysql_query($pres_prob, $tryconnection) or die(mysql_error()) ;
$row_prob = mysql_fetch_assoc($query_prob) ;

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

$GET_current = "CREATE TEMPORARY TABLE MPL1 (INVDATETIME DATE, INVUNITS INT(6), INVDESCR CHAR(30), INVCUST CHAR(7), INVPET INT(7), INVMAJ INT(2)) SELECT INVDATETIME, INVUNITS, INVDESCR, INVCUST, INVPET, INVMAJ FROM DVMINV WHERE INVCUST = '$client' AND INVPET = '$patient'" ;
$GET_current = mysql_query($GET_current, $tryconnection) or die(mysql_error());

$GET_last = "INSERT INTO MPL1 SELECT INVDATETIME, INVUNITS, INVDESCR, INVCUST, INVPET, INVMAJ FROM DVMILAST WHERE INVCUST = '$client' AND invpet = '$patient' ";
// $GET_last = mysql_query($GET_last, $tryconnection) or die(mysql_error());

$GET_Hx = "INSERT INTO MPL1 SELECT INVDATETIME, INVUNITS, INVDESCR, INVCUST, INVPET, INVMAJ FROM ARYDVMI WHERE INVCUST = '$client' AND invpet = '$patient'" ;
$GET_Hx = mysql_query($GET_Hx, $tryconnection) or die(mysql_error());

// and cut the hx off if it is more than 3 years old.

$TRIM_HX = "DELETE FROM MPL1 WHERE INVDATETIME  < DATE_SUB(NOW(),INTERVAL 3 YEAR) AND (INVMAJ = $pharmacy1 OR INVMAJ = $pharmacy2 OR  INVMAJ = $vaccines1 OR INVMAJ = $lab1 OR INVMAJ = $lab2)" ;
$TRIM_HX = mysql_query($TRIM_HX, $tryconnection) or die(mysql_error()) ;

// Then get the problems out of the PROBLEMS table.

$REAL_problems ="SELECT DATE_FORMAT(TREATDATE, '%m/%d/%Y') AS TREATDATE, TREATDESC FROM PROBLEMS WHERE CUSTNO = '$client' AND PETID = '$patient'" ;
$REAL_problems = mysql_query($REAL_problems, $tryconnection) or die(mysql_error());
$row_REAL_problems = mysql_fetch_assoc($REAL_problems);

// Then sort the temporary table into the six panels, in reverse chrono sequence.

$PHARMACY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  INVMAJ = $pharmacy1 OR INVMAJ = $pharmacy2 ORDER BY SORT DESC LIMIT 15" ;
$PHARMACY = mysql_query($PHARMACY, $tryconnection) or die(mysql_error());
$row_PHARMACY = mysql_fetch_assoc($PHARMACY);

$FOOD = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  INVMAJ = $food1 OR INVMAJ = $food2 ORDER BY SORT DESC LIMIT 10" ;
$FOOD = mysql_query($FOOD, $tryconnection) or die(mysql_error());
$row_FOOD = mysql_fetch_assoc($FOOD);

$SVACCINES = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  INVMAJ = $vaccines1 OR INVMAJ = $vaccines2 OR INVMAJ = $vaccines3 OR INVMAJ = $vaccines4 OR INVMAJ = $vaccines5 ORDER BY SORT DESC LIMIT 21" ;
$SVACCINES = mysql_query($SVACCINES, $tryconnection) or die(mysql_error());
$row_SVACCINES = mysql_fetch_assoc($SVACCINES);

$RADIOLOGY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  INVMAJ = $radiology1 OR INVMAJ = $radiology2 ORDER BY SORT DESC " ;
$RADIOLOGY = mysql_query($RADIOLOGY, $tryconnection) or die(mysql_error());
$row_RADIOLOGY = mysql_fetch_assoc($RADIOLOGY);

$SURGERY = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  INVMAJ = $surgery1 OR INVMAJ = $surgery2 ORDER BY SORT DESC " ;
$SURGERY = mysql_query($SURGERY, $tryconnection) or die(mysql_error());
$row_SURGERY = mysql_fetch_assoc($SURGERY);

$LAB = "SELECT DATE_FORMAT(INVDATETIME, '%m/%d/%Y') AS INVDATETIME, INVDATETIME AS SORT, INVUNITS, INVDESCR FROM MPL1 WHERE  YEAR(INVDATETIME) >= year(date_sub(now(), interval 3 year)) AND INVMAJ = $lab1 OR INVMAJ = $lab2  OR INVMAJ = $lab3 OR INVMAJ = $lab4 OR INVMAJ = $lab5 OR INVMAJ = $lab6 ORDER BY SORT DESC " ;
$LAB = mysql_query($LAB, $tryconnection) or die(mysql_error());
$row_LAB = mysql_fetch_assoc($LAB);

// finally check if there is an appointment today, and get the doctor.
$APPT = "SELECT TIMEOF, SHORTDOC,DOCREQ FROM APPTS WHERE DATEOF = SUBSTR(NOW(),1,10) AND PETID = '$patient' " ;
$GET_APPT = mysql_query($APPT, $tryconnection) or die(mysql_error()) ;
$row_APPT = mysql_fetch_assoc($GET_APPT) ;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />

<title>CASE HISTORY SUMMARY</title>


<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<script type="text/javascript">
function bodyonload(){
window.print();
window.onfocus=function(){window.close() ;}
//self.close();
}
</script>

<style type="text/css">
<!--
body{
background-color:#FFFFFF;
}
.table2 {	
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
-->
</style>

<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="60" colspan="3" valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
             <td width="70%" height="20" align="right" class="Verdana14B">APPOINTMENT CARD</td>
             <td width="30% class="Arial10" align="right"><?php echo date('l j F Y') ;?></td>
             </tr>
             <tr>
              <td width="59%" height="15" align="left" class="Verdana13B">
                <span style="background-color:#FFFF00">
                <?php echo $row_PATIENT_CLIENT['CUSTNO'] . ' ' . $row_PATIENT_CLIENT['TITLE'] . ' ' . $row_PATIENT_CLIENT['CONTACT']. ' ' . $row_PATIENT_CLIENT['COMPANY'] ;  echo ' at ' .$row_APPT['TIMEOF'] .' with ' . $row_APPT['SHORTDOC']; if ($row_APPT['DOCREQ'] == 1){echo '&hearts;' ;}?>
              </span></td>
          <td width="22%" valign="middle" align="center"></td>
          <td width="19%" colspan="2" align="center">&nbsp;</td>
        </tr>
            <tr>
              <td height="15" align="left" class="Verdana12">        
              <script type="text/javascript">document.write(sessionStorage.address+', '+sessionStorage.city);</script></td>
              <td width="22%" valign="middle" align="center">&nbsp;</td>
              <td width="19%" colspan="2" align="center">&nbsp;</td>
            </tr>
            <tr>
              <td height="15" align="left" class="Verdana12">        
              <script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
              <td width="22%" valign="middle" align="center">&nbsp;</td>
              <td width="19%" colspan="2" align="center">&nbsp;</td>
            </tr>
            <tr>
              <td height="15" colspan="4" align="left"  class="Verdana12"><span class="Verdana13B" style="background-color:#FFFF00">&nbsp;<?php echo $row_PATIENT_CLIENT['PETNAME'] ; ?>
  </span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
            </tr>
            <tr>
              <td height="15" colspan="4" align="left" class="Verdana12">
              <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
            </tr>
            <tr>
              <td height="15" colspan="4" align="left" class="Verdana12">
              <?php 
			  echo $row_PATIENT_CLIENT['PDATA']."<br />";
			  echo "*** ".$row_PATIENT_CLIENT['STICKIE']." ***";
			  ?>
              </td>
            </tr> 
      <tr height="15" colspan="4" align="left" class="Verdana12B">
      <td><?php if (!empty($row_prob)) {echo 'PRESENTING PROBLEM; ' . $row_prob['PROBLEM'] ;}?>
      </td>
      </tr>
            <tr>
      </tr>
        </table>
        </td>
        <td colspan="3" align="center" valign="top" class="Verdana13">&nbsp;</td>
      </tr>
    </table>
    <table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="5%">&nbsp;</td>
        <td width="9%">&nbsp;</td>
        <td width="86%">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" class="Verdana13B"><div align="center">MASTER PROBLEM LIST</div></td>
      </tr>
      <?php 
		  do {
		  echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_REAL_problems['TREATDATE'].'</td>
                <td>'.$row_REAL_problems['TREATDESC'].'</td>
            </tr>';
		  } while ($row_REAL_problems = mysql_fetch_assoc($REAL_problems));
		  ?>
      <tr> </tr>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">IMMUNIZATION</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_SVACCINES['INVDATETIME'].'</td>
                <td>'.$row_SVACCINES['INVDESCR'].'</td>
            </tr>';
      } while ($row_SVACCINES = mysql_fetch_assoc($SVACCINES));
      ?>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">SURGERY</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_SURGERY['INVDATETIME'].'</td>
                <td>'.$row_SURGERY['INVDESCR'].'</td>
            </tr>';
      } while ($row_SURGERY = mysql_fetch_assoc($SURGERY));
      ?>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">RADIOLOGY</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_RADIOLOGY['INVDATETIME'].'</td>
                <td>'.$row_RADIOLOGY['INVDESCR'].'</td>
            </tr>';
      } while ($row_RADIOLOGY = mysql_fetch_assoc($RADIOLOGY));
      ?>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">PHARMACY</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_PHARMACY['INVDATETIME'].'</td>
               <td>'.$row_PHARMACY['INVUNITS'].'&nbsp;'.$row_PHARMACY['INVDESCR'].'</td>
            </tr>';
      } while ($row_PHARMACY = mysql_fetch_assoc($PHARMACY));
      ?>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">LABORATORY</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_LAB['INVDATETIME'].'</td>
                <td>'.$row_LAB['INVDESCR'].'</td>
            </tr>';
      } while ($row_LAB = mysql_fetch_assoc($LAB));
      ?>
      <tr>
        <td colspan="3" height="20"><div align="center"></div></td>
      </tr>
      <tr>
        <td colspan="2" class="Verdana13B"><div align="center">FOOD/DIETS</div></td>
        <td><div align="center"></div></td>
      </tr>
      <?php 
      do {
      echo '<tr>
			    <td>&nbsp;</td>
                <td align="left" width="75">'.$row_FOOD['INVDATETIME'].'</td>
               <td>'.$row_FOOD['INVUNITS'].'&nbsp;'.$row_FOOD['INVDESCR'].'</td>
            </tr>';
      } while ($row_FOOD = mysql_fetch_assoc($FOOD));
      ?>
    </table>
<p>&nbsp;</p>
<table width=100% border="0" cellspacing="0" cellpadding="0">
<tr style="Verdana10" height="15">
<td width="25%">Eyes</td>
<td width="25%">Ears</td>
<td width="25%" colspan="2">Mouth</td>
<td width="25%">Coat/Skin</td>
</tr>
<tr height="15">
</tr>
<tr height="15">
</tr>
<tr style="Verdana10 height="15"">
<td width="25%">Legs/Paws</td>
<td width="25%">N_T</td>
<td width="25%" colspan="2">Urogenital</td>
<td width="25%">GI</td>
</tr>
<tr height="15">
</tr>
<tr height="15">
</tr>
<tr style="Verdana10 height="15"">
<td width="25%">Abdomen</td>
<td width="25%">CNS</td>
<td width="12%" ">Lungs</td>
<td width="13%" ">&nbsp;&nbsp;Body</td>
<td width="25%">Anal</td>
</tr>
<tr height="15">
</tr>
<tr height="15">
</tr>
<tr style="Verdana10 height="15"">
<td width="25%">Assessment</td>
<td width="25%">&nbsp;</td>
<td width="12%" ">&nbsp;</td>
<td width="13%" ">&nbsp;</td>
<td width="25%">&nbsp;</td>
</tr>
<tr height="15">
</tr>
<tr height="15">
</tr>
<tr style="Verdana10 height="15"">
<td width="25%">Plans</td>
<td width="25%">&nbsp;</td>
<td width="12%" ">&nbsp;</td>
<td width="13%" ">&nbsp;</td>
<td width="25%">&nbsp;</td>
</tr>
<tr height="15">
</tr>
<tr height="15">
</tr>
<tr style="Verdana10 height="15"">
<td width="25%">Rabies Tag#</td>
<td width="25%">1</td>
<td width="12%" ">or 2 Yr</td>
<td width="13%" ">&nbsp;</td>
<td width="25%">&nbsp;</td>
</table>
</body>
</html>