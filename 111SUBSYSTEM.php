<?php
session_start(); 
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];
 

mysqli_select_db($tryconnection, $database_tryconnection);
$query_SUBSYSTEM = "SELECT UCASE(TTYPE) AS TTYPE, TDESCR, TCATGRY, TNO, TID, TVAR1 FROM EXAMHOLD WHERE TCATGRY = '$_GET[category]' AND CUSTNO = '$client' AND PETNO = '$patient' ORDER BY TCATGRY, TNO";
$SUBSYSTEM = mysqli_query($tryconnection, $query_SUBSYSTEM) or die(mysqli_error($mysqli_link));
$row_SUBSYSTEM = mysqli_fetch_assoc($SUBSYSTEM);
$totalRows_SUBSYSTEM = mysqli_num_rows($SUBSYSTEM);

$query_MEMO = "SELECT TMEMO FROM EXAMHOLD WHERE TCATGRY = '$_GET[category]' AND TNO = '1' AND PETNO = '$patient'";
$MEMO = mysqli_query($tryconnection, $query_MEMO) or die(mysqli_error($mysqli_link));
$row_MEMO = mysqli_fetch_assoc($MEMO);


if (isset($_POST['check'])){ 
	foreach ($_POST['tvar1'] as $key => $value){
	mysqli_select_db($tryconnection, $database_tryconnection);
	$keyplusone = $key+1;
	$updateSQL = sprintf("UPDATE EXAMHOLD SET TVAR1 = '%d' WHERE TCATGRY = '%s' AND TNO = '%d' AND CUSTNO = '%d' AND PETNO = '%d'", $value, $_GET['category'], $keyplusone, $_GET['client'], $_GET['patient']);
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	}
	
	$update_MEMO = sprintf("UPDATE EXAMHOLD SET TMEMO = '%s' WHERE TCATGRY = '%s' AND TNO = '1' AND PETNO = '%d'",
				mysqli_real_escape_string($mysqli_link, $_POST['tmemo']), 
				$_GET['category'], 
				$_GET['patient']);
	$MEMO= mysqli_query($tryconnection, $update_MEMO) or die(mysqli_error($mysqli_link));

$closewin="opener.document.location.reload(); self.close();";
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $row_SUBSYSTEM['TTYPE']; ?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
function bodyonload()
{
<?php echo $closewin; ?>
var totalrows = '<?php echo $totalRows_SUBSYSTEM; ?>';
totalrows = 25*totalrows + 166;
resizeTo(400,totalrows);
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+0);
}

//function OnClose(){
//opener.document.getElementById('WindowBodyShadow').style.display="none";
//self.close();
//}

function checkbox() 
{
document.subsystem.submit();
}


function marksubsys(x,y){
    if(document.getElementById(x).checked){
	document.getElementById(y).value="1";
	
		if ( y != 'a<?php echo $row_SUBSYSTEM['TID']; ?>'){
		document.getElementById('1').checked = false;
		document.getElementById('a<?php echo $row_SUBSYSTEM['TID']; ?>').value = '0';
		}
		
	}
	else{
	document.getElementById(y).value="0";
	}
}


</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<div id="popup" style="height:auto">
<form method="post" action="" name="subsystem" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="400" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="26" height="10" class="Labels"></td>
    <td width="43" class="Labels"></td>
    <td width="152" class="Labels"></td>
    <td width="30" class="Labels"></td>
    <td width="162" class="Labels"></td>
    </tr>
  
   <?php 
    
	do { ?>

    <tr class="Verdana12" height="25">
    <td>&nbsp;</td>
    <td align="left"></td>
    <td colspan="3" align="left">
    <label><input type="checkbox" name="n<?php echo $row_SUBSYSTEM['TNO']; ?>" id="<?php echo $row_SUBSYSTEM['TNO']; ?>" value="<?php echo $row_SUBSYSTEM['TNO']; ?>" onclick="marksubsys(<?php echo $row_SUBSYSTEM['TNO']; ?>,'a<?php echo $row_SUBSYSTEM['TID']; ?>')" <?php if($row_SUBSYSTEM['TVAR1'] == '1') {echo "checked";} ?>/>&nbsp;&nbsp;<?php echo $row_SUBSYSTEM['TDESCR']; ?></label>        
    <input type="hidden" name="tvar1[]" id="a<?php echo $row_SUBSYSTEM['TID']; ?>" value="<?php echo $row_SUBSYSTEM['TVAR1']; ?>" size="2" />
    </td>
    </tr>
   
   <?php } while ($row_SUBSYSTEM = mysqli_fetch_assoc($SUBSYSTEM)); ?> 
 
  <tr>
    <td height="5" class="Labels"></td>
    <td align="right" class="Labels"></td>
    <td class="Labels"></td>
    <td align="right" class="Labels"></td>
    <td class="Labels"></td>
  </tr>
  <tr>
    <td height="" colspan="2" align="right" valign="top" class="Labels">Comments:</td>
    <td colspan="3" class="Labels"><textarea name="tmemo" cols="40" rows="4" class="commentarea" id="tmemo"><?php echo $row_MEMO['TMEMO']; ?></textarea></td>
    </tr>
  <tr>
    <td colspan="5" align="center" class="ButtonsTable">
    <input type="hidden" name="check" value="1" />
    <input type="button" class="button" value="SAVE" onclick="checkbox()" />
    <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="opener.document.location.reload(); self.close();"/></td>
    </tr>
</table>

</form>
</div>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>