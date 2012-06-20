<?php
require('newscred.php');

$text = "'Whitewash' could slow global warming.".
        " A Peruvian scientist has called on his country to help slow the melting of Andean glaciers by daubing white paint on the rock and earth left behind by receding ice so they will".      
        " absorb less heat. Eduardo Gold, president of non-governmental organisation Glaciers of Peru, made the suggestion in a presentation Tuesday to the country's parliamentary commission". 
        " on climate change. His idea has already attracted interest from the World Bank, and is among a series of projects to counter climate change that the organisation is considering,".
        " Gold told AFP.";
    
$query = (isset($_GET['text']) && !empty($_GET['text'])) ? stripslashes($_GET['text']) : $text;

//Search topics either using default or user supplied text
try{
    $topics_mentioned = NewsCredTopic::extract('c4bcc3f7c9bf9ec159f51da0a86ca658', $query, array('exact' => True));
    $topics_related   = NewsCredTopic::extract('c4bcc3f7c9bf9ec159f51da0a86ca658', $query);
}catch(Exception $e){
    die($e->getMessage());
}


?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Testing Topic extract</title>
    <link rel="stylesheet" type="text/css" href="site.css" />
</head>
<body>
    <h1>Testing Extract Topic Method</h1>
    <h2>The Text</h2>
    <p><?php echo $query; ?></p>
    <p>&nbsp;</p>
    <h2 style="text-decoration: underlined;">Topics Extracted - Mentioned</h2>
    <?php if(!empty($topics_mentioned)) { ?>
    <ul>
        <?php foreach($topics_mentioned as $topic) { ?>
        <li><?php echo $topic->name; ?></li>
        <?php } ?>
    </ul>
    <?php } else print "<b>No topics found.</b>"?>
    <p>&nbsp;</p>
    <h2 style="text-decoration: underlined;">Topics Extracted - Related</h2>
    <?php if(!empty($topics_related)) { ?>
    <ul>
        <?php foreach($topics_related as $topic) { ?>
        <li><?php echo $topic->name; ?></li>
        <?php } ?>
    </ul>
    <?php } else print "<b>No topics found.</b>"?>
    <p>&nbsp;</p>
    <!--
    <p>[Pass the text as request parameter i.e. ?text=Apple launches the new Iphone and see the output.]</p> -->
</body>
</html>