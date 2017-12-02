<?php 
require_once('../../tryconnection.php'); 

mysql_select_db($database_tryconnection, $tryconnection);
$query_SECINDEX = sprintf("SELECT * FROM SECINDEX WHERE SECINDEX = '$_GET[secindex]'");
$SECINDEX = mysql_query($query_SECINDEX, $tryconnection) or die(mysql_error());
$row_SECINDEX = mysqli_fetch_assoc($SECINDEX);

if (!empty($_POST['homepha'])){$homeph=$_POST['homepha'].'-'.$_POST['homephb'];}
if (!empty($_POST['worka'])){$work=$_POST['worka'].'-'.$_POST['workb'];}
if (!empty($_POST['mobilea'])){$mobile=$_POST['mobilea'].'-'.$_POST['mobileb'];}
if (!empty($_POST['othera'])){$other=$_POST['othera'].'-'.$_POST['otherb'];}


if (isset($_POST['save']) && $_GET['secindex']!="0") {
  $updateSQL = sprintf("UPDATE SECINDEX SET FNAME='%s', LNAME='%s', RELATION='%s', HOMEPH='%s', HAREA='%s', WORK='%s', WAREA='%s', MOBILE='%s', MAREA='%s', OTHER='%s', OAREA='%s', ADDRESS='%s', EMAIL2='%s', AUTHORIZED='%s' WHERE SECINDEX='%s'",
                      $_POST['fname'],
                      $_POST['lname'],
                      $_POST['relation'],
                      $homeph,
                      $_POST['harea'],
                      $work,
                      $_POST['warea'],
                      $mobile,
                      $_POST['marea'],
                      $other,
                      $_POST['oarea'],
                      $_POST['address'],
					  $_POST['email2'],
					  !empty($_POST['authorized']) ? "1" : "0",
                      $_GET['secindex']);
$Result1 = mysql_query($updateSQL, $tryconnection) or die(mysql_error());
$closewindow="self.close();";
}

else if (isset($_POST['save']) && $_GET['secindex']=="0") {
$insertSQL = sprintf("INSERT INTO SECINDEX (CUSTNO, FNAME, LNAME, RELATION, HOMEPH, HAREA, WORK, WAREA, MOBILE, MAREA, OTHER, OAREA, ADDRESS, EMAIL2, AUTHORIZED) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', '%s')",
						$_GET['client'],
						$_POST['fname'],
						$_POST['lname'],
						$_POST['relation'],
						$homeph,
						$_POST['harea'],
						$work,
						$_POST['warea'],
						$mobile,
						$_POST['marea'],
						$other,
						$_POST['oarea'],
						$_POST['address'],
						$_POST['email2'],
						!empty($_POST['authorized']) ? "1" : "0"
						);
$Result1 = mysql_query($insertSQL, $tryconnection) or die(mysql_error());
$closewindow="self.close();";
}

