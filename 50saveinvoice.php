<?php
include("tax.php");

// This first section is just for casual sales.

if (isset($_SESSION['casual'])){

if (isset($_POST['save']) || isset($_POST['prtsave'])){

$refno=$_SESSION['rcvdpayment'][0]['METHOD'];
$taxname=taxname($database_tryconnection, $tryconnection, $_SESSION['minvdte']);

if ($refno=='ONAC'){
$dtepaid=" ";
$refno=" ";
}
else {
$dtepaid=date("Y-m-d H:i:s");
$refno=$refno;
}
$tax=$GSTtotal;
$ptax=$PSTtotal;
$itotal=$TOTAL;
$discount=$INVdiscount;
$amtpaid=$_SESSION['rcvdpayment'][0]['AMOUNT'];
$ibal=$itotal-$amtpaid;
$prtid="X";
$npfee='X';

$query_CRITDATA = "SELECT HGSTNO FROM CRITDATA LIMIT 1";
$CRITDATA = mysqli_query($tryconnection, $query_CRITDATA) or die(mysqli_error($mysqli_link));
$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);

$query_invdatetime="SELECT STR_TO_DATE('$_SESSION[csminvdte]','%m/%d/%Y')";
$invdatetime= mysql_unbuffered_query($query_invdatetime, $tryconnection) or die(mysqli_error($mysqli_link));
$row_invdatetime=mysqli_fetch_array($invdatetime);

// here is where the transaction starts for InnoDB purposes.
BEGIN ;

//ARARECV - accounts receivables
//one record per invoice - at the time of saving a new invoice, it generates a consolidated record of all payment methods, except for ONAC and PDC
$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['csminvno'],
					$row_invdatetime[0],
					"0",
					"CASUAL SALE",
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$refno,
					$dtepaid,
					$tax,
					$ptax,
					$itotal,
					$discount,
					$ararecv_amtpaid,
					$ibal
					);
$result=mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
	
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[csminvno] '" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

//ARINVOI - stores finished invoices
$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, PRTID, REFVET, REFCLIN, NPFEE, INVORDDOC, PDEAD, INVPET,UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['csminvno'],
					$row_invdatetime[0],
					"0",
					"CASUAL SALE",
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$refno,
					$dtepaid,
					$tax,
					$ptax,
					$itotal,
					$discount,
					$amtpaid,
					$ibal,
					$prtid,
					mysqli_real_escape_string($mysqli_link, $_POST['refvet']),
					mysqli_real_escape_string($mysqli_link, $_POST['refclin']),
					$npfee,
					mysqli_real_escape_string($mysqli_link, $_SESSION['invdoc']),
					$pdead,
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$uni
					);
$result=mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));

//ARCASHR - stores record of cash coming in
//for each method of payment except for ONAC and PDC
if (isset($_SESSION['splitpayment'])){
$arvalue1=$_SESSION['splitpayment'];
}
else {
$arvalue1=$_SESSION['rcvdpayment'];
}

foreach ($arvalue1 as $value1){
if ($value1['METHOD'] != 'ONAC' && $value1['METHOD']!='PDC'){
$insert_ARCASHR=sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['csminvno'],
					$row_invdatetime[0],
					"0",
					"CASUAL SALE",
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$discount,
					$value1['AMOUNT'],
					date("Y-m-d H:i:s"),
					mysqli_real_escape_string($mysqli_link, $value1['METHOD'])
					);
$result=mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
$ararecv_amtpaid=$ararecv_amtpaid+$value1['AMOUNT'];
$refno=$value1['METHOD'];
}
}


//ARGST - stores GST info for easy further retreival
//one record per invoice
$insert_ARGST=sprintf("INSERT INTO ARGST (INVNO, INVDTE, CUSTNO, ITOTAL, GST, PROVTAX, GSTNO, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['csminvno'],
					$row_invdatetime[0],
					"0",
					$itotal,
					$tax,
					$ptax,
					$row_CRITDATA['HGSTNO'],
					$uni
					);
$result=mysqli_query($tryconnection, $insert_ARGST) or die(mysqli_error($mysqli_link));

$hxcats=array();
$petids=array();
//SALESCAT - stores details of the invoice - one record per each inline item + Other generated rows
foreach ($_SESSION['casual'] as $value2){
$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVGST, INVTAX, INVDISC, INVDOC, INVORDDOC, INVDESC, INVLGSM, INVREVCAT, INVDTE, INVNO, INVCUST, INVTNO, INVDECLINE, UNIQUE1) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', '%s')",
					"95",
					$value2['INVTOT'],
					$value2['INVGST'],
					$value2['INVTAX'],
					$value2['INVDISC'],
					mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
					mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
					mysqli_real_escape_string($mysqli_link, $value2['INVDESCR']),
					$value2['INVLGSM'],
					"95",
					$row_invdatetime[0],
					$_SESSION['csminvno'],
					"0",
					$value2['INVMIN'],
					$value2['INVDECLINE'],
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));
$hxcats[]=$value2['INVHXCAT'];
$petids[]=$value2['INVPET'];



/////////////////////////////////// DVMINV ////////////////////////////////

$insertSQL2 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVMIN, INVORDDOC, INVDOC, INVSTAFF, INVUNITS, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVTAX, INVFLAGS, INVVPC, IRADLOG, ISURGLOG, INARCLOG, IUAC, INVDECLINE, INVPRU, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  $value2['INVNO'],
								"0",
								"0",
							  $row_invdatetime[0],
							  "95",
							  $value2['INVMIN'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
							  mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
							  mysqli_real_escape_string($mysqli_link, $value2['INVSTAFF']),
							  $value2['INVUNITS'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDESCR']),
							  $value2['INVPRICE'],
							  $value2['INVTOT'],
							  "95",
							  $value2['INVTAX'],
							  $value2['INVFLAGS'],
							  $value2['INVVPC'],
							  $value2['IRADLOG'],
							  $value2['ISURGLOG'],
							  $value2['INARCLOG'],
							  $value2['IUAC'],
							  $value2['INVDECLINE'],
							  $value2['INVPRU'],
							  $uni
							  );
mysql_unbuffered_query($insertSQL2, $tryconnection);


///////////////////////////////////SOLD LIST and Inventory "Last Sale" update /////////////////////////////////////////////////////
	if (!empty($value2['INVVPC'])){
	$insert_INVSOLD = "INSERT INTO INVSOLD (INVVPC, INVDESC, INVUNITS) VALUES ('$value2[INVVPC]', '".mysqli_real_escape_string($mysqli_link, $value2['INVDESCR'])."', '$value2[INVUNITS]')";
	$INVSOLD = mysqli_query($mysqli_link, $insert_INVSOLD) or die(mysqli_error($mysqli_link));
	$VPC = $value2['INVVPC'] ;
	$update_INV = "UPDATE ARINVT SET LASTSALE = DATE(NOW()) WHERE VPARTNO = '$VPC'  LIMIT 1" ; 
	$INVENTORY = mysqli_query($tryconnection, $update_INV) or die(mysqli_error($mysqli_link)) ;
	$update_INV2 = "UPDATE ARINVT SET ONHAND = ONHAND - '$value2[INVUNITS]'  WHERE VPARTNO = '$VPC' AND MONITOR = 1 LIMIT 1" ; 
	$INVENTORY2 = mysqli_query($tryconnection, $update_INV2) or die(mysqli_error($mysqli_link)) ;
	}

}//foreach ($_SESSION['casual'] as $value2)



