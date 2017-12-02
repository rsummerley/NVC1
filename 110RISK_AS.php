<?php 
session_start();
require_once('../../../tryconnection.php');

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT = sprintf("SELECT PLIFE, PETTYPE FROM PETMAST WHERE PETMAST.PETID = %s", $patient);
$PATIENT = mysql_query($query_PATIENT, $tryconnection) or die(mysql_error());
$row_PATIENT = mysql_fetch_assoc($PATIENT);

$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysql_query($query_EXAM, $tryconnection) or die(mysql_error());
$row_EXAM = mysql_fetch_assoc($EXAM);
 
$species=$row_PATIENT['PETTYPE'];

$query_LIFESTYLE = "SELECT * FROM PETLIFESTYLE WHERE LSPECIES='$species' ORDER BY LIFESTYLE";
$LIFESTYLE = mysql_query($query_LIFESTYLE, $tryconnection) or die(mysql_error());
$row_LIFESTYLE = mysql_fetch_assoc($LIFESTYLE);
$totalRows_LIFESTYLE = mysql_num_rows($LIFESTYLE);

$plife=0;
if (isset($_POST['save'])){
$plife=implode(",",$_POST['plife']);
	if ($patient!=0){
	$updateSQL = "UPDATE PETMAST SET PLIFE='$plife'WHERE PETID='$patient'";
	$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
	}

$updateSQL = sprintf("UPDATE EXAMHOLD2 SET VACCINESMEMO = '%s', PARCONTROLMEMO = '%s' WHERE PETNO = '$patient'", 
				mysql_real_escape_string($_POST['vaccinesmemo']),
				mysql_real_escape_string($_POST['parcontrolmemo'])
				);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());

$closewin="opener.document.location.reload(); self.close();";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />

<title>RISK ASSESSMENT</title>


<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>

<script type="text/javascript">
function bodyonload()
{
<?php echo $closewin; ?>

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+150,toppos+30);
winclosed = 0;
}

function winonblur(){
if (winclosed != 1){
	window.focus();
	}
}

</script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()" onblur="winonblur()">

<form method="post" action="" name="riskas" style="position:absolute; top:0px; left:0px;">
  <input type="hidden" name="plife" value="<?php echo $plife; ?>" />
  <table width="450" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2" height="40" align="center" valign="middle" class="Verdana14B">Editing Lifestyle: <?php if ($_GET['species']=='1'){echo "Canine";} else if ($_GET['species']=='2'){echo "Feline";} else if ($_GET['species']=='3'){echo "Equine";}else if ($_GET['species']=='4'){echo "Bovine";}else if ($_GET['species']=='5'){echo "Caprine";}else if ($_GET['species']=='6'){echo "Porcine";}else if ($_GET['species']=='7'){echo "Avian";}else if ($_GET['species']=='8'){echo "Other";}; ?></td>
    </tr>
    
    <tr>
      <td colspan="2" align="left">
        
        <div style="height:364px;overflow:auto;">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            
            <?php 
    
	do { ?>
              
              <tr>
                <td width="19%" class="Labels">&nbsp;</td>
      <td width="81%" align="left" class="Verdana12">
        <label><input type="checkbox" name="plife[]" id="<?php echo $row_LIFESTYLE['LIFESTYLEID']; ?>" value="<?php echo $row_LIFESTYLE['LIFESTYLEID']; ?>"  <?php 
		$life = explode(",",$row_PATIENT['PLIFE']);
		if (in_array($row_LIFESTYLE['LIFESTYLEID'], $life))
		{echo "CHECKED";} 
		?> />
          <?php echo $row_LIFESTYLE['LIFESTYLE']; ?></label> </td>
      </tr>
              
              <?php } while ($row_LIFESTYLE = mysql_fetch_assoc($LIFESTYLE)); ?>
  </table>
    </div>  </td>
  </tr>  
    <tr>
      <td height="5" colspan="2" ></td>
    </tr>
    <tr>
      <td align="right" valign="top" class="Verdana12">Vaccines:</td>
      <td valign="top" class="Labels"><textarea name="vaccinesmemo" cols="40" rows="2" class="commentarea" id="vaccinesmemo"><?php echo $row_EXAM['VACCINESMEMO']; ?></textarea>      </td>
    </tr>
    <tr>
      <td align="right" valign="top" class="Verdana12">Parasite Control:</td>
      <td valign="top" class="Labels"><textarea name="parcontrolmemo" cols="40" rows="2" class="commentarea" id="parcontrolmemo"><?php echo $row_EXAM['PARCONTROLMEMO']; ?></textarea></td>
    </tr>
    <tr>
      <td height="10" align="right" valign="bottom" colspan="2" ><img src="../../../IMAGES/h copy.jpg" alt="h" width="30" height="30" class="hidden" /></td>
    </tr>
    <tr>
      <td height="45" align="center" class="ButtonsTable"  colspan="2" >
        <input type="submit" name="save" id="save" class="button" value="SAVE" />
        <input type="reset" name="button2" id="button2" class="button" value="CLOSE" onclick="winclosed = 1; opener.document.location.reload(); self.close();"/>      
        </td>
    </tr>
  </table>
</form>
</body>
</html>
