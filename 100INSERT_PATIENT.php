<?php 
session_start();
require_once('../tryconnection.php');

if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

mysqli_select_db($tryconnection, $database_tryconnection);

$query_CLIENT = "SELECT * FROM ARCUSTO WHERE CUSTNO = '$client'";
$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

$query_BREED = "SELECT * FROM PETBREED WHERE PETBREED.BSPECIES = '$_GET[species]' ORDER BY PETBREED.BREED";
$BREED = mysqli_query($tryconnection, $query_BREED) or die(mysqli_error($mysqli_link));
$row_BREED = mysqli_fetch_assoc($BREED);


$query_COLOUR = "SELECT * FROM PETCOLOR WHERE PETCOLOR.CSPECIES = '$_GET[species]' ORDER BY PETCOLOR.COLOUR";
$COLOUR = mysqli_query($tryconnection, $query_COLOUR) or die(mysqli_error($mysqli_link));
$row_COLOUR = mysqli_fetch_assoc($COLOUR);

$query_CLIENT = "SELECT CUSTNO, TITLE, CONTACT, COMPANY FROM ARCUSTO WHERE CUSTNO = '$client'";
$CLIENT = mysqli_query($tryconnection, $query_CLIENT) or die(mysqli_error($mysqli_link));
$row_CLIENT = mysqli_fetch_assoc($CLIENT);

