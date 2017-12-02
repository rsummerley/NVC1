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
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET TARTAR = '%s', GINGIVITIS = '%s', PD = '%s', NEEDSDENT = '%s', DENTALMEMO = '%s' WHERE PETNO = '$patient'", 
					$_POST['tartar'], 
					$_POST['gingivitis'], 
					$_POST['pd'], 
					$_POST['needsdent'], 
					mysqli_real_escape_string($mysqli_link, $_POST['dentalmemo'])
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
<title>DENTAL FINDINGS</title>
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

<form method="post" action="" name="riskas" style="position:absolute; top:0px; left:0px;">

<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="55" height="4" class="Labels"></td>
    <td width="64" height="4" class="Labels"></td>
    <td height="4" colspan="2" class="Labels"></td>
    <td width="67" height="4" class="Labels"></td>
    <td width="73" height="4" class="Labels"></td>
    <td width="78" height="4" class="Labels"></td>
    <td width="65" height="4" class="Labels"></td>
    <td width="30" height="4" class="Labels"></td>
  </tr>
  <tr>
    <td align="center" class="Labels">&nbsp;</td>
    <td colspan="3" align="center" class="Verdana12BBlue">Tartar</td>
    <td colspan="2" align="center" class="Verdana12BBlue">Gingivitis</td>
    <td colspan="2" align="center" class="Verdana12BBlue">Periodontitis</td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="right" class="Labels">&nbsp;</td>
    <td colspan="3" align="center" class="Verdana12"><label><input type="radio" name="tartar" value="0" <?php if ($row_EXAM['TARTAR']=='0'){echo "CHECKED";}; ?>/>&nbsp;0</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="gingivitis" value="0" <?php if ($row_EXAM['GINGIVITIS']=='0'){echo "CHECKED";}; ?>/>&nbsp;0</label></td>
    <td colspan="2" align="center" class="Verdana12"><input type="radio" name="pd" value="0" <?php if ($row_EXAM['PD']=='0'){echo "CHECKED";}; ?>/>0</td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="right" class="Labels">&nbsp;</td>
    <td colspan="3" align="center" class="Verdana12"><label><input type="radio" name="tartar" value="1" <?php if ($row_EXAM['TARTAR']=='1'){echo "CHECKED";}; ?>/>&nbsp;1</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="gingivitis" value="1" <?php if ($row_EXAM['GINGIVITIS']=='1'){echo "CHECKED";}; ?>/>&nbsp;1</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="pd" value="1" <?php if ($row_EXAM['PD']=='1'){echo "CHECKED";}; ?>/>1</label></td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="right" class="Labels">&nbsp;</td>
    <td colspan="3" align="center" class="Verdana12"><label><input type="radio" name="tartar" value="2" <?php if ($row_EXAM['TARTAR']=='2'){echo "CHECKED";}; ?>/>&nbsp;2</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="gingivitis" value="2" <?php if ($row_EXAM['GINGIVITIS']=='2'){echo "CHECKED";}; ?>/>&nbsp;2</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="pd" value="2" <?php if ($row_EXAM['PD']=='2'){echo "CHECKED";}; ?>/>2</label></td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" align="right" class="Labels">&nbsp;</td>
    <td colspan="3" align="center" class="Verdana12"><label><input type="radio" name="tartar" value="3" <?php if ($row_EXAM['TARTAR']=='3'){echo "CHECKED";}; ?>/>&nbsp;3</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="gingivitis" value="3" <?php if ($row_EXAM['GINGIVITIS']=='3'){echo "CHECKED";}; ?>/>&nbsp;3</label></td>
    <td colspan="2" align="center" class="Verdana12"><label><input type="radio" name="pd" value="3" <?php if ($row_EXAM['PD']=='3'){echo "CHECKED";}; ?>/>3</label></td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="36" colspan="3" align="right" valign="middle" class="Verdana12">Needs Dentistry:</td>
    <td colspan="2" align="center" valign="middle" class="Verdana12"><label><input type="radio" name="needsdent" value="1" <?php if ($row_EXAM['NEEDSDENT']=='1'){echo "CHECKED";}; ?>/>&nbsp;Yes</label></td>
    <td colspan="2" align="left" valign="middle" class="Verdana12"><label><input type="radio" name="needsdent" value="0" <?php if ($row_EXAM['NEEDSDENT']=='0'){echo "CHECKED";}; ?>/>&nbsp;No</label></td>
    <td class="Labels">&nbsp;</td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right" valign="top" class="Verdana12">Comments:</td>
    <td colspan="6" class="Labels"><textarea name="dentalmemo" cols="40" rows="6" class="commentarea" id="dentalmemo"><?php echo $row_EXAM['DENTALMEMO']; ?></textarea></td>
    <td align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td width="31"></td>
    <td width="37"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="9" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/>      </td>
    </tr>
</table>
</form>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
