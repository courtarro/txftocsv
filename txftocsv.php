<?php

// Usage: php txftocsv.php < input.txf > output.csv

$inFile = file_get_contents("php://stdin");

$trans = explode("^", $inFile);

array_shift($trans);		// remove the header
array_pop($trans);			// remove the footer

$out = array();
foreach ($trans as $thisTrans) {
	$thisTrans = trim($thisTrans);
	$rows = preg_split('/(\n|\r)+/i', $thisTrans);
	
	// Row 1 - Long v. Short
	switch ($rows[1]) {
		case "N321":
		case "N682":
		case "N711":
		case "N712":
			$newOut['term'] = "short";
			break;
		case "N323":
		case "N713":
		case "N714":
			$newOut['term'] = "long";
			break;
		default:
			$newOut['term'] = "unknown";
			break;
	}
	
	// Row 1 - 8949 code
	switch ($rows[1]) {
		case "N321":
		case "N323":
			$newOut['category'] = "A";
			break;
		case "N711":
		case "N713":
			$newOut['category'] = "B";
			break;
		case "N712":
		case "N714":
			$newOut['category'] = "C";
			break;
		case "N682":
		default:
			$newOut['category'] = "unknown";
			break;
	}
	
	// Row 4
	/* preg_match('/P(.*?) of (.*?) \((.*?)\)/i', $rows[4], $matches);
	$newOut['shares'] = $matches[1];
	$newOut['name'] = $matches[2];
	$newOut['symbol'] = $matches[3]; */
	$newOut['description'] = $rows[4];
	
	$newOut['purchase date'] = substr($rows[5], 1);
	$newOut['sale date'] = substr($rows[6], 1);
	$newOut['basis'] = substr($rows[7], 1);
	$newOut['proceeds'] = substr($rows[8], 1);
	$newOut['wash'] = (substr($rows[9], 1) ? "yes" : "no");
	
	$out[] = $newOut;
}

// Draw the headers
echo '"';
echo implode('","', array_keys($out[0]));
echo "\"\n";

// Draw the rows
foreach ($out as $row) {
	echo '"';
	echo implode('","', $row);
	echo "\"\n";
}
