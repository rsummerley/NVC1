<?php
session_start(); 
require_once('../tryconnection.php');

$client=$_GET['client'];

mysqli_select_db($tryconnection, $database_tryconnection);
$query_CLIENT = "SELECT CUSTNO, SOURCE, CODE, TERMS, SVC, DATE_FORMAT(LDATE,'%m/%d/%Y') AS LDATE, DATE_FORMAT(LASTPAY,'%m/%d/%Y') AS LASTPAY, DISC, BALANCE, YTDSLS, CREDIT, CVISIT, LASTMON, LASTINT, MEMO FROM ARCUSTO WHERE CUSTNO = '$client' LIMIT 1";
$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

//date and time stamp showing the last time the client file - accounting info was modified in any way. It happens at invoicing, and during client file edits
$adatetime=date("Y-m-d H:i:s");

if (isset($_POST['save'])) {
$updateSQL = sprintf("UPDATE ARCUSTO SET TERMS='%s', MEMO='%s', ADATETIME='$adatetime' WHERE CUSTNO='$client' LIMIT 1",
                      $_POST['terms'],
                      mysqli_real_escape_string($mysqli_link, $_POST['memo']));
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
$closewin="self.close();";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ACCOUNT DETAILS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload()
{
<?php
echo $closewin;
?>

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+100,toppos+100);

}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->

<form class="FormDisplay" method="POST" action="" name="account_details" style="position:absolute; top:0px; left:0px;">
<table width="600" border="1" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" bordercolor="#446441" frame="void" rules="none">
  <tr>
    <td align="center" valign="bottom">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="below" rules="all"> 
  <tr>
    <td width="50%" height="84" align="center">
    	<table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="57%" height="25">Current Balance:</td>
        <td width="43%" height="25"><?php echo $row_CLIENT['BALANCE']; ?></td>
      </tr>
      <tr>
        <td height="25">Current Deposit:</td>
        <td height="25"><?php echo $row_CLIENT['CREDIT']; ?></td>
      </tr>
      <tr>
        <td height="25">Year To Date Sales:</td>
        <td height="25"><?php echo $row_CLIENT['YTDSLS']; ?></td>
      </tr>
    </table></td>
    <td width="50%" height="84" align="center"><table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" height="25">Discount Percentage:</td>
        <td width="50%" height="25"><?php echo $row_CLIENT['DISC']; ?></td>
      </tr>
      <tr>
        <td height="25">Service Charges:</td>
        <td height="25"><?php echo $row_CLIENT['SVC']; ?></td>
      </tr>
      <tr>
        <td height="25">Statement Invoices:</td>
        <td height="25"><?php echo $row_CLIENT['CVISIT']; ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="56" align="center">
    <table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="57%" height="25">Last Sale:</td>
        <td width="43%" height="25"><?php echo $row_CLIENT['LDATE']; ?></td>
      </tr>
      <tr>
        <td height="25">Last Payment:</td>
        <td height="25"><?php echo $row_CLIENT['LASTPAY']; ?></td>
      </tr>
    </table></td>
    <td height="56" align="center">
    <table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="31%" height="15" align="left" valign="bottom">Payment Code:</td>
        <td width="69%" height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="25">&nbsp;</td>
        <td height="25" align="left" valign="top">
        <select name="terms">
          <option value="<?php echo $row_CLIENT['TERMS']; ?>" selected="selected"><?php if ($row_CLIENT['TERMS']=='1'){echo "Normal Credit";} else if ($row_CLIENT['TERMS']=='2'){echo "Cash Only";} else if ($row_CLIENT['TERMS']=='3'){echo "No Credit";} else if ($row_CLIENT['TERMS']=='4'){echo "Collection";} else if ($row_CLIENT['TERMS']=='5'){echo "Post Dated Cheque";} else if ($row_CLIENT['TERMS']=='6'){echo "Accept Cheque";}; ?></option>
          <option value="1">Normal Credit</option>
          <option value="2">Cash Only</option>
          <option value="3">No Credit</option>
          <option value="4">Collection</option>
          <option value="5">Post Dated Cheque</option>
          <option value="6">Accept Cheque</option>
		</select>        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="56" align="center">
    <table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="57%" height="25">Last Month's Interest:</td>
        <td width="43%" height="25"><?php echo $row_CLIENT['LASTINT']; ?></td>
      </tr>
      <tr>
        <td height="25">Last Month's Balance:</td>
        <td height="25"><?php echo $row_CLIENT['LASTMON']; ?></td>
      </tr>
    </table></td>
    <td height="56" align="center">
    <table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" height="25">Search Code #1:</td>
        <td width="50%" height="25"><?php echo $row_CLIENT['SOURCE']; ?></td>
      </tr>
      <tr>
        <td height="25">Search Code #2:</td>
        <td height="25"><?php echo $row_CLIENT['CODE']; ?></td>
      </tr>
    </table></td>
  </tr>
</table>
    </td>
  </tr>
  <tr>
    <td height="143" align="center" valign="middle">
    <textarea name="memo" cols="70" rows="7" class="commentarea"><?php echo $row_CLIENT['MEMO']; ?></textarea>    </td>
  </tr>
  <tr>
    <td height="35" align="center" valign="middle" bgcolor="#B1B4FF">
      <input name="save" type="submit" class="button" id="save" value="SAVE"/>
      <input name="update2" type="button" class="button" id="update2" value="PRINT" disabled="disabled" />
      <input name="update3" type="reset" class="button" id="update3" value="CLOSE" onclick="self.close()" />
      </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>