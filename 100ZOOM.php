<?php 
session_start();
require_once('../tryconnection.php');
include("../ASSETS/age.php");

$client=$_GET['client'];

mysql_select_db($database_tryconnection, $tryconnection);
////////////////////////////////// CLIENT ///////////////////////////////////
$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client'";
$CLIENT = mysql_query($query_CLIENT, $tryconnection) or die(mysql_error());
$row_CLIENT = mysqli_fetch_assoc($CLIENT);
///////////////////////////////// PATIENTS ////////////////////////////////////
$pdead=" AND PDEAD=0";
if (isset($_GET['pdead'])){
$pdead='';
}
$query_PATIENTS = "SELECT PETID, CUSTNO, PETNO, PETNAME, PETTYPE, PETBREED, PSEX, PDOB, PWEIGHT, PDEAD FROM PETMAST WHERE CUSTNO = '$client'".$pdead." ORDER BY PETNAME ASC";
$PATIENTS = mysql_query($query_PATIENTS, $tryconnection) or die(mysql_error());
$row_PATIENTS = mysqli_fetch_assoc($PATIENTS);
$totalRows_PATIENTS = mysqli_num_rows($PATIENTS);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>ZOOM</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+22,toppos+80);

//var browser=navigator.userAgent;
//var x;
//if (browser.match(/Firefox/i))
//{x=16;}
//else {x=16;}
//var rows=<?php echo $totalRows_PATIENTS; ?>;
//var height=x*rows+75;
//window.resizeTo(732,height);
//moveTo(30,130);
}


function highliteline(x,y){
//document.getElementById(x).style.cursor="pointer";
var coffee=document.getElementById('coffee').value;
if (coffee!=x){
document.getElementById(x).style.backgroundColor=y;
}
}
</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" style="position:absolute; top:0px; left:0px; background-color:#FFFFFF; z-index:10">
<input type="hidden" name="coffee" id="coffee" value="" />


    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr bgcolor="#000000" class="Verdana11Bwhite">
        <td width="15">&nbsp;</td>
        <td width="200" height="10">Name</td>
        <td width="50">Species</td>
        <td width="200">Breed</td>
        <td width="25" align="center">Sex</td>
        <td width="120" align="center">Age</td>
        <td width="70" align="center">Weight</td>
        <td>Notes</td>
      </tr>
      <tr>
      	<td colspan="8">
        <div style="width:732px;height:452px;overflow:auto;">        
            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
<?php do { ?>
                <tr class="Verdana11" id="<?php echo $row_PATIENTS['PETID']; ?>" onmouseover="highliteline(this.id,'<?php if ($row_PATIENTS['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';} ?>');" onmouseout="whiteoutline(this.id);" >
                  <td width="15" class="Verdana12B">&nbsp;</td>
                  <td width="200" height="15" class="Verdana12B"><?php echo $row_PATIENTS['PETNAME']; ?></td>
                  <td width="50" height="15" align="center"><?php if ($row_PATIENTS['PETTYPE']=='1'){echo "Can";} else if ($row_PATIENTS['PETTYPE']=='2'){echo "Fel";} else if ($row_PATIENTS['PETTYPE']=='3'){echo "Equ";}else if ($row_PATIENTS['PETTYPE']=='4'){echo "Bov";}else if ($row_PATIENTS['PETTYPE']=='5'){echo "Cap";}else if ($row_PATIENTS['PETTYPE']=='6'){echo "Por";}else if ($row_PATIENTS['PETTYPE']=='7'){echo "Avi";}else if ($row_PATIENTS['PETTYPE']=='8'){echo "Oth";}; ?></td>
                  <td width="200" height="15"><?php echo $row_PATIENTS['PETBREED']; ?></td>
                  <td width="25" height="15" align="center"><?php echo $row_PATIENTS['PSEX']; ?></td>
                  <td width="120" height="15" align="center">
				  <?php agecalculation($tryconnection,$row_PATIENTS['PDOB']); ?></td>
                  <td width="70" height="15" align="center"><?php if($row_PATIENTS['PWEIGHT']<'10'){echo '&nbsp;'.$row_PATIENTS['PWEIGHT'];} else {echo $row_PATIENTS['PWEIGHT'];} echo " ".$_SESSION['weightunit']; ?></td>
                  <td height="15"><span class="Verdana11B"><?php if ($row_PATIENTS['PDEAD']=='1'){echo "Dec.";}?></span></td>
                </tr>
                <?php } while ($row_PATIENTS = mysqli_fetch_assoc($PATIENTS)); ?>
             </table>    
        </div>      
        </td>
      </tr>
      <tr>
      	<td colspan="8" class="ButtonsTable" align="center">
    	<input type="button" name="" class="button" id="" value="OK" onclick="self.close()" />
    	<input type="button" name="" class="button" id="" value="ADD" onclick="opener.document.location='INSERT_PATIENT.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&species=0&patient=0'; self.close();" />
		<input type="button" name="ALL" class="<?php if($_GET['pdead']=='1'){echo "hidden";} else {echo "button";} ?>" id="DISPLAY ALL" value="ALL" onclick="self.location='ZOOM.php?pdead=1&client=<?php echo $_GET['client']; ?>'" />
        <input type="button" name="ALL" class="<?php if(!isset($_GET['pdead'])){echo "hidden";} else {echo "button";} ?>" id="LIVE" value="LIVE" onclick="self.location='ZOOM.php?client=<?php echo $_GET['client']; ?>'" />        <input type="button" name="" class="button" id="" value="CLOSE" onclick="self.close()" />        </td>
  	 </tr> 
 </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
