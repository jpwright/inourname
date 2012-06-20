<?php

include 'functions.php';
include 'config.php';

include 'newscredphp5/newscred.php';

$issue=$_GET["issue"];
$firstname=$_GET["firstname"];
$lastname=$_GET["lastname"];

//NewsCred Article API
//echo '<div class="title">in the news</div><br />';
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
	
	if(sizeof($articles) == 0) {
		echo '<div class="none">none</div><br />';
	}
} catch (NewsCredException $e) {
	// Handle exception here
	echo 'fail';
}

?>