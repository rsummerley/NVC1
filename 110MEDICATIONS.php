<?php 
session_start();
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysql_query($query_EXAM, $tryconnection) or die(mysql_error());
$row_EXAM = mysqli_fetch_assoc($EXAM);
 

if (isset($_POST['save'])){ 
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET CURRMEDS = '%s' WHERE PETNO = '$patient'", 
				mysql_real_escape_string($_POST['currmeds'])
				);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
$closewin="opener.document.location.reload(); self.close();";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CURRENT MEDICATIONS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
<?php echo $closewin; ?>

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+100);

document.meds.currmeds.focus();
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="meds" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="110" height="5" class="Labels"></td>
    <td width="390" height="5" class="Labels"></td>
  </tr>
  <tr>
    <td align="right" valign="top" class="Verdana12">Comment:</td>
    <td class="Labels"><textarea name="currmeds" cols="40" rows="6" class="commentarea" id="currmeds"><?php echo $row_EXAM['CURRMEDS']; ?></textarea></td>
  </tr>
  <tr>
    <td height="47" colspan="2" align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
        <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="winclosed = 1; opener.document.location.reload(); self.close();"/>      
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
