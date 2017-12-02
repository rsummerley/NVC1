<?php 
session_start();
require_once('../../../tryconnection.php'); 

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

$bkuptble = "EXHOLDBACKUP";
$bkuptble2 = "EXHOLDBACKUP2";


mysqli_select_db($tryconnection, $database_tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient' LIMIT 1";
$PATIENT_CLIENT = mysqli_query($tryconnection, $query_PATIENT_CLIENT) or die(mysqli_error($mysqli_link));
$row_PATIENT_CLIENT = mysqli_fetch_assoc($PATIENT_CLIENT);

$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR WHERE SIGNEDIN='1' ORDER BY DOCTOR ASC");
$DOCTOR = mysqli_query($tryconnection, $query_DOCTOR) or die(mysqli_error($mysqli_link));
$row_DOCTOR = mysqli_fetch_assoc($DOCTOR);

$query_STAFF = sprintf("SELECT STAFF FROM STAFF WHERE SIGNEDIN='1' ORDER BY STAFF ASC");
$STAFF = mysqli_query($tryconnection, $query_STAFF) or die(mysqli_error($mysqli_link));
$row_STAFF = mysqli_fetch_assoc($STAFF);

$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysqli_query($tryconnection, $select_MEDNOTE) or die(mysqli_error($mysqli_link));
$row_MEDNOTE = mysqli_fetch_assoc($select_MEDNOTE);

//create a copy of this patient's record in EXAMHOLD and EXAMHOLD2 to be able to revert the changes.
if (!isset($_SESSION['soapbackup'])){
$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "INSERT INTO EXHOLDBACKUP SELECT * FROM EXAMHOLD WHERE PETNO = '$patient' ORDER BY TCATGRY,TNO";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "INSERT INTO EXHOLDBACKUP2 SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient' ";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

//set this session to avoid re-creating the back up tables
$_SESSION['soapbackup']=1;
}


$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient' ";
$EXAM = mysqli_query($tryconnection, $query_EXAM) or die(mysqli_error($mysqli_link));
$row_EXAM = mysqli_fetch_assoc($EXAM);


$query_CATEGORIES = "SELECT DISTINCT TTYPE, TCATGRY FROM EXAMHOLD WHERE CUSTNO = '$client' AND PETNO = '$patient' ORDER BY TCATGRY ASC";
$CATEGORIES = mysqli_query($tryconnection, $query_CATEGORIES) or die(mysqli_error($mysqli_link));
$row_CATEGORIES = mysqli_fetch_assoc($CATEGORIES);
$totalRows_CATEGORIES = mysqli_num_rows($CATEGORIES);

if (isset($_POST['findings'])){

if (!empty($_POST['examtype'])){
$examtype = $_POST['examtype'];
}
else {
$examtype = $row_EXAM['EXAMTYPE'];
}

$updateSQL = "UPDATE EXAMHOLD2 SET EXAMTYPE='$examtype', FINDINGS = '$_POST[findings]' WHERE PETNO = '$patient'";
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));

	if ($_POST['findings'] == '1'){
	$updateSQL = "UPDATE EXAMHOLD SET TVAR1 = '1' WHERE TNO = '1' AND PETNO = '$patient'";
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	}
	else if ($_POST['findings'] == '2'){
	$updateSQL = "UPDATE EXAMHOLD SET TVAR1 = '1' WHERE TNO = '1' AND PETNO = '$patient'";
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	$opendir = "subsysdir=window.open('SUBSYSTEM_DIR.php?findings=".$_POST['findings']."','_blank','width=500, height=500');";
	}
	else if ($_POST['findings'] == '3'){
	$updateSQL = "UPDATE EXAMHOLD SET TVAR1 = '0' WHERE TNO = '1' AND PETNO = '$patient'";
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	$opendir = "subsysdir=window.open('SUBSYSTEM_DIR.php?findings=".$_POST['findings']."','_blank','width=500, height=500');";
	}
	else if ($_POST['findings'] == '4'){
	$updateSQL = "UPDATE EXAMHOLD SET TVAR1 = '0' WHERE TNO = '1' AND PETNO = '$patient'";
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	$opendir = "subsysdir=window.open('SUBSYSTEM_DIR.php?findings=".$_POST['findings']."','_blank','width=500, height=500');";
	}
	else if ($_POST['findings'] == '5'){
	$updateSQL = "UPDATE EXAMHOLD SET TVAR1 = '0', TMEMO='' WHERE PETNO = '$patient'";
	$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));
	}

$query_EXAM = "SELECT * FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$EXAM = mysqli_query($tryconnection, $query_EXAM) or die(mysqli_error($mysqli_link));
$row_EXAM = mysqli_fetch_assoc($EXAM);

//header("Location:OUT_PATIENT.php");
}

//if the user hits SAVE, it will save the record for further editing, drop the table and unset the back up session
else if (isset($_POST['save'])){

$updateSQL = "UPDATE EXAMHOLD2 SET EXAMTYPE='$_POST[xexamtype]', FINDINGS = '$_POST[physexam]' WHERE PETNO = '$patient'";
$Result1 = mysqli_query($tryconnection, $updateSQL) or die(mysqli_error($mysqli_link));

if (empty($row_MEDNOTE)){
$insert_MEDNOTE="INSERT INTO MEDNOTES (NPET, NPROBLEM, NPLANS) VALUES ('$patient', '".mysqli_real_escape_string($mysqli_link, $_POST['nproblem'])."', '".mysqli_real_escape_string($mysqli_link, $_POST['nplans'])."')";
$insert_MEDNOTE = mysqli_query($tryconnection, $insert_MEDNOTE) or die(mysqli_error($mysqli_link));
}
else {
$update_MEDNOTE="UPDATE MEDNOTES SET NPROBLEM = '".mysqli_real_escape_string($mysqli_link, $_POST['nproblem'])."', NPLANS='".mysqli_real_escape_string($mysqli_link, $_POST['nplans'])."' WHERE NPET = '$patient'";
$update_MEDNOTE = mysqli_query($tryconnection, $update_MEDNOTE) or die(mysqli_error($mysqli_link));
}


unset($_SESSION['soapbackup']);
header("Location:../PROCESSING_MENU.php");
}

