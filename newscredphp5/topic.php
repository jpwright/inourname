<?php

require_once('newscred.php');

$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';
$sources    = array('a5364a204a0422bdcf23acc6c5c88af8','f5cf0126fabbbf97a44c9252761b60dd','-e5b7feb6870f7c251d7ad10c8b9b8820');
try
{
	$searched_topics          = NewsCredTopic::search($access_key, 'barack obama');
	$topic  		          = new NewsCredTopic($access_key, $searched_topics[0]->guid);
	$related_topics           = $topic->getRelatedTopics(array('pagesize' => 4, 
	                                                           'topic_classifications' => array('Person', 'Company')
	                                                          )
	                                                    );
	$related_articles         = $topic->getRelatedArticles(array('pagesize' => 4));
	$related_articles_sources = $topic->getRelatedArticles(array('sources'=> $sources));
	$related_images 		  = $topic->getRelatedImages();
	$topic_sources			  = $topic->getRelatedSources();
	$extracted_topics  		  = NewsCredTopic::extract($access_key, 
	                                                   $related_articles[0]->title, 
	                                                   array('exact'=>False));
}
catch(Exception $e)
{
	die($e->getMessage());
}

?>
<html lang="en">
<head>
	<title>Topic Module | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<h1>Topic Module</h1>
	<ul>
		<li><a href="#search">Search Topics</a></li>
		<li><a href="#topic">Get Topic</a></li>
		<li><a href="#topics">Get Related Topics</a></li>
		<li><a href="#articles">Get Related Articles</a></li>
		<li><a href="#articles-sources">Get Related Articles Filtered By Sources</a></li>
		<li><a href="#images">Get Related Images</a></li>
		<li><a href="#sources">Get Topic Sources</a></li>
		<li><a href="#extract">Extract Related Topics</a></li>
	</ul><br />
	<h2 id="search">Search Topics</h2>
	<table>
		<?php foreach ($searched_topics as $searched_topic) { ?>
		<tr>
			<td class="first"><?php echo $searched_topic->name; ?></td>
			<td><?php echo $searched_topic->guid; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="topic">Get Topic</h2>
	<table>
		<tr>
			<td class="first">
				Name: <?php echo $topic->name; ?><br/>
				GUID: <?php echo $topic->guid; ?><br/>
				Classification: <?php echo $topic->classification; ?><br/>
				Sub-classification: <?php echo $topic->subclassification; ?><br/><br/>
				Description:<br/>
				<?php echo $topic->description; ?><br/><br/>	
			</td>
		</tr>
	</table>
	<h2 id="topics">Get Related Topics</h2>
	<table>
		<?php foreach ($related_topics as $topic) { ?>
		<tr>
			<td class="first"><?php printf("%s [%s - %s]", $topic->name, $topic->classification, $topic->subclassification); ?></td>
			<td><?php echo $topic->guid; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="articles">Get Related Articles</h2>
	<table>
		<?php foreach ($related_articles as $article) { ?>
		<tr>
			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
			<td><?php echo "From ".$article->source; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="articles-sources">Get Related Articles Filtered By Sources</h2>
	<table>
		<?php foreach ($related_articles_sources as $article) { ?>
		<tr>
			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
			<td><?php echo "From ".$article->source; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="images">Get Related Images</h2>
	<table>
		<?php foreach ($related_images as $image) { ?>
		<tr>
			<td class="first"><?php echo $image->image_small; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="sources">Get Topic Sources</h2>
	<table>
		<?php foreach ($topic_sources as $source) { ?>
		<tr>
			<td class="first"><?php echo $source->name; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="extract">Extract Related Topics</h2>
	<table>
		<?php foreach ($extracted_topics as $topic) { ?>
		<tr>
			<td class="first"><?php echo $topic->name; ?></td>
			<td><?php echo $topic->guid; ?></td>
		</tr>
		<?php } ?>
	</table>
	<a href="#">Back to top</a><br/>
</body>
</html>