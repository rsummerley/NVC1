<?php 
session_start();
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

mysqli_select_db($tryconnection, $database_tryconnection);
$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysqli_query($tryconnection, $query_EXAM) or die(mysqli_error($mysqli_link));
$row_EXAM = mysqli_fetch_assoc($EXAM);
 

if (isset($_POST['save'])){ 
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET WELLMEMO = '%s' WHERE PETNO = '$patient'", 
				mysqli_real_escape_string($mysqli_link, $_POST['wellmemo'])
				);
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
$closewin="opener.document.location.reload(); self.close();";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>WELLNESS TESTING</title>
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

document.wellness.wellmemo.focus();
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="wellness" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="51" height="23" class="Labels"></td>
    <td width="39" height="23" class="Labels"></td>
    <td width="131" height="23" class="Labels"></td>
    <td width="185" height="23" class="Labels"></td>
    <td width="58" height="23" class="Labels"></td>
    <td width="36" height="23" class="Labels"></td>
  </tr>
  <tr>
    <td colspan="2" align="right" valign="top" class="Verdana12">Comments:</td>
    <td colspan="3" align="left" class="Labels"><textarea name="wellmemo" cols="40" rows="6" class="commentarea" id="wellmemo"><?php echo $row_EXAM['WELLMEMO']; ?></textarea></td>
    <td align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
  </tr>
  <tr>
    <td height="25" colspan="6"></td>
    </tr>
  <tr>
    <td colspan="6" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
        <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="winclosed = 1; opener.document.location.reload(); self.close();"/>      
      </td>
    </tr>
</table>
<input type="hidden" name="check" value="1" />
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
