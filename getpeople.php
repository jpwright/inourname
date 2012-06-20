
<?php
include 'functions.php';
include 'config.php';

include 'sunlight-php/class.sunlightlabs.php';

$issue=$_GET["issue"];
$location=$_GET["location"];

$sf = new SunlightLegislator;

$apikey = '7301bdad876c4f9898acaa0df316d956';
$sf->api_key = $apikey;

$legisArray = array();
$legisNum = 1;

//Get legislator information. Validate zip code to minimize API calls.
if(validateUSAZip((int)$location) && strlen($issue)>2) {
	$return = $sf->legislatorZipCode($location);
	foreach($return as &$i) {
	
		//echo '<div class="result-wrapper">';
	
		$party = $i->legislator->party;
		$party_full = partyWriteOut($party);
		$bioguide_id = $i->legislator->bioguide_id;
		//echo '<div class="legislator-'.$party.'">';
		//echo '<img src="photos/'.$bioguide_id.'.jpg" class="profile" \>';
		$chamber = $i->legislator->chamber;
		//echo '<i>'.$chamber.' '.$party.'</i><br />';
		$firstname = $i->legislator->firstname;
		$middlename = $i->legislator->middlename;
		$lastname = $i->legislator->lastname;
		$state = $i->legislator->state;
		$district = $i->legislator->district;
		$webform = $i->legislator->webform;
		$phone = $i->legislator->phone;
		//echo '<a href="'.$webform.'">'.$firstname.' '.$middlename.' '.$lastname.'</a> ('.$state.'-'.$district.')<br />';
		//echo '<input id="legis'.$legisNum.'_bioguide_id" type="hidden" value="'.$bioguide_id.'" />';
		
		//Social media
		$twitter_id = $i->legislator->twitter_id;
		if (strlen($twitter_id)>0) {
			//echo '<a href="https://twitter.com/intent/tweet?screen_name='.$twitter_id.'" class="tweetbutton">@'.$twitter_id.'</a><br />';
		}
		
		
		$resultArray = array(
			"party" => $party,
			"party_full" => $party_full,
			"bioguide_id" => $bioguide_id,
			"chamber" => $chamber,
			"firstname" => $firstname,
			"middlename" => $middlename,
			"lastname" => $lastname,
			"state" => $state,
			"district" => $district,
			"webform" => $webform,
			"twitter_id" => $twitter_id,
			"phone" => $phone
		);
		
		$legisArray[$legisNum] = $resultArray;
		//echo '</div>';
		//echo '<div style="clear:both;"></div></div>'; //Close legislator box

		$legisNum = $legisNum + 1;
		
	}
}

//echo '<input type="hidden" id="numOfLegis" value="'.($legisNum).'" />';
//print_r($legisArray);

$encoded = json_encode($legisArray);

die($encoded);

?>