<?php
require('newscred.php');
$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';

$category = array($_GET['category']);

try {   
    $topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => $category, 
                                                            'pagesize' => 100));
}
catch (NewsCredException $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
    <title>Topic Directory for <?php echo $_GET['category']; ?> | NewsCred Platform</title>
    <link rel="stylesheet" type="text/css" href="site.css" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
    <div id="header">
    </div>
    
    <div id="content_topic_dir">
        <h1>NewsCred Topic Page Directory</h1>
        <h2>Showing all <?php echo $_GET['category']; ?> topics</h2>
        <ul class="topics">
            <?php foreach ($topics as $topic): ?>
            <li>
                <img src="<?php echo $topic->image_url; ?>" alt="<?php echo $topic->name;?>"/>
                <h3>
                    <a href="topicpage.php?query=<?php echo $topic->name?>"><?php echo $topic->name; ?>;</a>
                </h3>
            </li>
            <?php endforeach; ?>
            <li class="clear"> </li>
        </ul>
        <a class="more" href="topic_directory.php"> Back To Topic Directory Page &rarr;</a>        
        
    </div>

    <div id="footer"></div>
</body>
</html>