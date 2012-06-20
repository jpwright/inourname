<?php

require_once('newscred.php');
$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';
$sources    = array('a5364a204a0422bdcf23acc6c5c88af8',
                    '-e5b7feb6870f7c251d7ad10c8b9b8820',
                    'f5cf0126fabbbf97a44c9252761b60dd');

try
{
	$searched_articles 		  = NewsCredArticle::search($access_key, 'obama');
	$article  				  = new NewsCredArticle($access_key, $searched_articles[0]->guid);
	$related_topics 		  = $article->getRelatedTopics();
	$related_articles 		  = $article->getRelatedArticles();
	$related_articles_sources = $article->getRelatedArticles(array('sources'=> $sources));
	$related_images 		  = $article->getRelatedImages();
}
catch(Exception $e)
{
	die($e->getMessage());
}

?>
<html lang="en">
<head>
	<title>Article Module | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<h1>Article Module</h1>
	<ul>
		<li><a href="#search">Search Articles</a></li>
		<li><a href="#article">Get Article</a></li>
		<li><a href="#topics">Get Related Topics</a></li>
		<li><a href="#articles">Get Related Articles</a></li>
		<li><a href="#articles-sources">Get Related Articles Filtered By Sources</a></li>
		<li><a href="#images">Get Related Images</a></li>
	</ul><br />
	<h2 id="search">Search Articles</h2>
	<table>
		<?php foreach ($searched_articles as $article) { ?>
		<tr>
			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
			<td><?php echo $article->guid; ?></td>
		</tr>
		<?php } ?>
	</table>
	<h2 id="article">Get Article</h2>
	<table>
		<tr>
			<td class="first">
				Title: <a href=<?php echo $article->link ?>><?php echo $article->title; ?></a><br/>
				GUID: <?php echo $article->guid; ?><br/>		
				Description:<br/>
				<?php echo $article->description; ?><br/><br/>	
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
	<a href="#">Back to top</a><br/>
</body>
</html>