<?php 
session_start();
require_once('../tryconnection.php'); 

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
}
else if (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

mysql_select_db($database_tryconnection, $tryconnection);

$query_PATIENT = "SELECT PWEIGHT FROM PETMAST WHERE PETID = '$patient'";
$PATIENT = mysql_query($query_PATIENT, $tryconnection) or die(mysql_error());
$row_PATIENT = mysqli_fetch_assoc($PATIENT);


if (isset($_POST['save'])){
$update_PETMAST="UPDATE PETMAST SET PWEIGHT='$_POST[pweight]' WHERE PETID='$patient'";
$result=mysql_query($update_PETMAST, $tryconnection) or die(mysql_error());
$insert_PWEIGHTS="INSERT INTO PWEIGHTS (WPETID, WEIGHT) VALUES ('$patient','$_POST[pweight]')";
$result=mysql_query($insert_PWEIGHTS, $tryconnection) or die(mysql_error());
$closewin='opener.document.location.reload(); self.close(); opener.document.comingfromquickweight.submit();';
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>QUICK WEIGHT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+200,toppos+100);

<?php echo $closewin; ?>
document.patient_weight.pweight.focus();
}

function bodyonunload()
{

}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="patient_weight" id="patient_weight" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
  <table width="400" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <th height="55" align="center" valign="bottom" class="Verdana12" scope="col">Quickly enter weight for <?php if (isset($_SESSION['petname'])) {echo $_SESSION['petname'];} else {echo $_GET['petname'];} ?></th>
    </tr>
    <tr>
      <td height="110" align="center" valign="middle" class="Verdana12">
      <label>Weight&nbsp;&nbsp;<input name="pweight" type="text" class="Inputright" id="pweight" size="8" maxlength="8" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PWEIGHT']; ?>" />
      </label>
      <script type="text/javascript">
	  document.write(localStorage.weightunit);
	  </script>
      </td>
    </tr>
    <tr>
      <td align="center" class="ButtonsTable">
        <input name="save" type="submit" class="button" id="save" value="SAVE" />
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="self.close()" />
      </td>
    </tr>
  </table>
</form>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