////////NON-INVOICE ITEMS INTO DVMINV AND SALESCAT///////////
//DISCOUNT IF ANY
if ($discount>0){
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0', '%s')",
 							  $_SESSION['casual'][0]['INVNO'],
								"0",
								"0",
							  $row_invdatetime[0],
							  "96",
							  "Discount",
							  -$discount,
							  -$discount,
							  "96",
							  $uni
							  );
mysql_unbuffered_query($insertSQL3, $tryconnection);

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVREVCAT, INVDESC,INVDECLINE, UNIQUE1) VALUES ('%s','%s','%s','%s','%s', '%s','%s','0', '%s')",
					"96",
					-$discount,
					$row_invdatetime[0],
					$_SESSION['casual'][0]['INVNO'],
					"0",
					"Discount",
					"96",
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));

}

//PST IF ANY
if ($ptax>0){
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0','%s')",
 							  $_SESSION['casual'][0]['INVNO'],
								"0",
								"0",
							  $row_invdatetime[0],
							  "92",
							  "PST",
							  $ptax,
							  $ptax,
							  "92",
							  $uni
							  );
mysql_unbuffered_query($insertSQL3, $tryconnection);

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s','%s','%s','%s','%s', '0', '%s')",
					"92",
					$ptax,
					$row_invdatetime[0],
					$_SESSION['casual'][0]['INVNO'],
								"0",
								"92",
								$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));
}//if ($ptax>0)

//GST
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0','%s')",
 							  $_SESSION['casual'][0]['INVNO'],
								"0",
								"0",
							  $row_invdatetime[0],
							  "90",
							  $taxname,
							  $tax,
							  $tax,
							  "90",
							  $uni
							  );
mysql_unbuffered_query($insertSQL3, $tryconnection);

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVDESC, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s','%s','%s','%s','%s', '%s', '0', '%s')",
					"90",
					$tax,
					$row_invdatetime[0],
								"0",
								"0",
								$taxname,
								"90",
								$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));

$subtotalcomment=array();

	//insert the invoice items
	foreach ($_SESSION['casual'] as $item) 
	{
		//format units into no-decimal number when it's XX.00
		if (number_format($item['INVUNITS'],0)==$item['INVUNITS']){
		$invunits = number_format($item['INVUNITS'],0);
		}
		else {
		$invunits = $item['INVUNITS'];
		}
	
		//makeup the TREATDESC out of the casual values for each invoice item
		$treatdesc=$invunits.";".$item['INVDESCR'].";".number_format($item['INVTOT'],2);
		$hcat=($item['INVHXCAT']+8192);
		$hsubcat="62";
		
		
	
		//subtotal comment
		if (!empty($item['INVOICECOMMENT']) && $item['HISTCOMM']=='1'){
			if (strlen($item['INVOICECOMMENT']) > 200){
			$howmany=ceil(strlen($item['INVOICECOMMENT'])/200);
				for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
				$subtotalcomment[]=substr($item['INVOICECOMMENT'],$i,200);
				}
			}
			else {
			$subtotalcomment[]=$item['INVOICECOMMENT'];
			}
		}
	
	
	}//foreach ($_SESSION['casual'] as $item)
	
  // and this is where the transaction ends for InnoDB purposes.
 COMMIT ;

	if (isset($_POST['prtsave'])){
	$_SESSION['printinvoice']='1';
	//header("Location:../../INDEX.php");
	}
$wingoback="window.open('../../IMAGES/CUSTOM_DOCUMENTS/CASUAL_INVOICE_PREVIEW.php?cangoindex=1','_blank');";
//header("Location:../../INDEX.php"); document.location='../../INDEX.php';
}//if (isset($_POST['save']) || isset($_POST['prtsave']))
}//if (isset($_SESSION['casual']))

// end of casual sale routine.


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


else if (isset($_SESSION['invline'])){

if (isset($_POST['save']) || isset($_POST['prtsave'])){

//$_SESSIONs:
//payments	- array of how much was received from the client
//methods 	- array of methods of payments
//onaccount	- if and how much was put on account 

// $INVtotal	= array_sum($INVtotal);
// $GSTtotal	= array_sum($GSTtotal);
// $PSTtotal	= array_sum($PSTtotal);
// $INVdiscount	= array_sum($INVdiscount);
// $TOTAL		= $INVtotal + $GSTtotal + $PSTtotal - $INVdiscount;
// $GrandTOTAL	= $TOTAL + $row_PATIENT_CLIENT['BALANCE'];


	$query_CRITDATA = "SELECT HGSTNO FROM CRITDATA LIMIT 1";
	$CRITDATA = mysqli_query($tryconnection, $query_CRITDATA) or die(mysqli_error($mysqli_link));
	$row_CRITDATA = mysqli_fetch_assoc($CRITDATA);
	
	$query_invdatetime="SELECT STR_TO_DATE('$_SESSION[minvdte]','%m/%d/%Y')";
	$invdatetime= mysqli_query($tryconnection, $query_invdatetime) or die(mysqli_error($mysqli_link));
	$row_invdatetime=mysqli_fetch_array($invdatetime);




$itotal = round($TOTAL,2);
$discount = 0;


           $chkdup = "SELECT INVNO,UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno] '" ;
           $get_dup = mysqli_query($tryconnection, $chkdup) or die(mysqli_error($mysqli_link)) ;
           $row_dup = mysqli_fetch_assoc($get_dup) ;


/******************CASE 1******************/
// The client has a zero credit balance, and the invoice amount is the same as the payment amount. 
// This also handles returns with cashback
/******************************************/
/******************************************/
if ($row_PATIENT_CLIENT['CREDIT'] == 0 && array_sum($_SESSION['payments']) == $itotal){

BEGIN ;
           if (empty($row_dup)) {
	
			//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
			if ($_SESSION['methods'][$key]!='ONAC'){
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$discount,
					$payment,
					$row_invdatetime[0],
					$_SESSION['methods'][$key]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			}//if ($_SESSION['methods'][$key]!='ONAC'){
			
			}//foreach ($_SESSION['payments'] as $key => $payment)
		
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					0);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;
       
             $petname = mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['PETNAME']);

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, INVORDDOC, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, PRTID, REFVET, REFCLIN, NPFEE, PDEAD, INVPET,UNIQUE1) 
			       VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' , '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['invline']['INVDOC'][0]),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					0,
					0,
					mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['REFVET']),
					mysqli_real_escape_string($mysqli_link, $row_PATIENT_CLIENT['REFCLIN']),
					0,
					0,
					$petname,
					$uni );
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
		}	
}//if ($row_PATIENT_CLIENT['CREDIT'] == 0 && array_sum($_SESSION['payments']) == $itotal){




