<?php 
require_once('../../tryconnection.php'); 
mysql_select_db($database_tryconnection, $tryconnection);
$query_SECADDRESS = "SELECT * FROM SECADDRESS WHERE CUSTNO = '$_GET[client]'";
$SECADDRESS = mysql_query($query_SECADDRESS, $tryconnection) or die(mysql_error());
$row_SECADDRESS = mysql_fetch_assoc($SECADDRESS);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SECOND ADDRESSES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+170,toppos+100);
resizeTo(450,380) ;
}

function OnClose(){
self.close();
}

function bodyonunload()
{

}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" style="position:absolute; top:0px; left:0px;">
<table width="445" border="0" cellpadding="0" cellspacing="0">
<tr bgcolor="#000000" class="Verdana11Bwhite">
<td height="10">
Second Address</td>
</tr>
<tr>
<td height="297" bgcolor="#FFFFFF">
<div style="overflow:auto;height:297px;">
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF" frame="below" rules="rows">
  <?php if (!empty($row_SECADDRESS)){ do { ?>
      <tr class="Verdana12" id="<?php echo $row_SECADDRESS['SECADDRESS']; ?>" onmouseover="document.getElementById(this.id).style.cursor='pointer';" height="15" onclick="window.open('UPDATE_SECOND_ADDRESS.php?secaddress=<?php echo $row_SECADDRESS['SECADDRESS']; ?>&client=<?php echo $row_SECADDRESS['CUSTNO']; ?>','_self')">
        <td>&nbsp;</td>
        <td><?php echo $row_SECADDRESS['SECNAME']; ?>, <?php echo $row_SECADDRESS['STREET']; ?>, <?php echo $row_SECADDRESS['UNITNO']; ?>, <?php echo $row_SECADDRESS['CITY2']; ?>, <?php echo $row_SECADDRESS['PROV']; ?>, <?php echo $row_SECADDRESS['ZIP2']; ?></td>
    </tr>
    <?php } while ($row_SECADDRESS = mysql_fetch_assoc($SECADDRESS)); } 
	else {
	echo "<tr class='Verdana12' height='60' align='center'><td>&nbsp;</td><td>There is no record in the database for this client.<br /> To add new second address please click on ADD button.</td></tr>";
	}?>
    
    </table></div>
    </td>
    </tr>
    
    <tr class="ButtonsTable">
        <td align="center">
          <input type="button" class="button" value="ADD" onclick="window.open('UPDATE_SECOND_ADDRESS.php?secaddress=0&client=<?php echo $_GET['client']; ?>','_self')"/>
          <input type="reset" class="button" value="CANCEL" onclick="self.close();"/>        </td>
    </tr>
  </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php 
mysql_free_result($SECADDRESS);
?>