<?php
session_start();
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];
 
mysql_select_db($database_tryconnection, $tryconnection);
$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysql_query($query_EXAM, $tryconnection) or die(mysql_error());
$row_EXAM = mysql_fetch_assoc($EXAM);
 

if (isset($_POST['save'])){ 
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET WEIGHT = '%s', BCS = '%s', WEIGHTMEMO = '%s' WHERE PETNO = '$patient'", 
				$_POST['weight'], 
				$_POST['bcs'], 
				mysql_real_escape_string($_POST['weightmemo'])
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
<title>WEIGHT/BCS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function bodyonload(){
<?php echo $closewin; ?>

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+100);

document.weightbcs.weight.focus();
}


function bodyonunload(){
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

<form method="post" action="" name="weightbcs" style="position:absolute; top:0px; left:0px;">
<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="100" height="80" align="right" valign="middle" class="Verdana12">Weight</td>
    <td width="114" height="80" align="left" valign="middle" class="Labels">
    <input name="weight" type="text" class="Inputright" id="weight" value="<?php echo $row_EXAM['WEIGHT']; ?>" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>
    <script type="text/javascript">document.write(localStorage.weightunit);</script>
    </td>
    <td width="46" height="80" align="right" valign="middle" class="Verdana12">BCS</td>
    <td width="60" height="80" align="left" valign="middle" class="Labels"><input name="bcs" type="text" class="Inputright" id="bcs" value="<?php echo $row_EXAM['BCS']; ?>" size="3" maxlength="3" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
    <td width="180" height="80" align="left" valign="middle" class="Labels">1-Emaciated<br />
2-Underweight<br />
3-Ideal<br />
4-Overweight<br />
5-Obese</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="Verdana12">Comments:</td>
    <td colspan="4"><textarea name="weightmemo" cols="40" rows="4" class="commentarea" id="weightmemo"><?php echo $row_EXAM['WEIGHTMEMO']; ?></textarea></td>
    </tr>
  <tr>
    <td height="30" colspan="5" align="right" valign="bottom"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
    </tr>
  <tr>
    <td colspan="5" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/>
    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
