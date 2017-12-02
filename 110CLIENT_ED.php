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
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET CLIENTEDMEMO = '%s' WHERE PETNO = '$patient'", 
				mysql_real_escape_string($_POST['clientedmemo'])
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
<title>CLIENT EDUCATION</title>
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

document.clienteducation.clientedmemo.focus();
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="clienteducation" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="98" height="10" class="Labels"></td>
    <td width="132" height="10" class="Labels"></td>
    <td width="70" height="10" class="Labels"></td>
    <td width="70" height="10" class="Labels"></td>
    <td width="90" height="10" class="Labels"></td>
    <td width="40" height="10" class="Labels"></td>
  </tr>
    <tr>
    <td align="right" valign="top" class="Verdana12">Comments:</td>
    <td colspan="4" class="Labels"><textarea name="clientedmemo" cols="40" rows="6" class="commentarea" id="clientedmemo"><?php echo $row_EXAM['CLIENTEDMEMO']; ?></textarea></td>
    <td align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
    </tr>
  <tr>
    <td height="10" colspan="6" align="right"></td>
  </tr>
  <tr>
    <td colspan="6" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
        <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="winclosed = 1; opener.document.location.reload(); self.close();"/>      
    </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>