<?php 
session_start();
require_once('../tryconnection.php');
include("../../ASSETS/age.php");

unset($_SESSION['display']);
unset($_SESSION['number']);
unset($_SESSION['company']);
unset($_SESSION['start']);

$refID = $_GET['refID'];
//$pettype = $_GET['pettype'];
//$petname = $_GET['petname'];
//$psex = $_GET['psex'];
$llocalid = $_GET['llocalid'];

if (isset($row_PATIENTS['PETID'])){
$patient=$row_PATIENTS['PETID'];
}
else if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
else if (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}

if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}


mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);

$query_WEIGHTUNIT = "SELECT WEIGHTUNIT FROM CRITDATA LIMIT 1";
$WEIGHTUNIT = mysqli_query($tryconnection, $query_WEIGHTUNIT) or die(mysqli_error($mysqli_link));
$row_WEIGHTUNIT = mysqli_fetch_assoc($WEIGHTUNIT);



////////////////////---------VALUES-----------//////////////////////
$custname=$row_PATIENT_CLIENT['TITLE'].' '.$row_PATIENT_CLIENT['CONTACT'].' '.$row_PATIENT_CLIENT['COMPANY'];
//////
$custphone='('.$row_PATIENT_CLIENT['CAREA'].') '.$row_PATIENT_CLIENT['PHONE'].', ('.$row_PATIENT_CLIENT['CAREA2'].') '.$row_PATIENT_CLIENT['PHONE2'].', ('.$row_PATIENT_CLIENT['CAREA3'].') '.$row_PATIENT_CLIENT['PHONE3'].', ('.$row_PATIENT_CLIENT['CAREA4'].') '.$row_PATIENT_CLIENT['PHONE4'];
//////////
$address=$row_PATIENT_CLIENT['ADDRESS1'];
$address2=$row_PATIENT_CLIENT['ADDRESS2'] ;
$city=($row_PATIENT_CLIENT['CITY'].", ".$row_PATIENT_CLIENT['STATE'].", ".$row_PATIENT_CLIENT['ZIP']);
//////////
if ($row_PATIENT_CLIENT['TERMS']=='1'){$custterm = "NORMAL CREDIT";} 
else if ($row_PATIENT_CLIENT['TERMS']=='2'){$custterm = "CASH ONLY";} 
else if ($row_PATIENT_CLIENT['TERMS']=='3'){$custterm = "NO CREDIT";} 
else if ($row_PATIENT_CLIENT['TERMS']=='4'){$custterm = "COLLECTION";} 
else if ($row_PATIENT_CLIENT['TERMS']=='5'){$custterm = "POST DATED CHEQUE";} 
else if ($row_PATIENT_CLIENT['TERMS']=='6'){$custterm = "ACCEPT CHEQUE";}
//////
$custprevbal=$row_PATIENT_CLIENT['BALANCE'];
//////
$custcurbal=$row_PATIENT_CLIENT['CREDIT'];
$custlmonbal=$row_PATIENT_CLIENT['LASTMON'] ;
//////
$petname=$row_PATIENT_CLIENT['PETNAME'];
$_SESSION['petname']=mysqli_real_escape_string($mysqli_link, $petname);
//////
if ($row_PATIENT_CLIENT['PETTYPE']=='1'){$pettype = "Canine";} 
else if ($row_PATIENT_CLIENT['PETTYPE']=='2'){$pettype = "Feline";} 
else if ($row_PATIENT_CLIENT['PETTYPE']=='3'){$pettype = "Equine";}
else if ($row_PATIENT_CLIENT['PETTYPE']=='4'){$pettype = "Bovine";}
else if ($row_PATIENT_CLIENT['PETTYPE']=='5'){$pettype = "Caprine";}
else if ($row_PATIENT_CLIENT['PETTYPE']=='6'){$pettype = "Porcine";}
else if ($row_PATIENT_CLIENT['PETTYPE']=='7'){$pettype = "Avian";}
else if ($row_PATIENT_CLIENT['PETTYPE']=='8'){$pettype = "Other";}
$desco=$pettype.', '.$row_PATIENT_CLIENT['PETBREED'];
//////

//$age=agecalculation($tryconnection, $row_PATIENT_CLIENT['PDOB']);."(".$age.")"

$psex=$row_PATIENT_CLIENT['PSEX'];
$pdob=$row_PATIENT_CLIENT['PDOB'];
if ($row_PATIENT_CLIENT['PNEUTER']=='1' && $row_PATIENT_CLIENT['PSEX']=='M'){$pneuter = "(N)";} 
else if ($row_PATIENT_CLIENT['PNEUTER']=='1' && $row_PATIENT_CLIENT['PSEX']=='F'){$pneuter = "(S)";}
else {$pneuter = "";}

$desct=$row_PATIENT_CLIENT['PSEX'].$pneuter.', '.$row_PATIENT_CLIENT['PWEIGHT'].' '.$row_WEIGHTUNIT['WEIGHTUNIT'].', '.$row_PATIENT_CLIENT['PCOLOUR'].', Born: '. $row_PATIENT_CLIENT['PDOB'];
//////

?>
