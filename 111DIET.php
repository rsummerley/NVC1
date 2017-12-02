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
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET DIET = '%s', DIETMEMO = '%s' WHERE PETNO = '$patient'", 
				mysqli_real_escape_string($mysqli_link, $_POST['diet']),
				mysqli_real_escape_string($mysqli_link, $_POST['dietmemo'])
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
<title>DIETARY RECOMMENDATIONS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload(){
<?php echo $closewin; ?>
document.diet_form.diet.focus();
}
</script>

<style type="text/css">
<!--
.style39 {font-family: Verdana; font-weight: bold; }
-->
</style>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="diet_form" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

  <tr>
    <td width="15" align="left" valign="top" class="Labels2">&nbsp;</td>
    <td width="111" align="left" valign="top" class="Labels2">&nbsp;</td>
    <td width="374" class="Labels2">&nbsp;</td>
  </tr>
  <tr>
    <td width="15" align="left" valign="top" class="Labels2">&nbsp;</td>
    <td align="left" valign="top" class="Verdana12">Current Diet:</td>
    <td class="Labels2"><textarea name="diet" cols="40" rows="6" class="commentarea" id="diet"><?php echo $row_EXAM['DIET']; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="3" align="left" valign="top" class="Labels2">&nbsp;</td>
    </tr>
 
  <tr>
    <td width="15" align="left" valign="top" class="Labels2">&nbsp;</td>
    <td align="left" valign="top" class="Verdana12">Recommendation:</td>
    <td class="Labels2"><textarea name="dietmemo" cols="40" rows="6" class="commentarea" id="dietmemo"><?php echo $row_EXAM['DIETMEMO']; ?></textarea></td>
  </tr>
  <tr>
    <td height="30" colspan="3" align="right" valign="bottom" class="Labels2"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
    </tr>
  <tr>
    <td colspan="3" align="center" valign="middle" class="ButtonsTable">
    <input type="submit" name="save" id="button" class="button" value="SAVE" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/>
    </td>
    </tr>
</table>
</form>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
