<?php
session_start();

$petids2=array();
foreach ($_SESSION['invline'] as $value3){
$petids2[]=$value3['INVPET'];
}

$_SESSION['dischgpetid']=array();


$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysql_fetch_assoc($PREFER);

$treatmxx=$_SESSION['client']/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

	$query_CHECKTABLE="SELECT * FROM $treatmxx";
	$CHECKTABLE= mysql_query($query_CHECKTABLE, $tryconnection) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE $treatmxx";
	$result=mysql_query($create_TREATMXX, $tryconnection) or die(mysql_error());
	}

$petids2=array_unique($petids2);


//TAKE EACH PET
foreach ($petids2 as $pet){

$select_MEDNOTE="SELECT * FROM MEDNOTES WHERE NPET='$patient'";
$select_MEDNOTE = mysql_query($select_MEDNOTE, $tryconnection) or die(mysql_error());
$row_MEDNOTE = mysql_fetch_assoc($select_MEDNOTE);

$sumhxcats=2;

if (!empty($row_MEDNOTE)){

//HEADING FOR EXAM
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','EXAMINATION', $sumhxcats,'11', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);



//PRESENTING PROBLEM
$nproblem=array();

	if (strlen($row_MEDNOTE['NPROBLEM']) > 200){
		$howmany=ceil(strlen($row_MEDNOTE['NPROBLEM'])/200);
			for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
			$nproblem[]=substr($row_MEDNOTE['NPROBLEM'],$i,200);
			}
	}
	else {
	$nproblem[]=$row_MEDNOTE['NPROBLEM'];
	}//if (strlen($row_MEDNOTE['NPROBLEM']) > 200)

foreach ($nproblem as $nproblem1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','".mysql_real_escape_string($nproblem1)."', $sumhxcats,'12', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);
}//foreach ($nproblem as $nproblem1)


//DIAGNOSIS
$ndiagnosis=array();

	if (strlen($row_MEDNOTE['NDIAGNOSIS']) > 200){
		$howmany=ceil(strlen($row_MEDNOTE['NDIAGNOSIS'])/200);
			for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
			$ndiagnosis[]=substr($row_MEDNOTE['NDIAGNOSIS'],$i,200);
			}
		}
		else {
		$ndiagnosis[]=$row_MEDNOTE['NDIAGNOSIS'];
	}//if (strlen($row_MEDNOTE['NDIAGNOSIS']) > 200)

foreach ($ndiagnosis as $ndiagnosis1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','".mysql_real_escape_string($ndiagnosis1)."', $sumhxcats,'13', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);
}//foreach ($ndiagnosis as $ndiagnosis1)


//PROCEDURES
$nprocedures=array();

	if (strlen($row_MEDNOTE['NPROCEDURES']) > 200){
		$howmany=ceil(strlen($row_MEDNOTE['NPROCEDURES'])/200);
			for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
			$nprocedures[]=substr($row_MEDNOTE['NPROCEDURES'],$i,200);
			}
		}
		else {
		$nprocedures[]=$row_MEDNOTE['NPROCEDURES'];
	}//if (strlen($row_MEDNOTE['NPROCEDURES']) > 200)

foreach ($nprocedures as $nprocedures1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','".mysql_real_escape_string($nprocedures1)."', $sumhxcats,'16', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);
}//foreach ($subtotalcomment as $subtcom)


//CLIENT INSTRUCTIONS
$nclinstr=array();

	if (strlen($row_MEDNOTE['NCLINSTR']) > 200){
		$howmany=ceil(strlen($row_MEDNOTE['NCLINSTR'])/200);
			for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
			$nclinstr[]=substr($row_MEDNOTE['NCLINSTR'],$i,200);
			}
		}
		else {
		$nclinstr[]=$row_MEDNOTE['NCLINSTR'];
	}//if (strlen($row_MEDNOTE['NCLINSTR']) > 200)

foreach ($nclinstr as $nclinstr1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','".mysql_real_escape_string($nclinstr1)."', $sumhxcats,'17', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);
}//foreach ($nclinstr as $nclinstr1)


//CASE SUMMARY
$ncasesum=array();

	if (strlen($row_MEDNOTE['NCASESUM']) > 200){
		$howmany=ceil(strlen($row_MEDNOTE['NCASESUM'])/200);
			for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
			$ncasesum[]=substr($row_MEDNOTE['NCASESUM'],$i,200);
			}
		}
		else {
		$ncasesum[]=$row_MEDNOTE['NCASESUM'];
	}//if (strlen($row_MEDNOTE['NCASESUM']) > 200)

foreach ($ncasesum as $ncasesum1){
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','".mysql_real_escape_string($ncasesum1)."', $sumhxcats,'18', '".mysql_real_escape_string($_SESSION['invline'][0]['INVDOC'])."', NOW())";
mysql_query($insertSQL, $tryconnection);
}//foreach ($ncasesum as $ncasesum1)


//!!!!!!!!!!!!!!!!!!!!!DELETE FROM MEDNOTES!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//$query_discharge="DELETE QUICK FROM MEDNOTES WHERE NPET='$pet'";
//$discharge=mysql_query($query_discharge,$tryconnection) or die(mysql_error());
//!!!!!!!!!!!!!!!!!!!!!DELETE FROM MEDNOTES!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


$_SESSION['dischgpetid'][]=$pet;

}//if (!empty($row_MEDNOTE)){
}//foreach ($petid as $pet)


?>