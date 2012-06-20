
<?php
include 'functions.php';
include 'config.php';

//Set up the explanation array!
$explanationArray = array();
$explanationArray[] = array("key" => "Motion to Recommit", "exp" => 'A <a href="http://usinfo.org/house/rule/recommit.htm">motion to recommit</a> does not indicate support for a resolution, but instead advocates sending it back to committee.');
$explanationArray[] = array("key" => "Ordering the Previous Question", "exp" => 'Ordering the <a href="http://democrats.rules.house.gov/archives/prev_question.htm">previous question</a> brings the pending resolution to an immediate, final vote.');
$explanationArray[] = array("key" => "Cloture", "exp" => 'A <a href="http://en.wikipedia.org/wiki/Cloture#Procedure">cloture motion</a> limits debate on a resolution, used to overcome a <a href="http://en.wikipedia.org/wiki/Filibuster_in_the_United_States_Senate">filibuster</a>.');
$explanationArray[] = array("key" => "H.J. Res. 98", "exp" => 'This resolution would have <i>blocked</i> an increase in the debt limit.');
$explanationArray[] = array("key" => "H.R. 3590", "exp" => '<a href="http://www.govtrack.us/congress/bills/111/hr3590">H.R. 3590</a> is the Patient Protection and Affordable Care Act, also known as the "Senate bill".');
$explanationArray[] = array("key" => "H R 3590", "exp" => '<a href="http://www.govtrack.us/congress/bills/111/hr3590">H.R. 3590</a> is the Patient Protection and Affordable Care Act, also known as the "Senate bill".');
$explanationArray[] = array("key" => "S.Amdt. 1465", "exp" => '<a href="http://www.votesmart.org/bill/14253/37528/temporary-payroll-tax-cut-extension">S.Amdt. 1465</a> was a temporary extension of the payroll tax cut.');
$explanationArray[] = array("key" => "S. 2038", "exp" => '<a href="http://www.govtrack.us/congress/bills/112/s2038">S. 2038</a> is the STOCK Act, which prohibits Congress from trading on insider information.');



include 'sunlight-php/class.sunlightlabs.php';
include 'newscredphp5/newscred.php';

$issue=$_GET["issue"];
$location=$_GET["location"];

$issue = strtolower($issue);
$issueArray = array($issue);

//NewsCred Related Topics API

/*try {
    $options = array(
		'pagesize' => 4
    );
	$topics = NewsCredTopic::extract($newscredapikey, $issue, $options);
	
	//print_r($topics);
} catch (NewsCredException $e) {
	echo 'fail';
    // Handle exception here
}

for($k=0; $k<sizeof($topics); $k++) {
	$topicToAdd = $topics[$k]->name;
	//echo 'adding topic: '.$topicToAdd.'<br />';
	$issueArray[] = $topicToAdd;
}
*/

//Add related terms
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

$sumNum = 0;

$sf = new SunlightLegislator;

$apikey = '7301bdad876c4f9898acaa0df316d956';
$sf->api_key = $apikey;

$legisNum = 0;

