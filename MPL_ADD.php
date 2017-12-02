<?php 
session_start();
require_once('../../tryconnection.php');
include("../../ASSETS/age.php");

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}


if (isset($_GET['client'])){
$client=$_GET['client'];
$_SESSION['client']=$_GET['client'];
}
elseif (isset($_SESSION['client'])){
$client=$_SESSION['client'];
}

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT_CLIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT_CLIENT = mysql_query($query_PATIENT_CLIENT, $tryconnection) or die(mysql_error());
$row_PATIENT_CLIENT = mysql_fetch_assoc($PATIENT_CLIENT);
//$totalRows_PATIENT_CLIENT = mysql_num_rows($PATIENT_CLIENT);

$pdob=$row_PATIENT_CLIENT['PDOB'];
$psex=$row_PATIENT_CLIENT['PSEX'];

$query_DOCTOR = sprintf("SELECT DOCTOR FROM DOCTOR ORDER BY DOCTOR ASC");
$DOCTOR = mysql_query($query_DOCTOR, $tryconnection) or die(mysql_error());
$row_DOCTOR = mysql_fetch_assoc($DOCTOR);

$cat=$_GET['subcat'];

function categ($tryconnection)
{
$query_CATEGORY ="SELECT DISTINCT TCATGRY, TTYPE FROM DIAGNOSI ORDER BY TCATGRY ASC";
$CATEGORY = mysql_query($query_CATEGORY, $tryconnection) or die(mysql_error());
$row_CATEGORY = mysql_fetch_assoc($CATEGORY);

echo"<select name='category1' class='SelectList' id='category1' multiple='multiple' onchange='category();' >";
do {
echo"<option value='".$row_CATEGORY['TCATGRY']."'>";
echo $row_CATEGORY['TTYPE'];
echo"</option>\n";
} while ($row_CATEGORY = mysql_fetch_assoc($CATEGORY));
echo"</select>";		 

}

//////////////////////////LIST PRODUCT SERVICE FROM TREATMENT FEE FILE////////////////////
function subcateg($tryconnection, $cat)
{
$query_PRODUCTSERVICE = sprintf("SELECT TNO, TDESCR, TTYPE, TCATGRY FROM DIAGNOSI WHERE TCATGRY = '%s' ORDER BY TNO ASC",mysql_real_escape_string($cat));
$PRODUCTSERVICE = mysql_query($query_PRODUCTSERVICE, $tryconnection) or die(mysql_error());
$row_PRODUCTSERVICE = mysql_fetch_assoc($PRODUCTSERVICE);

echo "<input type='hidden' name='tno' value='".$row_PRODUCTSERVICE['TNO']."' />";
echo "<input type='hidden' name='ttype' value='".mysql_real_escape_string($row_PRODUCTSERVICE['TTYPE'])."' />";
echo "<input type='hidden' name='tcatgry' value='".mysql_real_escape_string($row_PRODUCTSERVICE['TCATGRY'])."' />";
echo"<select name='tdescr' id='tdescr' multiple='multiple' class='SelectList' ondblclick='catsubmit();' >";
do {
echo"<option value='".$row_PRODUCTSERVICE['TDESCR']."'>";
echo $row_PRODUCTSERVICE['TDESCR'];
echo"</option>";
} while ($row_PRODUCTSERVICE = mysql_fetch_assoc($PRODUCTSERVICE));
echo"</select>";		 

}

if (isset($_POST['check']) && !isset($_POST['save'])){

	if (!empty($_POST['tdescr2'])){
	$tdescr = $_POST['tdescr2'];
	}
	else {
	$tdescr = $_POST['tdescr'];
	}
	

$problem = array(
					'TTYPE' => $_POST['ttype'],
					'TCATGRY' => $_POST['tcatgry'],
					'TNO' => $_POST['tno'],
					'TDESCR' => $tdescr
);

$_SESSION['categorization'][] = $problem;
}

