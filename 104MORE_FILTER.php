<?php 
session_start(); 
require_once('../../tryconnection.php');

mysqli_select_db($tryconnection, $database_tryconnection);
$query_HXFILTER2 = "SELECT * FROM HXFILTER WHERE HXGROUP='2'";
$HXFILTER2 = mysqli_query($tryconnection, $query_HXFILTER2) or die(mysqli_error($mysqli_link));
$row_HXFILTER2 = mysqli_fetch_assoc($HXFILTER2);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ADVANCED FILTER</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+230,toppos+0);

if (opener.document.getElementById('Sub').checked){
	document.more_filter.Sub.checked=true;
	}

if (opener.document.getElementById('Obj').checked){
	document.more_filter.Obj.checked=true;
	}
	
<?php do{

echo "if (opener.document.getElementById('".substr($row_HXFILTER2['HXCNAME'],0,3)."').checked){
	document.more_filter.".substr($row_HXFILTER2['HXCNAME'],0,3).".checked=true;
	}";
} while ($row_HXFILTER2 = mysqli_fetch_assoc($HXFILTER2));

?>
}

function bodyonunload()
{
if (document.more_filter.Sub.checked){
	opener.document.getElementById('Sub').checked=true;
	}
if (document.more_filter.Obj.checked){
	opener.document.getElementById('Obj').checked=true;
	}
	
<?php 
$HXFILTER2 = mysqli_query($tryconnection, $query_HXFILTER2) or die(mysqli_error($mysqli_link));
$row_HXFILTER2 = mysqli_fetch_assoc($HXFILTER2);

do{

echo "if (document.more_filter.".substr($row_HXFILTER2['HXCNAME'],0,3).".checked){
	opener.document.getElementById('".substr($row_HXFILTER2['HXCNAME'],0,3)."').checked=true;
	}";
} while ($row_HXFILTER2 = mysqli_fetch_assoc($HXFILTER2));

?>
	
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="more_filter" id="more_filter" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="350" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr align="center">
    <td height="35" class="Verdana12B">&nbsp;</td>
    <td height="35" colspan="2" align="left" class="Verdana12B">Wellness/Exam filters</td>
    </tr>
  <tr>
    <td width="52">&nbsp;</td>
    <td width="56" height="20">&nbsp;</td>
    <td width="242" height="20"><label>
      <input type="checkbox" name="Sub" id="Sub" />
      Subjective</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="Obj" id="Obj" />
      Objective</label></td>
  </tr>
  <tr align="center">
    <td height="35" class="Verdana12B">&nbsp;</td>
    <td height="35" colspan="2" align="left" class="Verdana12B">Diagnostics filters</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="Lab" id="Lab" />
      Laboratory</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="Rad" id="Rad" />
      Radiology</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="Ult" id="Ult" />
      Ultrasound</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="End" id="End" />
      Endoscopy</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="ECG" id="ECG" />
      ECG</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="CAT" id="CAT" />
      CAT Scan</label></td>
  </tr>
  <tr align="center">
    <td height="35" class="Verdana12B">&nbsp;</td>
    <td height="35" colspan="2" align="left" class="Verdana12B">Invoicing</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20"><label>
      <input type="checkbox" name="Inc" id="Inc" />
      Include Estimates</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="20">&nbsp;</td>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="ButtonsTable">
    <input name="save" type="button" class="button" id="save" value="OK" onclick="self.close();" />
    <input name="cancel" type="reset" class="button" id="cancel" value="CLOSE" onclick="self.close();" /></td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