/******************CASE 2******************/
//credit exists and payments are the amount of the invoice. Leave the credit extant.
/******************************************/
/******************************************/

else if ($itotal > 0 && $row_PATIENT_CLIENT['CREDIT'] > 0 && array_sum($_SESSION['payments']) == $itotal){

BEGIN ;

           if (empty($row_dup)) {
//pay off the actual invoice
			//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
			if ($_SESSION['methods'][$key]!='ONAC'){
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$discount,
					$payment,
					$row_invdatetime[0],
					$_SESSION['methods'][$key]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			}//if ($_SESSION['methods'][$key]!='ONAC'){
			
			}//foreach ($_SESSION['payments'] as $key => $payment)
		
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					0);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
		
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					0,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));

	}

}//if ($row_PATIENT_CLIENT['CREDIT'] != 0 && array_sum($_SESSION['payments']) == $itotal){


/******************CASE 3******************/
// Credit non existent, and payment less than the total
/******************************************/
/******************************************/
else if ($itotal > 0 && $row_PATIENT_CLIENT['CREDIT'] == 0 && array_sum($_SESSION['payments']) < $itotal){

BEGIN ;
           if (empty($row_dup)) {
            $credit = 0 ;
			$ibal=$itotal ;
			//- array_sum($_SESSION['payments']);
			$credit = array_sum($_SESSION['payments']);

			//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC' && $payment != 0 ){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0.00,
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$_SESSION['methods'][0] ,
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
			$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]'+$ibal-$credit, CREDIT = CREDIT + $credit WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));
		
 }
}//if ($row_PATIENT_CLIENT['CREDIT'] == 0 && array_sum($_SESSION['payments']) < $itotal){


/******************CASE 4 A******************/
/******************************************/
/******************************************/
//THIS IS A CASE WHERE THE CLINIC IS GIVING MONEY BACK

else if ($row_PATIENT_CLIENT['CREDIT'] > 0 && $row_PATIENT_CLIENT['CREDIT'] > $itotal  && array_sum($_SESSION['payments']) < 0){


BEGIN ;

           if (empty($row_dup)) {
//IBAL = 0 because the invoice will be paid off entirely from the deposit

			$ibal=0;
//figure out the total cash handed back 
$cashback = array_sum($_SESSION['payments']) ;

			//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC'){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)


			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$discount,
					$itotal - $cashback,
					$row_invdatetime[0],
					"DEP.AP");
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));

			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$discount,
					-($itotal - $cashback),
					$row_invdatetime[0],
					"DEP.AP");
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));						
	
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					'DEP.AP.',
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					$itotal,
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					$itotal,
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
	$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]'+$itotal-".array_sum($_SESSION['payments'])." WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
	$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));

	$query_CREDIT = "UPDATE ARCUSTO SET CREDIT=CREDIT-$itotal+".array_sum($_SESSION['payments'])." WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
	$CREDIT = mysqli_query($tryconnection, $query_CREDIT) or die(mysqli_error($mysqli_link));
		
 }
}//if (array_sum($_SESSION['payments']) < $GrandTOTAL && array_sum($_SESSION['payments']) != $itotal){


/******************CASE 4B******************/
// There is a credit, equal to or greater than the qmount of this bill, and the payments are Zero. Put through a charge invoice, and leave the operators to use up the credit to pay it off.
/******************************************/
/******************************************/
else if ($row_PATIENT_CLIENT['CREDIT'] >= $itotal && array_sum($_SESSION['payments']) == 0 ) {

BEGIN ;

           if (empty($row_dup)) {
//treat the actual invoice as usual with zero payment.
			$ibal=$itotal ;
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0.00,
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
			$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]'+$ibal WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));

	
		}
}//if ($row_PATIENT_CLIENT['CREDIT'] == $itotal && array_sum($_SESSION['payments']) == 0 )


/******************CASE 4******************/
// There is a credit, and there are payments but less than this invoice. Leave this invoice, then put the payments against the credit
/******************************************/
/******************************************/
else if ($row_PATIENT_CLIENT['CREDIT'] > 0 && array_sum($_SESSION['payments']) < $itotal && array_sum($_SESSION['payments']) > 0 ) {
BEGIN;

           if (empty($row_dup)) {
//treat the actual invoice as a charge.
			$ibal = $itotal;
			$credit = array_sum($_SESSION['payments']);

			//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC' && $payment != 0 ){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					'DEP.',
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)
		
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					'  ',
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0.00,
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]'" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
			$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]'+$ibal -$credit, CREDIT = CREDIT + $credit WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));
			

		}
}//if (array_sum($_SESSION['payments']) < $GrandTOTAL && array_sum($_SESSION['payments']) != $itotal)


/******************CASE 4B******************/
// There is a credit, less than this invoice, and there is no payment. Put the invoice through as a charge, 
/******************************************/
/******************************************/
else if ($row_PATIENT_CLIENT['CREDIT'] > 0 && $row_PATIENT_CLIENT['CREDIT'] < $itotal && array_sum($_SESSION['payments'])  == 0 ) {
echo 'got to here' ;
BEGIN ;

           if (empty($row_dup)) {
//treat the actual invoice as usual with no payment.

			$ibal=$itotal ;
		    $zero = 0.00 ;
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					$zero,
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_POST['ponum']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					$zero,
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
			$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]'+$ibal WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));

		}
}//if ($row_PATIENT_CLIENT['CREDIT'] > 0 && $row_PATIENT_CLIENT['CREDIT'] < $itotal && array_sum($_SESSION['payments'])  == 0 )


/******************CASE 5******************/
// Overpayment of an invoice. Enter this one as unpaid, and accumulate the payment in the credit (deposit) field of arcusto.
/******************************************/
/******************************************/
else if ($itotal > 0 && array_sum($_SESSION['payments']) > $itotal){

BEGIN ;

           if (empty($row_dup)) {
		//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC'){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)
			//ARARECV - insert the invoice into receivables as unpaid
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0,
					$itotal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]'" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;
		     
 $remainingpayment = array_sum($_SESSION['payments']);
 $icredit = $row_PATIENT_CLIENT['CREDIT'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0, 
					$itotal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));

			
			$ibalance = $itotal + $_SESSION['prevbal'] - array_sum($_SESSION['payments']) ;
			$icredit = $icredit + $remainingpayment ;
			
			$query_CREDIT = "UPDATE ARCUSTO SET BALANCE=$ibalance, CREDIT=$icredit WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$CREDIT = mysqli_query($tryconnection, $query_CREDIT) or die(mysqli_error($mysqli_link));

  }
} //if (array_sum($_SESSION['payments']) < $GrandTOTAL && array_sum($_SESSION['payments']) != $itotal){


