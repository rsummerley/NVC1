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
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET PAS = '%s', PAINMEMO = '%s' WHERE PETNO = '$patient'", 
				$_POST['pas'], 
				mysql_real_escape_string($_POST['painmemo'])
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
<title>PAIN</title>
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

document.pain.pas.focus();
}

</script>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="pain" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="75" height="5"></td>
    <td width="50" height="5"></td>
    <td width="85" height="5"></td>
    <td width="66" height="5"></td>
    <td width="124" height="5"></td>
    <td width="100" height="5"></td>
  </tr>
  <tr>
    <td colspan="3" align="right" class="Verdana12">Pain Assessment Score:</td>
    <td class="Labels">
    <input name="pas" type="text" class="Inputright" id="pas" value="<?php echo $row_EXAM['PAS']; ?>" size="1" maxlength="1" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
    <td colspan="2" class="Labels">0-None<br />
      1-Probably None<br />
      2-Mild Discomfort<br />
      3-Mild<br />
      4-Mild/Moderate<br />
      5-Moderate<br />
      6-Moderate/Severe<br />
      7-Severe<br />
      8-Very Severe<br />
      9-Excruciating</td>
    </tr>
  <tr>
    <td colspan="2" align="right" valign="top" class="Verdana12">Comments:</td>
    <td colspan="3" class="Labels"><textarea name="painmemo" cols="40" rows="4" class="commentarea" id="painmemo"><?php echo $row_EXAM['PAINMEMO']; ?></textarea></td>
    <td align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
  </tr>
  <tr>
    <td colspan="6" height="10"></td>
    </tr>
  <tr>
    <td colspan="6" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="button" class="button" value="SAVE" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/>
    </td>
  </tr>
</table>
<input type="hidden" name="check" value="1" />
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
