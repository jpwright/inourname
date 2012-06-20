<?php
require('newscred.php');
$access_key = 'c4bcc3f7c9bf9ec159f51da0a86ca658';
?>
<?php


    try {
        $theme_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('theme'), 
                                                                      'pagesize' => 24));
    
                                                                                                                                                
        $people_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('person'), 
                                                                       'pagesize' => 24));

        $events_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('event'), 
                                                                       'pagesize' => 24));

                                                                                                                                                
        $company_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('company'), 
                                                                        'pagesize' => 24));
                                                                            
        $organization_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('organization'), 
                                                                             'pagesize' => 24));

    } catch (NewsCredException $e) {
        die($e->getMessage());
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
    <title>Topic Directory | NewsCred Platform</title>
    <link rel="stylesheet" type="text/css" href="site.css" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
    <div id="header"></div>
    <div id="content_topic_dir">
        <h1>NewsCred Topic Page Directory</h1>
        
        <h2>Themes</h2>
        <ul class="topics">
            <?php foreach (array_slice($theme_topics, 0, 24) as $theme_topic): ?>
            <li>
                <img src="<?php echo $theme_topic->image_url; ?>" alt="<?php echo $theme_topic->name; ?>" />
                <h3>
                    <a href="topicpage.php?query=<?php echo $theme_topic->name; ?>">
                        <?php echo $theme_topic->name; ?>
                    </a>
                </h3>
            </li>
            <?php endforeach; ?>
            <li class="clear"> </li>
        </ul>
        <a class="more" href="topic_directory_more.php?category=theme">More Theme Topics &rarr;  </a>
        
        <h2>People</h2>
        <ul class="topics">
            <?php foreach(array_slice($people_topics, 0, 24) as $people_topic): ?>
            <li>
                <img src="<?php echo $people_topic->image_url; ?>" alt="<?php echo $people_topic->name; ?>" />
                <h3>
                    <a href="topicpage.php?query=<?php echo $people_topic->name; ?>">
                        <?php echo $people_topic->name; ?>
                    </a>
                </h3>
            </li>
            <?php endforeach; ?>
            <li class="clear"> </li>
        </ul>
        <a class="more" href="topic_directory_more.php?category=person">More People Topics &rarr;  </a>
        
        <h2>Company</h2>
        <ul class="topics">
            <?php foreach(array_slice($company_topics, 0, 24) as $company_topic): ?>
            <li>
                <img src="<?php echo $company_topic->image_url; ?>" alt="<?php echo $company_topic->name; ?>" />
                <h3>
                    <a href="topicpage.php?query=<?php echo $company_topic->name; ?>">
                        <?php echo $company_topic->name; ?>
                    </a>
                </h3>
            </li>
            <?php endforeach; ?>
            <li class="clear"> </li>
        </ul>
        <a class="more" href="topic_directory_more.php?category=company">More Company Topics &rarr;  </a>
        
        <h2>Organaization</h2>
        <ul class="topics">
            <?php foreach(array_slice($organization_topics, 0, 24) as $organization_topic): ?>
            <li>
                <img src="<?php echo $organization_topic->image_url; ?>" alt="<?php echo $organization_topic->name; ?>" />
                <h3>
                    <a href="topicpage.php?query=<?php echo $organization_topic->name; ?>">
                        <?php echo $organization_topic->name; ?>
                    </a>
                </h3>
            </li>
            <?php endforeach; ?>
            <li class="clear"> </li>
        </ul>
        <a class="more" href="topic_directory_more.php?category=organization">More Organization Topics &rarr;  </a>
    </div>

    <div id="footer"></div>
</body>
</html>