<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Check Voucher</title>
<style type="text/css">
<!--

td.number {
    text-align: right;
}
.style4 {font-size: 10pt; font-family: Calibri; }
.style6 {font-size: 10pt; font-family: Calibri; font-weight: bold; }
.style7 {
	font-family: Calibri;
	font-size: 18px;
	font-weight: bold;
}
.style16 {font-size: 10pt}
.style17 {font-family: Calibri; font-size: 18pt; font-weight: bold; }
-->
</style>
</head>

<body onload="printTkt()">
<table width="845" border="0">
  <tr>
    <img src="http://54.225.135.82/weberpdev/companies/IIAP_DEV/logo.jpg" width="60" height="60" style="float:left">
    <td><span class="style4">Institute of Internal Auditors Philippines, Inc.</span></td>
  </tr>
  <tr>
    <td><span class="style4">Unit 702 Corporate Center 139 Valero St., Salcedo Village, Makati City 1227</span></td>
  </tr>
  <tr>
    <td><span class="style6">TIN No. 001-772-403-000  :  ' (+632) 940-9551 / 940-9552   :  : Fax (+632) 325-0414 </span></td>
  </tr>
</table>
<p align="center" class="style17">CHECK\PAYMENT VOUCHER</p>
<table width="843" border="0">
  <tr>
    <td width="65"><span class="style6">Payee:</span></td>
    <td width="351"><span class="style4"><?echo $_GET['suppName'];?></span></td>
    <td width="223"><div align="right" class="style4"><strong>Date:</strong></div></td>
    <td width="186"><? print(Date("m/d/Y")); ?> </td>
  </tr>
  <tr>
    <td><span class="style16"></span></td>
    <td><span class="style4"><?echo $_GET['suppAdd'];?></span></td>
    <td><div align="right" class="style4"><strong>Voucher No.:</strong></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right" class="style4"><strong>Check No.:</strong></div></td>
    <td><?echo $_GET['ChequeNum'];?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right" class="style4"><strong>Check Date:</strong></div></td>
    <td><?echo $_GET['Datepaid'];?></td>
  </tr>
</table>
<table width="844" border="1">
  <tr>
    <td width="647"><span class="style6">INPAYMENT FOR:</span></td>
    <td width="187"><span class="style6">AMMOUNT</span></td>
  </tr>
</table>
<table width="844" height="202" border="1">
  <tr>
    <td width="834"><table width="829" border="0">
      <tr>
        <td width="639">&nbsp;</td>
        <td width="180">&nbsp;</td>
      </tr>
      <tr>
        <td><?echo $_GET['PayTo'];?></td>
        <td class="number"><?echo number_format($_GET['amount'],2);?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="845" height="44" border="1">
  <tr>

    <?php
	$myTotal=number_format($_GET['totalAmount'],2);
	$myWord=number_to_words($_GET['totalAmount']);
    ?>
    <td width="516" height="38"><span class="style6">AMOUNT IN WORDS:</span><?echo $myWord;?> </td>
    <td width="117"><p class="style6">NET AMOUNT PAID (in figures)</p>    </td>
    <td width="190" class="number"><?echo $myTotal;?></td>
  </tr>
</table>
<table width="845" height="160" border="1">
  <tr>
    <td width="415"><table width="415" height="166" border="1">
      <tr>
        <td><span class="style6">Prepared by: <?echo $_GET['UserName'];?></span></td>
      </tr>
      <tr>
        <td><span class="style6">Verify by:</span></td>
      </tr>
      <tr>
        <td><span class="style6">Approved by:</span></td>
      </tr>
    </table></td>
    <td width="414"><p class="style6">This is to acknowledge receipt of above described payment/s from the Institute of Internal Auditors Philippines Inc.</p>
    <p align="center" class="style6">__________________________________________________</p>
    <p align="center" class="style6">SIGNATURE OVER PRINTED NAME \ DATE OF RECEIPT</p>
    <p>&nbsp;</p></td>
  </tr>
</table>
<p align="left" class="style7">&nbsp;</p>
</body>
</html>


<script>
 function printTkt(){
  window.print();

}
</script>



<?php
////////////////////////////////////////////////////////////////////////////////////

function number_to_words($Number) {

    if (($Number < 0) OR ($Number > 999999999)) {
                prnMsg(_('Number is out of the range of numbers that can be expressed in words'),'error');
                return _('error');
    }

        $Millions = floor($Number / 1000000);
        $Number -= $Millions * 1000000;
        $Thousands = floor($Number / 1000);
        $Number -= $Thousands * 1000;
        $Hundreds = floor($Number / 100);
        $Number -= $Hundreds * 100;
        $NoOfTens = floor($Number / 10);
        $NoOfOnes = $Number % 10;

        $NumberInWords = '';

        if ($Millions) {
                $NumberInWords .= number_to_words($Millions) . ' ' . _('million');
        }

    if ($Thousands) {
                $NumberInWords .= (empty($NumberInWords) ? '' : ' ') . number_to_words($Thousands) . ' ' . _('thousand');
        }

    if ($Hundreds) {
                $NumberInWords .= (empty($NumberInWords) ? '' : ' ') . number_to_words($Hundreds) . ' ' . _('hundred');
        }

        $Ones = array(  0 => '',
                                        1 => _('one'),
                                        2 => _('two'),
                                        3 => _('three'),
                                        4 => _('four'),
                                        5 => _('five'),
                                        6 => _('six'),
                                        7 => _('seven'),
                                        8 => _('eight'),
                                        9 => _('nine'),
                                        10 => _('ten'),
                                        11 => _('eleven'),
                                        12 => _('twelve'),
                                        13 => _('thirteen'),
                                        14 => _('fourteen'),
                                        15 => _('fifteen'),
                                        16 => _('sixteen'),
                                        17 => _('seventeen'),
                                        18 => _('eighteen'),
                                        19 => _('nineteen')     );

        $Tens = array(  0 => '',
                                        1 => '',
                                        2 => _('twenty'),
                                        3 => _('thirty'),
                                        4 => _('forty'),
                                        5 => _('fifty'),
                                        6 => _('sixty'),
                                        7 => _('seventy'),
                                        8 => _('eighty'),
                                        9 => _('ninety') );


    if ($NoOfTens OR $NoOfOnes) {
                if (!empty($NumberInWords)) {
                        $NumberInWords .= ' ' . _('and') . ' ';
                }

                if ($NoOfTens < 2){
                        $NumberInWords .= $Ones[$NoOfTens * 10 + $NoOfOnes];
                }
                else {
                        $NumberInWords .= $Tens[$NoOfTens];
                        if ($NoOfOnes) {
                                $NumberInWords .= '-' . $Ones[$NoOfOnes];
                        }
                }
        }

        if (empty($NumberInWords)){
                $NumberInWords = _('zero');
    }

        return $NumberInWords;
}

?>