$query_PATIENT = "SELECT *, DATE_FORMAT(PDOB, '%m/%d/%Y') AS PDOB, DATE_FORMAT(PDEADATE,'%m/%d/%Y') AS PDEADATE, DATE_FORMAT(PFIRSTDATE,'%m/%d/%Y') AS PFIRSTDATE, DATE_FORMAT(PLASTDATE,'%m/%d/%Y') AS PLASTDATE FROM PETMAST WHERE PETID = '$patient'";
$PATIENT = mysqli_query($tryconnection, $query_PATIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT = mysqli_fetch_assoc($PATIENT);

$query_PETNO = "SELECT PETNO FROM PETMAST WHERE CUSTNO='$client' ORDER BY PETNO DESC LIMIT 1";
$PETNO = mysqli_query($tryconnection, $query_PETNO) or die(mysqli_error($mysqli_link));
$row_PETNO = mysqli_fetch_assoc($PETNO);

$species=$row_PATIENT['PETTYPE'];
$query_LIFESTYLE = "SELECT * FROM PETLIFESTYLE WHERE LSPECIES='$species' ORDER BY LIFESTYLE";
$LIFESTYLE = mysqli_query($tryconnection, $query_LIFESTYLE) or die(mysqli_error($mysqli_link));
$row_LIFESTYLE = mysqli_fetch_assoc($LIFESTYLE);
$totalRows_LIFESTYLE = mysqli_num_rows($LIFESTYLE);


//////////ADD THE ITEMS DELETED FOR ONLDLOG - SUCH AS FELHW, SOSP, MOVED ETC...
$pneuter=!empty($_POST['pneuter']) ? "1" : "0";
$pdead=!empty($_POST['pdead']) ? "1" : "0";
$pdeclaw=!empty($_POST['pdeclaw']) ? "1" : "0";
$pmagnet=!empty($_POST['pmagnet']) ? "1" : "0";
$pmoved=!empty($_POST['pmoved']) ? "1" : "0";
$p6exam=!empty($_POST['p6exam']) ? "1" : "0";
$psosp=!empty($_POST['psosp']) ? "1" : "0";
$pfelhw=!empty($_POST['pfelhw']) ? "1" : "0";
$pwell=!empty($_POST['pwell']) ? "1" : "0";


if (!empty($_POST['pdobage'])){
$pdobage=$_POST['pdobage'];
$pdob = strtotime(date('m/d/Y')." - ".$pdobage." ".$_POST['pdobperiod']);
$pdob = date('m/d/Y',$pdob);
}
else {
$pdob = $_POST['pdob'];
}

if (isset($_POST["save"]) && $_GET['patient']!="0") {
$updateSQL = "UPDATE PETMAST SET PETNAME='".mysqli_real_escape_string($mysqli_link, $_POST['petname'])."', PETTYPE='$_POST[pettype]', PETBREED='".mysqli_real_escape_string($mysqli_link, $_POST['petbreed'])."', PCOLOUR='".mysqli_real_escape_string($mysqli_link, $_POST['pcolour'])."', PSEX='$_POST[psex]', PNEUTER='$pneuter', PDOB=STR_TO_DATE('".$pdob."','%m/%d/%Y'), PDEAD='$pdead', PDEADATE=STR_TO_DATE('".$_POST['pdeadate']."','%m/%d/%Y'),  PRABTAG='$_POST[prabtag]', PXRAYFILE='$_POST[pxrayfile]', PDATA='".mysqli_real_escape_string($mysqli_link, $_POST['pdata'])."', PHERD='$_POST[pherd]', PSTAB='$_POST[pstab]', PMAGNET='$pmagnet', PTATNO='$_POST[ptatno]', PDECLAW='$pdeclaw', PFILENO='$_POST[pfileno]', PWEIGHT='$_POST[pweight]', PRABSER='$_POST[prabser]',  P6EXAM='$p6exam', PSOSP='$psosp', PMOVED='$pmoved', PFELHW='$pfelhw', PWELL='$pwell', PLIFE='".mysqli_real_escape_string($mysqli_link, $_POST['plife'])."', STICKIE='".mysqli_real_escape_string($mysqli_link, $_POST['stickie'])." ' WHERE PETID='$patient'";
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
$winback="history.go(-2);";
//$winback="document.location='../CLIENT/CLIENT_PATIENT_FILE.php';";
//header("Location: ../CLIENT/CLIENT_PATIENT_FILE.php");
}

else if (isset($_POST["save"]) && $_GET['patient'] == "0") {
$insertSQL = "INSERT INTO PETMAST (CUSTNO, PETNAME, PETNO, PETTYPE, PETBREED, PCOLOUR, PSEX, PNEUTER, PDOB, PDEAD, PDEADATE, PRABTAG, PXRAYFILE, PDATA, PHERD, PSTAB, PTATNO, PDECLAW, PMAGNET, PFILENO, PWEIGHT, PRABSER,  P6EXAM, PSOSP, PMOVED, PFELHW, PWELL, PLIFE, STICKIE, PRABDAT, POTHDAT, PLEUKDAT, POTHTWO, POTHTHR, POTHFOR, POTHFIV, POTHSIX, POTHSEV, POTH8,POTH9, POTH10, POTH11, POTH12, POTH13, POTH14, POTH15, PRABYEARS, POTHYEARS, PLEUKYEARS, POTH02YEARS, POTH03YEARS, POTH04YEARS, POTH05YEARS, POTH06YEARS, POTH07YEARS, POTH08YEARS, POTH09YEARS, POTH10YEARS, POTH11YEARS, POTH12YEARS, POTH13YEARS, POTH14YEARS, POTH15YEARS, PFIRSTDATE) 
VALUES ('$client', '".mysqli_real_escape_string($mysqli_link, $_POST['petname'])."', '$_POST[petno]', '$_POST[pettype]', '".mysqli_real_escape_string($mysqli_link, $_POST['petbreed'])."', '".mysqli_real_escape_string($mysqli_link, $_POST['pcolour'])."', '$_POST[psex]', '$pneuter', STR_TO_DATE('".$pdob."','%m/%d/%Y'), '$pdead', STR_TO_DATE('$_POST[pdeadate]','%m/%d/%Y'), '$_POST[prabtag]', '$_POST[pxrayfile]', '".mysqli_real_escape_string($mysqli_link, $_POST['pdata'])."', '$_POST[pherd]', '$_POST[pstab]', '$_POST[ptatno]', '$pdeclaw', '$pmagnet', '$_POST[pfileno]', '$_POST[pweight]', '$_POST[prabser]', '$p6exam', '$psosp','$pmoved', '$pfelhw', '$pwell', '".mysqli_real_escape_string($mysqli_link, $_POST['plife'])."', '".mysqli_real_escape_string($mysqli_link, $_POST['stickie'])."', STR_TO_DATE('$_POST[PRABDAT]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHDAT]','%m/%d/%Y'), STR_TO_DATE('$_POST[PLEUKDAT]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHTWO]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHTHR]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHFOR]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHFIV]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHSIX]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTHSEV]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH8]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH9]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH10]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH11]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH12]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH13]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH14]','%m/%d/%Y'), STR_TO_DATE('$_POST[POTH15]','%m/%d/%Y'), '$_POST[PRABYEARS]', '$_POST[POTHYEARS]', '$_POST[PLEUKYEARS]', '$_POST[POTH02YEARS]', '$_POST[POTH03YEARS]', '$_POST[POTH04YEARS]', '$_POST[POTH05YEARS]', '$_POST[POTH06YEARS]', '$_POST[POTH07YEARS]', '$_POST[POTH08YEARS]', '$_POST[POTH09YEARS]', '$_POST[POTH10YEARS]', '$_POST[POTH11YEARS]', '$_POST[POTH12YEARS]', '$_POST[POTH13YEARS]', '$_POST[POTH14YEARS]', '$_POST[POTH15YEARS]', DATE(NOW()))";
$Result1 = mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
$winback="history.go(-3);";
//header("Location: ../CLIENT/CLIENT_PATIENT_FILE.php");
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PATIENT RECORD</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.xsphere {
	width: 20px;
	height: 20px;
	margin: 0;
	padding: 0;
}
-->
</style>

<script type="text/javascript">

function bodyonload(){
<?php echo $winback; ?>
document.patients.petname.focus();	
document.getElementById('inuse').innerText=localStorage.xdatabase;

//var browser=navigator.userAgent;
//if (browser.match(/Firefox/i)){
//document.getElementById('pdata').cols=80;
//document.getElementById('pdata').rows=2;
//}
//else {
//document.getElementById('pdata').cols=70;
//document.getElementById('pdata').rows=3;
//}							

spec="<?php echo $_GET['species']; ?>";
switch (spec)
{
case "0":
document.getElementById('insertform').style.display="none";
break;

case "1": //CANINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="";
document.getElementById('pcolour').style.display="";
document.getElementById('sixmonths').style.display="";
document.getElementById('p6exam').style.display="";
document.getElementById('rabiestag').style.display="";
document.getElementById('prabtag').style.display="";
document.getElementById('rabiesserum').style.display="";
document.getElementById('prabser').style.display="";
document.getElementById('wellness').style.display="";
document.getElementById('pwell').style.display="";
document.getElementById('lifestyle').style.display="";
document.getElementById('koule').style.display="";
document.getElementById('editlifestyle').style.display="";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="";
document.getElementById('sosp').style.display="";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="none";
document.getElementById('description').style.display="none";
document.getElementById('idno').style.display="";
document.getElementById('Tattoo').style.display="none";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="none";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="none";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="none";
document.getElementById('equine').style.display="none";
document.getElementById('bovine').style.display="none";
document.getElementById('caprine').style.display="none";
document.getElementById('porcine').style.display="none";
document.getElementById('avian').style.display="none";
document.getElementById('other').style.display="none";
break;

case "2": //FELINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="";
document.getElementById('pcolour').style.display="";
document.getElementById('sixmonths').style.display="";
document.getElementById('p6exam').style.display="";
document.getElementById('rabiestag').style.display="";
document.getElementById('prabtag').style.display="";
document.getElementById('rabiesserum').style.display="";
document.getElementById('prabser').style.display="";
document.getElementById('wellness').style.display="";
document.getElementById('pwell').style.display="";
document.getElementById('lifestyle').style.display="";
document.getElementById('koule').style.display="";
document.getElementById('editlifestyle').style.display="";
document.getElementById('dec').style.display="";
document.getElementById('pdeclaw').style.display="";
document.getElementById('psosp').style.display="";
document.getElementById('sosp').style.display="";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="none";
document.getElementById('description').style.display="none";
document.getElementById('idno').style.display="";
document.getElementById('Tattoo').style.display="none";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="none";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="none";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="none";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="none";
document.getElementById('bovine').style.display="none";
document.getElementById('caprine').style.display="none";
document.getElementById('porcine').style.display="none";
document.getElementById('avian').style.display="none";
document.getElementById('other').style.display="none";
break;

case "3": //EQUINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="none";
document.getElementById('pcolour').style.display="none";
document.getElementById('sixmonths').style.display="none";
document.getElementById('p6exam').style.display="none";
document.getElementById('rabiestag').style.display="none";
document.getElementById('prabtag').style.display="none";
document.getElementById('rabiesserum').style.display="none";
document.getElementById('prabser').style.display="none";
document.getElementById('wellness').style.display="none";
document.getElementById('pwell').style.display="none";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="none";
document.getElementById('sosp').style.display="none";
document.getElementById('neutered').style.display="none";
document.getElementById('pneuter').style.display="none";
document.getElementById('psex').style.display="";
document.getElementById('gelded').style.display="";
document.getElementById('koule1').style.display="<?php if($patient=='0'){echo "none";} ?>";
document.getElementById('description').style.display="<?php if($patient=='0'){echo "none";} ?>";
document.getElementById('idno').style.display="none";
document.getElementById('Tattoo').style.display="";
document.getElementById('pofeilabel').style.display="";
document.getElementById('pofei').style.display="";
document.getElementById('stable').style.display="";
document.getElementById('pstab').style.display="";
document.getElementById('hhprog').style.display="";
document.getElementById('pherd').style.display="";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="none";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";

break;

case "4":  //BOVINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="none";
document.getElementById('pcolour').style.display="none";
document.getElementById('sixmonths').style.display="none";
document.getElementById('p6exam').style.display="none";
document.getElementById('rabiestag').style.display="none";
document.getElementById('prabtag').style.display="none";
document.getElementById('rabiesserum').style.display="none";
document.getElementById('prabser').style.display="none";
document.getElementById('wellness').style.display="none";
document.getElementById('pwell').style.display="none";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="none";
document.getElementById('sosp').style.display="none";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="";
document.getElementById('description').style.display="";
document.getElementById('idno').style.display="";
document.getElementById('Tattoo').style.display="none";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="";
document.getElementById('hhprog').style.display="";
document.getElementById('pherd').style.display="";
document.getElementById('pmagnet').style.display="";
document.getElementById('magnet').style.display="";
document.getElementById('eartag').style.display="";
document.getElementById('location').style.display="";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="none";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";
break;

case "5":  //CAPRINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="none";
document.getElementById('pcolour').style.display="none";
document.getElementById('sixmonths').style.display="none";
document.getElementById('p6exam').style.display="none";
document.getElementById('rabiestag').style.display="none";
document.getElementById('prabtag').style.display="none";
document.getElementById('rabiesserum').style.display="none";
document.getElementById('prabser').style.display="none";
document.getElementById('wellness').style.display="none";
document.getElementById('pwell').style.display="none";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="none";
document.getElementById('sosp').style.display="none";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="none";
document.getElementById('description').style.display="none";
document.getElementById('idno').style.display="none";
document.getElementById('Tattoo').style.display="";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";
break;

case "6":  //PORCINE
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="none";
document.getElementById('pcolour').style.display="none";
document.getElementById('sixmonths').style.display="none";
document.getElementById('p6exam').style.display="none";
document.getElementById('rabiestag').style.display="none";
document.getElementById('prabtag').style.display="none";
document.getElementById('rabiesserum').style.display="none";
document.getElementById('prabser').style.display="none";
document.getElementById('wellness').style.display="none";
document.getElementById('pwell').style.display="none";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="";
document.getElementById('sosp').style.display="none";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="";
document.getElementById('description').style.display="";
document.getElementById('idno').style.display="none";
document.getElementById('Tattoo').style.display="";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="";
document.getElementById('opip').style.display="";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";
break;

case "7":  //AVIAN
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="none";
document.getElementById('pcolour').style.display="none";
document.getElementById('sixmonths').style.display="none";
document.getElementById('p6exam').style.display="none";
document.getElementById('rabiestag').style.display="";
document.getElementById('prabtag').style.display="";
document.getElementById('rabiesserum').style.display="none";
document.getElementById('prabser').style.display="none";
document.getElementById('wellness').style.display="none";
document.getElementById('pwell').style.display="none";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="none";
document.getElementById('sosp').style.display="none";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="";
document.getElementById('description').style.display="";
document.getElementById('idno').style.display="none";
document.getElementById('Tattoo').style.display="";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="none";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="none";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";
break;

case "8":  //OTHER
//document.getElementById('dropmsg2').style.display="";
document.getElementById('colour').style.display="";
document.getElementById('pcolour').style.display="";
document.getElementById('sixmonths').style.display="";
document.getElementById('p6exam').style.display="";
document.getElementById('rabiestag').style.display="";
document.getElementById('prabtag').style.display="";
document.getElementById('rabiesserum').style.display="";
document.getElementById('prabser').style.display="";
document.getElementById('wellness').style.display="";
document.getElementById('pwell').style.display="";
document.getElementById('lifestyle').style.display="none";
document.getElementById('koule').style.display="none";
document.getElementById('editlifestyle').style.display="none";
document.getElementById('dec').style.display="none";
document.getElementById('pdeclaw').style.display="none";
document.getElementById('psosp').style.display="";
document.getElementById('sosp').style.display="";
document.getElementById('neutered').style.display="";
document.getElementById('pneuter').style.display="";
document.getElementById('psex').style.display="none";
document.getElementById('gelded').style.display="none";
document.getElementById('koule1').style.display="none";
document.getElementById('description').style.display="none";
document.getElementById('idno').style.display="";
document.getElementById('Tattoo').style.display="none";
document.getElementById('pofeilabel').style.display="none";
document.getElementById('pofei').style.display="none";
document.getElementById('stable').style.display="none";
document.getElementById('pstab').style.display="none";
document.getElementById('hhprog').style.display="none";
document.getElementById('pherd').style.display="none";
document.getElementById('pmagnet').style.display="none";
document.getElementById('magnet').style.display="none";
document.getElementById('eartag').style.display="none";
document.getElementById('location').style.display="none";
document.getElementById('opip').style.display="none";
document.getElementById('fileno').style.display="";
document.getElementById('pfileno').style.display="";
document.getElementById('canine').style.display="";
document.getElementById('feline').style.display="";
document.getElementById('equine').style.display="";
document.getElementById('bovine').style.display="";
document.getElementById('caprine').style.display="";
document.getElementById('porcine').style.display="";
document.getElementById('avian').style.display="";
document.getElementById('other').style.display="";
break;

}
}

function species()
{
var specie=document.getElementById('pettype').value;
self.location='INSERT_PATIENT.php?client=<?php echo $row_CLIENT['CUSTNO']; ?>&species=' + specie + '&patient=<?php echo $_GET['patient'] ?>';
}

function checkdates()
{
valid = true;
var dob=document.getElementById('pdob').value;
var dobconverted=new Date(dob);
var deadate=document.getElementById('pdeadate').value;
var deadateconverted=new Date(deadate);
var today=new Date;
if (dobconverted>today){
alert ('You have entered an invalid date of birth.');
valid = false;
}
else if (deadateconverted>today){
alert ('You have entered an invalid date of demise.');
valid = false;
}
return valid;
}

</script>


<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../ASSETS/calendar.js"></script>
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onclick="window.open('/'+localStorage.xdatabase+'/INDEX.php','_self');" onmouseover="CursorToPointer(this.id)" title="Home">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="">About DV Manager</span></a></li>
                <li><a onclick="utilities();">Utilities</a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self'/'+localStorage.xdatabase+'/INVOICE/CASUAL_SALE_INVOICING/STAFF.php?refID=SCI)"><span class="">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick="nav0();">Regular Invoicing</a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick="nav11();">Estimate</a></li>
                <li><a href="#" onclick=""><span class="">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick="suminvoices()"><span class="">Summary Invoices</span></a></li>
                <li><a href="#" onclick="cashreceipts()"><span class="">Cash Receipts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Cancel Invoices</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/INVOICE/COMMENTS/COMMENTS_LIST.php?path=DIRECTORY','_blank','width=733,height=553,toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no')">Comments</a></li>
                <li><a href="#" onclick="tffdirectory()">Treatment and Fee File</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Worksheet File</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick="invreports();"><span class="">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')"><span class="">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick="reception();">Patient Registration</a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/RECEPTION/USING_REG_FILE.php','_blank','width=550,height=535')">Using Reception File</a></li>
                <li><a href="#" onclick="nav2();"><span class="hidden"></span>Examination Sheets</a></li>
                <li><a href="#" onclick="gexamsheets()"><span class="">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick="nav3();">Duty Log</a></li>
                <li><a href="#" onclick="staffsiso()">Staff Sign In &amp; Out</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="nav4();">Processing Menu</a> </li>
                <li><a href="#" onclick="nav5();">Review Patient Medical History</a></li>
                <li><a href="#" onclick="nav6();">Enter New Medical History</a></li>
                <li><a href="#" onclick="nav7();">Enter Patient Lab Results</a></li>
                <li><a href="#" onclick=""window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=ENTER SURG. TEMPLATES','_self')><span class="">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/CLIENT/CLIENT_SEARCH_SCREEN.php?refID=CREATE NEW CLIENT','_self','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no');">Create New Client</a></li>
                <li><a href="#" onclick="movepatient();">Move Patient to a New Client</a></li>
                <li><a href="#" onclick="searchpatient()">Rabies Tags</a></li>
                <li><a href="#" onclick="searchpatient()">Tattoo Numbers</a></li>
                <li><a href="#" onclick="nav8();"><span class="">Certificates</span></a></li>
                <li><a href="#" onclick="nav9();"><span class="">Clinical Logs</span></a></li>
                <li><a href="#" onclick="nav10();"><span class="">Patient Categorization</span></a></li>
                <li><a href="#" onclick="">Laboratory Templates</a></li>
                <li><a href="#" onclick="nav1();"><span class="">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""accreports()>Accounting Reports</a></li>
                <li><a href="#" onclick="inventorydir();" id="inventory" name="inventory">Inventory</a></li>
                <li><a href="#" onclick="" id="busstatreport" name="busstatreport"><span class="">Business Status Report</span></a></li>
                <li><a href="#" onclick="" id="hospstatistics" name="hospstatistics"><span class="">Hospital Statistics</span></a></li>
                <li><a href="#" onclick="" id="monthend" name="monthend"><span class="">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick="window.open('','_self')" ><span class="">Recalls and Searches</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Handouts</span></a></li>
                <li><a href="#" onclick="window.open('','_self')MAILING/MAILING_LOG/MAILING_LOG.php?refID=">Mailing Log</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick="window.open('/'+localStorage.xdatabase+'/MAILING/REFERRALS/REFERRALS_SEARCH_SCREEN.php?refID=1','_blank','width=567,height=473')">Referring Clinics and Doctors</a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Referral Adjustments</span></a></li>
                <li><a href="#" onclick="window.open('','_self')"><span class="">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->
