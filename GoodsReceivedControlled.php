<?php

/* $Id: GoodsReceivedControlled.php 5185 2012-04-01 02:42:45Z vvs2012 $*/
include('includes/DefinePOClass.php');
include('includes/DefineSerialItems.php');

include('includes/session.inc');

$title = _('Receive Controlled Items');
/* Session started in header.inc for password checking and authorisation level check */
include('includes/header.inc');

if (empty($_GET['identifier'])) {
	if (empty($_POST['identifier'])){
		$identifier=date('U');
	} else {
		$identifier=$_POST['identifier'];
	}
} else {
	$identifier=$_GET['identifier'];
}


if (!isset($_SESSION['PO'.$identifier])) {
	/* This page can only be called with a purchase order number for receiving*/
	echo '<div class="centre">
			<a href="' . $rootpath . '/PO_SelectOSPurchOrder.php">' . _('Select a purchase order to receive'). '</a>
		</div>
		<br />';
	prnMsg( _('This page can only be opened if a purchase order and line item has been selected') . '. ' . _('Please do that first'),'error');
	include('includes/footer.inc');
	exit;
}

if (isset($_GET['LineNo']) AND $_GET['LineNo']>0){
	$LineNo = $_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
	$LineNo = $_POST['LineNo'];
} else {
	echo '<div class="centre">
			<a href="' . $rootpath . '/GoodsReceived.php">' . _('Select a line Item to Receive').'</a>
		</div>';
	prnMsg( _('This page can only be opened if a Line Item on a PO has been selected') . '. ' . _('Please do that first'), 'error');
	include( 'includes/footer.inc');
	exit;
}

global $LineItem;
$LineItem = &$_SESSION['PO'.$identifier]->LineItems[$LineNo];

if ($LineItem->Controlled !=1 ){ /*This page only relavent for controlled items */

	echo '<div class="centre">
			<a href="' . $rootpath . '/GoodsReceived.php">' . _('Back to the Purchase Order'). '</a>
		</div>';
	prnMsg( _('The line being received must be controlled as defined in the item definition'), 'error');
	include('includes/footer.inc');
	exit;
}

/********************************************
  Get the page going....
********************************************/
echo '<div class="centre">
		<br />
		<a href="'.$rootpath.'/GoodsReceived.php?identifier=' .$identifier . '">'. _('Back To Purchase Order'). ' # '. $_SESSION['PO'.$identifier]->OrderNo . '</a>
		<br />
		<h4>'. _('Receive controlled item'). ' '. $LineItem->StockID  . ' - ' . $LineItem->ItemDescription . ' ' . _('on order') . ' ' . $_SESSION['PO'.$identifier]->OrderNo . ' ' . _('from') . ' ' . $_SESSION['PO'.$identifier]->SupplierName . '</h4>
	</div>';

/** vars needed by InputSerialItem : **/
$LocationOut = $_SESSION['PO'.$identifier]->Location;
$ItemMustExist = false;
$StockID = $LineItem->StockID;
$InOutModifier=1;
$ShowExisting = false;
include ('includes/InputSerialItems.php');

//echo '<br /><input type="submit" name=\'AddBatches\' value=\'Enter\' /><br />';

/*TotalQuantity set inside this include file from the sum of the bundles
of the item selected for dispatch */
$_SESSION['PO'.$identifier]->LineItems[$LineItem->LineNo]->ReceiveQty = $TotalQuantity;

include( 'includes/footer.inc');
?>