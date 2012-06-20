<?php
require_once('newscred.php');
$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';
try
{
	$author = new NewsCredAuthor($access_key, 'aab86c756ac5b04ea325e6fd3f105ddc');

	$related_topics   = $author->getRelatedTopics();
	$related_articles = $author->getRelatedArticles();
	$search_authors   = NewsCredAuthor::search($access_key,'paul');
}
catch(NewsCredException $e)
{
	die($e->getMessage());
}

?>
<html lang="en">
	<head>
		<title>Authors | NewsCred</title>
		<link rel="stylesheet" type="text/css" href="site.css" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	</head>
	<body>
		<h1>Author Module</h1>
		<ul>
			<li><a href="#author">Get Author</a></li>
			<li><a href="#topics">Get Related Topics</a></li>
			<li><a href="#articles">Get Related Articles</a></li>
			<li><a href="#search">Search Authors by the name 'Paul'</a></li>
		</ul><br />
		<h2 id="author">Get Author</h2>
		<table>
			<tr>
				<td class="first"><?php echo $author->first_name.' '.$author->last_name; ?></td>
				<td><?php echo $author->guid; ?></td>
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
		
		<h2 id="search">Search Authors by the name 'Paul'</h2>
		<table>
		<?php
		foreach($search_authors as $author)
		{
		?>
			<tr>
				<td class="first"><?php echo $author->first_name.' '.$author->last_name; ?></td>
				<td><?php echo $author->guid; ?></td>
			</tr>
		<?php	
		}
		?>
		</table>
	</body>
</html>