<form action="" name="patients" id="patients" method="POST" onsubmit="return checkdates();">
<input type="hidden" name="petno" value="<?php if ($totalRows_PETNO=="0"){echo "1";} else {echo $row_PETNO['PETNO']+1;} ?>" />
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="rows">
	<tr height="50">
		<td colspan="2" align="center" valign="middle" class="Verdana14B"><?php if ($_GET['patient']=="0"){echo "ADD NEW";} else {echo "EDIT";} ?> PATIENT <?php ?></td>
	</tr>
	<tr height="40" bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else if ($row_PATIENT['PSEX']=='F') {echo '#F9DEE9';} ?>">
      	<td colspan="2" align="center" valign="middle" class="Verdana12">
      	<span class="Verdana12"> <?php echo $row_CLIENT['TITLE']; ?> <?php echo $row_CLIENT['CONTACT']; ?> <?php echo $row_CLIENT['COMPANY']; ?>'s patient # <?php if ($patient=="0"){if ($totalRows_PETNO=="0"){echo "1";} else {echo $row_PETNO['PETNO']+1;}} else {echo $row_PATIENT['PETNO'];} ?></span>
            <select name="pettype" id="pettype" onchange="species()">
              <option value="<?php echo $_GET['species']; ?>" selected="selected"><?php if ($_GET['species']=='0'){echo "PLEASE SELECT SPECIES TYPE";} else if ($_GET['species']=='1'){echo "Canine";} else if ($_GET['species']=='2'){echo "Feline";} else if ($_GET['species']=='3'){echo "Equine";} else if ($_GET['species']=='4'){echo "Bovine";} else if ($_GET['species']=='5'){echo "Caprine";} else if ($_GET['species']=='6'){echo "Porcine";} else if ($_GET['species']=='7'){echo "Avian";} else if ($_GET['species']=='8'){echo "Other";}; ?></option>
              <option value="1">Canine</option>
              <option value="2">Feline</option>
              <option value="3">Equine</option>
              <option value="4">Bovine</option>
              <option value="5">Caprine</option>
              <option value="6">Porcine</option>
              <option value="7">Avian</option>
              <option value="8">Other</option>
            </select>         </td>
	</tr>
	<tr>
		<td height="427">
		  <div id="insertform"> 
			<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="all">
      			<tr>
        			<!-- LEFT SIDE - NAME - 6 MTH EXAM -->
                    <td width="50%">
          				<table width="100%" border="0" cellpadding="0" cellspacing="0">
       				  <tr height="30">
              					<td width="69" align="left" valign="middle" class="RequiredItems">Name</td>
			    				<td colspan="12" align="left" valign="middle"><input name="petname" type="text" class="Input" id="petnameF" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PETNAME']; ?>" size="20" maxlength="25"/></td>
        				  </tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="Labels">Breed</td>
			    				<td colspan="12" align="left" valign="middle">
								<input name="petbreed" type="text" class="Input" id="petbreed" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PETBREED']; ?>" size="10" maxlength="25"/><select name="xpetbreed" onchange="document.patients.petbreed.value=document.patients.xpetbreed.value">
								 <option <?php if ($row_PATIENT['PETID']!=='0'){echo "value='".$row_PATIENT['PETBREED']."' selected='selected'";}?> ><?php echo $row_PATIENT['PETBREED']; ?></option>
								 <?php do { ?>
								 <option value="<?php echo $row_BREED['BREED']; ?>"><?php echo $row_BREED['BREED']; ?></option>
								 <?php } while ($row_BREED = mysqli_fetch_assoc($BREED)); ?>
								</select></td>
           				  </tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="Labels">Birthdate</td>
			    				<td colspan="8" align="left" valign="middle" class="Labels2"><input name="pdob" type="text" class="Input" id="pdob" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id);" value="<?php if($row_PATIENT['PDOB']!="00/00/0000"){ echo $row_PATIENT['PDOB'];} else {echo "";} ?>" size="10" maxlength="10" title="MM/DD/YYYY" onclick="ds_sh(this<?php if ($row_PATIENT['PDOB']!="00/00/0000" && $patient!='0') {echo ", '".substr($row_PATIENT['PDOB'],0,2)."','".substr($row_PATIENT['PDOB'],3,2)."','".substr($row_PATIENT['PDOB'],6,4)."'";} ?>);"/> 
			    				  &nbsp;or Age: 
			    				    <input name="pdobage" type="text" class="Inputright" id="pdobage" size="2" maxlength="2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id);" />
                                <select name="pdobperiod">
                                <option value="years">Years</option>
                                <option value="months">Months</option>
                                <option value="weeks">Weeks</option>
                                </select>                                                				</td>
               				</tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="Labels">Sex</td>
		      					<td width="27" align="right" valign="middle"><input name="psex" type="radio" value="F" <?php if ($row_PATIENT['PSEX']=='F'){echo "CHECKED";}; ?>/></td>
			    				<td width="20" align="left" valign="middle" class="Labels">F</td>
	          					<td width="24" align="right" valign="middle"><input name="psex" type="radio" value="M" <?php if ($row_PATIENT['PSEX']=='M'){echo "CHECKED";}; ?>/></td>
	          					<td width="23" align="left" valign="middle" class="Labels">M</td>
			    				<td width="24" align="right" valign="middle"><input name="psex" type="radio" value="G" id="psex" <?php if ($row_PATIENT['PSEX']=='G'){echo "CHECKED";}; ?>/></td>
			    				<td align="left" valign="middle" class="Labels2"><span id="gelded">Gelded</span></td>
	            				<td align="right" valign="middle"><input type="checkbox" name="pneuter" id="pneuter" <?php if ($row_PATIENT['PNEUTER']=='1'){echo "CHECKED";}; ?>/></td>
	            				<td width="86" align="left" valign="middle" class="Labels"><span id="neutered">N/S</span></td>
		    				</tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="RequiredItems">Weight</td>
	      					  <td colspan="3" align="left" valign="middle"><input name="pweight" type="text" class="Inputright" id="pweight" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PWEIGHT']; ?>" size="8" maxlength="8"/></td>
		      					<td colspan="2" align="left" valign="middle" class="Labels2">
                                <script type="text/javascript">
								document.write(localStorage.weightunit);
                                </script>                                </td>
		      					<td width="61" align="left" valign="middle" class="Labels">&nbsp;</td>
		      					<td width="27" align="right" valign="middle"><input type="checkbox" name="pdeclaw" id="pdeclaw" <?php if ($row_PATIENT['PDECLAW']=='1'){echo "CHECKED";}; ?>/>
	      					    <input type="checkbox" name="pmagnet" id="pmagnet" <?php if ($row_PATIENT['PMAGNET']=='1'){echo "CHECKED";}; ?>/></td>
		      					<td align="left" valign="middle" class="Labels">
                                <span id="dec">Declawed</span>
                                <span id="magnet">Magnet</span></td>
	        				</tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="Labels">
                                <span id="colour">Colour</span></td>
		      					<td colspan="10" align="left" valign="middle">
             					<input name="pcolour" type="text" class="Input" id="pcolour" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PCOLOUR']; ?>" size="10" maxlength="25"/><select name="xpcolour" onchange="document.patients.pcolour.value=document.patients.xpcolour.value" />
								 <option <?php if ($row_PATIENT['PETID']!=='0'){echo "value='".$row_PATIENT['PCOLOUR']."' selected='selected'";}?> ><?php echo $row_PATIENT['PCOLOUR']; ?></option>
								 <?php do { ?>
								 <option value="<?php echo $row_COLOUR['COLOUR']; ?>"><?php echo $row_COLOUR['COLOUR']; ?></option>
								 <?php } while ($row_COLOUR = mysqli_fetch_assoc($COLOUR)); ?>
								</select>			  					</td>
	      					</tr>
            				<tr height="30">
              					<td width="69" align="left" valign="middle" class="Labels">&nbsp;</td>
		      					<td align="right" valign="middle" bgcolor="#FFFFFF"><input type="checkbox" name="p6exam" id="p6exam" <?php if ($row_PATIENT['P6EXAM']=='1'){echo "CHECKED";}; ?>/><img src="../IMAGES/koule.JPG" alt="koule" width="18" height="18" id="koule1" onmouseover="CursorToPointer(this.id)" onclick="window.open('ADDITIONAL DATA NEW PATIENT/PATIENT_DESCRIPTION.php?patient=<?php echo $patient; ?>','_blank','height=598,width=538')" class="koule"/></td>
			    				<td colspan="10" align="left" valign="middle" bgcolor="#FFFFFF" class="Labels">
                                <span class="Verdana11BPink" id="sixmonths">6 Mth Exam</span><span id="description" onmouseover="CursorToPointer(this.id)" onclick="window.open('ADDITIONAL DATA NEW PATIENT/PATIENT_DESCRIPTION.php?patient=<?php echo $patient; ?>','_blank','height=598,width=538')">EDIT DESCRIPTION</span><?php if($patient=='0' && $_GET['species']=='3'){echo "<span id='eqdesc' class='Verdana11Grey' title='To edit description, please save the patient first.'>EDIT DESCRIPTION</span>";} ?></td>
                            </tr>
   				  </table>					</td>
	  				<!-- RIGHT SIDE - FILE NO. - DECEASED -->
                    <td width="50%">
		    			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">
                                <span id="fileno">File No.</span>
                                <span id="eartag">Ear Tag</span></td>
			      				<td colspan="4" align="left" valign="middle"><input name="pfileno" type="text" class="Input" id="pfileno" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PFILENO']; ?>" size="6" maxlength="12"/></td>
		      				</tr>
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">
                                <span id="rabiestag">Rabies Tag</span>
                                <span id="stable">Stable</span>
                                <span id="location">Location</span></td>
			      				<td colspan="4" align="left" valign="middle">
                                <input name="prabtag" type="text" class="Input" id="prabtag" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PRABTAG']; ?>" size="12" maxlength="14"/>
                                <input name="pstab" type="text" class="Input" id="pstab" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PSTAB']; ?>" size="6" maxlength="12"/></td>
		      				</tr>
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">
                                <span id="rabiesserum">Rabies Serum</span>
                                <span id="pofeilabel">OFEI</span></td>
			      				<td colspan="4" align="left" valign="middle">
                                <input name="prabser" type="text" class="Input" id="prabser" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PRABSER']; ?>" size="20" maxlength="25"/>
			      				<input name="pofei" type="text" class="Input" id="pofei" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['POFEI']; ?>" size="20" maxlength="20"/></td>
		      				</tr>
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">
                                <span id="idno">ID#</span>
                                <span id="Tattoo">Tattoo #</span></td>
			      				<td colspan="5" align="left" valign="middle"><input name="ptatno" type="text" class="Input" id="ptatno" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PTATNO']; ?>" size="20" maxlength="25"/></td>
		      				</tr>
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">X Ray File</td>
			      				<td colspan="2" align="left" valign="middle"><input name="pxrayfile" type="text" class="Input" id="pxrayfile" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PXRAYFILE']; ?>" size="6" maxlength="12"/></td>
			      				<td width="48" align="right" valign="middle" class="Labels">
                                <input type="checkbox" name="pwell" id="pwell" <?php if ($row_PATIENT['PWELL']=='1'){echo "CHECKED";}; ?>/>
                                <span id="hhprog">HHProg</span></td>
			      				<td align="left" valign="middle" class="Labels">
                                <span id="wellness">Wellness</span>
			        			<input name="pherd" type="text" class="Input" id="pherd" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PHERD']; ?>" size="2" maxlength="12"/></td>
		      				</tr>
			    			<tr height="30">
			      				<td width="54" align="right" valign="middle"><input type="checkbox" name="pmoved" value="checkbox" <?php if ($row_PATIENT['PMOVED']=='1'){echo "CHECKED";}; ?>/></td>
			      				<td width="62" align="left" valign="middle" class="Labels"><span>Moved</span></td>
			      				<td width="33" align="right" valign="middle"><input type="checkbox" name="pfelhw" value="checkbox" <?php if ($row_PATIENT['PFELHW']=='1'){echo "CHECKED";}; ?>/></td>
			      				<td width="60" align="left" valign="middle" class="Labels"><span>Insured</span></td>
			      				<td width="48" align="right" valign="middle" class="Labels2"><input type="checkbox" name="psosp" id="psosp" <?php if ($row_PATIENT['PSOSP']=='1'){echo "CHECKED";}; ?>/></td>
			      				<td align="left" valign="middle" class="Labels">
                                <span id="sosp">SOSP</span>
                                <span id="opip">OPIP</span></td>
		      				</tr>
			    			<tr height="30">
			      				<td colspan="2" align="left" valign="middle" class="Labels">Date Deceased </td>
			      				<td colspan="2" align="left" valign="middle"><input name="pdeadate" type="text" class="Input" id="pdeadate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php if($row_PATIENT['PDEADATE']!="00/00/0000"){ echo $row_PATIENT['PDEADATE'];} else {echo "";} ?>" size="10" maxlength="10" onclick="ds_sh(this);" title="MM/DD/YYYY"/></td>
		          				<td align="right" valign="middle" class="Labels2"><input type="checkbox" name="pdead" id="pdead" <?php if ($row_PATIENT['PDEAD']=='1'){echo "CHECKED";}; ?>/></td>
		          				<td align="left" valign="middle" class="Labels2">Deceased</td>
			    			</tr>
        				</table>					</td>
      			</tr>
	  			<tr height="217" valign="top">
	  				<!-- COMMENT + LIFESTYLE -->
                    <td colspan="2">
	 					<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  		  <tr>
	  <td width="75" height="31" align="left" valign="middle" class="Labels">First</td>
      <td height="31" colspan="3" align="left" valign="middle" class="style3"><input name="pfirstdate" type="text" class="Input" id="pfirstdate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PFIRSTDATE']; ?>" size="10" maxlength="10"/></td>
      <td width="429" height="31" align="center" valign="middle" class="Labels">&lt;- Appointments -&gt; </td>
      <td width="80" height="31" align="right" valign="middle" class="Labels"><input name="plastdate" type="text" class="Input" id="plastdate" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo $row_PATIENT['PLASTDATE']; ?>" size="10" maxlength="10"/></td>
      <td width="66" height="31" align="right" valign="middle" class="Labels">Last</td>
      </tr>
                            <tr>
                              <td height="33" align="left" valign="middle" class="Labels" title="Sticky note will be displayed as an alert in the Client file screen"><span class="alerttext10">&nbsp;Sticky Note&nbsp;</span></td>
                              <td colspan="7" align="left" valign="middle"><textarea name="stickie" cols="70" rows="1" class="commentarea" id="stickie"><?php echo $row_PATIENT['STICKIE']; ?></textarea></td>
                            </tr>
                          <tr>
      <td height="58" align="left" valign="middle" class="Labels">Comment</td>
      <td colspan="7" align="left" valign="middle"><textarea name="pdata" cols="70" rows="3" wrap="virtual" id="pdata" class="commentarea"><?php echo $row_PATIENT['PDATA']; ?></textarea></td>
      </tr>
      <tr>
      <td height="31" align="left" valign="middle" class="Labels"><span id="lifestyle">Lifestyle</span></td>
      <td width="38" height="31" align="left" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td width="35" align="right" valign="middle"><img src="../IMAGES/koule.JPG" alt="koule" id="koule" name="koule" width="18" height="18" class="koule" onclick="window.open('ADDITIONAL DATA NEW PATIENT/LIFESTYLE.php?species=<?php echo $_GET['species']; ?>&patient=<?php echo $_GET['patient']; ?>','_blank')" onmouseover="CursorToPointer(this.id)"/></td>
      <td height="31" colspan="4" align="left" valign="middle" class="Labels"><span id="editlifestyle" onclick="window.open('ADDITIONAL DATA NEW PATIENT/LIFESTYLE.php?species=<?php echo $_GET['species']; ?>&patient=<?php echo $_GET['patient']; ?>','_blank','width=340,height=530')" onmouseover="CursorToPointer(this.id)">EDIT
        LIFESTYLE</span>
				<?php 
				$alife=array();
                do {
                $life = explode(",",$row_PATIENT['PLIFE']);
                if (in_array($row_LIFESTYLE['LIFESTYLEID'], $life))
                {$alife[]=$row_LIFESTYLE['LIFESTYLE'];}
                }
                while ($row_LIFESTYLE = mysqli_fetch_assoc($LIFESTYLE));
				echo implode(', ', $alife);		 
				?>        </td>
      <td width="1" height="31"></td>
      </tr>
		<tr>
        <td colspan="8" align="center" valign="bottom" class="RequiredItems">*Items
          in blue must be completed</td>
      </tr>
		</table>				  
        </td>
	  			</tr>
  			</table>
		  </div>		</td>
	</tr>
    
    
        <tr>
        <td colspan="2" class="ButtonsTable" align="center">
        <input name="save" type="submit" class="button" id="save" value="SAVE" title="Click to save" />
        <input class="button" type="button" name="dates" value="DATES" onclick="window.open('ADDITIONAL DATA NEW PATIENT/DATES.php?species=<?php echo $_GET['species']; ?>','_blank','width=350,height=480');" />
        <input class="button" type="button" name="flags" value="FLAGS" disabled="disabled" />
        <input name="cancel" type="reset" class="button" id="cancel" value="CANCEL" onclick="history.back()" />
      </td>
     </tr>      
