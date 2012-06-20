<?php

require_once('newscred.php');

$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';
$query = isset($_GET['query']) ? $_GET['query'] : 'Barack Obama';
try
{
	$topics         = NewsCredTopic::search($access_key, $query);
	
	if(empty($topics)) die('No Topic found.');
	
	$related_topics = $topics[0]->getRelatedTopics(array(
	                                                     'topic_classifications'    => array('Person'), 
	                                                     'topic_subclassifications' => array('Lawyer', 'Politician'),
	                                                    )
	                                              );
	$related_articles = $topics[0]->getRelatedArticles(array(
	                                                          'media_types' => array('Newspaper', 'Blog')
	                                                        )
	                                                  );
	$related_images = $topics[0]->getRelatedImages(array('safe_search' => True));
	$related_videos = $topics[0]->getRelatedVideos(array('pagesize' => 3));
    $related_tweets = $topics[0]->getRelatedTweets(array('pagesize' => 5));
}
catch(Exception $e)
{
	die ($e->getMessage());
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<title>Sample Topic Page | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<div id="header"></div>
	<div id="content">
		<img class="topic" src=<?php echo $topics[0]->image_url; ?> />
		<h1 class="topic"><?php echo $topics[0]->name; ?></h1>
		<span class="type"><?php echo strtoupper($topics[0]->subclassification); ?></span>
		<div class="clear"></div>		
		<ul>
			<?php foreach ($related_articles as $article) { ?>
			<li>
				<span><a href="<?php echo $article->source->website; ?>"><?php echo $article->source->name; ?></a></span>
				<h2><?php echo $article->title; ?></h2>
				<p><?php echo substr($article->description, 0, 400); ?></p>
				<a href=<?php echo $article->link ?>>Read Full Story</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<div id="sidebar">
		<h2 class="first">About</h2>
		<p><?php echo substr($topics[0]->description, 0, 430) . '...'; ?></p>		
		<h2>Related Topics</h2>
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
	<div id="video-sidebar">		
		<div id="related-videos">
			<h2>Related Videos</h2>
			<ul>
			  <?php foreach($related_videos as $video) { 
			    $title = 'Source :'.$video->source_name.'| Date:'.date('j F Y',strtotime($video->published_at))
			    ?>
				<li>
					<a href="javascript:void(0);" class="video_thumbnail"><img src="<?php echo $video->thumbnail; ?>" alt="Related Video Sample" title="<?php echo $title; ?>"/></a>
					<a href="javascript:void(0);" class="video_title"><p><?php echo $video->title; ?></p></a>
					<span class="video_embed_code" style="display:none;"><?php echo $video->embed_code; ?></span>
					<br class="clear" />
				</li>
				<?php } ?>
			</ul>
			
		</div>		
	</div>
	<div id="twitter-sidebar">
		<div id="twitter-buzz">
			<h2>Twitter Buzz</h2>
			<br/>
			<ul>
				<?php foreach($related_tweets as $tweet) {?>
				  <li>
				    <img src="<?php echo $tweet->thumbnail; ?>" width="20" height="20" alt="Scott Msn Mini" />
				      <a class="name" href="<?php echo $tweet->author_link; ?>"><?php echo $tweet->author_name; ?></a> <?php echo $tweet->title; ?>
				    </li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div id="footer"></div>
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript" src="site.js"></script>
</body>
</html>