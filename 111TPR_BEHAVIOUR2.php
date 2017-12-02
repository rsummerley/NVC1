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
$updateSQL = sprintf("UPDATE EXAMHOLD2 SET TEMP = '%s', PULSE = '%s', RESPRATE = '%s', RESPCHAR = '%s', MUCOUSM = '%s', CRT = '%s', CRTNORMAL='%s', PULSENORM = '%s', RESPNORM = '%s', ATTITUDE = '%s', HYDRATION = '%s', HYDRPC = '%s', TPRMEMO = '%s', BLOODPRES = '%s' WHERE PETNO = '$patient'", 
				$_POST['temp'], 
				$_POST['pulse'], 
				$_POST['resprate'], 
				!empty($_POST['respchar']) ? "1" : "0", 
				$_POST['mucousm'], 
				$_POST['crt'], 
				!empty($_POST['crtnormal']) ? "1" : "0", 
				!empty($_POST['pulsenorm']) ? "1" : "0", 
				!empty($_POST['respnorm']) ? "1" : "0", 
				$_POST['attitude'], 
				$_POST['hydration'], 
				$_POST['hydrpc'], 
				mysql_real_escape_string($_POST['tprmemo']), 
				$_POST['bloodpres'], 
				$_GET['client'], 
				$_GET['patient']);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
$closewin="opener.document.location.reload(); self.close();";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>TPR/BEHAVIOUR</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="application/javascript">
function bodyonload(){
<?php echo $closewin; ?>

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+100);

document.tpr.temp.focus();
}

function tpr(){
    if(document.getElementById('tprnormal').checked){
	document.getElementById('tpr').value="1";
	}
	else{
	document.getElementById('tpr').value="0";
	}
}

function crt(){
    if (document.tpr.crtnormal.checked == true){
	document.tpr.crt.value="Normal";
	} 
	else{
	document.tpr.crt.value="";
	}
}

    </script>
