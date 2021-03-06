<?php

/* $Id: ExchangeRateTrend.php 5443 2012-06-20 02:43:30Z tehonu $*/

include('includes/session.inc');
$title = _('View Currency Trends');

include('includes/header.inc');

$graph = '/graph120.png';

$FunctionalCurrency = $_SESSION['CompanyRecord']['currencydefault'];

if ( isset($_GET['CurrencyToShow']) ){
	$CurrencyToShow = $_GET['CurrencyToShow'];
} elseif ( isset($_POST['CurrencyToShow']) ) {
	$CurrencyToShow = $_POST['CurrencyToShow'];
}

// ************************
// SHOW OUR MAIN INPUT FORM
// ************************

	echo '<form method="post" id="update" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<div class="centre"><p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/money_add.png" title="' .
		_('View Currency Trend') . '" alt="" />' . ' ' . _('View Currency Trend') . '</p></div>';
	echo '<table>'; // First column

	$SQL = "SELECT * FROM currencies";
	$result=DB_query($SQL,$db);


	// CurrencyToShow Currency Picker
	echo '<tr><td><select name="CurrencyToShow" onchange="ReloadForm(update.submit)">';

	DB_data_seek($result,0);
	while ($myrow=DB_fetch_array($result)) {
		if ($myrow['currabrev']!=$_SESSION['CompanyRecord']['currencydefault']){
			if ( $CurrencyToShow==$myrow['currabrev'] )	{
				echo '<option selected="selected" value="' . $myrow['currabrev'] . '">' . $myrow['country'] . ' ' . $myrow['currency'] . '&nbsp;(' . $myrow['currabrev'] . ')'. '</option>';
			} else {
				echo '<option value="' . $myrow['currabrev'] . '">' . $myrow['country'] . ' ' . $myrow['currency'] . '&nbsp;(' . $myrow['currabrev'] . ')'. '</option>';
			}
		}
	}
	echo '</select></td></tr>';

   	echo '</table>'; /*close off the table in the third column */

	echo '<br /><div class="centre"><input type="submit" name="submit" value="' . _('Accept') . '" /></div>';
    echo '</div>';
   	echo '</form>';

// **************
// SHOW OUR GRAPH
// **************


	$graphA = $CurrencyToShow. '/' . $FunctionalCurrency . $graph;
	$image = 'http://www.x-rates.com/d/' . $graphA;

	echo '<br /><table class="selection">';
	echo '<tr><th><div class="centre"><b>' . $FunctionalCurrency . ' / ' . $CurrencyToShow . '</b></div></th></tr>';
	echo '<tr><td><img src="' . $image . '" alt="' ._('Trend Currently Unavailable') . '" /></td></tr>';
	echo '</table>';

	$graphB = $FunctionalCurrency. '/' . $CurrencyToShow . $graph;
	$image = 'http://www.x-rates.com/d/' . $graphB;

	echo '<br /><table class="selection">';
	echo '<tr><th><div class="centre"><b>' . $CurrencyToShow . ' / ' . $FunctionalCurrency . '</b></div></th></tr>';
	echo '<tr><td><img src="' . $image . '" alt="' ._('Trend Currently Unavailable') . '" /></td></tr>';
	echo '</table>';


include('includes/footer.inc');
?>