if (isset($_POST['save'])){
	//insert into PROBLEMS
	foreach ($_SESSION['categorization'] as $categorization){
	$query_PROBLEMS = "INSERT INTO PROBLEMS (CUSTNO, PETID, TREATDATE, TREATDESC, TCATGRY, TNO, TDOCTOR, TDATE) VALUES ('$client', '$patient', STR_TO_DATE('$_SESSION[treatdate]', '%m/%d/%Y'), '".mysql_real_escape_string($categorization['TTYPE'].": ".$categorization['TDESCR'])."','$categorization[TCATGRY]', '$categorization[TNO]', '".mysql_real_escape_string($_SESSION['tdoctor'])."', NOW())";
	$query_PROBLEMS = mysql_query($query_PROBLEMS, $tryconnection) or die(mysql_error());
	}

	//insert into MEDICAL HISTORY
	$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
	$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
	$row_PREFER = mysql_fetch_assoc($PREFER);
	
	$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
	$treatmxx="TREATM".floor($treatmxx);
	
	$insert_HISTORY = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$client', '$patient', 'NON-ROUTINE EXAM', 1, '01', STR_TO_DATE('$_SESSION[treatdate]', '%m/%d/%Y'), '".mysql_real_escape_string($_SESSION['tdoctor'])."')";
	mysql_query($insert_HISTORY, $tryconnection) or die(mysql_error());

	foreach ($_SESSION['categorization'] as $categorization){
	$insert_HISTORY = "INSERT INTO $treatmxx (CUSTNO, PETID, TREATDESC, HCAT, HSUBCAT, TREATDATE, WHO) VALUE ('$client', '$patient', '".mysql_real_escape_string($categorization['TTYPE']."::".$categorization['TDESCR'])."', 2, '58', STR_TO_DATE('$_SESSION[treatdate]', '%m/%d/%Y'), '".mysql_real_escape_string($_SESSION['tdoctor'])."')";
	mysql_query($insert_HISTORY, $tryconnection) or die(mysql_error());
	}



unset($_SESSION['categorization']);
unset($_SESSION['tdoctor']);
unset($_SESSION['treatdate']);
header("Location: MPL.php");

}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/ClientPatientTemplate.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>CATEGORIZATION</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../../ASSETS/styles.css" />
<script type="text/javascript" src="../../ASSETS/scripts.js"></script>


<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

function bodyonload(){
document.getElementById('inuse').innerText=localStorage.xdatabase;

//HIGHLIGHT SELECTED ITEMS IN SELECT LISTS
var loc=<?php echo $_GET['subcat']; ?>;
var i=loc-1;
	{
	document.mpl_add.category1.options[i].selected="selected";
	}
}

//LIST PRODCUT SERVICE ON CATEGORY SELECTION
function category()
{
var cat=document.getElementById('category1').value;
self.location='MPL_ADD.php?subcat=' + cat + '&product=j';
}

function catsubmit(){
document.mpl_add.submit();
}

</script>

<style type="text/css">
<!--
.table2 {	
	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
.SelectList {	
	width: 100%;
	height: 100%;
	font-family: "Verdana";
	font-size: 11px;
	border-width: 0px;
	padding: 5 px;
	outline-width: 0px;
}
#table {	border-color: #CCCCCC;
	border-collapse: separate;
	border-spacing: 1px;
}
-->
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../../ASSETS/navigation.js"></script>
</head>

<body onload="bodyonload()" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion4" -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;" >
<tr><td id="ds_calclass"></td></tr>
</table>
<script type="text/javascript" src="../../ASSETS/calendar.js"></script>
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
  <form action="" name="mpl_add" method="post">
    
  <table width="100%" height="553" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="60" colspan="3" valign="top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="59%" height="15" align="left" class="Verdana12B">
        <span style="background-color:#FFFF00">
        <script type="text/javascript">document.write(sessionStorage.custname);</script>
        </span></td>
        <td width="22%" rowspan="2" valign="middle" align="center"><span class="Verdana11">
        <script type="text/javascript">document.write(sessionStorage.custterm);</script>          
        </span>
        <?php //echo $treatmxx; ?>        </td>
        <td width="19%" colspan="2" rowspan="4" align="center">
        <table width="100%" border="1" cellspacing="0" cellpadding="0" class="table2">
            <tr>
              <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="18" colspan="2" align="center"><span class="Verdana11B" style="background-color:#FFFF00"><?php echo date('m/d/Y'); ?></span></td>
                  </tr>
                  <tr>
                    <td width="41%" height="18" align="right" class="Labels2">        
					<script type="text/javascript">document.write(sessionStorage.custprevbal);</script></td>
                    <td width="59%" height="18" class="Labels2">&nbsp;Balance</td>
                  </tr>
                  <tr>
                    <td height="18" align="right" class="Labels2">
                    <script type="text/javascript">document.write(sessionStorage.custcurbal);</script></td>
                    <td height="18" class="Labels2">&nbsp;Deposit</td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td height="15" align="left" class="Labels2">        
		<script type="text/javascript">document.write(sessionStorage.custphone);</script></td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
        <td height="15" colspan="2" align="left"  class="Labels2"><span class="Verdana12B" style="background-color:#FFFF00">&nbsp;<script type="text/javascript">document.write(sessionStorage.petname);</script>
