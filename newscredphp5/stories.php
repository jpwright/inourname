<?php
require_once 'newscred.php';

$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';

/*Search stories about Haiti*/
$query = 'haiti';
$options = array('cluster_size' => 2); //Get two articles in each cluster, default is one

//returns a array of clusters, each cluster is an array of articles in itself
$haiti_stories = NewsCredArticle::searchStories($access_key, $query, $options);

/*Get Stories on the category Sports*/
$category = new NewsCredCategory($access_key, 'sports');
//returns a array of clusters, each cluster is an array of articles in itself
$sport_stories = $category->getRelatedStories($options);

/*Get Stories on the topic Roger Federar*/
$topic = new NewsCredTopic($access_key, '49a8d8523c7d5b0c86504d37031ceea3');
//returns a array of clusters, each cluster is an array of articles in itself
$topic_stories = $topic->getRelatedStories($options);

?>
<html lang="en">
<head>
	<title>Article Clustering | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
	<h1>Article Clustering</h1>
	
	<ul>
		<li><a href="#search">Search Stories on Haiti</a></li>
		<li><a href="#category">Search Stories on Sports</a></li>
		<li><a href="#topic">Search Stories on topic Roger Federar</a></li>
	</ul><br />
	
	<h2 id="search">Search Stories on Haiti</h2>
	<?php 
	$i = 1;
	foreach($haiti_stories as $cluster):?>
	<h3><?php print "Cluster ".$i?></h3>
	    <table>
    		<?php foreach ($cluster as $article) : ?>
    		<tr>
    			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
    			<td><?php echo "From ".$article->source->name;  ?></td>
    		</tr>
    		<?php endforeach; ?>
    	</table>
	<?php $i++; endforeach;?>
	
	<a href="#">Back to top</a><br/>
	
	<h2 id="category">Search Stories on Sports</h2>
	<?php 
	$i = 1;
	foreach($sport_stories as $cluster):?>
	<h3><?php print "Cluster ".$i?></h3>
	    <table>
    		<?php foreach ($cluster as $article) : ?>
    		<tr>
    			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
    			<td><?php echo "From ".$article->source->name;  ?></td>
    		</tr>
    		<?php endforeach; ?>
    	</table>
	<?php $i++; endforeach;?>
    
    <a href="#">Back to top</a><br/>
    
    <h2 id="topic">Search Stories on Roger Federar</h2>
	<?php 
	$i = 1;
	foreach($topic_stories as $cluster):?>
	<h3><?php print "Cluster ".$i?></h3>
	    <table>
    		<?php foreach ($cluster as $article) : ?>
    		<tr>
    			<td class="first"><a href=<?php echo $article->link ?>><?php echo $article->title; ?></a></td>
    			<td><?php echo "From ".$article->source->name;  ?></td>
    		</tr>
    		<?php endforeach; ?>
    	</table>
	<?php $i++; endforeach;?>
	
	<a href="#">Back to top</a><br/>
</body>
</html>