<?php

include 'functions.php';
include 'config.php';

$issue=$_GET["issue"];
$location=$_GET["location"];
$num=$_GET["num"];
$bioguide_id=$_GET["bioguide_id"];

$searchString = str_replace(' ','%20',$issue);

//echo 'BIOGUIDE:'.$bioguide_id.'<br />';
$billURL = 'http://api.realtimecongress.org/api/v1/bills.xml?apikey='.$apikey.'&search='.$searchString.'&cosponsor_ids__in='.$bioguide_id.'&sections=short_title,official_title,last_action,enacted,summary,bill_type,number,session&page=1&per_page=8';

//$billURL_open = 'http://api.opencongress.org/bills_by_query?q='.$searchString;

//echo $billURL.'<br />';

$obj = getXmlObj($billURL);

//$obj_open = getXmlObj($billURL_open);
//print_r($obj_open);

echo '<div class="title">legislation co-sponsored</div><br />';
//print_r($obj);
foreach($obj->bills->bill as $bill) {
	$typeCode = $bill->bill_type;
	$type = billTypeWriteOut($typeCode);
	$number = $bill->number;
	$session = $bill->session;
	if (strlen($bill->short_title) != 0) {
		$title = $bill->short_title;
	} else {
		$title = $bill->official_title;
	}
	echo $type.' '.$number.' - <a href="http://www.govtrack.us/congress/bills/'.$session.'/'.$typeCode.$number.'">'.$title.'</a><br />';
	$summary = $bill->summary;
	$sumNum = $sumNum+1;
	$shortSummary = shortenString($summary, 200);
	
	echo '<div class="summary-legislation" id="'.$sumNum.'" onClick="expandSummary(\''.$sumNum.'\',\''.addslashes(str_replace("\"","''",str_replace("\n"," ",$summary))).'\',\''.addslashes(str_replace("\"","''",str_replace("\n"," ",$shortSummary))).'\')">';
	
	echo $shortSummary;
	echo '</div>';
}


?>