/****************CASE 5B*******************/
/******************************************/
/******************************************/

// The operator puts through a zero invoice, with a payment for old invoices

else if ($itotal == 0 && array_sum($_SESSION['payments']) > $itotal) {

BEGIN ;		
 
           if (empty($row_dup)) {
//ARCASHR
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC'){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)


			//ARARECV - insert the invoice into receivables as unpaid
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0,
					$itotal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
			
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;
		     
 $remainingpayment = array_sum($_SESSION['payments']);
 $icredit = $row_PATIENT_CLIENT['CREDIT'] ;			
 
 //ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0, 
					$itotal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));

			
			$ibalance = $itotal + $_SESSION['prevbal'] - array_sum($_SESSION['payments']) ;
			$icredit = $icredit + $remainingpayment ;
			
			$query_CREDIT = "UPDATE ARCUSTO SET BALANCE=$ibalance, CREDIT=$icredit WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$CREDIT = mysqli_query($tryconnection, $query_CREDIT) or die(mysqli_error($mysqli_link));
 }
}  //$itotal = 0 && array_sum($_SESSION['payments']) > $itotal
/****************CASE 5C*******************/
/******************************************/
/******************************************/

// The client returns something,(so a negative invoice)  and makes payment(s) on old invoices

else if ($itotal < 0 && array_sum($_SESSION['payments']) > 0 ) {

BEGIN ;		

           if (empty($row_dup)) {
//ARCASHR Treat the payment(s) as a deposit.
			foreach ($_SESSION['payments'] as $key => $payment){
			
	
			if ($_SESSION['methods'][$key] != 'ONAC'){
			
			$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DISCOUNT, AMTPAID, DTEPAID) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					'DEP.' ,
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][$key],
					$discount,
					$payment,
					$row_invdatetime[0]);
			mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
			
			}//if ($_SESSION['methods'][$key]) != 'ONAC'){
			}//foreach ($_SESSION['payments'] as $key => $payment)


			//ARARECV - insert the invoice into receivables as unpaid
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0,
					$itotal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
			
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno]' LIMIT 1" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;
		     
            $remainingpayment = array_sum($_SESSION['payments']);
 
 //ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					0, 
					$itotal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));

			
			$ibalance = $itotal + $_SESSION['prevbal'] - array_sum($_SESSION['payments']) ;
		
			
            $icredit = $row_PATIENT_CLIENT['CREDIT'] ;
            
			$query_CREDIT = "UPDATE ARCUSTO SET BALANCE=$ibalance, CREDIT = $icredit + $remainingpayment  WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$CREDIT = mysqli_query($tryconnection, $query_CREDIT) or die(mysqli_error($mysqli_link));
  }
}  //$itotal < 0 && array_sum($_SESSION['payments']) > 0 

/******************CASE 6 no payment, possibly a zero invoice.******************/
/******************************************/
/******************************************/

else if (array_sum($_SESSION['payments']) == 0){
  BEGIN;
  
           if (empty($row_dup)) {
			$ibal=$itotal; //- array_sum($_SESSION['payments']);
	
			//ARARECV
			$insert_ARARECV=sprintf("INSERT INTO ARARECV (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL) 
			        VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					$ibal);
			mysqli_query($tryconnection, $insert_ARARECV) or die(mysqli_error($mysqli_link));
			
			// now get the unique number the system has assigned to this receivable, so that it can be put into the invoice record.
		     $GET_UNIQUE1 = "SELECT UNIQUE1 FROM ARARECV WHERE INVNO = '$_SESSION[minvno] '" ;
		     $FOR_INVOICE = mysqli_query($tryconnection, $GET_UNIQUE1) or die(mysqli_error($mysqli_link)) ;
		     $row_ARFORIN = mysqli_fetch_assoc($FOR_INVOICE) ;
		     $uni = $row_ARFORIN['UNIQUE1'] ;

			//ARINVOI
			$insert_ARINVOI=sprintf("INSERT INTO ARINVOI (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, REFNO, DTEPAID, TAX, PTAX, ITOTAL, DISCOUNT, AMTPAID, IBAL, UNIQUE1) 
			        VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s' )",
					$_SESSION['minvno'],
					$row_invdatetime[0],
					$_SESSION['client'],
					mysqli_real_escape_string($mysqli_link, $_POST['company']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['staff']),
					mysqli_real_escape_string($mysqli_link, $_SESSION['petname']),
					$_SESSION['methods'][0],
					$row_invdatetime[0],
					$GSTtotal,
					$PSTtotal,
					$itotal,
					$discount,
					array_sum($_SESSION['payments']),
					$ibal,
					$uni);
			mysqli_query($tryconnection, $insert_ARINVOI) or die(mysqli_error($mysqli_link));
	
			$query_BALANCE = "UPDATE ARCUSTO SET BALANCE = '$_SESSION[prevbal]' + $ibal WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
			$BALANCE = mysqli_query($tryconnection, $query_BALANCE) or die(mysqli_error($mysqli_link));
 }
}//else if (array_sum($_SESSION['payments']) == 0){


