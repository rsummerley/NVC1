<?php ////////////////////AGE CALCULATION/////////////////////
function agecalculation($tryconnection, $DOB){
			$myears = ' years ';
			$mmonths = ' months';
			// This defined the birth date for testing purposes. 
			//$mpdob = strtotime("-467 days") ;
			
			
			//date of birth
			$mpdob=strtotime($DOB);
			//today's date
			$now = mktime(date('m/d/Y')) ;
			//TODAY - DOB transformed into number of seconds
			$diff = round(($now - $mpdob) / (60*60*24)) ;
			
			//under 6 months it is weeks
			if ($diff < 185) { 
			$week= round($diff/7) ;
			$age = $week .' weeks'  ;
			} 
			
			//over 6 months it's years and months
			else { 
			 $years = round($diff / 365.25) ;
			 $months = round($diff/30.4) - $years*12 ;
			 	if ($months < 0) {
			   		$months += 12;
			   		$years -= 1 ;
			 		}
					
			 	if (round($years) == 1) {
			   		$myears = ' year ' ;
			 		}
				if (round($months) == 1) {
			  		$mmonths = ' month' ;
			 		}
				
				if(round($years) == 0) {
					$age = $months.$mmonths;
					}	
			 	
			 	elseif (round($months) == 0) {
			 		$age = $years .$myears;
			 		} 
				else 
					{
			 		$age = $years  .$myears. $months .$mmonths;  
					}
			}
$age2=$age;
if ($DOB == '00/00/0000' ) {$age = 'No data' ;}
echo $age;
//return $age2;
}
?>