///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
//if the user hits COMMIT, save the record into MEDICAL HISTORY
else if (isset($_POST['commit'])){

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysqli_query($tryconnection, $query_PREFER) or die(mysqli_error($mysqli_link));
$row_PREFER = mysqli_fetch_assoc($PREFER);

$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

	$query_CHECKTABLE="SELECT * FROM $treatmxx LIMIT 1";
	$CHECKTABLE= mysqli_query($tryconnection, $query_CHECKTABLE) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE TREATM0";
	$result=mysqli_query($tryconnection, $create_TREATMXX) or die(mysqli_error($mysqli_link));
	}
	if ($_POST['xexamtype']=="1"){
	//create the INITIAL ASSESSMENT heading
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'NON-ROUTINE EXAM', 1, '01', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	}
	else if ($_POST['xexamtype']=="2"){
	//create the SOAP EXAMINATION heading
	$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', 'WELLNESS EXAMINATION', 2, '11', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
	mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
	}



//PRIMARY PROBLEM
if (!empty($_POST['nproblem'])){
$nproblem=array();

	if (strlen($_POST['nproblem']) > 500){
		$howmany=ceil(strlen($_POST['nproblem'])/500);
			for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
			$nproblem[]=substr($_POST['nproblem'],$i,500);
			}
	}
	else {
	$nproblem[]=$_POST['nproblem'];
	}//if (strlen($_POST['nproblem']) > 500)

foreach ($nproblem as $nproblem1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$patient','".mysqli_real_escape_string($mysqli_link, $nproblem1)."', 2,'12', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
mysqli_query($tryconnection, $insertSQL);
}//foreach ($nproblem as $nproblem1)
}
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$patient','Examination Findings:', 2,'13', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
mysqli_query($tryconnection, $insertSQL);

//insert the blue box record - check one field after another to see if there's anything in it and if so, stuff it into history.
//WEIGHT
if ($row_EXAM['WEIGHT'] > 0){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Weight::".$row_EXAM['WEIGHT'])." $_POST[weightunit]', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));

$insertW = "UPDATE PETMAST SET PWEIGHT = '$row_EXAM[WEIGHT]' WHERE PETID = '$_SESSION[patient]' LIMIT 1" ;
mysqli_query($tryconnection, $insertW) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['BCS'] > 0){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "BCS::".$row_EXAM['BCS'])." / 5', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['WEIGHTMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "::".$row_EXAM['WEIGHTMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

