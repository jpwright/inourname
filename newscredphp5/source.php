<?php

require_once('newscred.php');

$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';

try
{
	$source 		  = new NewsCredSource($access_key, 'a5364a204a0422bdcf23acc6c5c88af8');
	$related_topics   = $source->getRelatedTopics();
	$related_articles = $source->getRelatedArticles();
	$search_sources   = NewsCredSource::search($access_key, 'guardian');
}
catch(Exception $e)
{
	die($e->getMessage());
}
?>
<html lang="en">
<head>
	<title>Source Module | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<h1>Source Module</h1>
	<ul>
		<li><a href="#source">Get Source</a></li>
		<li><a href="#topics">Get Related Topics</a></li>
		<li><a href="#articles">Get Related Articles</a></li>
		<li><a href="#search">Search Sources by the name 'Guardian'</a></li>
	</ul><br />
	<h2 id="source">Get Source</h2>
	<table>
		<tr>
			<td class="first"><?php echo $source->name; ?></td>
			<td><?php echo $source->guid; ?></td>
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
	<h2 id="search">Search Sources by the name 'Guardian'</h2>
	<table>
		<?php foreach ($search_sources as $source) { ?>
		<tr>
			<td class="first">
			    <img src="<?php echo $source->thumbnail;?>" />
			    <a href=<?php echo $source->website;?>><?php echo $source->name; ?></a>
			</td>
			<td><?php echo $source->guid; ?></td>
		</tr>
		<?php } ?>
	</table>
	<a href="#">Back to top</a><br/>
</body>
</html>