// and now do the rest of the files, as cash, receivables and invoice files are done
// Start with Autopay by finding whether there are any outstanding receivables, and any credit available.


           if (empty($row_dup)) {
           
$query_AR = "SELECT SUM(IBAL) AS BALANCE FROM ARARECV WHERE CUSTNO = '$_SESSION[client]' AND IBAL > 0 " ;
$ISAR = mysqli_query($tryconnection, $query_AR) or die(mysqli_error($mysqli_link)) ;
$row_balance = mysqli_fetch_assoc($ISAR) ;
$balance=$row_balance['BALANCE'] ;
$query_CRED="SELECT CREDIT FROM ARCUSTO WHERE CUSTNO = '$_SESSION[client]' LIMIT 1" ;
$ISCRED = mysqli_query($tryconnection, $query_CRED) or die(mysqli_error($mysqli_link)) ;
$row_credit = mysqli_fetch_assoc($ISCRED) ;
$credit=$row_credit['CREDIT'] ;


// If there is any credit left, and there are positive receivable(s), go to it.

	
			if ($credit > 0 ){
		  $remainingpayment = $credit ;	
		  $refno = 'DEP.AP.' ;
		  $query_ARARECV2="SELECT * FROM ARARECV WHERE CUSTNO='$_SESSION[client]' AND IBAL > 0 ORDER BY INVDTE, UNIQUE1 ASC";
				$ARARECV2=mysqli_query($tryconnection, $query_ARARECV2) or die(mysqli_error($mysqli_link));
				$row_ARARECV2=mysqli_fetch_assoc($ARARECV2);
// and AUTOPAY
				do {
					
					if ($remainingpayment <= $row_ARARECV2['IBAL']){
						$amtpaid = $remainingpayment;
					}
					else  {
						$amtpaid = $row_ARARECV2['IBAL'];
					}
					
				    $credit = $credit - $amtpaid ;
					if ($amtpaid > 0) {
					$update_ARARECV2="UPDATE ARARECV SET AMTPAID=AMTPAID+$amtpaid, IBAL=(IBAL-$amtpaid), DTEPAID='$row_invdatetime[0]',
					     REFNO = '$refno' WHERE INVNO='$row_ARARECV2[INVNO]' AND UNIQUE1 = '$row_ARARECV2[UNIQUE1]' LIMIT 1";
					mysqli_query($tryconnection, $update_ARARECV2) or die(mysqli_error($mysqli_link));
				
					$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) 
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
							$row_ARARECV2['INVNO'],
							$row_ARARECV2['INVDTE'],
							$row_ARARECV2['CUSTNO'],
							mysqli_real_escape_string($mysqli_link, $row_ARARECV2['COMPANY']),
							mysqli_real_escape_string($mysqli_link, $_POST['salesmn']),
							mysqli_real_escape_string($mysqli_link, $row_ARARECV2['PONUM']),
							$row_ARARECV2['DISCOUNT'],
							$amtpaid,
							$row_invdatetime[0],
							$refno);
					mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));
					
					$insert_ARCASHR = sprintf("INSERT INTO ARCASHR (INVNO, INVDTE, CUSTNO, COMPANY, SALESMN, PONUM, DISCOUNT, AMTPAID, DTEPAID, REFNO) 
					VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
							$row_ARARECV2['INVNO'],
							$row_ARARECV2['INVDTE'],
							$row_ARARECV2['CUSTNO'],
							mysqli_real_escape_string($mysqli_link, $row_ARARECV2['COMPANY']),
							mysqli_real_escape_string($mysqli_link, $_POST['salesmn']),
							mysqli_real_escape_string($mysqli_link, $row_ARARECV2['PONUM']),
							$row_ARARECV2['DISCOUNT'],
							-$amtpaid,
							$row_invdatetime[0],
							$refno);
					mysqli_query($tryconnection, $insert_ARCASHR) or die(mysqli_error($mysqli_link));

					$remainingpayment=$remainingpayment - $amtpaid;

					}//if ($amtpaid > 0){
				} while ($row_ARARECV2=mysqli_fetch_assoc($ARARECV2) );
			
			
			$update_ARCUSTO = "UPDATE ARCUSTO SET CREDIT='$remainingpayment' WHERE CUSTNO='$_SESSION[client]' LIMIT 1 ";
			$RESULT = mysqli_query($tryconnection, $update_ARCUSTO) or die(mysqli_error($mysqli_link));
			
}

// to here
//ARGST
$insert_ARGST=sprintf("INSERT INTO ARGST (INVNO, INVDTE, CUSTNO, ITOTAL, GST, PROVTAX, GSTNO, UNIQUE1) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s','%s')",
		$_SESSION['minvno'],
		$row_invdatetime[0],
		$_SESSION['client'],
		$itotal,
		$GSTtotal,
		$PSTtotal,
		$row_CRITDATA['HGSTNO'],
		$uni);
mysqli_query($tryconnection, $insert_ARGST) or die(mysqli_error($mysqli_link));




$hxcats=array();
$petids=array();
$_SESSION['certifpetids']=array();

// Initialise a sequence number so that reprints of the invoice appear in the same order as the original.
// Then dump out the entire array with each line item

$invseq = 0 ;
foreach ($_SESSION['invline'] as $value2){
$invseq ++ ;

//SALESCAT - stores details of the invoice - one record per each inline item + Other generated rows
$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVGST, INVTAX, INVDISC, INVDOC, INVORDDOC, INVDESC, INVLGSM, INVREVCAT, INVDTE, INVNO, INVCUST, INVTNO, INVDECLINE, INVSTAT, UNIQUE1) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
					$value2['INVMAJ'],
					$value2['INVTOT'],
					$value2['INVGST'],
					$value2['INVTAX'],
					$value2['INVDISC'],
					mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
					mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
					mysqli_real_escape_string($mysqli_link, $value2['INVDESCR']),
					$value2['INVLGSM'],
					$value2['INVREVCAT'],
					$row_invdatetime[0],
					$value2['INVNO'],
					$value2['INVCUST'],
					$value2['INVMIN'],
					$value2['INVDECLINE'],
					$value2['INVSTAT'],
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));
$hxcats[]=$value2['INVHXCAT'];
$petids[]=$value2['INVPET'];

/////////////////////////////////////////////////////////////////////////////

/////////////////////////////////// DVMINV ////////////////////////////////

