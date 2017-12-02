<?php 
session_start();
require_once('../tryconnection.php'); 
include("../ASSETS/age.php");

if (isset($_GET['patient'])){
$patient=$_GET['patient'];
$_SESSION['patient']=$_GET['patient'];
}
elseif (isset($_SESSION['patient'])){
$patient=$_SESSION['patient'];
}
unset($_SESSION['certificate']);

mysql_select_db($database_tryconnection, $tryconnection);
$query_PATIENT = "SELECT *, DATE_FORMAT(PDOB,'%m/%d/%Y') AS PDOB, DATE_FORMAT(PDEADATE,'%m/%d/%Y') AS PDEADATE, DATE_FORMAT(PRABDAT,'%m/%d/%Y') AS PRABDAT, DATE_FORMAT(POTHDAT,'%m/%d/%Y') AS POTHDAT, DATE_FORMAT(PLEUKDAT,'%m/%d/%Y') AS PLEUKDAT, DATE_FORMAT(POTHTWO,'%m/%d/%Y') AS POTHTWO, DATE_FORMAT(POTHTHR,'%m/%d/%Y') AS POTHTHR, DATE_FORMAT(POTHFOR,'%m/%d/%Y') AS POTHFOR, DATE_FORMAT(POTHFIV,'%m/%d/%Y') AS POTHFIV, DATE_FORMAT(POTHSIX,'%m/%d/%Y') AS POTHSIX, DATE_FORMAT(POTHSEV,'%m/%d/%Y') AS POTHSEV, DATE_FORMAT(POTH8,'%m/%d/%Y') AS POTH8, DATE_FORMAT(POTH9,'%m/%d/%Y') AS POTH9, DATE_FORMAT(POTH10,'%m/%d/%Y') AS POTH10, DATE_FORMAT(POTH11,'%m/%d/%Y') AS POTH11, DATE_FORMAT(POTH12,'%m/%d/%Y') AS POTH12, DATE_FORMAT(POTH13,'%m/%d/%Y') AS POTH13, DATE_FORMAT(POTH14,'%m/%d/%Y') AS POTH14, DATE_FORMAT(POTH15,'%m/%d/%Y') AS POTH15, DATE_FORMAT(PFIRSTDATE,'%m/%d/%Y') AS PFIRSTDATE, DATE_FORMAT(PLASTDATE,'%m/%d/%Y') AS PLASTDATE FROM PETMAST JOIN ARCUSTO ON (ARCUSTO.CUSTNO=PETMAST.CUSTNO) WHERE PETID = '$patient'";
$PATIENT = mysql_query($query_PATIENT, $tryconnection) or die(mysql_error());
$row_PATIENT = mysql_fetch_assoc($PATIENT);

$species=$row_PATIENT['PETTYPE'];
$query_LIFESTYLE = "SELECT * FROM PETLIFESTYLE WHERE LSPECIES='$species' ORDER BY LIFESTYLE";
$LIFESTYLE = mysql_query($query_LIFESTYLE, $tryconnection) or die(mysql_error());
$row_LIFESTYLE = mysql_fetch_assoc($LIFESTYLE);

$query_VIEW="CREATE OR REPLACE VIEW PATIENTS AS SELECT PETNAME, PETID FROM PETMAST WHERE CUSTNO='$_SESSION[client]' ORDER BY PETNAME ASC";
$VIEW= mysql_query($query_VIEW, $tryconnection) or die(mysql_error());

$query_PETNAME="SELECT * FROM PATIENTS";
$PETNAME= mysql_query($query_PETNAME, $tryconnection) or die(mysql_error());
$row_PETNAME = mysql_fetch_assoc($PETNAME);

$ids= array();
do {
$ids[]=$row_PETNAME['PETID'];
}
while ($row_PETNAME = mysql_fetch_assoc($PETNAME));

$key=array_search($row_PATIENT['PETID'],$ids);

$petids=$_SESSION['petids'];
$key=array_search($_GET['patient'],$petids);



function validity($mydate,$interv){
	if ($interv=='1' || $interv=='2' || $interv=='3'){
	$interv=$interv." year";
	}
	else if ($interv=='4' || $interv=='8'){
	$interv=$interv." weeks";	
	}
	else if ($interv=='6'){
	$interv=$interv." months";	
	}
	else {
	$interv="1 year";	
	}
	
	
	$mydate = strtotime($mydate." + ".$interv);
	$mydate = date('m/d/Y',$mydate);
	
	return $mydate;
}