//Get legislator information. Validate zip code to minimize API calls.
if(validateUSAZip((int)$location) && strlen($issue)>2) {
	$return = $sf->legislatorZipCode($location);
	foreach($return as &$i) {
	
		echo '<div class="result-wrapper">';
	
		$party = $i->legislator->party;
		$bioguide_id = $i->legislator->bioguide_id;
		echo '<div class="legislator-'.$party.'">';
		echo '<img src="photos/'.$bioguide_id.'.jpg" class="profile" \>';
		$chamber = $i->legislator->chamber;
		echo '<i>'.$chamber.' '.partyWriteOut($party).'</i><br />';
		$firstname = $i->legislator->firstname;
		$middlename = $i->legislator->middlename;
		$lastname = $i->legislator->lastname;
		$state = $i->legislator->state;
		$district = $i->legislator->district;
		$webform = $i->legislator->webform;
		echo '<a href="'.$webform.'">'.$firstname.' '.$middlename.' '.$lastname.'</a> ('.$state.'-'.$district.')<br />';
		echo '<input id="legis'.$legisNum.'_bioguide_id" type="hidden" value="'.$bioguide_id.'" />';
		
		//Social media
		$twitterID = $i->legislator->twitter_id;
		if (strlen($twitterID)>0) {
			echo '<a href="https://twitter.com/intent/tweet?screen_name='.$twitterID.'" class="tweetbutton">@'.$twitterID.'</a><br />';
		}
		
		echo '<div style="clear:both;"></div></div>'; //Close legislator box
		echo '<div class="record">';
		
		//RTC API--Bills
		
		echo '<div id="legis'.$legisNum.'">';
		
		//echo '<script type="text/javascript">getBills(\''.$issue.'\',\''.$location.'\',\''.$legisNum.'\');</script>';
		
		$searchString = str_replace(' ','%20',$issue);
		
		$billURL = 'http://api.realtimecongress.org/api/v1/bills.xml?apikey='.$apikey.'&search='.$searchString.'&cosponsor_ids__in='.$bioguide_id.'&sections=short_title,official_title,last_action,enacted,summary,bill_type,number,session&page=1&per_page=8';

		$obj = getXmlObj($billURL);
		
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
		
		echo '</div>';
		
		//RTC API--Votes
		echo '<div class="title">recent votes</div><br />';
		$voteArray = array();
		$lineNum = 0;
		foreach($issueArray as $issue) {
		
			$searchString = stripBillType(str_replace(' ','%20',$issue));
			
			$voteURL = 'http://api.realtimecongress.org/api/v1/votes.xml?apikey='.$apikey.'&search='.$searchString.'&voters.'.$bioguide_id.'__exists=true&sections=voters.'.$bioguide_id.',bill,question&page=1&per_page=8';
			$obj = getXmlObj($voteURL);
			
			
			//print_r($obj);
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
					$voteLine = '<div class="vote-special">';
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
				$voteLine = $voteLine.'</div>';
				$voteArray[$lineNum]=$voteLine;
				$lineNum = $lineNum + 1;
			}
		}
		//Removes duplicate votes.
		$voteArray = array_unique($voteArray);
		
		//Print the first few.
		for($k=0;$k<10;$k++) {
			echo $voteArray[$k];
		}
		
		//NewsCred Article API
		echo '<div class="title">in the news</div><br />';
		try {

			// API call with custom arguments
			$options = array(
				'pagesize'         => 5,
				'get_topics' => 'false'
			);
			$articles = NewsCredArticle::search($newscredapikey, $issue.' "'.$firstname.' '.$lastname.'"', $options);
			
			for($q=0;$q<sizeof($articles);$q++) {
				$articleTitle = $articles[$q]->title;
				$articleDescription = $articles[$q]->description;
				$articleLink = $articles[$q]->link;
				$articleSource = $articles[$q]->source->name;
				$articleDate = $articles[$q]->published_at;
				echo '<a href="'.$articleLink.'">'.$articleTitle.'</a> ('.$articleSource.', '.$articleDate.')<br />';
				echo '<div class="summary">'.shortenString(strip_tags($articleDescription), 300).'</div>';
			}
		} catch (NewsCredException $e) {
			// Handle exception here
			echo 'fail';
		}
		
		//Capitol Words API
		echo '<div class="title">statements on record</div><br />';
		echo '<div class="summary">';
		$quoteArray = array();
		$lineNum = 0;
		foreach($issueArray as $issue) {
		
			$wordsURL = 'http://capitolwords.org/api/text.json?phrase='.$searchString.'&bioguide_id='.$bioguide_id.'&start_date=2008-01-01&apikey='.$apikey;
			//echo $wordsURL;
			$obj = getJsonObj($wordsURL);
			
			
			foreach($obj->results as $result) {
				$date = $result->date;
				$origin_url = $result->origin_url;
				$speakingArray = $result->speaking;
				//print_r($speaking);
				foreach($speakingArray as $j) {
					//print_r($j);
					$loc = stripos($j, $issue);
					//$speaking = stristr($j, $issue);
					if ($loc != false) {
						$start = $loc-140;
						if ($start<0) { 
							$start=0;
						} else {
							$start = strpos($j, ' ', 0)+1;
						}
						$speaking = substr($j,$start);
						if ($start>0) {
							$speaking = '...'.$speaking;
						}
						if (strlen($speaking)>280) {
							$pos = strpos($speaking, ' ', 280);
							if ($pos !== false) {
								$speaking = substr($speaking, 0, $pos).'...';
							} else {
								$speaking = substr($speaking,0,280);
							}
						}
						$speaking = str_ireplace($issue, '<b>'.substr($speaking,stripos($speaking,$issue),strlen($issue)).'</b>', $speaking);
						$line = $date.': "'.$speaking.'" <a href="'.$origin_url.'">&raquo;</a><br />';
						$quoteArray[$lineNum] = $line;
						//print_r($quoteArray);
						//echo $line;
						$lineNum = $lineNum+1;
						//echo $date.': "'.$speaking.'"<br />';
					}
				}
			}
		}
		//Sorts the array of quotes such that more recent ones appear first.
		rsort($quoteArray);
		
		//Print the first few.
		for($k=0;$k<8;$k++) {
			echo $quoteArray[$k];
		}
		echo '</div>';
		echo '</div>';
		
		echo '<div style="clear:both;"></div></div>'; //Close result box
		$legisNum = $legisNum + 1;
		
	}
	/* echo '<pre>';
	var_dump( $return );
	echo '</pre>'; */
}

echo '<input type="hidden" id="numOfLegis" value="'.($legisNum).'" />';

?>