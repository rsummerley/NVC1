<?php
session_start();
require_once('../tryconnection.php');
include('../ASSETS/photo_directory.php');


$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysqli_query($tryconnection, $query_PREFER) or die(mysqli_error($mysqli_link));
$row_PREFER = mysqli_fetch_assoc($PREFER);
$treatmxx=$_SESSION['client']/$row_PREFER['TRTMCOUNT'];
$treatmxx="PHOTO".floor($treatmxx);
$uploaddir = "$treatmxx"."/";
$uploadfile = $uploaddir . $_SESSION['patient'].".jpg";

//FOR REFRESHING THE PARENT WINDOW AFTER ENTERING QUICK WEIGHT
if (isset($_POST['check2'])){
$closewin='parent.window.document.location.reload();';
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/IFRAME.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.5" />

<title>DV MANAGER MAC</title>

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<style type="text/css">
<!--
#WindowBody {
	position:absolute;
	top:0px;
	width:733px;
	height:553px;
	z-index:1;
	font-family: "Verdana";
	outline-style: ridge;
	outline-color: #FFFFFF;
	outline-width: medium;
	background-color: #FFFFFF;
	left: 0px;
	color: #000000;
	text-align: left;
}
-->
</style>

</head>
<!-- InstanceBeginEditable name="EditRegion2" -->
<script type="text/javascript">
var patient = '<?php echo $_SESSION['patient']; ?>';
var custlmonbal = '<?php echo $custlmonbal ;?>' ;
function bodyonload()
{
var patient = '<?php echo $_SESSION['patient']; ?>';
<?php echo $closewin; ?>
if (sessionStorage.refID==null){
document.getElementById('goto').style.display="none";
document.getElementById('advice').innerText='Please select from the menu what you would like to do with this patient.';
}
else if (sessionStorage.refID=='MOVE PATIENT'){
document.getElementById('goto').value="SELECT TARGET CLIENT";
}
else if (sessionStorage.refID=='TARGET1' || sessionStorage.refID=='TARGET2' || sessionStorage.refID=='TARGET3'){
document.getElementById('goto').value="FINISH MOVING";
}
else if (sessionStorage.refID=='TARGET4'){
document.getElementById('goto').value="FINISH MERGING";
}
else{
//document.getElementById('goto').value="GO TO "+sessionStorage.refID;
document.getElementById('goto').value=sessionStorage.refID;
}
opener.document.getElementById(patient).style.backgroundColor="<?php if ($psex=='M'){echo '#2FC3F5';} else {echo '#FF99CC';}; ?>";
opener.document.getElementById('coffee').value=patient;
}

function bodyonunload()
{
var patient = '<?php echo $_SESSION['patient']; ?>';
opener.document.getElementById(patient).style.backgroundColor="#FFFFFF";
}

function dutylog(patient,client){
var dlog=window.open('../RECEPTION/DUTY_LOG/ADD_EDIT_DUTY_LOG.php?patient='+patient+'&client='+client,'_blank');
dlog.focus();
}
</script>

<style type="text/css">
.customizedbutton{
font-family: Verdana;
	font-size: 20px;
	width: auto;
	height: 27px;
}

</style>
<style type="text/css">
<!--
.CustomizedButton1 {
	font-family: Verdana;
	font-size: 14px;
	width: 78px;
	height: 27px;
	margin:1px;
}

.CustomizedButton2 {
	font-family: Verdana;
	font-size: 14px;
	width: 60px;
	height: 27px;
	margin-left:1px;
	margin-right:1px;
	z-index:-2;
}

.CustomizedButton3 {
	font-family: Verdana;
	font-size:14px;
	width: 100px;
}
.CustomizedButton11 {	font-family: Verdana;
	font-size: 14px;
	width: 80px;
	height: 27px;
}
.CustomizedButton21 {	font-family: Verdana;
	font-size: 14px;
	width: 60px;
	height: 27px;
}
.style1 {font-weight: bold}

-->
</style>


<!-- InstanceEndEditable -->


