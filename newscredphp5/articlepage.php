<?php

require_once('newscred.php');

$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';

try {
	$articles 			= NewsCredArticle::search($access_key , 'barack obama');
	$related_articles 	= NewsCredArticle::search($access_key, $articles[0]->title);
	$related_topics   	= NewsCredTopic::extract( $access_key, $articles[0]->title, 
	                                              array (
	                                                    'topic_classifications' => array('Person','Event'),
	                                                    'topic_subclassifications' => array('Politician')
	                                                    )
	                                            );
	$related_images = $articles[0]->getRelatedImages(array('safe_search' => True));    
}
catch(Exception $e) {
	
	die($e->getMessage());
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<title>Sample Article Page | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<div id="header"></div>
	<div id="content">
		<span><?php echo $articles[0]->source->name ?></span>		
		<h1 class="article"><?php echo $articles[0]->title; ?></h1>
		<p><?php echo substr($articles[0]->description, 0, 400); ?></p>
		<a href=<?php echo $articles[0]->link ?>>Read Full Story</a>
		<h1 class="related">Related Articles</h1>		
		<ul>
			<?php foreach ($related_articles as $article) { ?>
			<li>
				<span><?php echo $article->source->name; ?></span>
				<h2><?php echo $article->title; ?></h2>
				<p><?php echo substr($article->description, 0, 400); ?></p>
				<a href=<?php echo $article->link ?>>Read Full Story</a>
			</li>
			<?php } ?>
		</ul>		
	</div>
	<div id="sidebar">		
		<h2 class="first">Related Topics</h2>
		<ul>
			<?php foreach ($related_topics as $topic) { ?>
			<li>
				<img src=<?php echo $topic->image_url; ?> />
				<h3><?php echo $topic->name; ?></h3>
				<div class="clear"></div>
			</li>
			<?php } ?>
		</ul>
		<h2>Related Photos</h2>
		<div id="slideshow">
			<?php foreach ($related_images as $image) { ?>			
			<img src=<?php echo $image->image_large.'?width=240&height=180'; ?> />
			<?php } ?>
		</div>			
	</div>
	<div id="footer"></div>
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript" src="site.js"></script>	
</body>
</html>