$next_APP = "SELECT SHORTDOC,DATE_FORMAT(DATEOF,'%a %b %d %Y') AS DATEOF,TIMEOF,PROBLEM,CANCELLED FROM APPTS WHERE DATEOF >= substr(NOW(),1,10) AND PETID = '$patient'  LIMIT 4" ;
$get_APP = mysql_query($next_APP, $tryconnection) or die(mysql_error()) ;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/POP UP WINDOWS TEMPLATE.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>PATIENT DETAIL</title>
<!-- InstanceEndEditable -->

<link rel="stylesheet" type="text/css" href="../ASSETS/styles.css" />
<script type="text/javascript" src="../ASSETS/scripts.js"></script>
<script type="text/javascript" src="../ASSETS/navigation.js"></script>

<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">

var leftpos = opener.window.screenX;
var toppos = opener.window.screenY;
moveTo(leftpos+70,toppos+0);


var petids=sessionStorage.petids.split(',');

function bodyonload(){

	window.resizeTo(660,620) ;	
	if (sessionStorage.filetype!='0'){
	document.getElementById('inuse').innerText=sessionStorage.fileused;
	}
	else {
	document.getElementById('inuse').innerHTML="&nbsp;";
	}
//
//var browser=navigator.userAgent;
//if (browser.match(/Firefox/i)){
//document.getElementById('reason').cols=45;
//document.getElementById('reason').rows=3;
//}
//else {
//document.getElementById('reason').cols=40;
//document.getElementById('reason').rows=4;
//}							

}


function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

/*function bodyonunload()
{
parent.window.opener.document.location='DUTY_LOG.php';
parent.window.opener.document.location='DUTY_LOG.php';
}
*/
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>

<!-- InstanceEndEditable -->



</head>