</span>        <script type="text/javascript">document.write(sessionStorage.desco);</script>         </td>
      </tr>
      <tr bgcolor="<?php if ($psex=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" >
        <td height="15" colspan="2" align="left" class="Labels2">
        <script type="text/javascript">document.write(sessionStorage.desct);</script> (<?php agecalculation($tryconnection,$pdob); ?>)		</td>
      </tr>
    </table></td>
      </tr>
    <tr height="457">
      <td height="457" colspan="3" align="center" valign="top"><table id="table" width="733" border="1" cellpadding="0" cellspacing="0" >
        <tr bgcolor="#000000">
          <td width="133" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a category'>Category</td>
          <td width="200" height="13" align="center" valign="middle" bgcolor="#000000" class="Verdana11Bwhite" title='Click to select a product/service'>Problem</td>
          <td rowspan="2" align="center" valign="middle" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="Verdana12B">&nbsp;Custom Problem Statement</td>
              </tr>
              <tr>
                <td><input type="text" class="Input" name="tdescr2" id="tdescr2" onfocus="InputOnFocus(this.id)" onblur="InputOnBlur(this.id)" size="50" maxlength="80"/>
                </td>            
              </tr>
              <tr>
                <td height="30"><input type="button" class="button" name="ok" id="ok" value="OK" style="width:50px;" onclick="catsubmit();"/>
                </td>            
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td width="133" rowspan="2" align="center" valign="top"><!--LIST CATEGORIES-->
              <?php categ($tryconnection); ?>          </td>
          <td width="200" rowspan="2" align="center" valign="top"><!--LIST PRODUCT SERVICE-->
              <?php subcateg($tryconnection,$cat); ?>          </td>
        </tr>
        <tr>
          <td height="382" align="left" valign="top">
          
          
          <table width="304" border="0" cellspacing="0" cellpadding="0" >
            <tr>
              <td colspan="5" height="25" align="left" bgcolor="#FFFFFF" class="Verdana12B">
              Diagnosis by: <span class="Verdana12BBlue"><?php echo $_SESSION['tdoctor']; ?></span>
              </td>
			  <td class="Verdana12B" align="right"><?php echo $_SESSION['treatdate']; ?></td>
            </tr>
            <tr>
              <td colspan="6">
              <div style="width:384px;overflow:auto;">
                  <table width="384" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="20"></td>
                      <td width="130"></td>
                      <td height="0"></td>
                    </tr>
                <?php 
				foreach ($_SESSION['categorization'] as $categorization){
					echo '<tr>
                      <td></td>
                      <td class="Verdana11" height="16">'.$categorization['TTYPE'].':</td>
                      <td class="Verdana12">'.$categorization['TDESCR'].'</td>
                    </tr>';
				}
				?>
                
                  </table>
              </div></td>
            </tr>
          </table></td>
        </tr>
        
      </table></td>
    </tr>
    <tr>
      <td height="35" colspan="5" align="center" valign="middle" bgcolor="#B1B4FF">
      <input name="save" class="button" type="submit" value="SAVE"/>
      <input name="cancel" class="button" type="button" value="CANCEL" onclick="document.location='MPL.php'"/>
      <input type="hidden" name="check" value="1"/>
      </td>
    </tr>
  </table>
    </form>
<!-- InstanceEndEditable --></div>
</body>
<!-- InstanceEnd --></html>