<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Journal Voucher</title>
<style type="text/css">
<!--
.style1 {
	font-family: Calibri;
	font-weight: bold;
	font-size: 12px;
}
.style3 {
	font-family: Calibri;
	font-weight: bold;
	font-size: 18pt;
}
.style5 {
	font-family: Calibri;
	font-weight: bold;
	font-size: 10px;
}
.style7 {
	font-family: Calibri;
	font-size: 10pt;
	font-weight: bold;
}
.style16 {font-family: Arial; font-size: 12px; font-weight: bold; }
.style17 {
	font-family: Arial;
	font-weight: bold;
	font-size: 18px;
}

td.number {
    text-align: right;
}
-->
</style>
</head>
<?php
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
?>


<?
$GLItem = unserialize(rawurldecode($_GET['GLItems']));
echo "<pre>";
//print_r($GLItem);
echo "</pre>";
?>

<body>
<p class="style1">Institute of Internal Auditors Philippines, Inc.</p>
<p align="center" class="style3">JOURNAL VOUCHER</p>
<table width="860" border="0">
  <tr>
    <td width="259"><span class="style7">Particulars:</span></td>
    <td width="336">&nbsp;</td>
    <td width="89"><div align="right"><span class="style7">Date:</span></div></td>
    <td width="158"><?echo $_GET['ProcessDate'];?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right"><span class="style7">Number:</span></div></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="859" border="1">
  <tr>
    <td width="256"><span class="style16">ACCT. CODE</span></td>
    <td width="296"><span class="style16">ACCOUNT TITLE</span></td>
    <td width="123"><span class="style16">DEBIT</span></td>
    <td width="156"><span class="style16">CREDIT</span></td>
  </tr>
</table>
<table width="860" height="220" border="1">
  <tr>
    <td>

	<table width="859" border="1">
  	<?foreach ($GLItem as $g){?>
	 <tr>
    		<td width="256"><span class="style16"><?=$g['GLCode']?></span></td>
    		<td width="296"><span class="style16"><?echo $g['GLActName'];?></span></td>
    		<td width="123"><span class="style16">DEBIT</span></td>
    		<td width="156"><span class="style16">CREDIT</span></td>
  	 </tr>
	<? } ?>
	</table>



    </td>
  </tr>
</table>
<table width="860" border="1">
  <tr>
    <td width="568"><div align="center" class="style17">TOTAL</div></td>
    <td width="121" class="number"><?echo number_format($_GET['DebitTotal'],2);?></td>
    <td width="157" class="number"><?echo number_format($_GET['CreditTotal'],2);?></td>
  </tr>
</table>

<table width="860" height="30" border="1">
      <tr>
        <td><span class="style6">Prepared by:</span><?echo ' '.$_GET['UserName'];?></td>
      </tr>
    
    </table></td>

<p align="left" class="style5">&nbsp;</p>
</body>

</html>

