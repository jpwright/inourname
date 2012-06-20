<?php

include 'functions.php';
include 'config.php';

$issue=$_GET["issue"];
$bioguide_id=$_GET["bioguide_id"];

$issue = strtolower($issue);
$issueArray = array($issue);

switch($issue) {
	case "boehner":
		$issueArray[] = "Election of the Speaker";
		break;
	case "campaign finance";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "campaign finance reform";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "dadt";
		$issueArray[] = "Don't Ask, Don't Tell";
		break;
	case "debt ceiling";
		$issueArray[] = "H.R. 1954";
		$issueArray[] = "H.J. Res. 98";
		break;
	case "debt limit";
		$issueArray[] = "H.R. 1954";
		$issueArray[] = "H.J. Res. 98";
		break;
	case "disclose";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "disclose act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "election";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "election reform";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "elections";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "elections reform";
		$issueArray[] = "DISCLOSE Act";
		$issueArray[] = "S. 3295";
		$issueArray[] = "H.R. 5175";
		break;
	case "health care":
		$issueArray[] = "H.R. 3590";
		$issueArray[] = "H.R. 4872";
		$issueArray[] = "Affordable Care Act";
		break;
	case "insider trading":
		$issueArray[] = "STOCK Act";
		$issueArray[] = "S. 2038";
		break;
	case "ndaa":
		$issueArray[] = "S. 1867";
		$issueArray[] = "H.R. 1540";
		$issueArray[] = "National Defense Authorization Act";
		break;
	case "national defense authorization act":
		$issueArray[] = "S. 1867";
		$issueArray[] = "H.R. 1540";
		break;
	case "payroll tax cut":
		$issueArray[] = "S.Amdt. 1465";
		$issueArray[] = "H.R. 3743";
		break;
	case "pelosi":
		$issueArray[] = "Election of the Speaker";
		break;
	case "sopa":
		$issueArray[] = "H.R. 3261";
		$issueArray[] = "Stop Online Piracy Act";
		break;
	case "stock act":
		$issueArray[] = "S. 2038";
		break;
	case "stop online piracy act":
		$issueArray[] = "H.R. 3261";
		break;
}

$voteArray = array();

for($k=0; $k<sizeof($issueArray); $k++) {
	$searchString = str_replace(' ','%20',$issueArray[$k]);
	//echo '<div class="title">recent votes</div><br />';

	$searchString = stripBillType(str_replace(' ','%20',$issueArray[$k]));

	$voteURL = 'http://api.realtimecongress.org/api/v1/votes.xml?apikey='.$apikey.'&search='.$searchString.'&voters.'.$bioguide_id.'__exists=true&sections=voters.'.$bioguide_id.',bill,question&page=1&per_page=8';

	//echo htmlspecialchars($voteURL).'<br />';

	$obj = getXmlObj($voteURL);

	//print_r($obj);
	if (isset($obj->votes)) {
		foreach($obj->votes->vote as $vote) {
			$type = billTypeWriteOut($vote->bill->bill_type);
			//$number = $vote->bill->number;
			//$title = $vote->bill->official_title;
			$question = $vote->question;
			$theirvote = $vote->voters->$bioguide_id->vote;
			
			//Cleanup for the indecisive...
			$theirvote = str_replace('Not Voting','Abstain',$theirvote);
			$theirvote = str_replace('Present','Abstain',$theirvote);
			if (in_array($theirvote, array('Yea','Nay','Abstain'))) {
				$voteLine = '<div class="vote-'.$theirvote.'">';
				$specialVote = false;
			} else {
				$voteLine = '<div class="vote-Special">';
				$specialVote = true;
			}
			//echo $type.' '.$number.' - '.$title.'<br />';
			$voteLine = $voteLine.$question.'<br />';
			if (strcmp($theirvote, 'Abstain')==0) {
				$voteLine = $voteLine.'<div class="explanation">This representative declined to vote, either by abstaining or by voting \'present\'.</div>';
			}
			if ($specialVote) {
				$voteLine = $voteLine.'<div class="explanation">This is a special vote, e.g., for the Speaker of the House. This representative\'s vote was "'.$theirvote.'".</div>';
			}
			foreach($explanationArray as $needle) {
				if (strpos($question, $needle['key']) != false) {
					$voteLine = $voteLine.'<div class="explanation">'.$needle['exp'].'</div>';
				}
			}
			
			$voteLine = addGovtrack($voteLine, $vote->bill->session);
			
			$voteLine = $voteLine.'</div>';
			$voteArray[]=$voteLine;
		}
	} else {
		print_r($obj);
	}	
}

//Removes duplicate votes.
$voteArray = array_unique($voteArray);

//Print the first few.
for($k=0;$k<10;$k++) {
	echo $voteArray[$k];
}

if(sizeof($voteArray) == 0) {
	echo '<div class="none">none</div><br />';
}

//Explain that related terms were added
if(sizeof($issueArray) > 1) {
	echo '<div class="explanation">The following terms were added to your search automatically: ';
	for ($q=1;$q<sizeof($issueArray);$q++) {
		echo $issueArray[$q];
		if ($q != sizeof($issueArray)-1) {
			echo ', ';
		}
	}
	echo '</div>';
}

?>