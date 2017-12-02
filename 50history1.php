<?php
session_start();
function history($database_tryconnection, $tryconnection, $filter){
mysql_select_db($database_tryconnection, $tryconnection);

$patient=$_SESSION['patient'];
$client=$_SESSION['client'];

$filter=array_sum($_POST['filter']);
if (empty($filter)){$filter=32767;}

if (!empty($_POST['from']) || !empty($_POST['to'])){
	if (!empty($_POST['from'])){
	$startdate=$_POST['from'];
	}
	else {
	$startdate='00/00/0000';
	}
	
	$startdate="SELECT STR_TO_DATE('$startdate','%m/%d/%Y')";
	$startdate=mysql_query($startdate, $tryconnection) or die(mysql_error());
	$startdate=mysql_fetch_array($startdate);
	
	if (!empty($_POST['to'])){
	$enddate=$_POST['to'];
	}
	else {
	$enddate=date('m/d/Y');
	}
	
	$enddate="SELECT STR_TO_DATE('$enddate','%m/%d/%Y')";
	$enddate=mysql_query($enddate, $tryconnection) or die(mysql_error());
	$enddate=mysql_fetch_array($enddate);
$fromto="AND TREATDATE >= '$startdate[0]' AND TREATDATE <= '$enddate[0]'";
}//if (!empty($_POST['from']) || !empty($_POST['to']))


////////////////////////VIEW FROM TREATMXX////////////////////////WHERE PETID='$patient'
$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection) or die(mysql_error());
$row_PREFER = mysql_fetch_assoc($PREFER);

$treatmxx=$client/$row_PREFER['TRTMCOUNT'];
$treatmxx="TREATM".floor($treatmxx);

$hxview="HX".$patient;

$query_VIEWHX="CREATE OR REPLACE VIEW $hxview AS SELECT * FROM $treatmxx WHERE PETID='$patient'";
$VIEWHX= mysql_query($query_VIEWHX, $tryconnection) or $none=1;




$query_HX="SELECT *, DATE_FORMAT(TREATDATE,'%m/%d/%Y') AS TREATDATE FROM $hxview WHERE PETID='$patient' AND HCAT & '$filter' ".$fromto." ORDER BY TREATDATE, LINENUMBER";
$HX= mysql_query($query_HX, $tryconnection) or die(mysql_error());
$row_HX = mysql_fetch_assoc($HX);

if (empty($row_HX)){
echo "I'm sorry, this client doesn't have any record in the medical history. It's not my fault.";
}

