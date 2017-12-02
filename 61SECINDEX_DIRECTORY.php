<?php 
require_once('../../tryconnection.php'); 
mysql_select_db($database_tryconnection, $tryconnection);
$query_SECINDEX = "SELECT * FROM SECINDEX WHERE CUSTNO = '$_GET[client]'";
$SECINDEX = mysql_query($query_SECINDEX, $tryconnection) or die(mysql_error());
$row_SECINDEX = mysql_fetch_assoc($SECINDEX);
$totalRows_SECINDEX = mysql_num_rows($SECINDEX);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>SECOND INDICES</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+170,toppos+100);
resizeTo(445,350) ;
//opener.document.getElementById('WindowBodyShadow').style.display="";
}

function OnClose()
{
opener.document.getElementById('WindowBodyShadow').style.display="none";
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
<form method="post" action="" name="" id="" class="FormDisplay" style="position:absolute; top:0px; left:0px;">
  <table width="442" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >

<tr height="10" bgcolor="#000000" class="Verdana11Bwhite">
  <td width="10">&nbsp;</td>
  <td>Second Indices</td>
</tr>
 <tr>
 <td colspan="2"> 
  
  <div style="height:278px;overflow:auto;">
  <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="below" rules="rows">
  <?php if ($totalRows_SECINDEX != 0){ do { ?>
            <tr class="Verdana12" id="<?php echo $row_SECINDEX['SECINDEX']; ?>" onmouseover="document.getElementById(this.id).style.cursor='pointer';" height="15" onclick="window.open('UPDATE_SECOND_INDEX.php?secindex=<?php echo $row_SECINDEX['SECINDEX']; ?>&client=<?php echo $row_SECINDEX['CUSTNO']; ?>','_self')">
        <td>&nbsp;</td>
        <td><?php echo $row_SECINDEX['FNAME']; ?>, <?php echo $row_SECINDEX['LNAME']; ?>, <?php echo $row_SECINDEX['RELATION']; ?>, <?php echo $row_SECINDEX['ADDRESS']; ?>, <?php echo $row_SECINDEX['AUTHORIZED']; ?></td>
    </tr>
    <?php } while ($row_SECINDEX = mysql_fetch_assoc($SECINDEX)); } 
	else {
	echo "<tr class='Verdana12' height='60' align='center'><td>&nbsp;</td><td>There is no record in the database for this client.<br />To add new second index please click on ADD button.</td></tr>";
	}
	?>
    </table></div>
    </td>
    </tr>
    <tr class="ButtonsTable">
      <td align="center">&nbsp;</td>
        <td align="center">
          <input type="button" class="button" value="ADD" onclick="window.open('UPDATE_SECOND_INDEX.php?secindex=0&client=<?php echo $_GET['client']; ?>','_self')"/>
          <input type="reset" class="button" value="CLOSE" onclick="self.close();" />
    </tr>
  </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>