//TPR/BEHAVIOUR
if ($row_EXAM['TEMP'] > 0){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Temperature::".$row_EXAM['TEMP'])." c', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['PULSE'] > 0){
	if ($row_EXAM['PULSENORM'] == '1'){
	$pulsenorm = "Normal";	
	}
	else {
	$pulsenorm = "";	
	}
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Pulse::".$row_EXAM['PULSE']." ".$pulsenorm)."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['RESPRATE']  > 0 ){
	if ($row_EXAM['RESPNORM'] == '1'){
	$respnorm = "Normal";	
	}
	else {
	$respnorm = "";	
	}
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Respiratory Rate::".$row_EXAM['RESPRATE']." ".$respnorm)."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['MUCOUSM'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Mucous Membrane::".$row_EXAM['MUCOUSM'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['CRT']  > 0 ){
	if ($row_EXAM['CRTNORMAL'] == '1'){
	$crtnorm = "< 2";	
	}
	else {
	$crtnorm = $row_EXAM['CRT'];	
	}
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Capillary Refill Time::".$crtnorm)." s', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['ATTITUDE'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Attitude::".$row_EXAM['ATTITUDE'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}
 
if ($row_EXAM['HYDRATION'] != ''){
	if ($row_EXAM['HYDRPC'] > 0){
	$hydrpc = $row_EXAM['HYDRPC']." %";	
	}
	else {
	$hydrpc = "";	
	}
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Hydration::".$row_EXAM['HYDRATION']." ".$hydrpc)."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['BLOODPRES'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Blood Pressure::".$row_EXAM['BLOODPRES'])." mmHg', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['TPRMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "::".$row_EXAM['TPRMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

//PAIN
if ($row_EXAM['PAS'] > 0){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Pain Assessment::".$row_EXAM['PAS'])." / 9', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['PAINMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "::".$row_EXAM['PAINMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['DIET'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Current Diet::".$row_EXAM['DIET'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['DIETMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Recommendation::".$row_EXAM['DIETMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['TARTAR'] != ""){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Tartar::".$row_EXAM['TARTAR'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['GINGIVITIS'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Gingivitis::".$row_EXAM['GINGIVITIS'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['PD'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Periodontitis::".$row_EXAM['PD'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['NEEDSDENT'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Needs Dentistry::".$row_EXAM['NEEDSDENT'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['DENTALMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Dental::".$row_EXAM['DENTALMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['VACCINESMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Vaccines::".$row_EXAM['VACCINESMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['PARCONTROLMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Parasite Control::".$row_EXAM['PARCONTROLMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['CLIENTEDMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Client Education::".$row_EXAM['CLIENTEDMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['BEHAVIOURMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Behaviour::".$row_EXAM['BEHAVIOURMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['CURRMEDS'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Current Medications::".$row_EXAM['CURRMEDS'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

if ($row_EXAM['WELLMEMO'] != ''){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, "Wellness Testing::".$row_EXAM['WELLMEMO'])."', 2, '57', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
}

 
//INSERT SUBSYSTEMS:
//select categories

$query_CATEGORIES = "SELECT DISTINCT TTYPE, TCATGRY FROM EXAMHOLD WHERE CUSTNO = '$client' AND PETNO = '$patient'";
$CATEGORIES = mysqli_query($tryconnection, $query_CATEGORIES) or die(mysqli_error($mysqli_link));
$row_CATEGORIES = mysqli_fetch_assoc($CATEGORIES);

//for each category, select the subsystems that are marked
do { 
	$query_SUBSYSTEM = "SELECT TCATGRY, TTYPE, TDESCR, TVAR1, TMEMO FROM EXAMHOLD WHERE TCATGRY='$row_CATEGORIES[TCATGRY]' AND PETNO = '$patient' AND TVAR1='1' ORDER BY TCATGRY,TTYPE";
	$SUBSYSTEM = mysqli_query($tryconnection, $query_SUBSYSTEM) or die(mysqli_error($mysqli_link));
	$row_SUBSYSTEM = mysqli_fetch_assoc($SUBSYSTEM);
	
	$query_TMEMO = "SELECT TMEMO FROM EXAMHOLD WHERE TCATGRY='$row_CATEGORIES[TCATGRY]' AND TNO=1 AND PETNO = '$patient'";
	$TMEMO = mysqli_query($tryconnection, $query_TMEMO) or die(mysqli_error($mysqli_link));
	$row_TMEMO = mysqli_fetch_assoc($TMEMO);
	
	//for each market subsystem, create a record in the history
		do { 
			
			if (!empty($row_SUBSYSTEM)){
			//insert the checkboxes
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '".mysqli_real_escape_string($mysqli_link, $row_SUBSYSTEM['TTYPE']."::".$row_SUBSYSTEM['TDESCR'])."', 2, '58', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}

			//insert the memo
			if (!empty($row_TMEMO['TMEMO'])){
			$tmemo = $row_TMEMO['TMEMO'];
			}
			else {
			$tmemo = 0;
			}

			//$tcatgry = $row_SUBSYSTEM['TCATGRY'];
			
			} while ($row_SUBSYSTEM = mysqli_fetch_assoc($SUBSYSTEM));
			
			if (!empty($tmemo)){
			$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$_SESSION[client]', '$_SESSION[patient]', '::".mysqli_real_escape_string($mysqli_link, $tmemo)."', 2, '58', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'), '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."')";
			mysqli_query($tryconnection, $insertSQL) or die(mysqli_error($mysqli_link));
			}

} while ($row_CATEGORIES = mysqli_fetch_assoc($CATEGORIES));




//ASSESSMENT/PLANS
if (!empty($_POST['nplans'])){

$findme='Plans:';
$pos= strpos($_POST['nplans'], $findme);

//if ($pos!==false){
$assessment = substr($_POST['nplans'],11,$pos-11);
//}
//else {
//$assessment = substr($_POST['nplans'],10);
//}

$plans = substr($_POST['nplans'],$pos+6);

//assessment
$nassessment=array();
	if (strlen($assessment) > 500){
		$howmany=ceil(strlen($assessment)/500);
			for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
			$nassessment[]=substr($assessment,$i,500);
			}
	}
	else {
	$nassessment[]=$assessment;
	}//if (strlen($_POST['nplans']) > 500)

foreach ($nassessment as $nassessment1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$patient','".mysqli_real_escape_string($mysqli_link, $nassessment1)."', 2,'16', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
mysqli_query($tryconnection, $insertSQL);
}//foreach ($nassessment as $nassessment1)

//plans
$nplans=array();
	if (strlen($plans) > 500){
		$howmany=ceil(strlen($plans)/500);
			for ($i=0; $i<($howmany*500); $i=($i/500+1)*500){
			$nplans[]=substr($plans,$i,500);
			}
	}
	else {
	$nplans[]=$plans;
	}//if (strlen($_POST['nplans']) > 500)

foreach ($nplans as $nplans1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$patient','".mysqli_real_escape_string($mysqli_link, $nplans1)."', 2,'17', '".mysqli_real_escape_string($mysqli_link, $_POST['who'])."', STR_TO_DATE('$_POST[treatdate]', '%m/%d/%Y'))";
mysqli_query($tryconnection, $insertSQL);
}//foreach ($nproblem as $nproblem1)
}


$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

// wipe out the mednote entry, then redo the original query so that it is empty.

$delete_MEDNOTE="DELETE FROM MEDNOTES WHERE NPET='$patient'";
$deleted_MEDNOTE = mysqli_query($tryconnection, $delete_MEDNOTE) or die(mysqli_error($mysqli_link));

$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysqli_query($tryconnection, $select_MEDNOTE) or die(mysqli_error($mysqli_link));
$row_MEDNOTE = mysqli_fetch_assoc($select_MEDNOTE);

unset($_SESSION['soapbackup']);
header("Location:../PROCESSING_MENU.php");
}
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////


//if the user hits REVERT, 
else if (isset($_POST['revert'])){

$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "INSERT INTO EXAMHOLD SELECT * FROM EXHOLDBACKUP WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "INSERT INTO EXAMHOLD2 SELECT * FROM EXHOLDBACKUP2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

unset($_SESSION['soapbackup']);
header("Location:../PROCESSING_MENU.php");
}

//if the user hits TRASH, 
else if (isset($_POST['trash'])){

$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXAMHOLD2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "DELETE QUICK FROM EXHOLDBACKUP2 WHERE PETNO = '$patient'";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXAMHOLD2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$query_SOAPBACKUP = "OPTIMIZE TABLE EXHOLDBACKUP2";
$SOAPBACKUP = mysqli_query($tryconnection, $query_SOAPBACKUP) or die(mysqli_error($mysqli_link));

$update_MEDNOTE="UPDATE MEDNOTES SET NPROBLEM = '', NPLANS='' WHERE NPET = '$patient'";
$update_MEDNOTE = mysqli_query($tryconnection, $update_MEDNOTE) or die(mysqli_error($mysqli_link));

unset($_SESSION['soapbackup']);
header("Location:../PROCESSING_MENU.php");
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/DVMBasicTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>OUT-PATIENT</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../../ASSETS/styles.css" />
<script type="text/javascript" src="../../../ASSETS/scripts.js"></script>

<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">

function bodyonload(){
<?php echo $opendir; ?>
document.getElementById('inuse').innerText=localStorage.xdatabase;
document.out_patient.weightunit.value=localStorage.weightunit;
if (sessionStorage.nproblem){document.out_patient.nproblem.value=sessionStorage.nproblem;}
else {
sessionStorage.setItem('nproblem','<?php if (!empty($row_MEDNOTE['NPROBLEM'])) {echo mysqli_real_escape_string($mysqli_link, $row_MEDNOTE['NPROBLEM']);} else {echo "";} ?>');
document.out_patient.nproblem.value=sessionStorage.nproblem;
}

if (sessionStorage.nplans){document.out_patient.nplans.value=sessionStorage.nplans;}
else {
sessionStorage.setItem('nplans','<?php if (!empty($row_MEDNOTE['NPLANS'])) {echo mysqli_real_escape_string($mysqli_link, $row_MEDNOTE['NPLANS']);} else {echo "Assessment: \\nPlans: ";} ?>');
document.out_patient.nplans.value=sessionStorage.nplans;
}

var extype = '<?php echo $row_EXAM['EXAMTYPE']; ?>';
var extype = 2 ; // temporary fix
if (extype == '2') {
	document.getElementById('nplans').rows=13;
	document.getElementById('tprbox').style.borderColor="#FF0099";
	document.getElementById('label1').style.backgroundColor="#FFCCFF";
	document.getElementById('label2').style.backgroundColor="#FFFFFF";
	document.getElementById('label2').style.color="#CCCCCC";
}
else  {
	document.getElementById('nproblemtr').style.display="none";
	document.getElementById('primproblemtr').style.display="none";
	document.getElementById('nplans').rows=18;
	document.getElementById('tprbox').style.borderColor="#00CC66";
	document.getElementById('label2').style.backgroundColor="#CBFFCD";
	document.getElementById('label1').style.backgroundColor="#FFFFFF";
	document.getElementById('label1').style.color="#CCCCCC";
}
//document.getElementById('WindowBodyShadow').style.display="none";
}


function opensubsys(tcatgry, custno, patient){
subsysdir=window.open('SUBSYSTEM.php?category=' + tcatgry + '&client=' + custno + '&patient=' + patient ,'_blank','width=400, height=500');
}

function examtype(y){
document.form_findings.examtype.value = y;
document.form_findings.submit();
//	if (y == '1'){
//		document.getElementById('tprbox').style.borderColor="#FF0099";
//		document.getElementById('nproblemtr').style.display="";
//		document.getElementById('primproblemtr').style.display="";
//		document.getElementById('nplans').rows=13;
//		document.getElementById('label1').style.backgroundColor="#FFCCFF";
//		document.getElementById('label2').style.backgroundColor="#FFFFFF";
//		document.getElementById('label1').style.color="#000000";
//		document.getElementById('label2').style.color="#CCCCCC";
//		}
//	else if (y == '2'){
//		document.getElementById('tprbox').style.borderColor="#00CC66";
//		document.getElementById('nproblemtr').style.display="none";
//		document.getElementById('primproblemtr').style.display="none";
//		document.getElementById('nplans').rows=18;
//		document.getElementById('label2').style.backgroundColor="#CBFFCD";
//		document.getElementById('label1').style.backgroundColor="#FFFFFF";
//		document.getElementById('label2').style.color="#000000";
//		document.getElementById('label1').style.color="#CCCCCC";
//		}
}

function findings(x){
	if (x=='4'){
	document.form_findings.findings.value = x;
	document.form_findings.submit();
	}
	else if (x=='3'){
	document.form_findings.findings.value = x;
	document.form_findings.submit();
	}
	else if (x=='2'){
	document.form_findings.findings.value = x;
	document.form_findings.submit();
	}
	else {
	document.form_findings.findings.value = x;
	document.form_findings.submit();
	}
	
}



function setnproblem(){
var nproblem=document.out_patient.nproblem.value;
sessionStorage.setItem('nproblem',nproblem);
}

function setnplans(){
var nplans=document.out_patient.nplans.value;
sessionStorage.setItem('nplans',nplans);
}


function checknames(){
valid = true;
	if (document.out_patient.whichbutton.value=='commit'){	
	var who=document.out_patient.who;
		if (document.out_patient.who.options[0].selected==true){
			alert ('Please enter your name.');
			valid = false;
		}
	return valid;	
	}
	else if (document.out_patient.whichbutton.value=='trash'){	
		if (confirm("This will delete the entire SOAP examination without an option to revert. Are you sure you want to proceed?")){
			valid = true;
		}
		else {
			valid = false;
		}
	return valid;	
	}
}

</script>

<style type="text/css">
#WindowBody{
width:auto;
}

#apDiv1 {
	position:relative;
	z-index:32768;
	left: 0px;
	float:left;
	margin-top:-10px;
	margin-left:0px;
	margin-right:0px;
}
#apDiv2 {
	position:relative;
	z-index:32768;
	right: 0px;
	float:left;
	margin-top:-10px;
	margin-left:0px;
	margin-right:0px;
}
#betwaps {
	float:left;
}
#apDiv3 {
	position:absolute;
	z-index:32768;
	left: 0px;
	top: 2px;
}
#apDiv4 {
	position:absolute;
	z-index:32768;
	right: 0px;
	top: 2px;
}
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()" onfocus="checkopen(subsysdir);" onblur="winblur(subsysdir);">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../../ASSETS/calendar.js"></script>
<!-- InstanceEndEditable -->

<!-- InstanceBeginEditable name="HOME" -->
<div id="LogoHead" onmouseover="document.getElementById(this.id).style.cursor='default';">DVM</div>
<!-- InstanceEndEditable -->

<div id="MenuBar">

	<ul id="navlist">
                
<!--FILE-->                
                
		<li><a href="#" id="current">File</a> 
			<ul id="subnavlist">
                <li><a href="#"><span class="disabled">About DV Manager</span></a></li>
                <li><a onclick=""><span class="disabled">Utilities</span></a></li>
			</ul>
		</li>
                
<!--INVOICE-->                
                
		<li><a href="#" id="current">Invoice</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Casual Sale Invoicing</span></a></li>
                <li><!-- InstanceBeginEditable name="reg_nav" --><a href="#" onclick=""><span class="disabled">Regular Invoicing</span></a><!-- InstanceEndEditable --></li>
                <li><a href="#" onclick=""><span class="disabled">Estimate</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Barn/Group Invoicing</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Summary Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cash Receipts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Cancel Invoices</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Comments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Treatment and Fee File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Worksheet File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Procedure Invoicing File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Invoicing Reports</span></a></li>
			</ul>
		</li>
                
<!--RECEPTION-->                
                
		<li><a href="#" id="current">Reception</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Appointment Scheduling</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Registration</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Using Reception File</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Generic Examination Sheets</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Duty Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Staff Sign In &amp; Out</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">End of Day Accounting Reports</span></a></li>
                    </ul>
                </li>
                
<!--PATIENT-->                
                
                <li><a href="#" id="current">Patient</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Processing Menu</span></a> </li>
                <li><a href="#" onclick=""><span class="disabled">Review Patient Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter New Medical History</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Patient Lab Results</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Enter Surgical Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Create New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Move Patient to a New Client</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Rabies Tags</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Tatoo Numbers</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Certificates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Clinical Logs</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Patient Categorization</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Laboratory Templates</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Quick Weight</span></a></li>
<!--                <li><a href="#" onclick="window.open('','_self')"><span class="">All Treatments Due</span></a></li>
-->			</ul>
		</li>
        
<!--ACCOUNTING-->        
		
        <li><a href="#" id="current">Accounting</a>
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Accounting Reports</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Inventory</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Business Status Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Hospital Statistics</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Month End Closing</span></a></li>
			</ul>
		</li>
        
<!--MAILING-->        
		
        <li><a href="#" id="current">Mailing</a> 
			<ul id="subnavlist">
                <li><a href="#" onclick=""><span class="disabled">Recalls and Searches</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Handouts</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Mailing Log</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Vaccine Efficiency Report</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referring Clinics and Doctors</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Referral Adjustments</span></a></li>
                <li><a href="#" onclick=""><span class="disabled">Labels</span></a></li>
			</ul>
		</li>
	</ul>
</div>

<div id="inuse" title="File in memory"><!-- InstanceBeginEditable name="fileinuse" -->
<!-- InstanceEndEditable --></div>



<div id="WindowBody">
<!-- InstanceBeginEditable name="DVMBasicTemplate" -->



<!--<div id="WindowBodyShadow">
</div>
-->
<form name="out_patient" method="post" action=""  onsubmit="return checknames();">
    <table width="732" border="0" cellspacing="0" cellpadding="0">
  <tr align="center">
    <td height="10" colspan="3" align="center" valign="top">
    <label id="label1" class="Verdana12B" onmouseover="document.getElementById(this.id).style.cursor='pointer';" style="background-color: #FFCCFF;">
    <input name="xexamtype" type="radio" id="examtype1" onclick="examtype('1');" value="1" <?php if ($row_EXAM['EXAMTYPE']=='1') {echo "checked";} ?>  />
    &nbsp;NON-ROUTINE EXAM&nbsp;</label>
     <label id="label2" class="Verdana12B" onmouseover="document.getElementById(this.id).style.cursor='pointer';" style="background-color: #CBFFCD;">
     <input name="xexamtype" type="radio" id="examtype2" onclick="examtype('2');" value="2" <?php if ($row_EXAM['EXAMTYPE']!='1') {echo "checked";} ?> />
     &nbsp;WELLNESS EXAM&nbsp;</label>     </td>
    </tr>
  <tr id="primproblemtr" align="center">
    <td height="22" colspan="3" align="center" valign="bottom" class="Verdana12B">SUBJECTIVE</td>
    </tr>
  <tr id="nproblemtr">
    <td colspan="3" align="center">
    <textarea name="nproblem" cols="90" rows="3" class="commentarea" id="nproblem" onkeyup="setnproblem();">
    </textarea>    </td>
  </tr>
  <tr>
    <td height="60" colspan="3" align="center" valign="middle">
      <table id="tprbox" width="680" border="1" cellpadding="0" cellspacing="0" bordercolor="#00CC66" frame="box" rules="none" style="border-width:2px;">
      <tr class="">
        <td height="25" align="center" valign="middle" class="Verdana11B">     
        <div style="float:left; width:140px; text-align:center;"><label id="weightbcs" onmouseover="CursorToPointer(this.id)">
        <input name="pinkbox" type="checkbox" id="pinkbox"  onclick="subsysdir=window.open('WEIGHT_BCS.php','_blank','width=500, height=225')" <?php if ($row_EXAM['WEIGHT'] > 0 || $row_EXAM['BCS'] > 0 || $row_EXAM['WEIGHTMEMO'] != '') {echo "checked";} ?> />
        Weight/BCS
        </label></div>     
        
        
        <div style="float:left; width:150px; text-align:left;"><label id="tprbeh" onmouseover="CursorToPointer(this.id)">
        <input name="pinkbox6" type="checkbox" id="pinkbox6" onclick="subsysdir=window.open('TPR_BEHAVIOUR.php','_blank','width=500, height=393')" <?php if ($row_EXAM['TEMP'] > 0 || $row_EXAM['PULSE']  > 0 || $row_EXAM['RESPRATE']  > 0 || $row_EXAM['RESPCHAR'] != '' || $row_EXAM['MUCOUSM'] != '' || $row_EXAM['PULSENORM'] != '' || $row_EXAM['CRT']  > 0 || $row_EXAM['ATTITUDE'] != '' || $row_EXAM['HYDRATION'] != '' || $row_EXAM['HYDRPC'] > 0 || $row_EXAM['TPRMEMO'] != '' || $row_EXAM['BLOODPRES'] != '' || $row_EXAM['RESPNORM'] != '') {echo "checked";} ?>/>
        TPR/Behaviour</label></div>
                
           <div style="float:left; width:110px; text-align:left;"><label id="diet" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox2" type="checkbox" id="pinkbox2"  onclick="subsysdir=window.open('DIET.php','_blank','width=500, height=303')" <?php if ($row_EXAM['DIET'] != '' || $row_EXAM['DIETMEMO'] != '') {echo "checked";} ?>/>
        Nutrition        </label></div> 
        
               
       <div style="float:left; width:90px; text-align:left;"><label id="pain" onmouseover="CursorToPointer(this.id)">
        <input name="pinkbox4" type="checkbox" id="pinkbox4" onclick="subsysdir=window.open('PAIN.php','_blank','width=500, height=265')" <?php if ($row_EXAM['PAS'] > 0 || $row_EXAM['PAINMEMO'] != '') {echo "checked";} ?>/>
        Pain        </label>  </div>
        
              
            <div style="float:left; width:80px; text-align:left;"><label id="dental" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox5" type="checkbox" id="pinkbox5" onclick="subsysdir=window.open('DENTAL.php','_blank','width=500, height=330')" <?php if ($row_EXAM['TARTAR'] != "" || $row_EXAM['GINGIVITIS'] != '' || $row_EXAM['PD'] != '' || $row_EXAM['DENTALMEMO'] != '' || $row_EXAM['NEEDSDENT'] != '') {echo "checked";} ?>/>
        Dental        </label></div>       
           
            <div style="float:left; width:80px; text-align:left;"><label id="riskas" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox3" type="checkbox" id="pinkbox3"  onclick="subsysdir=window.open('RISK_AS.php','_blank','width=450, height=550')" <?php if ($row_EXAM['VACCINESMEMO'] != '' || $row_EXAM['PARCONTROLMEMO'] != '') {echo "checked";} ?>/>
        Risk As.        </label></div>        </td>
        </tr>
      <tr class="">
        <td height="17" align="center" valign="middle" class="Verdana11B">
        <label id="cliented"  onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox7" type="checkbox" id="pinkbox7" onclick="subsysdir=window.open('CLIENT_ED.php','_blank','width=500, height=170')" <?php if ($row_EXAM['CLIENTEDMEMO'] != '') {echo "checked";} ?>/>
        Client Education</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label id="medications" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox8" type="checkbox" id="pinkbox8" onclick="subsysdir=window.open('BEHAVIOUR.php','_blank','width=500, height=200')" <?php if ($row_EXAM['BEHAVIOURMEMO'] != '') {echo "checked";} ?>/>Behaviour</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="medications" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox8" type="checkbox" id="pinkbox8" onclick="subsysdir=window.open('MEDICATIONS.php','_blank','width=500, height=210')" <?php if ($row_EXAM['CURRMEDS'] != '') {echo "checked";} ?>/>Medications</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label id="wellnesstest" onmouseover="CursorToPointer(this.id)" class="">
        <input name="pinkbox9" type="checkbox" id="pinkbox9" onclick="subsysdir=window.open('WELLNESS_TEST.php','_blank','width=500, height=200')" <?php if ($row_EXAM['WELLMEMO'] != '') {echo "checked";} ?>/>Wellness testing</label>
        
        </td>
        </tr>
    </table></td>
  </tr>
  <tr align="center">
    <td width="302" height="30" align="left" valign="bottom" class="Verdana12B"><img src="../../../IMAGES/v copy.jpg" alt="v" id="v" width="30" height="30" class="style1" onclick="subsysdir=window.open('../../PATIENT_DETAIL.php','_blank', 'width=660,height=540')" onmouseover="CursorToPointer(this.id)" title="Click to view patient detail" />
      <img src="../../../IMAGES/h copy.jpg" alt="h" id="h" width="30" height="30" onclick="subsysdir=window.open('../../HISTORY/REVIEW_HISTORY.php?path=2close','_blank')" onmouseover="CursorToPointer(this.id)" title="Click to view patient's history" />
      <img src="../../../IMAGES/m.jpg" alt="m" width="30" height="30" />    </td>
    <td width="136" height="30" align="center" valign="middle" class="Verdana12B">PHYSICAL EXAM</td>
    <td width="300" height="30" align="right" valign="bottom" class="Verdana12B"><input type="button" name="zoom" id="zoom" value="ZOOM" onclick="subsysdir=window.open('SUBSYSTEM_DIR.php?findings=','_blank','width=500, height=500')"/>
      <input name="button3" type="button" class="hidden" id="button3" value="PRINT" /></td>
  </tr>
  <tr>
    <td colspan="3" align="center" valign="top">
    <span class="Verdana12BBlue">&nbsp;&nbsp;
    <label>
    <input type="radio" name="physexam" id="allnaf" value="1" onclick="findings('1');" <?php if ($row_EXAM['FINDINGS']=='1') {echo "checked";} ?>/>
    &nbsp;All-NAF</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type="radio" name="physexam" id="allsnaf" value="2" onclick="findings('2');" <?php if ($row_EXAM['FINDINGS']=='2') {echo "checked";} ?>/>
&nbsp;All-with AF</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type="radio" name="physexam" id="snaf" value="3" onclick="findings('3');" <?php if ($row_EXAM['FINDINGS']=='3') {echo "checked";} ?>/>
&nbsp;Some-NAF</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type="radio" name="physexam" id="saf" value="4" onclick="findings('4');" <?php if ($row_EXAM['FINDINGS']=='4') {echo "checked";} ?>/>
&nbsp;Some-with AF</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type="radio" name="physexam" id="none" value="5" onclick="findings('5');" <?php if ($row_EXAM['FINDINGS']=='5') {echo "checked";} ?>/>
&nbsp;None</label>
    <br />
    </span>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="15">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    </table>    </td>
    </tr>
  <tr class="Verdana12">
    <td width="4%" align="left" class="Verdana12"></td>
    <td width="96%" height="5" align="left" class="Verdana12"></td>
  </tr>
  <tr class="Verdana12">
    <td align="left" class="Verdana12">&nbsp;</td>
    <td height="30" align="left" class="Verdana12">     
    <?php 
	do { 

	$query_SUBSYSTEM = "SELECT TVAR1 FROM EXAMHOLD WHERE TCATGRY = '$row_CATEGORIES[TCATGRY]' AND PETNO = '$patient' AND TVAR1='1' LIMIT 1";
	$SUBSYSTEM = mysqli_query($tryconnection, $query_SUBSYSTEM) or die(mysqli_error($mysqli_link));
	$row_SUBSYSTEM = mysqli_fetch_assoc($SUBSYSTEM);
	if (!empty($row_SUBSYSTEM)) {
	$tvar1 = 1;
	}
	else {
	$tvar1 = 0;
	}
	
	?>
	<div style="width:<?php if (strlen($row_CATEGORIES['TTYPE']) == 4) { echo '70';} else {echo '140';} ?>px; float:left;"><label style="white-space:nowrap;" class="<?php if ($tvar1 == 1) {echo 'Verdana11';} else {echo "Verdana11";}?>" onmouseover="CursorToPointer(this.id)">&nbsp;<input type="checkbox" name="subsystem" id="<?php echo $row_CATEGORIES['TCATGRY']; ?>" onclick="opensubsys('<?php echo $row_CATEGORIES['TCATGRY']; ?>','<?php echo $row_PATIENT_CLIENT['CUSTNO']; ?>','<?php echo $row_PATIENT_CLIENT['PETID']; ?>');" <?php if ($tvar1 == 1) {echo "checked";}?>/>&nbsp;<?php echo $row_CATEGORIES['TTYPE']; ?></label></div>
    <?php } while ($row_CATEGORIES = mysqli_fetch_assoc($CATEGORIES)); ?>   </td>
    </tr>
</table>    </td>
  </tr>
  <tr>
    <td height="20" colspan="3" align="center" valign="bottom" class="Verdana12B">ASSESSMENT/PLANS</td>
  </tr>
  <tr>
    <td colspan="3" align="center"><textarea name="nplans" cols="90" rows="" class="commentarea" id="nplans"  onkeyup="setnplans();">
    </textarea></td>
  </tr>
  <!--<tr class="hidden">
    <td width="142" height="17" align="center">&nbsp;</td>
    <td width="456" height="17" align="center" class="Verdana11BBlue">INVOICING</td>
    <td colspan="2" rowspan="2" align="left">
    <table width="90%" height="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><input type="submit" name="button5" id="button5" value="ESTIMATE" />
          <input type="submit" name="button6" id="button6" value="PROCEDURE" />
          <input type="submit" name="button7" id="button7" value="EDIT" /></td>
          </tr>
      <tr>
        <td height="85" bgcolor="#FFFF99"></td>
          </tr>
      
        <tr>
          <td align="right"><input type="submit" name="button8" id="button8" value="RLS" /></td>
          </tr>
    </table></td>
    </tr>-->
  <!--<tr align="center" class="hidden">
    <td colspan="2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td width="11%" align="right" class="Verdana11"><input name="checkbox24" type="checkbox" id="checkbox24" /></td>
        <td width="26%" align="left" class="Verdana11">SERVICE</td>
        <td width="5%" align="right" class="Verdana11"><input name="checkbox31" type="checkbox" id="checkbox31" /></td>
        <td width="26%" align="left" class="Verdana11">PHARMACY</td>
        <td width="5%" align="right" class="Verdana11"><input name="checkbox36" type="checkbox" id="checkbox36" /></td>
        <td width="27%" align="left" class="Verdana11">FOOD</td>
      </tr>
      <tr>
        <td align="right" class="Verdana11"><input name="checkbox25" type="checkbox" id="checkbox25" /></td>
        <td align="left" class="Verdana11">IMMUNIZATION</td>
        <td align="right" class="Verdana11"><input name="checkbox32" type="checkbox" id="checkbox32" /></td>
        <td align="left" class="Verdana11">SURGERY</td>
        <td align="right" class="Verdana11"><input name="checkbox37" type="checkbox" id="checkbox37" /></td>
        <td align="left" class="Verdana11">ANAESTHESIA</td>
      </tr>
      <tr>
        <td align="right" class="Verdana11"><input name="checkbox26" type="checkbox" id="checkbox26" /></td>
        <td align="left" class="Verdana11">RADIOLOGY</td>
        <td align="right" class="Verdana11"><input name="checkbox33" type="checkbox" id="checkbox33" /></td>
        <td align="left" class="Verdana11">HOSPITAL/BOARD</td>
        <td align="right" class="Verdana11"><input name="checkbox38" type="checkbox" id="checkbox38" /></td>
        <td align="left" class="Verdana11">LABORATORY-IN</td>
      </tr>
      <tr>
        <td align="right" class="Verdana11"><input name="checkbox28" type="checkbox" id="checkbox28" /></td>
        <td align="left" class="Verdana11">LABORATORY-OUT</td>
        <td align="right" class="Verdana11"><input name="checkbox34" type="checkbox" id="checkbox34" /></td>
        <td align="left" class="Verdana11">DENTISTRY</td>
        <td align="right" class="Verdana11"><input name="checkbox39" type="checkbox" id="checkbox39" /></td>
        <td align="left" class="Verdana11">EUTHANASIA</td>
      </tr>
      <tr>
        <td align="right" class="Verdana11"><input name="checkbox29" type="checkbox" id="checkbox29" /></td>
        <td align="left" class="Verdana11">GROOMING</td>
        <td align="right" class="Verdana11"><input name="checkbox35" type="checkbox" id="checkbox35" /></td>
        <td align="left" class="Verdana11">OTHER</td>
        <td align="right" class="Verdana11"><input name="checkbox40" type="checkbox" id="checkbox40" /></td>
        <td align="left" class="Verdana11">DISCOUNTS</td>
      </tr>
      <tr>
        <td align="right" class="Verdana11"><input name="checkbox30" type="checkbox" id="checkbox30" /></td>
        <td align="left" class="Verdana11">M.V.I.</td>
        <td align="right" class="Verdana11">&nbsp;</td>
        <td align="left" class="Verdana11">&nbsp;</td>
        <td align="right" class="Verdana11">&nbsp;</td>
        <td align="left" class="Verdana11">&nbsp;</td>
      </tr>
    </table></td>
    </tr>-->
  
  <tr>
    <td height="25" align="left" valign="middle" class="Verdana12">
    <label>Effective date: 
      <input name="treatdate" type="text" class="Input" id="treatdate" size="10" maxlength="10" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" value="<?php echo date("m/d/Y"); ?>" onclick="ds_sh(this)" />
      </label>
    <label></label>&nbsp;</td>
    <td align="left" valign="middle" class="Verdana12">&nbsp;</td>
    <td align="right" valign="middle" class="Verdana12"><label>Name:
        <select name="who">
          <option></option>
          <?php do { ?>
          <option value="<?php echo $row_DOCTOR['DOCTOR']; ?>"><?php echo $row_DOCTOR['DOCTOR']; ?></option>
          <?php } while ($row_DOCTOR = mysqli_fetch_assoc($DOCTOR)); ?>
          <?php do { ?>
          <option value="<?php echo $row_STAFF['STAFF']; ?>"><?php echo $row_STAFF['STAFF']; ?></option>
          <?php } while ($row_STAFF = mysqli_fetch_assoc($STAFF)); ?>
        </select>
    </label></td>
  </tr>
  <tr class="ButtonsTable">
    <td height="27" colspan="3" align="center" bgcolor="#B1B4FF">
      <input type="submit" name="commit" id="commit" class="button" value="FINISH" title="This will commit the exam record into medical history." onclick="document.out_patient.whichbutton.value='commit';"/>
      <input type="submit" name="save" id="save" class="button" value="SAVE" title="This will save the exam record for further editing." onclick="document.out_patient.whichbutton.value='save';"/>
      <input type="submit" name="trash" id="trash" class="button" value="TRASH" title="This will delete the entire SOAP examination." onclick="document.out_patient.whichbutton.value='trash';"/>
      <input type="submit" name="revert" id="revert" class="button" value="REVERT" title="This will revert the changes that you have made and exit this screen."  onclick="document.out_patient.whichbutton.value='revert';"/></td>
  </tr>
</table>

<div id="apDiv3"><span class="Verdana12B" style="background-color:#FFFF00"><?php echo $row_PATIENT_CLIENT['TITLE']; ?>&nbsp;<?php echo $row_PATIENT_CLIENT['CONTACT']; ?> <?php echo $row_PATIENT_CLIENT['COMPANY']; ?></span></div>
 <div id="apDiv4"><span class="Verdana12B" style="background-color:#FFFF00">Patient: <?php echo $row_PATIENT_CLIENT['PETNAME']; ?></span></div>   

<input type="hidden" name="weightunit" value=""  />
<input type="hidden" name="whichbutton" value=""  />
</form>

<form action="" method="post" name="form_findings">
<input type="hidden" name="examtype" value="<?php echo $row_EXAM['EXAMTYPE']; ?>"  />
<input type="hidden" name="findings" value="<?php echo $row_EXAM['FINDINGS']; ?>"  />
</form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>
