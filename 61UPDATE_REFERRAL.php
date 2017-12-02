<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php if ($_GET['client']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> REFERRAL</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->


<script langauge="javascript">
function bodyonload(){
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+80,toppos+100);
resizeTo(650,230) ;
document.referral.refvet.focus();
document.referral.refvet.value = opener.document.add_edit_client.refvet.value;
document.referral.refclin.value = opener.document.add_edit_client.refclin.value;
}
/*function help()
{
document.referral.reftype.value = document.referral.refvet.value;
}
*/


function post_value(){
opener.document.add_edit_client.refvet.value = document.referral.refvet.value;
opener.document.add_edit_client.refclin.value = document.referral.refclin.value;
//opener.document.getElementById('WindowBodyShadow').style.display="none";
self.close();
}

</script>


<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form action="" name="referral" class="FormDisplay" method="POST"  style="position:absolute; top:0px; left:0px;">
<table bgcolor="#FFFFFF" width="633" height="193" border="0" cellpadding="0" cellspacing="0"><!--DWLayoutTable-->
  <tr>
    <td width="177" height="59" align="right" valign="middle" class="Labels">Referring Veterinarian </td>
    <td width="282" valign="middle" class="Labels"><input name="refvet" type="text" class="Input" id="refvet" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="" size="30" maxlength="30"/></td>
    <td width="166" valign="middle" class="Labels">
<!--    <select name="reftype" id="reftype" onchange="help()">
      <option>Use Help</option>
      <option value="Not sure">Not sure</option>
      <option value="No regular doctor">No Regular Doctor</option>
      <option value="No regular clinic">No Regular Clinic</option>
      <option value="Do not know">Do Not Know</option>
      <option value="Non-member clinic">Non-Member Clinic</option>
    </select>    
-->    </td>
  </tr>
  <tr>
    <td height="56" align="right" valign="middle" class="Labels">Referring Clinic </td>
    <td colspan="2" valign="middle" class="Labels"><input name="refclin" type="text" class="Input" id="refclin" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="" size="40" maxlength="40"/></td>
    </tr>
  <tr>
    <td height="35" colspan="3" align="center" valign="middle" bgcolor="#B1B4FF">
      <input name="SAVE" type="button" class="button" id="SAVE" value="SAVE" onclick="post_value();">
      <input name="button" type="button" class="button" id="button" value="SCAN" onclick="window.open('../../MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?tea=1','_blank','width=700, height=420')" />
      <input name="CANCEL" type="reset" class="button" id="CANCEL" value="CANCEL" onclick="self.close()" />      
	</td>
    </tr>
</table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