$insertSQL2 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVMIN, INVORDDOC, INVDOC, INVSTAFF, INVUNITS, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVTAX, INVFLAGS, INVVPC, IRADLOG, ISURGLOG, INARCLOG, IUAC, INVDECLINE, PETNAME, INVPRU, INVOICECOMMENT, LCOMMENT, INVLGSM, INVSTAT, INVNOHST, INVSEQ, UNIQUE1) 
VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
 							  $value2['INVNO'],
							  $value2['INVCUST'],
							  $value2['INVPET'],
							  $row_invdatetime[0],
							  $value2['INVMAJ'],
							  $value2['INVMIN'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
							  mysqli_real_escape_string($mysqli_link, $value2['INVDOC']),
							  mysqli_real_escape_string($mysqli_link, $value2['INVSTAFF']),
							  $value2['INVUNITS'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVDESCR']),
							  $value2['INVPRICE'],
							  $value2['INVTOT'],
							  $value2['INVREVCAT'],
							  $value2['INVTAX'],
							  $value2['INVFLAGS'],
							  $value2['INVVPC'],
							  $value2['IRADLOG'],
							  $value2['ISURGLOG'],
							  $value2['INARCLOG'],
							  $value2['IUAC'],
							  $value2['INVDECLINE'],
							  mysqli_real_escape_string($mysqli_link, $value2['PETNAME']),
							  $value2['INVPRU'],
							  mysqli_real_escape_string($mysqli_link, $value2['INVOICECOMMENT']),
							  mysqli_real_escape_string($mysqli_link, $value2['LCOMMENT']),
					          $value2['INVLGSM'],
					          $value2['INVNOHST'],
					          $value2['INVSTAT'],
					          $invseq,
							  $uni
							  );
mysql_unbuffered_query($insertSQL2, $tryconnection);




///////////////////////////////////SOLD LIST and Inventory "Last Sale" and onhand updates /////////////////////////////////////////////////////
	if (!empty($value2['INVVPC'])){
	$insert_INVSOLD = "INSERT INTO INVSOLD (INVVPC, INVDESC, INVUNITS) VALUES ('$value2[INVVPC]', '".mysqli_real_escape_string($mysqli_link, $value2['INVDESCR'])."', '$value2[INVUNITS]')";
	$INVSOLD = mysqli_query($mysqli_link, $insert_INVSOLD) or die(mysqli_error($mysqli_link));
	$VPC = $value2['INVVPC'] ;
	$update_INV = "UPDATE ARINVT SET LASTSALE = DATE(NOW()) WHERE VPARTNO = '$VPC'  LIMIT 1" ; 
	$INVENTORY = mysqli_query($tryconnection, $update_INV) or die(mysqli_error($mysqli_link)) ;
	$update_INV2 = "UPDATE ARINVT SET ONHAND = ONHAND - '$value2[INVUNITS]'  WHERE VPARTNO = '$VPC' AND MONITOR = 1 LIMIT 1" ; 
	$INVENTORY2 = mysqli_query($tryconnection, $update_INV2) or die(mysqli_error($mysqli_link)) ;
	}
//////////////////////////////////////////////////////////////////////////////
// Here are the patient updates
if (!empty($value2['INVFLAGS'])){
	
	if(substr($value2['INVFLAGS'],15,1)){ //Annual exam,no years
	$update_DATES = "UPDATE PETMAST SET POTH8='$row_invdatetime[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  //$_SESSION['prtcertificate4']='4';
	 }
	if(substr($value2['INVFLAGS'],5,1)){ //Heartworm, years default to 1
	$update_DATES = "UPDATE PETMAST SET POTHFOR='$row_invdatetime[0]', POTH04YEARS='1' WHERE  PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	 //$_SESSION['prtcertificate']='1';
	 } 
	 
	if (substr($value2['INVFLAGS'],0,1)==1  &&  strpos($value2['INVDESCR'], " Dur ") > 1) { //Rabies, years from INVDESCR
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$rabserum=explode("(",$value2['INVDESCR']);
	$update_DATES = "UPDATE PETMAST SET PRABDAT='$row_invdatetime[0]', PRABYEARS='$years[1]', PRABSER='$rabserum[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	 $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
		}   
	//rabtag number
	if (substr($value2['INVDESCR'],0,10)=="Rabies Tag"){
	$rabtag=explode(' ',$value2['INVDESCR']);
	$rabtag=array_reverse($rabtag);
	$update_DATES = "UPDATE PETMAST SET PRABLAST = PRABTAG, PRABTAG='$rabtag[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));	
	}  
	  
	if(substr($value2['INVFLAGS'],6,1)){//fecal, no years
	$update_DATES = "UPDATE PETMAST SET POTHFIV='$row_invdatetime[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  }
	  
	if (substr($value2['INVFLAGS'],1,1) == 1 && strpos($value2['INVDESCR'], " Dur ") > 1 ) {  //Da2P, FVRCP for dogs and cats, others for other species.
	
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTHDAT='$row_invdatetime[0]', POTHYEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	// check for the kitten and puppies vacount flags.
	$is_PUPKIT = $row_PATIENT_CLIENT['PVACOUNT'] ;
	  if ($is_PUPKIT < 3) {
		//date of birth
			$mpdob=strtotime($row_PATIENT_CLIENT['PDOB']);
			//today's date
			$now = mktime(date('m/d/Y')) ;
			//TODAY - DOB transformed into number of seconds
			$diff = round(($now - $mpdob) / (60*60*24)) ;
			
			//under 5 months it is in for kitten/puppy shots
			if ($diff < 155) { 
			$is_PUPKIT++ ;
			$update_PVACOUNT = "UPDATE PETMAST SET PVACOUNT = '$is_PUPKIT' WHERE PETID='$value2[INVPET]' LIMIT 1" ;
			mysqli_query($tryconnection, $update_PVACOUNT) or die(mysqli_error($mysqli_link)) ;			
			} 
	   }
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  } 
	if(substr($value2['INVFLAGS'],13,1)){// neuter, no years
	$update_DATES = "UPDATE PETMAST SET PNEUTER='1' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate2']='2';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids2'])){
	 $_SESSION['certifpetids2'][]=$value2['INVPET'];}
	  } 
	if (substr($value2['INVFLAGS'],2,1) == 1 && strpos($value2['INVDESCR'], " Dur ") > 1) { 
//	   strpos($value2['INVDESCR'], "Lepto") > 0)){//fel leuk, Lepto
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ; 
	 }
	$update_DATES = "UPDATE PETMAST SET PLEUKDAT='$row_invdatetime[0]', PLEUKYEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  } 
//	if(substr($value2['INVFLAGS'],10,1)){
//	$update_DATES = "UPDATE PETMAST SET PXRAYFILE='$row_invdatetime[0]' WHERE PETID='$value2[INVPET]'";
//	mysql_query($update_DATES, $tryconnection) or die(mysql_error());
//	  } 
	if(substr($value2['INVFLAGS'],3,1)== 1 && strpos($value2['INVDESCR'], " Dur ") > 0){//Corona
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTHTWO='$row_invdatetime[0]', POTH02YEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  } 
	if(substr($value2['INVFLAGS'],11,1)){  //Microchip
	$tatno=explode(' ',$value2['INVDESCR']);
	$update_DATES = "UPDATE PETMAST SET PTATNO='$tatno[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  } 
	if(substr($value2['INVFLAGS'],4,1)== 1 && strpos($value2['INVDESCR'], " Dur ") > 0){//Parvo, FIP
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTHTHR='$row_invdatetime[0]', POTH03YEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  }
	if(substr($value2['INVFLAGS'],14,1)){
	$pweight=explode(' ',$value2['INVDESCR']);
	$update_DATES = "UPDATE PETMAST SET PWEIGHT='$pweight[0]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  } 
	if(substr($value2['INVFLAGS'],7,1)==1  &&  strpos($value2['INVDESCR'], " Dur ") > 1){//Bordetella, FIV
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTHSIX='$row_invdatetime[0]', POTH06YEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  } 
	if(substr($value2['INVFLAGS'],8,1)){ //neuter
	$update_DATES = "UPDATE PETMAST SET PNEUTER='1' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate2']='2';
	  } 
	if(substr($value2['INVFLAGS'],9,1)){ //deceased
	$update_DATES = "UPDATE PETMAST SET PDEADATE='$row_invdatetime[0]', PDEAD='1' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
//	  $_SESSION['prtcertificate3']='3';
	  } 
	if(substr($value2['INVFLAGS'],12,1)){//Lyme,Declaw,Magnet
		if ($value2['INVLGSM']=='1' &&  strpos($value2['INVDESCR'], " Dur ") > 1){
	        $years=explode(' ',$value2['INVDESCR']);
	        $years=array_reverse($years);
			$update_DATES = "UPDATE PETMAST SET POTHSEV='$row_invdatetime[0]', POTH07YEARS ='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
			mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
			$_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
			}
		else if ($value2['INVLGSM']=='2'){
			$update_DATES = "UPDATE PETMAST SET PDECLAW='1' WHERE PETID='$value2[INVPET]' LIMIT 1";
			mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));			
			}
		else if ($value2['INVLGSM']=='4'){
			$update_DATES = "UPDATE PETMAST SET PMAGNET='1' WHERE PETID='$value2[INVPET]' LIMIT 1";
			mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));			
			}
	  } 
	if(substr($value2['INVFLAGS'],16,1) == 1 && strpos($value2['INVDESCR'], " Dur ") > 0){//Distemper
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTH9='$row_invdatetime[0]', POTH09YEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  } 
	if(substr($value2['INVFLAGS'],17,1)==1  &&  strpos($value2['INVDESCR'], " Dur ") > 1){//Giardia
	$years=explode(' ',$value2['INVDESCR']);
	$years=array_reverse($years);
	if ($years[1] < 1 || $years[1] > 4) {
	$years[1] = 1 ;
	 }
	$update_DATES = "UPDATE PETMAST SET POTH10='$row_invdatetime[0]', POTH10YEARS='$years[1]' WHERE PETID='$value2[INVPET]' LIMIT 1";
	mysqli_query($tryconnection, $update_DATES) or die(mysqli_error($mysqli_link));
	  $_SESSION['prtcertificate']='1';
	 if (!in_array($value2['INVPET'],$_SESSION['certifpetids'])){
	 $_SESSION['certifpetids'][]=$value2['INVPET'];}
	  }
 }//if (!empty($value2['INVFLAGS']))

}//foreach ($_SESSION['invline'] as $value2)


