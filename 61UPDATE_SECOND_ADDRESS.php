<?php 
require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);
$query_SECADDRESS = sprintf("SELECT * FROM SECADDRESS WHERE SECADDRESS = '%s'", $_GET['secaddress']);
$SECADDRESS = mysql_query($query_SECADDRESS, $tryconnection) or die(mysql_error());
$row_SECADDRESS = mysql_fetch_assoc($SECADDRESS);

if (!empty($_POST['tela'])){$tel=$_POST['tela'].'-'.$_POST['telb'];}
if (!empty($_POST['tel2a'])){$tel2=$_POST['tel2a'].'-'.$_POST['tel2b'];}

if (isset($_POST['save']) && $_GET['secaddress']!='0') {
$updateSQL = sprintf("UPDATE SECADDRESS SET CUSTNO='%s', STREET='%s', UNITNO='%s', CITY2='%s', PROV='%s', ZIP2='%s', COUNTRY2='%s', TEL='%s', AREA='%s', EXT='%s', TEL2='%s', AREA2='%s', EXT2='%s', LEGAL='%s', SECNAME='%s' WHERE SECADDRESS='%s'",
                       $_GET['client'],
                       mysql_real_escape_string($_POST['street']),
                       $_POST['unitno'],
                       mysql_real_escape_string($_POST['city2']),
                       $_POST['prov'],
                       strtoupper($_POST['zip2']),
                       mysql_real_escape_string($_POST['country2']),
                       $tel,
                       $_POST['area'],
                       $_POST['ext'],
                       $tel2,
                       $_POST['area2'],
                       $_POST['ext2'],
	                   !empty($_POST['legal']) ? "1" : "0",
                       mysql_real_escape_string($_POST['secname']),
                       $_GET['secaddress']);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
$closewindow="window.open('SECADDRESS_DIRECTORY.php?client=".$_GET['client']."','_self');";
}


else if (isset($_POST['save']) && $_GET['secaddress']=='0') {
$insertSQL = sprintf("INSERT INTO SECADDRESS (CUSTNO, STREET, UNITNO, CITY2, PROV, ZIP2, COUNTRY2, TEL, AREA, EXT, TEL2, AREA2, EXT2, LEGAL, SECNAME) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', '%s')",
						$_GET['client'],
						mysql_real_escape_string($_POST['street']),
						$_POST['unitno'],
						mysql_real_escape_string($_POST['city2']),
						$_POST['prov'],
                       strtoupper($_POST['zip2']),
					    mysql_real_escape_string($_POST['country2']),
					    $tel,
					    $_POST['area'],
					    $_POST['ext'],
					    $tel2,
						$_POST['area2'],
						$_POST['ext2'],
						!empty($_POST['legal']) ? "1" : "0",
                       mysql_real_escape_string($_POST['secname'])
						);
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
$closewindow="window.open('SECADDRESS_DIRECTORY.php?client=".$_GET['client']."','_self');";
}

