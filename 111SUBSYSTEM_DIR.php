<?php
session_start(); 
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];
 

mysql_select_db($database_tryconnection, $tryconnection);
$query_CATEGORIES = "SELECT DISTINCT TTYPE, TCATGRY FROM EXAMHOLD WHERE PETNO = '$patient' ORDER BY TCATGRY";
$CATEGORIES = mysql_query($query_CATEGORIES, $tryconnection) or die(mysql_error());
$row_CATEGORIES = mysql_fetch_assoc($CATEGORIES);
$totalRows_CATEGORIES = mysql_num_rows($CATEGORIES);

$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysql_query($query_EXAM, $tryconnection) or die(mysql_error());
$row_EXAM = mysql_fetch_assoc($EXAM);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PHYSICAL EXAM: <?php if ($row_EXAM['FINDINGS'] == '1') {echo "All - No Abnormal Findings";} else if ($row_EXAM['FINDINGS'] == '2') {echo "All - Some Abnormal Findings";} else if ($row_EXAM['FINDINGS'] == '3') {echo "Some - No Abnormal Findings";} else if ($row_EXAM['FINDINGS'] == '4') {echo "Some - With Abnormal Findings";} else if ($row_EXAM['FINDINGS'] == '5') {echo "No Exam Conducted";}?></title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">


function bodyonload()
{
window.focus();
<?php echo $closewin; ?>
var totalrows = '<?php echo $totalRows_CATEGORIES; ?>';
totalrows = 25*totalrows + 105;
resizeTo(700,totalrows);
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+40,toppos+90);
//opener.document.getElementById('WindowBodyShadow').style.display="";
}

function checkbox() 
{
document.subsystem.submit();
}

function marksubsys(x,y){
    if(document.getElementById(x).checked){
	document.getElementById(y).value="1";
	}
	else{
	document.getElementById(y).value="0";
	}
}


function opensubsys(tcatgry, custno, patient){
subsystem=window.open('SUBSYSTEM.php?category=' + tcatgry + '&client=' + custno + '&patient=' + patient ,'_blank','width=400, height=500');
}

</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()" onfocus="checkopen(subsystem);">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="subsystem" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">

<table width="700" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="20" width="15"></td>
    <td width=""></td>
    <td width="400"></td>
  </tr>
    
    <?php
	
	do { 
	
	$query_SUBSYSTEM = "SELECT TVAR1, TDESCR FROM EXAMHOLD WHERE TCATGRY = '$row_CATEGORIES[TCATGRY]' AND PETNO = '$patient' AND TVAR1='1' ";
	$SUBSYSTEM = mysql_query($query_SUBSYSTEM, $tryconnection) or die(mysql_error());
	$row_SUBSYSTEM = mysql_fetch_assoc($SUBSYSTEM);

	$query_TMEMO = "SELECT TMEMO FROM EXAMHOLD WHERE TCATGRY = '$row_CATEGORIES[TCATGRY]' AND TNO = '1' AND PETNO = '$patient'";
	$TMEMO = mysql_query($query_TMEMO, $tryconnection) or die(mysql_error());
	$row_TMEMO = mysql_fetch_assoc($TMEMO);

	if (!empty($row_SUBSYSTEM)) {
	$tvar1 = 1;
	}
	else {
	$tvar1 = 0;
	}
		
	?>
  <tr>
    <td height="25" align="right" valign="top">&nbsp;</td>
    <td class="Verdana12" align="left" width="170">
	
    <label class="<?php if ($tvar1 == 1) {echo 'Verdana12B';} else {echo "Verdana12B";}?>" onmouseover="CursorToPointer(this.id)" <?php //if ($tvar1 == 1) {echo 'style="background-color:#FFFF00"';}?> >
    <input type="checkbox" name="subsystem" id="<?php echo $row_CATEGORIES['TCATGRY']; ?>" onclick="opensubsys('<?php echo $row_CATEGORIES['TCATGRY']; ?>','<?php echo $client; ?>','<?php echo $patient; ?>');" <?php if ($tvar1 == 1) {echo "checked";}?>/>&nbsp;<?php echo $row_CATEGORIES['TTYPE']; ?></label>
    
    </td>
    <td height="25" align="left">
    <div style="height:25px; width:450px; overflow:auto;"><?php do { echo $row_SUBSYSTEM['TDESCR']." &nbsp;"; } while ($row_SUBSYSTEM = mysql_fetch_assoc($SUBSYSTEM)); ?><br  />
	<?php if (!empty($row_TMEMO['TMEMO'])) {echo "&bull;".$row_TMEMO['TMEMO'];} ?></div>
    </td>
    
  </tr>
    <?php } while ($row_CATEGORIES = mysql_fetch_assoc($CATEGORIES)); ?>   </td>
  <tr>
    <td height="20" class="Labels"></td>
    <td class="Labels"><span id="petra"></span>
    
    <script type="text/javascript">//document.write(top.location);</script>
    <script type="text/javascript">//document.write(opener.document.location);</script>
    
    </td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="ButtonsTable">
    <input type="hidden" name="check" value="1" />
    <input type="reset" name="button2" id="button2" class="button" value="OK" onclick="opener.document.location.reload(); self.close();"/>
    </td>
    </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>