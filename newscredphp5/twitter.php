<?php
require_once('newscred.php');

$key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';

$query = (isset($_GET['query'])) ? $_GET['query'] : 'obama';
$options = array('pagesize' => 6);

$tweets = NewsCredTwitter::search($key, $query, $options);
?>

<html lang="en">
<head>
	<title>Twitter Module | NewsCred Platform</title>
	<link rel="stylesheet" type="text/css" href="site.css" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
    <body>
        <h1>Twitter Module</h1>
        <div>
            <h2>Twitter Buzz</h2>
            <?php if (!empty($tweets)) : ?>
            <ul>
                <?php foreach($tweets as $tweet) : ?>
                  <li>
                    <img src="<?php echo $tweet->thumbnail; ?>" width="20" height="20" />
                    <a class="name" href="<?php echo $tweet->author_link; ?>">
                        <?php echo $tweet->author_name; ?>
                    </a>
                    <?php echo $tweet->title; ?>
                  </li>
                <?php endforeach; ?>
            </ul>
            <?php else: echo "No Tweets found.";
            endif;?>
        </div>
        <p>&nbsp;</p>
        <p>[Pass the query as request parameter i.e. <em>?query=Iphone</em>  and see the output.]</p>
    </body>
</html>