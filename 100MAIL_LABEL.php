<?php 
session_start();
require_once('../tryconnection.php');

include('../ASSETS/photo_directory.php');
include('../ASSETS/age.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />

<title>LABEL</title>


<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<script type="text/javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+100);
<?php echo $closewin; ?>
document.tff.commtext.focus();
}


</script>

<style type="text/css">
<!--
.Labels2{
font-family:Arial, Helvetica, sans-serif;
}
.commentarea{
font-family:Arial, Helvetica, sans-serif;
}
#apDiv1 {
	position:absolute;
	width:306px;
	height:38px;
	z-index:1;
	left: 94px;
	top: 137px;
	border:solid black thin;
}
-->
</style>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">

<table style="" width="305" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" onclick="window.self.close();">
  <tr>
    <td height="5" colspan="2"></td>
    </tr>
  <tr>
    <td width="5" rowspan="6"></td>
    <td width="300" align="left" class="Verdana18">
    <span class="Verdana20B"><?php  echo $row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY']; ?>  </td>
    </tr>
  <tr>
    <td align="left" class="Verdana15">
    <?php 
        echo $row_PATIENT_CLIENT['ADDRESS1'].' '.$row_PATIENT_CLIENT['ADDRESS2'] ; ?></td> </tr>
	<tr>
    <td align="left" class="Verdana15">
    <?php
	echo $row_PATIENT_CLIENT['CITY'].", ".$row_PATIENT_CLIENT['STATE']."&nbsp;&nbsp;".$row_PATIENT_CLIENT['ZIP']; 
	?></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<!--<input type="button" name="button3" id="button3" value="print" onclick="DYMOLabelPlugin.Paste();"/>-->
</body>
</html>
