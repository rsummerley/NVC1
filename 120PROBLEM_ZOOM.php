<?php
session_start();
//unset($_SESSION['']);
require_once('../tryconnection.php');
include("../ASSETS/age.php");

$timeformat=$_SESSION['timeformat'];
$recepid=$_GET['recepid'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_RECEP = "SELECT *, DATE_FORMAT(DATEIN, '%m/%d/%Y') AS DATEIN, DATE_FORMAT(TIME, '$timeformat') AS TIME FROM RECEP WHERE RECEPID='$recepid'";
$RECEP = mysql_query($query_RECEP, $tryconnection) or die(mysql_error());
$row_RECEP = mysqli_fetch_assoc($RECEP);


$patient=$row_RECEP['RFPETID'];
$client=$row_RECEP['CUSTNO'];

include("../ASSETS/photo_directory.php");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PRESENTING PROBLEM DETAIL</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+100,toppos+50);

}

function OnClose()
{
self.close();
}

function bodyonunload()
{

}
</script>
<style type="text/css">
#table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
#table2 {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
</style>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" style="position:absolute; top:0px; left:0px;">

<table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    <td height="60" colspan="3" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <?php echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY']; ?>
<!--        <script type="text/javascript">document.write(sessionStorage.custname);</script>-->        
		</span>
        </td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <?php echo $custterm; ?>
        <!--<script type="text/javascript">document.write(sessionStorage.custterm);</script>-->          
        </span>
        </td>
        <td width="19%" colspan="2" rowspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="0" id="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2"> 
                    <?php echo $row_PATIENT_CLIENT['BALANCE']; ?>       
					<!--<script type="text/javascript">document.write(sessionStorage.custprevbal);</script>--></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <?php echo $row_PATIENT_CLIENT['BALANCE']; ?>
                    <!--<script type="text/javascript">document.write(sessionStorage.custcurbal);</script>--></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<?php echo $row_PATIENT_CLIENT['AREA'].'-'.$row_PATIENT_CLIENT['PHONE'].', '.$row_PATIENT_CLIENT['CAREA2'].'-'.$row_PATIENT_CLIENT['PHONE2'].', '.$row_PATIENT_CLIENT['CAREA3'].'-'.$row_PATIENT_CLIENT['PHONE3'].', '.$row_PATIENT_CLIENT['CAREA4'].'-'.$row_PATIENT_CLIENT['PHONE4']; ?>
		<!--<script type="text/javascript">document.write(sessionStorage.custphone);</script>--></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        
<?php  echo $pettype.', '.$row_PATIENT_CLIENT['PETBREED'];?>
<!--<script type="text/javascript">document.write(sessionStorage.desco);</script>-->
         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <?php echo  $desct; ?>
        <!--<script type="text/javascript">document.write(sessionStorage.desct);</script>--> (<?php agecalculation($tryconnection,$pdob); ?>)
		</td>
      </tr>
    </table>    </td>
    </tr>
  <tr>
    <td height="" colspan="3" align="center" valign="top">
    
    
    <table class="table" width="600" height="265" border="1" cellpadding="0" cellspacing="0" >
    <tr>
      <td align="center">
      <table width="90%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55" height="25" class="Verdana12B">Date:</td>
          <td width="214" height="25" class="Verdana12"><?php echo $row_RECEP['DATEIN']."&nbsp;&nbsp;".$row_RECEP['TIME']; ?></td>
          <td width="269" rowspan="4" align="center" class="Verdana14B"><span class="disabled">PHOTO</span></td>
        </tr>
        <tr class="Verdana12" >
          <td height="25" class="Verdana12B">Doctor:</td>
          <td height="25"><?php echo $row_RECEP['CLINICIAN']; ?></td>
        </tr>
        <tr class="Verdana14B" >
          <td height="25" colspan="2" class="Verdana12B">Presenting problem:</td>
          </tr>
        <tr class="Verdana12">
          <td height="150" colspan="2" valign="top"><?php echo $row_RECEP['PROBLEM']; ?></td>
        </tr>
      </table></td>
    </tr>
    </table>    
    </td>
  </tr>
    <tr class="ButtonsTable">
      <td align="center">
      <input class="button" type="button" value="OK" onclick="self.close();" />
      </td>
    </tr>
  
</table>
</form>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