////////NON-INVOICE ITEMS INTO DVMINV AND SALESCAT///////////
//DISCOUNT IF ANY
if ($INVdiscount>0){
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, INVSEQ, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  $row_invdatetime[0],
							  "96",
							  "Discount",
							  -$INVdiscount,
							  -$INVdiscount,
							  "96",
							  "996",
							  $uni
							  );
mysqli_query($tryconnection, $insertSQL3) or die(mysqli_error($mysqli_link));

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVDESC, INVREVCAT,INVDECLINE,UNIQUE1) VALUES ('%s','%s','%s','%s','%s','%s', '%s', '0', '%s')",
					"96",
					-$INVdiscount,
					$row_invdatetime[0],
					$_SESSION['invline'][0]['INVNO'],
					$_SESSION['invline'][0]['INVCUST'],
					"Discount",
					"96",
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));

}

//PST IF ANY
if ($PSTtotal>0){
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, INVSEQ, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  $row_invdatetime[0],
							  "92",
							  "PST",
							  $PSTtotal,
							  $PSTtotal,
							  "92",
							  "997",
							  $uni
							  );
mysqli_query($tryconnection, $insertSQL3)  or die(mysqli_error($mysqli_link));

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVDESC, INVREVCAT, INVDECLINE, UNIQUE1) VALUES ('%s','%s','%s','%s','%s', '%s', '%s', '0', '%s')",
					"92",
					$PSTtotal,
					$row_invdatetime[0],
					$_SESSION['invline'][0]['INVNO'],
					$_SESSION['invline'][0]['INVCUST'],
					"PST",
					"92",
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));
}//if ($ptax>0)

//GST
$insertSQL3 = sprintf("INSERT INTO DVMINV (INVNO, INVCUST, INVPET, INVDATETIME, INVMAJ, INVDESCR, INVPRICE, INVTOT, INVREVCAT, INVDECLINE, INVSEQ, UNIQUE1) VALUES ('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '0', '%s', '%s')",
 							  $_SESSION['invline'][0]['INVNO'],
							  $_SESSION['invline'][0]['INVCUST'],
							  $_SESSION['invline'][0]['INVPET'],
							  $row_invdatetime[0],
							  "90",
							  $taxname,
							  $GSTtotal,
							  $GSTtotal,
							  "90",
							  "998",
							  $uni
							  );
mysqli_query($tryconnection, $insertSQL3)  or die(mysqli_error($mysqli_link));

$insert_SALESCAT=sprintf("INSERT INTO SALESCAT (INVMAJ, INVTOT, INVDTE, INVNO, INVCUST, INVDESC, INVDECLINE, INVREVCAT, UNIQUE1) VALUES ('%s','%s','%s','%s','%s', '%s','0', '%s', '%s')",
					"90",
   				    $GSTtotal,
					$row_invdatetime[0],
					$_SESSION['invline'][0]['INVNO'],
					$_SESSION['invline'][0]['INVCUST'],
					$taxname,
					"90",
					$uni
					);
$result=mysqli_query($tryconnection, $insert_SALESCAT) or die(mysqli_error($mysqli_link));


COMMIT ;



///////////////////////////////////HISTORY/////////////////////////////////////////////////////
$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysqli_query($tryconnection, $query_PREFER) or die(mysqli_error($mysqli_link));
$row_PREFER = mysqli_fetch_assoc($PREFER);

$treatmxx=$_SESSION['client']/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

	$query_CHECKTABLE="SELECT * FROM $treatmxx LIMIT 1";
	$CHECKTABLE= mysqli_query($tryconnection, $query_CHECKTABLE) or $none=1;
	
	if (isset($none)){
	$create_TREATMXX="CREATE TABLE $treatmxx LIKE TREATM0";
	$result=mysqli_query($tryconnection, $create_TREATMXX) or die(mysqli_error($mysqli_link));
	}

$petids=array_unique($petids);
$hxcats=array_unique($hxcats);
$sumhxcats=(array_sum($hxcats)+8192);



//insertion only for the specific pet individually
foreach ($petids as $pet){
//DELETE FROM RECEP FILE
$query_discharge="DELETE FROM RECEP WHERE RFPETID='$pet'";
$discharge=mysqli_query($tryconnection, $query_discharge) or die(mysqli_error($mysqli_link));

//insert the heading with the invoice #
$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$client','$pet','INVOICE #$value2[INVNO]', $sumhxcats,'61', '".mysqli_real_escape_string($mysqli_link, $value2['INVDOC'])."', '$row_invdatetime[0]')";
mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));

