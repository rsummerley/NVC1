<?php
session_start(); 
require_once('../../tryconnection.php'); 
include("../../ASSETS/tax.php");


if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}

mysqli_select_db($tryconnection, $database_tryconnection);
$query_CLIENT = sprintf("SELECT CUSTNO, SOURCE, CODE, TERMS, SVC, DISC, CVISIT, PTAX, GTAX, LOCKED FROM ARCUSTO WHERE CUSTNO = '$client' LIMIT 1");
$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

if (isset($_POST['save'])) {
$updateSQL = sprintf("UPDATE ARCUSTO SET SOURCE='%s', CODE='%s', TERMS='%s', SVC='%s', DISC='%s', CVISIT='%s', PTAX='%s', GTAX='%s', ADATETIME='%s', LOCKED='%s' WHERE CUSTNO='$client' LIMIT 1",
                       $_POST['source'],
                       $_POST['code'],
                       $_POST['terms'],
                       !empty($_POST['svc']) ? "1" : "0",
                       $_POST['disc'],
                       !empty($_POST['cvisit']) ? "1" : "0",
                       !empty($_POST['ptax']) ? "1" : "0",
                       !empty($_POST['gtax']) ? "1" : "0",
					   date("Y-m-d H:i:s"),
                       !empty($_POST['locked']) ? "1" : "0"
                       );
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if ($_GET['client']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> ACCOUNT INFORMATION</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->




<script langauge="javascript">

function bodyonload()
{
resizeTo(470,350) ;
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+170,toppos+100);
//opener.document.getElementById('WindowBodyShadow').style.display="";
}


function bodyonunload(){
self.close();
}

</script>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->

<form action="" name="account_info" class="FormDisplay" method="POST" style="position:absolute; top:0px; left:0px;">
  <table bgcolor="#FFFFFF" width="447" height="311" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="42" align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle" class="Labels">Payment Code </td>
      <td class="Labels">
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
    <tr>
      <td align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle"><label><input type="checkbox" name="svc" <?php if ($row_CLIENT['SVC']=='1'){echo "CHECKED";}; ?>/> Service Charge</label></td>
      <td align="left" valign="middle"><label><input type="checkbox" name="ptax" <?php if ($row_CLIENT['PTAX']=='1'){echo "CHECKED";}; ?>/> PST exempt</label></td>
    </tr>
    <tr>
      <td align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle"><label><input type="checkbox" name="cvisit" <?php if ($row_CLIENT['CVISIT']=='1'){echo "CHECKED";}; ?>/> Statement Invoices</label></td>
      <td align="left" valign="middle"><label><input type="checkbox" name="gtax" <?php if ($row_CLIENT['GTAX']=='1'){echo "CHECKED";}; ?>/> <?php taxname($database_tryconnection, $tryconnection, date('m/d/Y')); ?> exempt</label></td>
    </tr>
    <tr>
      <td align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle" class="Labels">Discount Percentage</td>
      <td class="Labels"><input name="disc" type="text" class="Input" id="disc" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['DISC']; ?>" size="2" maxlength="2" /></td>
    </tr>
    <tr>
      <td align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle" class="Labels">Search Code #1</td>
      <td class="Labels"><input name="source" type="text" class="Input" id="source" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['SOURCE']; ?>" size="1" maxlength="1" /></td>
    </tr>
    <tr>
      <td align="right" valign="middle" class="Labels">&nbsp;</td>
      <td align="left" valign="middle" class="Labels">Search Code #2 </td>
      <td class="Labels"><input name="code" type="text" class="Input" id="code" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_CLIENT['CODE']; ?>" size="2" maxlength="2" /></td>
    </tr>
    <tr>
      <td colspan="2" class="Labels">&nbsp;</td>
      <td><label><input type="checkbox" name="locked" <?php if ($row_CLIENT['LOCKED']=='1'){echo "CHECKED";}; ?>/> Invoicing</label></td>
    </tr>
    <tr>
      <td height="35" colspan="3" align="center" valign="middle" bgcolor="#B1B4FF">
        <input name="save" type="submit" class="button" id="save" value="SAVE" />
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close()" />      
      </td>  
    </tr>
  </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>