$theend='0';
$xxx=$row_HX['HSUBCAT'];
do { 

if (($row_HX['HSUBCAT'] < 53 || $xxx < 53) || ($row_HX['HSUBCAT'] > 80 || $xxx > 80)){


		if ($row_HX['HSUBCAT']!=$xxx){
		echo "<tr height='15'>";
			if (substr($xxx,1,1)=='1'){
					//date
					echo "<td valign='top' align='left'>";
					echo "<span class='Verdana11B'>".$row_HX['TREATDATE']."</span>";
					}
			elseif ($xxx=='12'){
					//Subjective
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Presenting Problem'>Subjective:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='13'){
					//Objective
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;'>Objective:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='16'){
					//Assessment
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;'>Assessment:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='17')  {
					//Plan
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Client Instructions'>Plan:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='18'){
					//Case Summary
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Case Summary'>Summary:</span>&nbsp;&nbsp;";
					}
			else {
			echo "<td valign='top' align='left'>";
			}

					echo "</td>";

			//INITIAL ASSESSMENT heading, display it in green
			if ($xxx=='01'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #FFCCFF'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";	
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}
			//SOAP EXAMINATION heading, display it in green
			elseif ($xxx=='11'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #CBFFCD'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";	
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}
			//IMAGERY heading, display it in grey
			elseif ($xxx=='21'){
					echo "<td colspan='4' class='Verdana11Bwhite' style='background-color: #330099;'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			//SCAN - display heading & link to the scan
			elseif ($row_HX['HSUBCAT']=='29'){
					echo "<td colspan='4' class='Verdana11Bwhite' style='background-color: #000000;'>&nbsp;";
					$xscan = explode(":",$row_HX['TREATDESC']);
					echo $xscan[1]. $xscan[2];
					echo "</td>";				
				}							
			//DUTY LOG heading, display it in grey (!!!!!!!!!!!!)
			elseif ($xxx=='31'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #EEEEEE;'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			//PROCEDURES heading, display it in grey
			elseif ($xxx=='41'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #EEEEEE;'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			//LABORATORY heading, display it in grey
			elseif ($xxx=='51'){
					echo "<td colspan='4' class='Verdana11Bwhite' style='background-color: #FF33CC;'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			//CLIENT COMMUNICATION heading, display it in grey
			elseif ($xxx=='81'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #EEEEEE;'>&nbsp;";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			//PROGRESS NOTES heading, display it in grey
			elseif ($xxx=='91'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #EEEEEE;'>";
					echo $treatdesc_chunk;
					echo "</td>";				
					echo "<td  align='center'>".$row_HX['WHO']."</td>";
				}							
			else {
					echo "<td colspan='4' class='Verdana11' style='white-space:pre-wrap'>";
					echo $treatdesc_chunk;
					echo "</td>";
					echo "<td></td>";
				}
		echo "</tr>\n";
		}
				if ($row_HX['HSUBCAT']!=$xxx){
				$treatdesc_chunk=$row_HX['TREATDESC'];
				}	
				else {
				$treatdesc_chunk = $treatdesc_chunk.$row_HX['TREATDESC'];
				}		


}

//NON-HEADING ENTRIES
if ($row_HX['HSUBCAT'] > 53 && $row_HX['HSUBCAT'] < 80){
unset($treatdesc_chunk);

		//row tag
		echo "<tr height='15'>";

//FIRST COLUMN

		//first column with date or Subj./Obj./As./Plan
			//checks for the subcategory if to display date or words
			if (substr($row_HX['HSUBCAT'],1,1)=='1' || $row_HX['HSUBCAT']==29){
					//date
					echo "<td valign='top' align='left'>";
					echo "<span class='Verdana11B'>".$row_HX['TREATDATE']."</span>";
					}
			else {
			echo "<td valign='top' align='left'>";
			}
			
		
		echo "</td>";

//2.,3.,4.,5. COLUMN
		
		//checks if there is a '::' in a string (for exams & report card)
		$findme='::';
		$pos= strpos($row_HX['TREATDESC'], $findme);
		
			//if there IS a '::' in a string, it breaks it into two pieces
			if ($pos!==false){
				//fills second column with the first part of the string
				echo "<td align='left' class='Verdana11B' valign='top' id='a".$row_HX['LINENUMBER']."' onmouseover=\"document.getElementById('a".$row_HX['LINENUMBER']."').style.backgroundColor='#E5E5E5';document.getElementById('b".$row_HX['LINENUMBER']."').style.backgroundColor='#E5E5E5';document.getElementById('a".$row_HX['LINENUMBER']."').style.cursor='default';\" onmouseout=\"document.getElementById('a".$row_HX['LINENUMBER']."').style.backgroundColor='#FFFFFF';document.getElementById('b".$row_HX['LINENUMBER']."').style.backgroundColor='#FFFFFF';\">";
	
					if ($colon != substr($row_HX['TREATDESC'],0,$pos+1) && substr($row_HX['TREATDESC'],0,$pos+1)!=':'){
				echo substr($row_HX['TREATDESC'],0,$pos+1);
					}
					if (substr($row_HX['TREATDESC'],0,$pos+1)==':'){
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;";
					}
				echo "&nbsp;";
				echo "</td>";
				//fills third column with the second part of the string
				echo "<td colspan='3' valign='top' id='b".$row_HX['LINENUMBER']."'>";
				
//				if ($colon != substr($row_HX['TREATDESC'],0,$pos+1) && substr($row_HX['TREATDESC'],0,$pos+1)!=':'){
//				echo "<hr size='1' color='#CCCCCC' style='margin:0px;' />";
//				}
				
				echo substr($row_HX['TREATDESC'],$pos+2);
				echo "</td>";
				//echo "<td></td>";
				
				$colon = substr($row_HX['TREATDESC'],0,$pos+1);				
				}

			
			//if there is NO ':' display the following
			else {
					//LAB SUBCATEGORY, break into an array and display in 4 columns		
					if ($row_HX['HSUBCAT']=='53'){
					$lab=explode(';',$row_HX['TREATDESC']);
					echo "<td class='Verdana11' align='right'>$lab[0]</td>";
					echo "<td class='Verdana11' align='center'>$lab[1]</td>";
					echo "<td class='Verdana11Grey' align='center'>$lab[2]</td>";
					echo "<td class='Verdana11Grey'>$lab[3]</td>";					
						}
					//INVOICE heading, display it in grey
					elseif ($row_HX['HSUBCAT']=='61'){
					echo "<td colspan='4' class='Verdana11B' style='background-color:#FFFCE2;'>";
					echo $row_HX['TREATDESC'];
					echo "</td>";				
						}							
					//ESTIMATE heading, display it in grey
					elseif ($row_HX['HSUBCAT']=='71'){
					echo "<td colspan='4' class='Verdana11B' style='background-color: #DBEBF0;'>";
					echo $row_HX['TREATDESC'];
					echo "</td>";				
						}													
					//invoice items
					else if ($row_HX['HSUBCAT']=='62'){
					$inv=explode(';',$row_HX['TREATDESC']);
					echo "<td colspan=4><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='50' align='right'>$inv[0]&nbsp;</td>";
					echo "<td width='200'>$inv[1]</td>";
					echo "<td width='50' align='right'>";
					 if ($_POST['supress']!=1) {echo $inv[2];}
					echo "</td>";
					echo "<td width='150'></td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					//estimated items
					else if ($row_HX['HSUBCAT']=='72'){
					$inv=explode(';',$row_HX['TREATDESC']);
					echo "<td colspan=4 class='Verdana11Blue'><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='50' align='right'>$inv[0]&nbsp;</td>";
					echo "<td width='200'>$inv[1]</td>";
					echo "<td width='50' align='right'>";
					 if ($_POST['supress']!=1) {echo $inv[2];}
					echo "</td>";
					echo "<td width='150'></td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					//declined items
					else if ($row_HX['HSUBCAT']=='77' || $row_HX['HSUBCAT']=='67'){
					$inv=explode(';',$row_HX['TREATDESC']);
					echo "<td colspan=4 class='Verdana11Red'><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='50' align='right'>$inv[0]&nbsp;</td>";
					echo "<td width='200'>$inv[1]</td>";
					echo "<td width='50' align='right'>";
					 if ($_POST['supress']!=1) {echo $inv[2];}
					echo "</td>";
					echo "<td align='center'>Declined</td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					//label
					else if ($row_HX['HSUBCAT']=='63'){
					echo "<td colspan=4><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='50'></td>";
					echo "<td class='Verdana11Grey'>".$row_HX['TREATDESC']."</td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					//vaccines
					else if ($row_HX['HSUBCAT']=='64'){
					//$inv=explode(';',$row_HX['TREATDESC']);
					echo "<td colspan=4><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='60'></td>";
					echo "<td class='Verdana10'>".$row_HX['TREATDESC']."</td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					//autocomment
					else if ($row_HX['HSUBCAT']=='65'){
					echo "<td colspan=4><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td class='Verdana11Grey'><i>".$row_HX['TREATDESC']."</i></td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}
										
					//inline note
					else if ($row_HX['HSUBCAT']=='66'){
					echo "<td colspan=4><table border='0' cellspacing='0' cellpadding='0' width='100%'>";
					echo "<tr>";
					echo "<td width='50'></td>";
					echo "<td class='Verdana11Grey'>*".$row_HX['TREATDESC']."</td>";
					echo "</tr>";
					echo "</table></td>";
					echo "</td>";				
					}

					else {
					echo "<td colspan='4' class='Verdana11' style='white-space:pre-wrap'>";
					echo $row_HX['TREATDESC'];
					echo "</td>";				
					}
						
						
						
//					echo "</td>";				
				}
		
//SIXTH COLUMN
		
		//here should be the doctor		
		echo "<td align='center'>";
			if ((substr($row_HX['HSUBCAT'],1,1)=='1' || $row_HX['HSUBCAT']=='29') || (substr($row_HX['HSUBCAT'],0,1)=='6' && $row_HX['WHO']!= $yyy) || (substr($row_HX['HSUBCAT'],0,1)=='7'  && $row_HX['WHO']!= $yyy)){
					//who
					echo "<span class='Verdana11'>".$row_HX['WHO']."</span>";
					}

		echo "</td>";
		echo "</tr>\n";

}

		$yyy = $row_HX['WHO'];
		$xxx = $row_HX['HSUBCAT'];


//if the entry is a SCAN - display heading & link to the scan
if ($row_HX['HSUBCAT']=='29'){
echo "<tr height='15'>";
echo "<td colspan='6' class='Verdana11B' >&nbsp;";
//echo "HELLO";
$treatdate = $row_HX['TREATDATE'];
$datefolder = date_parse_from_format("m/d/Y", $treatdate);
$datefolder = $datefolder['year'].$datefolder['month'];

$scanarray=explode(":", $row_HX['TREATDESC']);
$scanname=substr($scanarray[2],1);

echo '<embed src="../../../../SC'.$scanarray[0].'/'.$datefolder.'/'.$scanname.'" width="700" height="900" id="'.$scanname.'" style="display:;"></embed>';
echo "</td>";				
echo "</tr>\n";

	
  }//if ($row_HX['HSUBCAT']=='29'){
						

}
while ($row_HX = mysql_fetch_assoc($HX));





if ( $xxx < 53 || $xxx > 80){


		if ($row_HX['HSUBCAT']!=$xxx){
		echo "<tr height='15'>";
			if (substr($xxx,1,1)=='1'){
					//date
					echo "<td valign='top' align='left'>";
					echo "<span class='Verdana11B'>".$row_HX['TREATDATE']."</span>";
					}
			elseif ($xxx=='12'){
					//Subjective
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Presenting Problem'>Subjective:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='13'){
					//Objective
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;'>Objective:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='16'){
					//Assessment
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;'>Assessment:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='17')  {
					//Plan
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Client Instructions'>Plan:</span>&nbsp;&nbsp;";
					}
			elseif ($xxx=='18'){
					//Case Summary
					echo "<td valign='top' align='right'>";
					echo "<span style='background-color:#FFFF00; color:#FF0000;' title='Case Summary'>Summary:</span>&nbsp;&nbsp;";
					}
			else {
			echo "<td valign='top' align='left'>";
			}

					echo "</td>";

					echo "<td colspan='4' class='Verdana11' style='white-space:pre-wrap'>";
					echo $treatdesc_chunk;
					echo "</td>";
					echo "<td></td>";
		echo "</tr>\n";
		}

}




$query_VIEWHX="DROP VIEW $hxview";
$VIEWHX= mysql_query($query_VIEWHX, $tryconnection) or $none=1;
}
?>