<body onload="bodyonload();MM_preloadImages('../IMAGES/left_arrow_dark.JPG','../IMAGES/right_arrow_dark.JPG')" onunload="bodyonunload()">
<!-- InstanceBeginEditable name="EditRegion3" -->
<form method="post" action="" name="patientdetail" id="patientdetail" style="position:absolute; top:0px; left:0px; background-color:#FFFFFF; z-index:10">
  <table width="660" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="void" rules="cols">
    <tr>
      <td height="20" colspan="2" bgcolor="#FFFF00">
        <!-- HEADING -->
        <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="below" rules="none">
          <tr>
            <td width="50%" height="20" align="left" class="Verdana12B">&nbsp;<?php echo $row_PATIENT['PETNAME']; ?></td>
          <td width="50%" height="20" align="right" class="Verdana12B"><?php echo $row_PATIENT['TITLE']; ?> <?php echo $row_PATIENT['CONTACT']; ?> <?php echo $row_PATIENT['COMPANY']; ?>'s patient #<?php echo $row_PATIENT['PETNO']; ?>&nbsp;</td>
          </tr>
          </table>        </td>
      </tr>
    <tr>
      <td width="473" height="50" align="left" valign="top">
        <!-- PATIENT INFO + LIFESTYLE -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
            <td width="9" height="19" align="left" class="Verdana11B">&nbsp;</td>
            <td colspan="2" align="left" bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" class="Verdana12"><?php if ($row_PATIENT['PETTYPE']=='1'){echo "Canine";} else if ($row_PATIENT['PETTYPE']=='2'){echo "Feline";} else if ($row_PATIENT['PETTYPE']=='3'){echo "Equine";}else if ($row_PATIENT['PETTYPE']=='4'){echo "Bovine";}else if ($row_PATIENT['PETTYPE']=='5'){echo "Caprine";}else if ($row_PATIENT['PETTYPE']=='6'){echo "Porcine";}else if ($row_PATIENT['PETTYPE']=='7'){echo "Avian";}else if ($row_PATIENT['PETTYPE']=='8'){echo "Other";}; ?>
              , <?php echo $row_PATIENT['PETBREED']; ?>, <?php echo $row_PATIENT['PCOLOUR']; ?> </td>
          </tr>
          <tr bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
            <td width="9" height="17" align="left" class="Verdana11B">&nbsp;</td>
            <td height="17" colspan="2" align="left" bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>" class="Verdana12">
            <?php echo $row_PATIENT['PSEX']; if ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='M'){echo "(N)";} elseif ($row_PATIENT['PNEUTER']=='1' && $row_PATIENT['PSEX']=='F'){echo "(S)";} if ($row_PATIENT['PDECLAW']=='1'){echo ", Declawed";}?>, <?php echo $row_PATIENT['PWEIGHT']; ?> <script type="text/javascript">document.write(localStorage.weightunit);</script>
            </td>
          </tr>
          <tr bgcolor="<?php if ($row_PATIENT['PSEX']=='M'){echo '#DBEBF0';} else {echo '#F9DEE9';}; ?>">
            <td width="9" height="17" align="left" class="Verdana11B">&nbsp;</td>
            <td height="17" colspan="2" align="left" class="Verdana12">Born: <?php echo $row_PATIENT['PDOB']; ?> &nbsp;(<?php agecalculation($tryconnection,$row_PATIENT['PDOB']);	?>)</td>
          </tr>
          <tr>
            <td width="9" align="left" class="Verdana11B">&nbsp;</td>
            <td width="85" height="20" align="left" class="Verdana11B">Rab. Tag:</td>
            <td width="320" height="20" align="left" class="Verdana11"><?php echo $row_PATIENT['PRABTAG'].'&nbsp;&nbsp;&nbsp; Old Tag: ' . $row_PATIENT['PRABLAST'] ; ?></td>
          </tr>
          <tr>
            <td align="left" class="Verdana11B">&nbsp;</td>
            <td height="20" align="left" class="Verdana11B">Rab. Serum:</td>
              <td height="20" align="left" class="Verdana11"><?php echo $row_PATIENT['PRABSER']; ?></td>
          </tr>
          <tr>
            <td align="left" class="Verdana11B">&nbsp;</td>
            <td height="20" align="left" class="Verdana11B">X Ray File:</td>
              <td height="20" align="left" class="Verdana11"><?php echo $row_PATIENT['PXRAYFILE']; ?></td>
          </tr>
          <tr>
            <td align="left" class="Verdana11B">&nbsp;</td>
            <td height="20" align="left" class="Verdana11B">File #:</td>
            <td height="20" align="left" class="Verdana11"><?php echo $row_PATIENT['PFILENO']; ?></td>
          </tr>
          <tr>
            <td align="left" class="Verdana11B">&nbsp;</td>
            <td height="20" align="left" class="Verdana11B">ID #:</td>
            <td height="20" align="left" class="Verdana11"><?php echo $row_PATIENT['PTATNO']; ?></td>
          </tr>
          <tr>
            <td align="left" class="Verdana11B">&nbsp;</td>
            <td height="20" colspan="2" align="left" class="Verdana11"><?php if ($row_PATIENT['PFELHW']=='1'){echo "Insured";} if ($row_PATIENT['PSOSP']=='1'){echo ", SOSP";} if ($row_PATIENT['PWELL']=='1'){echo ", Wellness";}?></td>
          </tr>
          <tr>
            <td height="1" colspan="3" align="left" bgcolor="#CCCCCC"></td>
          </tr>
          <tr height="30">
            <td align="left" valign="top" class="Verdana11B">&nbsp;</td>
            <td align="left" valign="top" class="Verdana11B">
              Lifestyle:</td>
              <td align="left" valign="top" class="Verdana11">
                <?php 
				$alife=array();
                do {
                $life = explode(",",$row_PATIENT['PLIFE']);
                if (in_array($row_LIFESTYLE['LIFESTYLEID'], $life))
                {$alife[]=$row_LIFESTYLE['LIFESTYLE'];}
                }
                while ($row_LIFESTYLE = mysql_fetch_assoc($LIFESTYLE));
				echo implode(', ', $alife);
                ?>            </td>
          </tr>
      </table>        </td>
        <td width="254" rowspan="2" align="center" valign="middle" class="Verdana14B">
            <span class="disabled">PHOTO</span></td>
      </tr>
    <tr>
      <td align="left" valign="top">
      <!-- COMMENT --></td>
    </tr>
    <tr>
      <td><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" frame="hsides" rules="none">
        <tr height="30">
          <td width="77"align="left" valign="top" class="Verdana11B">&nbsp;&nbsp;Comment:</td>
          <td width="331" rowspan="2" align="left" valign="top" class="Monaco11"><?php if ($row_PATIENT['PMOVED']=='1'){echo "<span class='Verdana11BBlue'>Moved</span>; ";}echo $row_PATIENT['PDATA']; ?></td>
        </tr>
        <tr>
          <td width="77"align="left" valign="top">&nbsp;</td>
        </tr>
      </table></td>
        <td>
          <!-- APPOINTMENTS -->
          <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="above" rules="none">
            <tr>
              <td height="15" colspan="3" align="center" bgcolor="#FFFF00" class="Verdana12B">Appointments</td>
          </tr>
            <tr>
              <td width="31%" height="20" align="right" class="Verdana11B">First&nbsp;</td>
          <td width="4%" align="left" class="Verdana11">&nbsp;</td>
          <td width="65%" height="20" align="left" class="Verdana11">&nbsp;<?php echo ($row_PATIENT['PFIRSTDATE']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['PFIRSTDATE']; ?></td>
          </tr>
            <tr>
              <td height="20" align="right" class="Verdana11B">Last&nbsp;</td>
          <td align="left" class="Verdana11">&nbsp;</td>
          <td height="20" align="left" class="Verdana11">&nbsp;<?php echo ($row_PATIENT['PLASTDATE']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['PLASTDATE']; ?></td>
          </tr>
          </table>        </td>
      </tr>
    <?php while ($row_APP = mysql_fetch_array($get_APP)) {echo '<tr> 
    <td class="Verdana11">';  if($row_APP['CANCELLED'] == 1 ){$canc = ' CANCELLED';} else {$canc = '' ;} if (substr($row_APP['TIMEOF'],0,2) > '12') {echo 'Next Apt. on ' . $row_APP['DATEOF']. ' at '.(substr($row_APP['TIMEOF'],0,2)-12) .substr($row_APP['TIMEOF'],2,3) . ' with '  . $row_APP['SHORTDOC'] . '  for ' . $row_APP['PROBLEM'].$canc;} else {echo 'Next Apt. on ' . $row_APP['DATEOF']. ' at '.$row_APP['TIMEOF'] . ' with '  . $row_APP['SHORTDOC'] . '  for ' . $row_APP['PROBLEM'] . $canc ;} 
    echo '</td>
    </tr>'; }?>
    <tr>
      <td align="left" valign="bottom" class="Verdana11Grey">
      <!--<img src="../IMAGES/TEST.png" alt="test" /> -->      <?php echo $_SESSION['patient']; ?></td>
  <td colspan="2">
          <!-- VACCINES -->
          <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#446441" frame="above" rules="none">
            <tr>
              <td height="15" colspan="4" align="center" bgcolor="#FFFF00" class="Verdana12B">Dates</td>
          </tr>
            <tr>
              <td height="2" colspan="4" align="center"></td>
          </tr>
            <tr>
              <td width="45%" height="17" align="right" class="Verdana11B">Annual Exam</td>
          <td width="14%" height="17" align="center" class="Verdana11">&nbsp;</td>
          <td height="17" class="Verdana11"><span <?php if ($row_PATIENT['P6EXAM'] == '1'){$annval = .5; } else { $annval = 1 ; } if (strtotime(validity($row_PATIENT['POTH8'],$annval)) < time() && $row_PATIENT['POTH8']!='00/00/0000') {echo "class='alerttext12'";}?>><?php echo ($row_PATIENT['POTH8']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTH8']; ?></span></td>
          <!--<td width="37%" height="17" class="Verdana11"><?php echo ($row_PATIENT['POTH8']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTH8']; ?></td> -->
          <td width="4%" height="15" class="Labels2">&nbsp;</td>
          </tr>
           
           
            <tr>
              <td height="17" align="right" class="Verdana11B">Rabies</td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['PRABYEARS']=='8' || $row_PATIENT['PRABYEARS']=='4'){echo $row_PATIENT['PRABYEARS']."W"; } else if ($row_PATIENT['PRABYEARS']>0 && $row_PATIENT['PRABYEARS']<4){echo $row_PATIENT['PRABYEARS']."Y";} else if ($row_PATIENT['PRABYEARS']=='6') {echo $row_PATIENT['PRABYEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['PRABDAT'],$row_PATIENT['PRABYEARS'])) < time() && $row_PATIENT['PRABDAT']!='00/00/0000') {echo "class='alerttext12'";}?>><?php echo ($row_PATIENT['PRABDAT']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['PRABDAT']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
           
           
           
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "DA2P";} elseif ($row_PATIENT['PETTYPE']=='2'){echo "FVRCP";} else {echo "N/A";}?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTHYEARS']=='8' || $row_PATIENT['POTHYEARS']=='4'){echo $row_PATIENT['POTHYEARS']."W"; } else if ($row_PATIENT['POTHYEARS']>0 && $row_PATIENT['POTHYEARS']<4){echo $row_PATIENT['POTHYEARS']."Y";} else if ($row_PATIENT['POTHYEARS']=='6') {echo $row_PATIENT['POTHYEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTHDAT'],$row_PATIENT['POTHYEARS'])) < time() && $row_PATIENT['POTHDAT']!='00/00/0000') {echo "class='alerttext12'";}?>><?php echo ($row_PATIENT['POTHDAT']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHDAT']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Lepto";} elseif ($row_PATIENT['PETTYPE']=='2'){echo "Feline Leukemia";} else {echo "N/A";}?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['PLEUKYEARS']=='8' || $row_PATIENT['PLEUKYEARS']=='4'){echo $row_PATIENT['PLEUKYEARS']."W"; } else if ($row_PATIENT['PLEUKYEARS']>0 && $row_PATIENT['PLEUKYEARS']<4){echo $row_PATIENT['PLEUKYEARS']."Y";} else if ($row_PATIENT['PLEUKYEARS']=='6') {echo $row_PATIENT['PLEUKYEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['PLEUKDAT'],$row_PATIENT['PLEUKYEARS'])) < time() && $row_PATIENT['PLEUKDAT']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['PLEUKDAT']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['PLEUKDAT']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Corona";} elseif ($row_PATIENT['PETTYPE']=='2'){echo "Chlamydia";} else {echo "";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH02YEARS']=='8' || $row_PATIENT['POTH02YEARS']=='4'){echo $row_PATIENT['POTH02YEARS']."W"; } else if ($row_PATIENT['POTH02YEARS']>0 && $row_PATIENT['POTH02YEARS']<4){echo $row_PATIENT['POTH02YEARS']."Y";} else if ($row_PATIENT['POTH02YEARS']=='6') {echo $row_PATIENT['POTH02YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTHTWO'],$row_PATIENT['POTH02YEARS'])) < time() && $row_PATIENT['POTHTWO']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTHTWO']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHTWO']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Parvo";} elseif ($row_PATIENT['PETTYPE']=='2'){echo "FIP";} else {echo "N/A";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH03YEARS']=='8' || $row_PATIENT['POTH03YEARS']=='4'){echo $row_PATIENT['POTH03YEARS']."W"; } else if ($row_PATIENT['POTH03YEARS']>0 && $row_PATIENT['POTH03YEARS']<4){echo $row_PATIENT['POTH03YEARS']."Y";} else if ($row_PATIENT['POTH03YEARS']=='6') {echo $row_PATIENT['POTH03YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTHTHR'],$row_PATIENT['POTH03YEARS'])) < time() && $row_PATIENT['POTHTHR']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTHTHR']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHTHR']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Bordetella";} else {echo "";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH06YEARS']=='8' || $row_PATIENT['POTH06YEARS']=='4'){echo $row_PATIENT['POTH06YEARS']."W"; } else if ($row_PATIENT['POTH06YEARS']>0 && $row_PATIENT['POTH06YEARS']<4){echo $row_PATIENT['POTH06YEARS']."Y";} else if ($row_PATIENT['POTH06YEARS']=='6') {echo $row_PATIENT['POTH06YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTHSIX'],$row_PATIENT['POTH06YEARS'])) < time() && $row_PATIENT['POTHSIX']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTHSIX']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHSIX']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Lyme disease";} elseif ($row_PATIENT['PETTYPE']=='2'){echo "Declawed";} elseif ($row_PATIENT['PETTYPE']=='4'){echo "Magnet";} else {echo "N/A";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH07YEARS']=='8' || $row_PATIENT['POTH07YEARS']=='4'){echo $row_PATIENT['POTH07YEARS']."W"; } else if ($row_PATIENT['POTH07YEARS']>0 && $row_PATIENT['POTH07YEARS']<4){echo $row_PATIENT['POTH07YEARS']."Y";} else if ($row_PATIENT['POTH07YEARS']=='6') {echo $row_PATIENT['POTH07YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTH7'],$row_PATIENT['POTH07YEARS'])) < time() && $row_PATIENT['POTH07']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTHSEV']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHSEV']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Distemper";} elseif($row_PATIENT['PETTYPE']=='3'){echo "Equine Arteritis";} else {echo "";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH09YEARS']=='8' || $row_PATIENT['POTH09YEARS']=='4'){echo $row_PATIENT['POTH09YEARS']."W"; } else if ($row_PATIENT['POTH09YEARS']>0 && $row_PATIENT['POTH09YEARS']<4){echo $row_PATIENT['POTH09YEARS']."Y";} else if ($row_PATIENT['POTH09YEARS']=='6') {echo $row_PATIENT['POTH09YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php  if (strtotime(validity($row_PATIENT['POTH9'],$row_PATIENT['POTH09YEARS'])) < time() && $row_PATIENT['POTH9']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTH9']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTH9']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Giardia";} else {echo "";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11"><?php if ($row_PATIENT['POTH10YEARS']=='8' || $row_PATIENT['POTH10YEARS']=='4'){echo $row_PATIENT['POTH10YEARS']."W"; } else if ($row_PATIENT['POTH10YEARS']>0 && $row_PATIENT['POTH10YEARS']<4){echo $row_PATIENT['POTH10YEARS']."Y";} else if ($row_PATIENT['POTH10YEARS']=='6') {echo $row_PATIENT['POTH10YEARS']."M";} else {echo "&nbsp;";} ?></td>
          <td height="17" class="Verdana11"><span <?php  if (strtotime(validity($row_PATIENT['POTH10'],$row_PATIENT['POTH10YEARS'])) < time() && $row_PATIENT['POTH10']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTH10']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTH10']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B"><span class="Verdana11">
                <?php if($row_PATIENT['PETTYPE']=='1'){echo "Heartworm";} elseif ($row_PATIENT['PETTYPE']=='3'){echo "West Nile";} else {echo "";} ?>
              </span></td>
          <td height="17" align="center" class="Verdana11">&nbsp;</td>
          <td height="17" class="Verdana11"><span <?php if (strtotime(validity($row_PATIENT['POTHFOR'],$row_PATIENT['POTH04YEARS'])) < time() && $row_PATIENT['POTHFOR']!='00/00/0000') {echo "class='alerttext12'";} ?>><?php echo ($row_PATIENT['POTHFOR']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHFOR']; ?></span></td>
          <td height="15" class="Labels2">&nbsp;</td>
          </tr>
            <tr>
              <td height="17" align="right" class="Verdana11B">Fecal</td>
              <td height="17" align="center" class="Verdana11"></td>
              <td height="17" class="Verdana11"><?php echo ($row_PATIENT['POTHFIV']=='00/00/0000') ? "&nbsp;" : $row_PATIENT['POTHFIV']; ?></td>
              <td height="15" class="Labels2">&nbsp;</td>
            </tr>
            <tr>
              <td height="2" colspan="4" align="right"></td>
          </tr>
      </table>        </td>
    </tr>
    <tr>
      <td height="30" colspan="2">
        <!-- BUTTONS -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="11%" height="30" align="center" bgcolor="#B1B4FF" style="display:">
              <a href="PATIENT_DETAIL.php?patient=<?php if ($key==0){ echo $petids[0]; } else { echo $petids[$key-1];} ?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','../IMAGES/left_arrow_dark.JPG',1)">
                <img src="../IMAGES/left_arrow_light.JPG" alt="left" name="Image5" width="28" height="28" border="0" id="Image5" title="previous patient" /></a>
              <a href="PATIENT_DETAIL.php?patient=<?php if ($key==(count($petids)-1)){echo $petids[$key];} else {echo $petids[$key+1];} ?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','../IMAGES/right_arrow_dark.JPG',1)">
              <img src="../IMAGES/right_arrow_light.JPG" alt="right" name="Image4" width="28" height="28" border="0" id="Image4" title="next patient"/></a></td>
            <td width="89%" height="30" align="center" class="ButtonsTable">
              <input name="ok" type="button" class="button" id="ok" value="CLOSE" onclick="self.close()" />&nbsp;&nbsp;&nbsp;</td>
          </tr>
          </table>        </td>
      </tr>
  </table>
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
