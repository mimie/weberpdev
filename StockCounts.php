<?php
/* $Id: StockCounts.php 5603 2012-08-20 09:21:43Z daintree $*/

//$PageSecurity = 2;

include('includes/session.inc');

$title = _('Stock Check Sheets Entry');

include('includes/header.inc');

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/inventory.png" title="' .
	_('Inventory Adjustment') . '" alt="" />' . ' ' . $title . '</p>';

if (!isset($_POST['Action']) and !isset($_GET['Action'])) {
	$_GET['Action'] = 'Enter';
}
if (isset($_POST['Action'])) {
	$_GET['Action'] = $_POST['Action'];
}

if ($_GET['Action']!='View' and $_GET['Action']!='Enter'){
	$_GET['Action'] = 'Enter';
}

echo '<table class="selection"><tr>';
if ($_GET['Action']=='View'){
	echo '<td><a href="' . $rootpath . '/StockCounts.php?&amp;Action=Enter">' . _('Resuming Entering Counts') . '</a> </td><td>' . _('Viewing Entered Counts') . '</td>';
} else {
	echo '<td>'._('Entering Counts') .'</td><td> <a href="' . $rootpath . '/StockCounts.php?&amp;Action=View">' . _('View Entered Counts') . '</a></td>';
}
echo '</tr></table><br />';

if ($_GET['Action'] == 'Enter'){

	if (isset($_POST['EnterCounts'])){

		$Added=0;
		for ($i=1;$i<=10;$i++){
			$InputError =False; //always assume the best to start with

			$Quantity = 'Qty_' . $i;
			$BarCode = 'BarCode_' . $i;
			$StockID = 'StockID_' . $i;
			$Reference = 'Ref_' . $i;
			
			if (strlen($_POST[$BarCode])>0){
				$sql = "SELECT stockmaster.stockid
								FROM stockmaster
								WHERE stockmaster.barcode='". $_POST[$BarCode] ."'";
			
				$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
				$DbgMsg = _('The sql that was used to determine if the part being ordered was a kitset or not was ');
				$KitResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
				$myrow=DB_fetch_array($KitResult);
			
				$_POST[$StockID] = strtoupper($myrow['stockid']);		
			}

			if (mb_strlen($_POST[$StockID])>0){
				if (!is_numeric($_POST[$Quantity])){
					prnMsg(_('The quantity entered for line') . ' ' . $i . ' ' . _('is not numeric') . ' - ' . _('this line was for the part code') . ' ' . $_POST[$StockID] . '. ' . _('This line will have to be re-entered'),'warn');
					$InputError=True;
				}
			$SQL = "SELECT stockid FROM stockcheckfreeze WHERE stockid='" . $_POST[$StockID] . "'";
				$result = DB_query($SQL,$db);
				if (DB_num_rows($result)==0){
					prnMsg( _('The stock code entered on line') . ' ' . $i . ' ' . _('is not a part code that has been added to the stock check file') . ' - ' . _('the code entered was') . ' ' . $_POST[$StockID] . '. ' . _('This line will have to be re-entered'),'warn');
					$InputError = True;
				}

				if ($InputError==False){
					$Added++;
					$sql = "INSERT INTO stockcounts (stockid,
									loccode,
									qtycounted,
									reference)
								VALUES ('" . $_POST[$StockID] . "',
									'" . $_POST['Location'] . "',
									'" . $_POST[$Quantity] . "',
									'" . $_POST[$Reference] . "')";

					$ErrMsg = _('The stock count line number') . ' ' . $i . ' ' . _('could not be entered because');
					$EnterResult = DB_query($sql, $db,$ErrMsg);
				}
			}
		} // end of loop
		prnMsg($Added . _(' Stock Counts Entered'), 'success' );
		unset($_POST['EnterCounts']);
	} // end of if enter counts button hit


	echo '<table cellpadding="2" class="selection">';
	echo '<tr><th colspan="3">' ._('Stock Check Counts at Location') . ':<select name="Location">';
	$sql = 'SELECT loccode, locationname FROM locations';
	$result = DB_query($sql,$db);

	while ($myrow=DB_fetch_array($result)){

		if (isset($_POST['Location']) and $myrow['loccode']==$_POST['Location']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></th></tr>';
	echo '<tr>
			<th>' . _('Bar Code') . '</th>
			<th>' . _('Stock Code') . '</th>
			<th>' . _('Quantity') . '</th>
			<th>' . _('Reference') . '</th>
		</tr>';

	for ($i=1;$i<=10;$i++){

		echo '<tr>
				<td><input type="text" name="BarCode_' . $i . '" maxlength="20" size="20" /></td>
				<td><input type="text" name="StockID_' . $i . '" maxlength="20" size="20" /></td>
				<td><input type="text" name="Qty_' . $i . '" maxlength="10" size="10" /></td>
				<td><input type="text" name="Ref_' . $i . '" maxlength="20" size="20" /></td>
			</tr>';

	}

	echo '</table>
			<br />
			<div class="centre">
				<input type="submit" name="EnterCounts" value="' . _('Enter Above Counts') . '" />
			</div>';

//END OF action=ENTER
} elseif ($_GET['Action']=='View'){

	if (isset($_POST['DEL']) AND is_array($_POST['DEL']) ){
		foreach ($_POST['DEL'] as $id=>$val){
			if ($val == 'on'){
				$sql = "DELETE FROM stockcounts WHERE id='".$id."'";
				$ErrMsg = _('Failed to delete StockCount ID #').' '.$i;
				$EnterResult = DB_query($sql, $db,$ErrMsg);
				prnMsg( _('Deleted Id #') . ' ' . $id, 'success');
			}
		}
	}

	//START OF action=VIEW
	$SQL = "select * from stockcounts";
	$result = DB_query($SQL, $db);
	echo '<input type="hidden" name="Action" value="View" />';
	echo '<table cellpadding="2" class="selection">';
	echo "<tr>
		<th>" . _('Stock Code') . "</th>
		<th>" . _('Location') . "</th>
		<th>" . _('Qty Counted') . "</th>
		<th>" . _('Reference') . "</th>
		<th>" . _('Delete?') . '</th></tr>';
	while ($myrow=DB_fetch_array($result)){
		echo "<tr>
			<td>".$myrow['stockid']."</td>
			<td>".$myrow['loccode']."</td>
			<td>".$myrow['qtycounted']."</td>
			<td>".$myrow['reference']."</td>
			<td>";
        echo '<input type="checkbox" name="DEL[' . $myrow['id'] . ']" maxlength="20" size="20" /></td></tr>';

	}
	echo '</table><br /><div class="centre"><input type="submit" name="SubmitChanges" value="' . _('Save Changes') . '" /></div>';

//END OF action=VIEW
}

echo '</div>
      </form>';
include('includes/footer.inc');

?>