</table>
<input type="hidden" name="plife" value="<?php echo $row_PATIENT['PLIFE']; ?>" />

<input type="hidden" name="PRABDAT" value="" />
<input type="hidden" name="POTHDAT" value="" />
<input type="hidden" name="PLEUKDAT" value="" />
<input type="hidden" name="POTHTWO" value="" />
<input type="hidden" name="POTHTHR" value="" />
<input type="hidden" name="POTHFOR" value="" />
<input type="hidden" name="POTHFIV" value="" />
<input type="hidden" name="POTHSIX" value="" />
<input type="hidden" name="POTHSEV" value="" />
<input type="hidden" name="POTH8" value="" />
<input type="hidden" name="POTH9" value="" />
<input type="hidden" name="POTH10" value="" />
<input type="hidden" name="POTH11" value="" />
<input type="hidden" name="POTH12" value="" />
<input type="hidden" name="POTH13" value="" />
<input type="hidden" name="POTH14" value="" />
<input type="hidden" name="POTH15" value="" />

<input type="hidden" name="PRABYEARS" value="" />
<input type="hidden" name="POTHYEARS" value="" />
<input type="hidden" name="PLEUKYEARS" value="" />
<input type="hidden" name="POTH02YEARS" value="" />
<input type="hidden" name="POTH03YEARS" value="" />
<input type="hidden" name="POTH04YEARS" value="" />
<input type="hidden" name="POTH05YEARS" value="" />
<input type="hidden" name="POTH06YEARS" value="" />
<input type="hidden" name="POTH07YEARS" value="" />
<input type="hidden" name="POTH08YEARS" value="" />
<input type="hidden" name="POTH09YEARS" value="" />
<input type="hidden" name="POTH10YEARS" value="" />
<input type="hidden" name="POTH11YEARS" value="" />
<input type="hidden" name="POTH12YEARS" value="" />
<input type="hidden" name="POTH13YEARS" value="" />
<input type="hidden" name="POTH14YEARS" value="" />
<input type="hidden" name="POTH15YEARS" value="" />

</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