$subtotalcomment=array();

	//isert the invoice items
	foreach ($_SESSION['invline'] as $item) 
	{
	if ($item['INVPET']==$pet && $item['INVEST']=='0'){
		
		//if it is a serums
		if ($item['INVSERUM']=='2'){
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','".mysqli_real_escape_string($mysqli_link, $item['INVDESCR'])."', $hcat,'64','$item[INVMAJ]', '".mysqli_real_escape_string($mysqli_link, $item['INVSTAFF'])."', '$row_invdatetime[0]')";
	mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}//if ($item['INVSERUM']=='2')
		else {	
		//format units into no-decimal number when it's XX.00
		if (number_format($item['INVUNITS'],0)==$item['INVUNITS']){
		$invunits = number_format($item['INVUNITS'],0);
		}
		else {
		$invunits = $item['INVUNITS'];
		}
	
		//makeup the TREATDESC out of the invline values for each invoice item
		$treatdesc=$invunits.";".$item['INVDESCR'].";".number_format($item['INVTOT'],2);
		$hcat=($item['INVHXCAT']+8192);
		$hsubcat="62";
		
		//if it is an inline note
		if ($item['MEMO']=='1'){ 
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','".mysqli_real_escape_string($mysqli_link, $item['INVDESCR'])."', $hcat,'66','$item[INVMAJ]', '".mysqli_real_escape_string($mysqli_link, $item['INVSTAFF'])."', '$row_invdatetime[0]')";
	mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}
		//if it is a declined item
		else if ($item['INVDECLINE']=='1'){
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','".mysqli_real_escape_string($mysqli_link, $treatdesc)."', $hcat,'67','$item[INVMAJ]', '".mysqli_real_escape_string($mysqli_link, $item['INVDOC'])."', '$row_invdatetime[0]')";
		mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}
		else {// if it is an invoice item with a $ value 		
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','".mysqli_real_escape_string($mysqli_link, $treatdesc)."', $hcat,'$hsubcat','$item[INVMAJ]', '".mysqli_real_escape_string($mysqli_link, $item['INVSTAFF'])."', '$row_invdatetime[0]')";
		mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}
		}
		//label
		if ($item['INVPRU']=='1' && !empty($item['LCOMMENT'])){
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, HTMAJ, WHO, TREATDATE) VALUES ('$item[INVCUST]','$item[INVPET]','".mysqli_real_escape_string($mysqli_link, $item['LCOMMENT'])."', $hcat,'63','$item[INVMAJ]', '".mysqli_real_escape_string($mysqli_link, $item['INVSTAFF'])."', '$row_invdatetime[0]')";
	mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}
	
		//subtotal comment
		if (!empty($item['INVOICECOMMENT']) && $item['HISTCOMM']=='1'){
			if (strlen($item['INVOICECOMMENT']) > 200){
			$howmany=ceil(strlen($item['INVOICECOMMENT'])/200);
				for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
				$subtotalcomment[]=substr($item['INVOICECOMMENT'],$i,200);
				}
			}
			else {
			$subtotalcomment[]=$item['INVOICECOMMENT'];
			}
		}
	
	
	}//if ($item['INVPET']==$pet)
	}//foreach ($_SESSION['invline'] as $item)

	//insert collected subtotal comments (65)
	foreach ($subtotalcomment as $subtcom){
		$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$item[INVCUST]','$pet','".mysqli_real_escape_string($mysqli_link, $subtcom)."', $sumhxcats,'65', '".mysqli_real_escape_string($mysqli_link, $value2['INVSTAFF'])."', '$row_invdatetime[0]')";
		mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
		}
	 
	if ($pet > 0) {
		$query_PETHOLD = "SELECT * FROM PETHOLD WHERE PHPETID=$pet AND MEDINV='1'";
		$PETHOLD = mysqli_query($tryconnection, $query_PETHOLD) or die(mysqli_error($mysqli_link));
		$row_PETHOLD = mysqli_fetch_assoc($PETHOLD);
			if (!empty($row_PETHOLD['SUBTCOM'])){
			$splitasterisks=explode('*',$row_PETHOLD['SUBTCOM']);
			foreach ($splitasterisks as $splitcomment){
				$subtotalcomment=array();
				if (strlen($splitcomment) > 200){
				$howmany=ceil(strlen($splitcomment)/200);
				for ($i=0; $i<($howmany*200); $i=($i/200+1)*200){
				$subtotalcomment[]=substr($splitcomment,$i,200);
				}
				}
				else {
				$subtotalcomment[]=$splitcomment;
				}
				
				foreach ($subtotalcomment as $subtcom){
				$insertSQL = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, WHO, TREATDATE) VALUES ('$item[INVCUST]','$pet','".mysqli_real_escape_string($mysqli_link, $subtcom)."', $sumhxcats,'65', '".mysqli_real_escape_string($mysqli_link, $value2['INVSTAFF'])."', '$row_invdatetime[0]')";
				mysqli_query($tryconnection, $insertSQL)  or die(mysqli_error($mysqli_link));
				}//foreach ($subtotalcomment as $subtcom)
			  }
			
			}//if (!empty($row_PETHOLD['SUBTCOM']))
$query_PLASTDATE = "UPDATE PETMAST SET PLASTDATE=NOW() WHERE PETID=$pet LIMIT 1";
$PLASTDATE = mysqli_query($tryconnection, $query_PLASTDATE) or die(mysqli_error($mysqli_link));
$query_INVNO_PETHOLD = "UPDATE PETHOLD SET PHINVNO='$_SESSION[minvno]' WHERE PHPETID=$pet";
$INVNO_PETHOLD = mysqli_query($tryconnection, $query_INVNO_PETHOLD) or die(mysqli_error($mysqli_link));
//$query_PETHOLD = "DELETE FROM PETHOLD WHERE PHPETID=$pet";
//$PETHOLD = mysql_query($query_PETHOLD, $tryconnection) or die(mysql_error());
} // if $pet > 0 
}//foreach ($petid as $pet)

//delete from INVHOLD
$lock_it = "LOCK TABLES INVHOLD WRITE, RECEP WRITE, ARCUSTO WRITE" ;  
$Qlock = mysqli_query($tryconnection, $lock_it) or die(mysqli_error($mysqli_link)) ;
$deleteSQL = "DELETE  FROM INVHOLD WHERE INVCUST='$_SESSION[client]'";
mysqli_query($tryconnection, $deleteSQL)  or die(mysqli_error($mysqli_link));
$optimize = "OPTIMIZE TABLE INVHOLD";
mysqli_query($tryconnection, $optimize)  or die(mysqli_error($mysqli_link));


//OPTIMIZE RECEP AFTER DELETION OF THE PETS
$query_optimize="OPTIMIZE TABLE RECEP ";
$optimize=mysqli_query($tryconnection, $query_optimize) or die(mysqli_error($mysqli_link));
if (array_sum($_SESSION['payments']) == 0){
  $query_LOCK = "UPDATE ARCUSTO SET LOCKED='0', LDATE = '$row_invdatetime[0]' WHERE CUSTNO = '$_SESSION[client]' LIMIT 1"; }
else {
  $query_LOCK = "UPDATE ARCUSTO SET LOCKED='0', LDATE = '$row_invdatetime[0]', LASTPAY = '$row_invdatetime[0]' WHERE CUSTNO = '$_SESSION[client]' LIMIT 1";
}
$LOCK = mysqli_query($tryconnection, $query_LOCK) or die(mysqli_error($mysqli_link));


$unlock_it = "UNLOCK TABLES" ;
$Qunlock = mysqli_query($tryconnection, $unlock_it) or die(mysqli_error($mysqli_link)) ;
//unset($_SESSION['payments']) ;
}
	if (isset($_POST['prtsave'])){
	$_SESSION['printinvoice']='1';
	}

//include('mednotes.php');

	header("Location:PRINT_INVOICE.php");
	}
	}
?>