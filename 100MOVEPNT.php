<?php 
/* Logic for  moving patients.
Choices are 1) Move patients
            2) Move patients and invoices (open receivables only)
            3) Move invoices (receivables) only
            4) Merge patients' histories
            
            For all of these. 	The original Client number is in $Oclient.
            					The original Patient ID is in $Opetid.
            					The Target Client number is in $Tclient.
            					Where applicable (Cases 3 & 4), the target patient ID is in $Tpetid.
            
For 1) The Petmast record has to be copied, assigned a new Petid, and inserted with the new owner's
CUSTNO, and the next PETNO for the new client. The history has to be copied, with new CUSTNO, PETNO
and PETID's being inserted. The old history remains where it is.

For 2) As in 1 above, plus any receivables that are for that patient have to be removed from the old
client's receivables, and inserted into the new client's records, with CUSTNO, PETNO, PETID being
updated. The old invoice number stays the same. - can we just update the custno in the invoicing files?

For 3) A Target patient has to be identified in the receiving client, then the Receivable is updated
with the new CUSTNO, PONUM. The invoice number stays the same.

For 4) A Target patient has to be identified in the new client's file, then the history is duplicated
into the new TREAMXX file, with the CUSTNO, PETNO and PETID being replaced. The old history stays 
where it was. As the file is sorted chronologically, the histories will merge.

The input screens ask for which case to execute. The resulting value is stored in the variable $xxx.

*/
/* Case 1 and 2. Start by finding the new PETNO in the new client's file (New client number = $Tclient)
          Old Client numnber = $Oclient.
*/
if ($xxx = 1 || $xxx = 2)  {

mysql_select_db($database_tryconnection, $tryconnection);
$GETNEW = "SELECT PETNO, PETID FROM PETMAST WHERE CUSTNO = '$Tclient'" ;
$ALLPET = mysql_query($GETNEW, $tryconnection or die(mysql_error()) ;
$row_NUMPET = mysql_fetch_array($ALLPET,MYSQL_NUM) ;
$NEXTPETNO = 1 ;
WHILE ($ROW = mysql_fetch_array($row_NUMPET) {
  $NEXTPETNO ++ ;
  }
/*
  Then create a temporary table,and pick out the old record from Petmast (PETID = $Opetid)
  Finally, retrieve the last PETID from the new entry to use in the Hx.
*/
$Move1 = "DROP TEMPORARY TABLE if EXISTS MOVEPAT" ;
$do1 = mysql_query($Move1, $tryconnection) or die(mysql_error) ;
$Move2 = "CREATE TEMPORARY TABLE MOVEPAT LIKE PETMAST" ;
$do1 = mysql_query($Move2, $tryconnection) or die(mysql_error) ;
$NEWPAT = "INSERT INTO MOVEPAT SELECT * FROM PETMAST WHERE PETID = '$Opetid'" ;
$do3 = mysql_query($NEWPAT,$tryconnection) or die(mysql_error) ;
$OLDCLIENT = 'SELECT CUSTNO FROM MOVEPAT'
/* The following may be redundant, if the opening code has already defined it.
*/
$Oclient = mysql_query($OLDCLIENT, $tryconnection) or die(mysql_error) ;
$DATAIS = mysql_query($NEWPAT, $tryconnection) or die(mysql_error()) 
UPDATE MOVEPAT SET CUSTNO = '$Tclient', PETNO = '$NEXTPETNO' ;
INSERT INTO PETMAST SELECT * FROM MOVEPAT ;
DROP TEMPORARY TABLE MOVEPAT ;
$NEXTID = "SELECT PETID FROM PETMAST WHERE CUSTNO = '$Tclient' .AND. PETNO = '$NEXTPETNO'" ;
$NEWID = mysql_query($NEXTID, $tryconnection) or die(mysql_error) ;

}

if $xxx ! 3 {

/*
Now pick up the hx from the old TREATM file, find the new TREATM file into which it must go, 
and insert it.
*/

$query_PREFER="SELECT TRTMCOUNT FROM PREFER LIMIT 1";
$PREFER= mysql_query($query_PREFER, $tryconnection or die(mysql_error());
$row_PREFER = mysql_fetch_assoc($PREFER);

$treatmxx=$Oclient/$row_PREFER['TRTMCOUNT'] ;
$treatmxx="TREATM".floor($treatmxx) ;

DROP TEMPORARY TABLE if EXISTS MOVEHX ;
CREATE TEMPORARY TABLE MOVEHX LIKE $treatmxx ;
$SELECTHX = "INSERT INTO MOVEHX SELECT * FROM $treatmxx WHERE PETID = '$Opetid'" ;
$NEWHX = mysql_query($SELECTHX, tryconnection or die(mysql_error()) ;

UPDATE MOVEHX SET PETID = $NEWID WHERE CUSTNO < 10000000 ;
UPDATE MOVEHX SET CUSTNO = $Tclient WHERE CUSTNO < 10000000 ;
UPDATE MOVEHX SET PETNO = $NEXTPETNO WHERE CUSTNO < 10000000 ;

$treatmxx = $Tclient/$row_PREFER['TRTMCOUNT'] ;
$treatmxx = "treatm".floor($treatmxx) ;

$PASTEHX = "INSERT INTO $treatmxx SELECT * FROM MOVEHX" ;
$FINALLY = mysql_query($PASTEHX, $tryconnection or die(mysql_error()) ;

DROP TEMPORARY TABLE MOVEHX ;

/* end of case 1
*/

}
if $xxx = 2 OR $xxx = 3  {
 CREATE VIEW CLRECEV AS SELECT * FROM ARARECV WHERE CUSTNO = $Oclient AND IBAL != 0 ;
 $Nohit = 1
 /* Let Dreamweaver do pretty things to list these, and let the operator select one. If selected,
 set $Nohit to 0, and assign the invoice number and date to $invno and $invdate respectively.
 */
 if $Nohit = 0 {
  /* Check DVMINV, DVMILAST and ARYDVMI to find the invoice, and check that there is 
     only one patient on it. Skip over the Administrative items - Taxes, cancels, etc.
  */
  $Qcur = "SELECT INVPET,INVMAJ FROM DVMINV WHERE INVCUST = $client AND INVNO = $invno ";
  $cur = mysql_query($Qcur, $tryconnection or die(mysql_error()) ;
   if $cur != 0 {
     $Lcur = "SELECT INVPET,INVMAJ FROM DVMILAST WHERE INVCUST = $client AND INVNO = $invno ";
     $cur = mysql_query($Lcur,$tryconnection or die(mysql_error() )
      if $cur != 0 {
        $Ycur = "SELECT INVPET,INVMAJ FROM ARYDVMI WHERE INVCUST = $client AND INVNO = $invno ";
        $cur = mysql_query($Ycur,$tryconnection or die(mysql_error()) ;
        if $cur != 0 {
        /*  At this point, the invoice details do not exist, so tell the operator it can't be done,
            and try another number.
        */
        }
      }
   }
   /*
    Now check to ensure there is only one patient on this invoice. If multiples, NO GO.
   */
   $Nohit = 1
   while ($row = mysql_fetch_array($cur)) {
     if INVMAJ < 87 {
       if $row['INVPET'] != $Opetid {
       $Nohit = 0
       }
      }
   }
  UPDATE CLRECV AS SET CUSTNO TO $Tclient WHERE INVNO = $invno AND INVDTE = $invdte ;
 }
}
if $xxx = 4 {

}
?>