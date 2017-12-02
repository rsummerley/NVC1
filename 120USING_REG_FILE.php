<?php
if (isset($_POST['save'])){
$regist1=!empty($_POST['regist1']) ? "Y" : "N";
$regist2=!empty($_POST['regist2']) ? "Y" : "N";
$regist3=!empty($_POST['regist3']) ? "Y" : "N";
$regist4=!empty($_POST['regist4']) ? "Y" : "N";
$regist5=!empty($_POST['regist5']) ? "Y" : "N";
$regist6=!empty($_POST['regist6']) ? "Y" : "N";
$regist7=!empty($_POST['regist7']) ? "Y" : "N";
$regist8=!empty($_POST['regist8']) ? "Y" : "N";
$regist9=!empty($_POST['regist9']) ? "Y" : "N";
$regist10=!empty($_POST['regist10']) ? "Y" : "N";
$regist11=!empty($_POST['regist11']) ? "Y" : "N";
$regist=$regist1.$regist2.$regist3.$regist4.$regist5.$regist6.$regist7.$regist8.$regist9.$regist10.$regist11;
$theme=$_POST['theme'];
$close=1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>RECEPTION FILE SETTINGS</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script langauge="javascript">

function bodyonload()
{
<?php
if ($close==1){
echo "localStorage.setItem('regist','".$regist."');";
echo "localStorage.setItem('theme','".$theme."');";
echo "self.close();";
}
?>
var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+100,toppos+0);
resizeTo(550,570)  ;
if (localStorage.theme){
document.getElementById(localStorage.theme).checked="checked";
}
if (localStorage.regist){
	for (i=0;i<=10;i++)
		{
		if (localStorage.regist.substr(i,1)=="Y"){
		document.getElementById(i).checked="checked";}
		}
	}
}

function OnClose()
{
self.close();
}

function bodyonunload()
{
opener.document.location.reload();
}


</script>

<style type="text/css">
.table {
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
</style>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="" id="" style="position:absolute; top:0px; left:0px;">
<table width="550" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr>
    <td height="50" align="center" class="Verdana13B">Please select the program(s) to use the registration file</td>
  </tr>
  <tr>
    <td height="140" align="center" valign="top">
    <table width="90%" border="1" cellspacing="0" cellpadding="0" class="table">
      <tr>
        <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="5" colspan="3"></td>
            </tr>
          <tr>
            <td width="4%" class="Verdana11">&nbsp;</td>
            <td width="50%" height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist1" id="0"/>
              Processing Menu</label></td>
            <td width="46%" height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist7" id="6"/>
              Enter New Medical History</label></td>
          </tr>
          <tr>
            <td class="Verdana11">&nbsp;</td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist2" id="1"/>
              Regular Invoicing</label></td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist8" id="7"/>
              Enter Patient Lab Results</label></td>
          </tr>
          <tr>
            <td class="Verdana11">&nbsp;</td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist3" id="2"/>
              Quick Weight</label></td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist9" id="8"/>
              Certificates</label></td>
          </tr>
          <tr>
            <td class="Verdana11">&nbsp;</td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist4" id="3"/>
              Examination Sheets</label></td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist10" id="9"/>
              Clinical Logs</label></td>
          </tr>
          <tr>
            <td class="Verdana11">&nbsp;</td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist5" id="4"/>
              Duty Log</label></td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist11" id="10"/>
              Categorization</label></td>
          </tr>
          <tr>
            <td class="Verdana11">&nbsp;</td>
            <td height="25" class="Verdana11"><label>
              <input type="checkbox" name="regist6" id="5"/>
              Review Patient Medical History</label></td>
            <td height="25" class="Verdana11">&nbsp;</td>
          </tr>
          <tr>
            <td height="5" colspan="3"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="50" align="center" class="Verdana13B">Please select the color theme for the registration file</td>
  </tr>
  <tr>
    <td height="140" align="center" valign="top"><table width="90%" border="1" cellspacing="0" cellpadding="0" class="table">
      <tr>
        <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="10" colspan="5"></td>
            </tr>
            <tr>
              <td width="14" height="15" class="Verdana11">&nbsp;</td>
			  <td width="60" height="15" align="center" bgcolor="#CC0033" class="Verdana11">&nbsp;</td>
              <td width="187" height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="CC0033" value="CC0033"/>
                 Radish</label></td>
              <td width="60" height="15" align="center" bgcolor="#FF99CC" class="Verdana11">&nbsp;</td>
              <td width="123" height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="FF99CC" value="FF99CC"/>
                Roses</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#FF0099" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
              <input type="radio" name="theme" id="FF0099" value="FF0099"/> 
              Seriously Pink
</label></td>
              <td width="60" height="15" align="center" bgcolor="#F9DEE9" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="F9DEE9" value="F9DEE9"/>
                Baby Pink</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#FFFF00" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="FFFF00" value="FFFF00"/>
                Sunshine</label></td>
              <td width="60" height="15" align="center" bgcolor="#DBEBF0" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="DBEBF0" value="DBEBF0"/>
                Smog</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#00CC66" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="00CC66" value="00CC66"/> 
                Meadow</label></td>
              <td width="60" height="15" align="center" bgcolor="#B1B4FF" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="B1B4FF" value="B1B4FF"/>
                 Dusk</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#2FC3F5" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="2FC3F5" value="2FC3F5"/>
                Caribbean Sea</label></td>
              <td width="60" height="15" align="center" bgcolor="#E1E1E1" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="E1E1E1" value="E1E1E1"/>
                 Rain</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#0000FF" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="0000FF" value="0000FF"/>
                Seriously Blue</label></td>
              <td width="60" height="15" align="center" bgcolor="#DCF6DD" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="DCF6DD" value="DCF6DD"/>
                Light Green</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#330099" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="330099" value="330099"/>
                 Violets</label></td>
              <td width="60" height="15" align="center" bgcolor="#FFFCE2" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="FFFCE2" value="FFFCE2"/>
                Vanilla</label></td>
            </tr>
            <tr>
              <td height="5" colspan="5"></td>
            </tr>
            <tr>
              <td height="15" class="Verdana11">&nbsp;</td>
              <td width="60" height="15" align="center" bgcolor="#000000" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="000000" value="000000"/>
                Night</label></td>
              <td width="60" height="15" align="center" bgcolor="#FF701E" class="Verdana11">&nbsp;</td>
              <td height="15" class="Verdana11"><label>
                <input type="radio" name="theme" id="FF701E" value="FF701E" />
                Pumpkin
              </label></td>
            </tr>
            
            <tr>
              <td height="10" colspan="5"></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="ButtonsTable">
    <input name="save" type="submit" class="button" id="button" value="SAVE" />
    <input name="cancel" type="reset" class="button" id="button2" value="CLOSE" onclick="self.close();" /></td>
  </tr>
</table>

</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