else if (isset($_POST['delete'])){
$delete="DELETE FROM SECADDRESS WHERE SECADDRESS='$_GET[secaddress]'";
$result=mysql_query($delete, $tryconnection) or die(mysql_error());
$closewindow="window.open('SECADDRESS_DIRECTORY.php?client=".$_GET['client']."','_self');";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if ($totalRows_SECADDRESS == "0"){echo "ADD NEW";} else {echo "EDIT";} ?> SECOND ADDRESS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
</style>


<script langauge="javascript">

function bodyonload(){
<?php echo $closewindow; ?>
document.second_address.secname.focus();
}

//x=element, y=value
function skip(x,y){
	if (y.length==x.maxLength){
	next=x.tabIndex;
	document.forms[0].elements[next+2].focus();
	document.forms[0].elements[next+2].select();
	}
}


</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form action="" name="second_address" method="post" style="position:absolute; top:0px; left:0px;">
<table bgcolor="#FFFFFF" width="445" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="5" align="left" valign="middle" class="Labels" height="17"></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Address Name</td>
    <td height="25" colspan="4" align="left" valign="middle" class="Labels"><input name="secname" type="text" class="Input" id="secname" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['SECNAME']; ?>" size="29" maxlength="25"/></td>
  </tr>
  <tr>
    <td width="133" height="25" align="right" valign="middle" class="Labels">Street</td>
    <td height="25" colspan="4" align="left" valign="middle" class="Labels"><input name="street" type="text" class="Input" id="street" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['STREET']; ?>" size="29" maxlength="25"/></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Unit No. </td>
    <td height="25" colspan="4" align="left" valign="middle" class="Labels"><input name="unitno" type="text" class="Input" id="unit" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['UNITNO']; ?>" size="29" maxlength="25"/></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">City</td>
    <td height="25" colspan="4" align="left" valign="middle" class="Labels"><input name="city2" type="text" class="Input" id="city2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['CITY2']; ?>" size="29" maxlength="25"/></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Prov./State</td>
    <td width="57" height="25" align="left" valign="middle" class="Labels"><input name="prov" type="text" class="Input" id="prov" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['PROV']; ?>" size="3" maxlength="3"/></td>
    <td colspan="2" align="right" valign="middle" class="Labels">P.Code/Zip
      <input name="zip2" type="text" class="Input" id="zip2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['ZIP2']; ?>" size="7" maxlength="7"/></td>
    <td width="97" height="25" align="right" valign="middle" class="Labels">&nbsp;</td>
    </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Country</td>
    <td height="25" colspan="5" align="left" valign="middle" class="Labels"><input name="country2" type="text" class="Input" id="country2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['COUNTRY2']; ?>" size="29" maxlength="25"/>    </td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Telephone 1</td>
    <td height="25" colspan="2" align="left" valign="middle" class="Labels">
    <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="area" type="text" class="Input" id="area" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECADDRESS['AREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="8"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="tela" type="text" class="Input" id="tela" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECADDRESS['TEL'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="10"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="telb" type="text" class="Input" id="telb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECADDRESS['TEL'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="11"/>    </td>
    <td width="78" height="25" align="right" valign="middle" class="Labels">Ext.
      <input name="ext" type="text" class="Input" id="ext" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['EXT']; ?>" size="5" maxlength="5" tabindex="12" style="margin-left:0px;margin-right:0px; width:30px;"/></td>
    <td height="25" colspan="2" align="left" valign="middle" class="Labels">&nbsp;</td>
    </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Labels">Telephone 2</td>
    <td height="25" colspan="2" align="left" valign="middle" class="Labels">
    <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="area2" type="text" class="Input" id="area2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECADDRESS['AREA2']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="15"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="tel2a" type="text" class="Input" id="tel2a" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECADDRESS['TEL2'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="17"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="tel2b" type="text" class="Input" id="tel2b" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECADDRESS['TEL2'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px; width:30px;" onkeyup="skip(this, this.value);" tabindex="18"/>    </td>
    <td height="25" align="right" valign="middle" class="Labels">Ext. 
      <input name="ext2" type="text" class="Input" id="ext2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECADDRESS['EXT2']; ?>" size="5" maxlength="5" tabindex="19" style="margin-left:0px;margin-right:0px; width:30px;"/></td>
    <td height="25" colspan="2" align="left" valign="middle" class="Labels">&nbsp;</td>
    </tr>
  <tr> 
    <td height="30" colspan="5" align="center" valign="middle" class="Labels">
    <label><input type="checkbox" name="legal" id="legal" <?php if ($row_SECADDRESS['LEGAL']=='1'){echo "CHECKED";}; ?>/>&nbsp;&nbsp;Legal address</label>    </td>
    </tr>
  <tr>
    <td height="2"></td>
    <td height="2"></td>
    <td width="80" height="2"></td>
    <td height="2"></td>
    <td height="2"></td>
  </tr>
  <tr>
    <td colspan="5" align="center" valign="middle" class="ButtonsTable">
    <input name="save" type="submit" class="button" id="save" value="SAVE"/>
    <input name="delete" type="submit" class="button" id="delete" value="DELETE"/>
    <input name="cancel" type="reset" class="button" id="CANCEL" value="CLOSE" onclick="self.close()" />    </td>  
   </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
<?php 
mysql_free_result($SECADDRESS);
?>