<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="tpr" style="position:absolute; top:0px; left:0px;">
<table width="500" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="27" height="5" class="Labels"></td>
    <td width="87" height="5" class="Labels"></td>
    <td width="39" height="5" class="Labels"></td>
    <td width="68" height="5" class="Labels"></td>
    <td width="39" height="5" class="Labels"></td>
    <td width="73" height="5" class="Labels"></td>
    <td width="39" height="5" class="Labels"></td>
    <td width="92" height="5" class="Labels"></td>
    <td width="36" height="5" class="Labels"></td>
  </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Temp:</td>
    <td colspan="2" align="left" class="Labels">
    <input name="temp" type="text" class="Inputright" id="temp" value="<?php echo $row_EXAM['TEMP']; ?>" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>    </td>
    <td colspan="5" class="Labels">&nbsp;</td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Pulse:</td>
    <td colspan="2" align="left" class="Labels">
    <input name="pulse" type="text" class="Inputright" id="pulse" value="<?php echo $row_EXAM['PULSE']; ?>" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>    </td>
    <td colspan="5" class="Verdana12">
    <label>
    <input type="checkbox" name="pulsenorm" id="pulsenorm" <?php if ($row_EXAM['PULSENORM']=='1'){echo "CHECKED";}; ?>/>
    Normal</label></td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Resp.Rate:</td>
    <td colspan="2" align="left" class="Labels">
    <input name="resprate" type="text" class="Inputright" id="resprate" value="<?php echo $row_EXAM['RESPRATE']; ?>" size="6" maxlength="6" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>    </td>
    <td colspan="5" class="Verdana12">
    <label>
    <input type="checkbox" name="respnorm" id="respnorm" <?php if ($row_EXAM['RESPNORM']=='1'){echo "CHECKED";}; ?>/>
    Normal</label>&nbsp;&nbsp;
    <label>
    <input type="checkbox" name="respchar" id="respchar" <?php if ($row_EXAM['RESPCHAR']=='1'){echo "CHECKED";}; ?>/>
    Panting</label>    </td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Blood Pres.:</td>
    <td colspan="7" align="left" class="Labels"><input name="bloodpres" type="text" class="Input" id="bloodpres" value="<?php echo $row_EXAM['BLOODPRES']; ?>" size="15" maxlength="15" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Mucous&nbsp;Mem.:</td>
    <td colspan="7" align="left" class="Labels"><input name="mucousm" type="text" class="Input" id="mucousm" value="<?php echo $row_EXAM['MUCOUSM']; ?>" size="15" maxlength="15" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">CRT:</td>
    <td colspan="2" align="left" class="Labels"><input name="crt" type="text" class="Inputright" id="crt" value="<?php echo $row_EXAM['CRT']; ?>" size="2" maxlength="2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/></td>
    <td colspan="5" class="Verdana12">
    <label>
    <input type="checkbox" name="crtnormal" id="crtnormal" onclick="crt()" value="1" <?php if ($row_EXAM['CRTNORMAL']=='1'){echo "CHECKED";}; ?>/>
    &lt;2</label>    </td>
    </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Attitude:</td>
    <td colspan="2" class="Verdana12">
    <label>
    <input type="radio" name="attitude" value="Bright Alert and Responsive" <?php if ($row_EXAM['ATTITUDE']=='BAR'){echo "CHECKED";}; ?>/>&nbsp;BAR</label></td>
    <td colspan="2" class="Verdana12">
    <label>
    <input type="radio" name="attitude" value="Quite Alert and Responsive" <?php if ($row_EXAM['ATTITUDE']=='QAR'){echo "CHECKED";}; ?>/>&nbsp;QAR</label></td>
    <td colspan="2" class="Verdana12">
    <label>
    <input type="radio" name="attitude" value="Depressed" <?php if ($row_EXAM['ATTITUDE']=='Depressed'){echo "CHECKED";}; ?>/>&nbsp;Dep.</label></td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td height="25" class="Labels">&nbsp;</td>
    <td align="left" class="Verdana12">Hydration:</td>
    <td colspan="2" class="Verdana12">
    <label>
    <input type="radio" name="hydration" value="Normal" <?php if ($row_EXAM['HYDRATION']=='Normal'){echo "CHECKED";}; ?>/>&nbsp;Normal</label></td>
    <td colspan="3" class="Verdana12">
    <label>
    <input type="radio" name="hydration" value="Dehydrated" <?php if ($row_EXAM['HYDRATION']=='Dehydrated'){echo "CHECKED";}; ?>/>&nbsp;Dehydrated</label></td>
    <td align="left" class="Labels"><input name="hydrpc" type="text" class="Inputright" id="hydrpc" value="<?php echo $row_EXAM['HYDRPC']; ?>" size="2" maxlength="2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)"/>
      %</td>
    <td class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" valign="top" class="Verdana12">&nbsp;</td>
    <td align="left" valign="top" class="Verdana12">Comments:</td>
    <td colspan="6" align="left" valign="top" class="Labels"><textarea name="tprmemo" cols="40" rows="4" class="commentarea" id="tprmemo"><?php echo $row_EXAM['TPRMEMO']; ?></textarea></td>
    <td align="right" valign="bottom" class="Labels"><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
  </tr>
  <tr>
    <td align="right" valign="top" class="Labels">&nbsp;</td>
    <td align="right" valign="top" class="Labels">&nbsp;</td>
    <td colspan="2" align="right" valign="top" class="Labels">&nbsp;</td>
    <td colspan="2" align="right" valign="top" class="Labels">&nbsp;</td>
    <td align="right" valign="top" class="Labels">&nbsp;</td>
    <td align="right" valign="top" class="Labels">&nbsp;</td>
    <td align="right" valign="top" class="Labels">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="9" align="center" class="ButtonsTable">
    <input type="submit" name="save" id="save" class="button" value="SAVE" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/></td>
    </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