<!--<input type="hidden" name="custlmonbal" value="<?php echo $custlmonbal ; ?>" /> -->
<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion1" -->
<div id="WindowBody">
<form name="photo_directory" action="" class="FormDisplay" method="post">
<table width="238" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="none">
<tr>
            <td height="183" align="center" valign="middle" class="Verdana14B">
            
            <img src='../../MSDVManager/<?php echo $uploadfile ; ?>' id="mug_shot" alt="patient picture" name="mugshot" width="170" height="170" id="mugshot" />
            <!--<input type="file" name="photo" id="photo" style="position:absolute; left:90px; z-index:2; opacity:0;"/> -->
           <!--<input type="button" name="PHOTO" class="CustomizedButton2" id="PHOTO" value="PHOTO" onclick="window.open('../../MSDVManager/ADD_SCAN.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&petid=<?php echo $_SESSION['patient']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/> -->
            </td>
      </tr>
          <tr>
            <td height="20" align="center">
            <img src="../IMAGES/e3 copy.jpg" alt="e" id="e" width="30" height="30" onclick="window.open('INSERT_PATIENT.php?patient=<?php echo $patient; ?>&client=<?php echo $client; ?>&species=<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>','_parent')" onmouseover="CursorToPointer(this.id)" title="Click to edit <?php echo mysqli_real_escape_string($mysqli_link, $petname); ?>"/>
            <img src="../IMAGES/v copy.jpg" alt="v" id="v" width="30" height="30" class="style1" onclick="window.open('PATIENT_DETAIL.php?patient=<?php echo $patient; ?>','_blank','width=660,height=540')"  onmouseover="CursorToPointer(this.id)" title="Click to open <?php echo mysqli_real_escape_string($mysqli_link, $petname); ?>'s detail"/>
            <img src="../IMAGES/h copy.jpg" alt="h" id="h" width="30" height="30" onclick="sessionStorage.setItem('prefID',sessionStorage.refID); sessionStorage.setItem('refID','REVIEW MED. HISTORYx'); openpage('<?php echo $patient; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $custname); ?>', '<?php echo $custphone; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $petname); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $desco); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $desct); ?>','<?php echo $custprevbal; ?>','<?php echo $custcurbal; ?>','<?php echo $custterm; ?>','<?php echo $psex; ?>','<?php echo $pdob; ?>','<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $address); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $city); ?>');"  onmouseover="CursorToPointer(this.id)" title="Click to review <?php echo mysqli_real_escape_string($mysqli_link, $petname); ?>'s medical history"/>
            <img src="../IMAGES/m.jpg" alt="m" id="m" width="30" height="30"  onmouseover="CursorToPointer(this.id)"/>
             <input type="button" name="PHOTO" class="CustomizedButton2" id="PHOTO" value="PHOTO" onclick="window.open('../../MSDVManager/ADD_SCAN.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&petid=<?php echo $_SESSION['patient']; ?>','_blank','status=no,scrolling=no,width=500,height=300')"/></td>
      </tr>
          <tr>
            <td height="26" align="center">
            <input name="check" type="hidden" value="1"/>
            <span id="advice" class="Verdana11Grey"></span>
            <input type="button" name="goto" id="goto" value="" class="customizedbutton" onclick="openpage('<?php echo $patient; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $custname); ?>', '<?php echo $custphone; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $petname); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $desco); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $desct); ?>','<?php echo $custprevbal; ?>','<?php echo $custcurbal; ?>','<?php echo $custterm; ?>','<?php echo $psex; ?>','<?php echo $pdob; ?>','<?php echo $row_PATIENT_CLIENT['PETTYPE']; ?>','<?php echo mysqli_real_escape_string($mysqli_link, $address); ?>','<?php echo mysqli_real_escape_string($mysqli_link, $city); ?>','<?php echo $custlmonbal ;?>');" <?php if ($row_PATIENT_CLIENT['LOCKED']=='1' && $_GET['refID']=='REG'){ echo "style='display:none;'";} ?>/>
            <span class="Verdana12BRed" title="File is locked for invoicing."><?php if ($row_PATIENT_CLIENT['LOCKED']=='1' && $_GET['refID']=='REG'){ echo "INVOICE OPEN ELSWHERE";} ?></span>
            </td>
      </tr>
    </table>
</form>
</div>
<form name="comingfromquickweight" method="post" action="" >
<input type="hidden" name="check2" value="1"  />
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