else if (isset($_POST['delete'])){
$delete="DELETE FROM SECINDEX WHERE SECINDEX='$_GET[secindex]'";
$result=mysql_query($delete, $tryconnection) or die(mysql_error());
$closewindow='self.close();';
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if ($_GET['secindex'] == "0"){echo "ADD NEW";} else {echo "EDIT";} ?> SECOND INDEX</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php
echo $closewindow;
?>
document.second_index.fname.focus();
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
<form action="" name="second_index" class="FormDisplay" method="post" style="position:absolute; top:0px; left:0px;">
<table bgcolor="#FFFFFF" width="442" cellspacing="0" border="0">
  <tr>
    <td width="73" height="35" align="right" valign="middle" class="Labels">First Name</td>
    <td width="130" align="left" valign="middle"><input name="fname" type="text" class="Input" id="fname" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECINDEX['FNAME']; ?>" size="12" maxlength="20" /></td>
    <td colspan="2" align="left" valign="middle" class="Labels">Last Name
      <input name="lname" type="text" class="Input" id="lname" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECINDEX['LNAME']; ?>" size="16" maxlength="20" tabindex="1"/></td>
    </tr>
  <tr>
    <td height="35" colspan="1" align="right" valign="middle" class="Labels">Comment</td>
    <td height="35" colspan="3" align="left" valign="middle" class="Labels"><textarea name="relation" cols="39" rows="2" wrap="virtual" id="relation" class="commentarea" tabindex="3"><?php echo $row_SECINDEX['RELATION']; ?></textarea></td>
  </tr>
  <tr>
    <td height="35" colspan="1" align="right" valign="middle" class="Labels">Home</td>
    <td height="35" align="left" valign="middle">
    
    <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="harea" type="text" class="Input" id="harea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECINDEX['HAREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="4"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="homepha" type="text" class="Input" id="homepha" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['HOMEPH'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="6"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="homephb" type="text" class="Input" id="homephb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['HOMEPH'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px;" onkeyup="skip(this, this.value);" tabindex="8"/>    </td>
    <td width="38" height="35" align="right" valign="middle" class="Labels">Cell</td>
    <td width="193" height="35" align="left" valign="middle">
        <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="marea" type="text" class="Input" id="marea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECINDEX['MAREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="10"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="mobilea" type="text" class="Input" id="mobilea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['MOBILE'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="12"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="mobileb" type="text" class="Input" id="mobileb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['MOBILE'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px;" onkeyup="skip(this, this.value);" tabindex="14"/>    </td>
  </tr>
  <tr>
    <td height="35" colspan="1" align="right" valign="middle" class="Labels">Work</td>
    <td height="35" align="left" valign="middle">
    
        <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="warea" type="text" class="Input" id="warea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECINDEX['WAREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="16"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="worka" type="text" class="Input" id="worka" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['WORK'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="18"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="workb" type="text" class="Input" id="workb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['WORK'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px;" onkeyup="skip(this, this.value);" tabindex="20"/>    </td>
    <td height="35" align="right" valign="middle" class="Labels">Other</td>
    <td height="35" align="left" valign="middle">
    
         <input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="(" disabled="disabled" /><input name="oarea" type="text" class="Input" id="oarea" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" onkeyup="skip(this, this.value);" size="3" maxlength="3" value="<?php echo $row_SECINDEX['OAREA']; ?>" style="margin-left:0px;margin-right:0px; width:22px;" tabindex="22"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value=")" disabled="disabled" /><input name="othera" type="text" class="Input" id="othera" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['OTHER'],0,3); ?>"  size="3" maxlength="3" style="margin-left:0px;margin-right:0px; width:22px;" onkeyup="skip(this, this.value);" tabindex="24"/><input type="text" class="Input" size="1" style="margin-left:0px;margin-right:0px; width:5px;" value="-" disabled="disabled" /><input name="otherb" type="text" class="Input" id="otherb" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo substr($row_SECINDEX['OTHER'],4,4); ?>" size="4" maxlength="4" style="margin-left:0px;margin-right:0px;" onkeyup="skip(this, this.value);" tabindex="25"/>    </td>
  </tr>
  
  <tr>
    <td height="35" align="right" valign="middle" class="Labels">Address</td>
    <td height="35" colspan="3" align="left" valign="middle"><input name="address" type="text" class="Input" id="address" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECINDEX['ADDRESS']; ?>" size="40" maxlength="50" tabindex="26"/></td>
  </tr>
  <tr>
    <td height="35" align="right" valign="middle" class="Labels">Email</td>
    <td height="35" colspan="3" align="left" valign="middle"><input name="email2" type="text" class="Input" id="email2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_SECINDEX['EMAIL2']; ?>" size="40" maxlength="50" tabindex="27"/></td>
  </tr>
  <tr>
    <td height="35" colspan="4" align="center" valign="middle" class="Labels"><label><input type="checkbox" name="authorized" <?php if ($row_SECINDEX['AUTHORIZED']=='1'){echo "CHECKED";}; ?>/> Authorized to make financial decisions</label></td>
    </tr>
  <tr>
    <td height="35" colspan="4" align="center" valign="middle" bgcolor="#B1B4FF">
    <input name="save" type="submit" class="button" id="SAVE" value="SAVE"/>
    <input name="delete" type="submit" class="button" id="delete" value="DELETE"/>
    <input name="cancel" type="reset" class="button" id="CANCEL" value="CANCEL" onclick="self.close()" />    </td>
  </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
