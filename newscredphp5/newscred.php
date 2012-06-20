<?php

/**
 * PHP5 Wrapper for NewsCred Platform API
 * 
 * This file contains classes that make RESTful web service requests to the NewsCred Platform
 * server and pull contents(topics, articles, images, videos, Twitter conversations etc.) 
 *
 * @author  Rubayeet Islam <rubayeet@newscred.com>
 * @version 0.9.5
 * @package NewsCredPHP5
 */

abstract class NewsCred
{
    const NEWSCRED_DOMAIN = 'http://api.newscred.com';
    
    public $key  = '';
    public $guid = '';
    public $url  = '';
    public $module;

    /**
     * Get topics related to the Topic/Article/Category/Source
     * @access public
     * @param array $options
     * @return array
     */
    public function getRelatedTopics($options = array())
    {       
        if (property_exists($this, 'has_related_topics') && 
            $this->has_related_topics === FALSE) 
            return;
        
        return $this->getRelatedStuff('topic', $options);
    }

    /**
     * Get articles related to the Topic/Article/Category/Source
     * @param array $options
     * @return array
     */
    public function getRelatedArticles($options = array())
    {
        if (property_exists($this, 'has_related_articles') && 
            $this->has_related_articles === FALSE) 
            return;
        
        return $this->getRelatedStuff('article', $options);
    }
    
    protected function populate()
    {
        $this->url = sprintf("%s/%s/%s?access_key=%s", self::NEWSCRED_DOMAIN,
                                                       $this->module, 
                                                       $this->guid,
                                                       urlencode($this->key));
        try {
            
            $xml = NewsCred::get($this->url);
        
        } catch (NewsCredException $e) {
            
            throw new NewsCredException('Class::  '.__CLASS__.
                                        ' Line:: '.__LINE__.' '.$e->getMessage());
        }

        $parsed_xml = NewsCredParser::parse($this->module, $xml, $this->key);

        foreach(get_object_vars($this) as $property => $value) {
            
            if(property_exists($parsed_xml[0], $property))
                $this->$property = $parsed_xml[0]->$property;
        }
    }
    
    protected function getRelatedStuff($name, $options = array())
    {
        $pluralize = create_function('$name', 'return ($name !== "story") ? $name."s" : "stories";');
        
        if (empty($this->key)) {
            
            throw new NewsCredException('Class::  '.__CLASS__.' Line:: '.__LINE__.' '.
                                        NewsCredException::EXCEPTION_NO_ACCESS_KEY);
            return;
        }
        
        $identifier = ($this->module === 'category') ? $this->name : $this->guid;
        
        $this->url = sprintf("%s/%s/%s/%s?access_key=%s", NewsCred::NEWSCRED_DOMAIN,
                                                          $this->module,
                                                          $identifier,
                                                          $pluralize($name),
                                                          urlencode($this->key));                                                          
        if ($options)
            $this->url .= NewsCred::getRequestParams($options);

        try {
            
            $xml = NewsCred::get($this->url);
        
        } catch(NewsCredException $e) {
            
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.' '.
                                        $e->getMessage());
        }
        
        if ($name === 'story') {
            return NewsCredParser::parse('cluster', $xml, $this->key);
        }
        
        return  NewsCredParser::parse($name, $xml, $this->key);
    }
    
    protected static function _search($key, $name, $query, $options = array())
    {
        $pluralize = create_function('$name', 'return ($name !== "story") ? $name."s" : "stories";');
        
        if (empty($key)) {
            
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.' '.
                                        NewsCredException::EXCEPTION_NO_ACCESS_KEY);
            return;
        }
        
        $url = sprintf("%s/%s?access_key=%s&query=%s", self::NEWSCRED_DOMAIN, 
                                                       $pluralize($name), 
                                                       $key, 
                                                       urlencode($query));
        if ($options)
            $url .= self::getRequestParams($options);
        
        try {
            
            $xml = self::get($url);
        
        } catch(NewsCredException $e) {
            
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.
                                        ' '.$e->getMessage());
        }
        
        if ($name === 'story') {
            return NewsCredParser::parse('cluster', $xml, $key);
        }
        
        return NewsCredParser::parse($name, $xml, $key);
    }
    
    /**
     * Request the NewsCred API $url
     * @param string $url
     * @param string $format (xml|json)
     * @return SimpleXML|stdClass
     * @access public
     * @static
     */
    public static function get($url, $format='xml')
    {
        if ($format === 'json')
            $url .= '&format=json';

        if (function_exists('curl_init')) {
            
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 120);

            $response = curl_exec($curl);

            $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $content_type     = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
            curl_close($curl);

            if ($http_status_code !== 200){
                NewsCred::handleAPIError($url, $response, $content_type);
            }
        } 
        else {
            
            if (ini_get('allow_url_fopen') === '') {
                
                throw new NewsCredException('Class:: '.__CLASS__.
                                            ' Line:: '.__LINE__.' '.
                                            NewsCredException::EXCEPTION_REMOTE_URL_ACCESS_DENIED);
                return;
            }

            $response = file_get_contents($url);
        }
        
        //throw exception if fails to get response from the platform
        if ($response === FALSE) {
            
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.
                                        ' '.NewsCredException::EXCEPTION_API_RESPONSE_GET_FAILED.
                                        $url);
        }

        return NewsCred::parseResponse($response, $url, $format);
    }
    
    /**
     * Parse XML/JSON response returned by NewsCred API
     * @access public
     * @param <string> $response
     * @param <string> $url
     * @param <string> $format
     * @return <SimpleXML>|<stdClass>
     * @static
     */
    public static function parseResponse($response, $url, $format='xml')
    {
        $parsed_response = ($format === 'json') ? json_decode($response) : @simplexml_load_string($response);

        if ($parsed_response === NULL) {
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: ' .__LINE__.
                                        ' '.NewsCredException::EXCEPTION_JSON_PARSE_ERROR.$url);
        }
        elseif ($parsed_response === False) {
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: ' .__LINE__.
                                        ' '.NewsCredException::EXCEPTION_XML_PARSE_ERROR.$url);
        }

        return $parsed_response;
    }
    
    /**
     * Parses error info returned by the API
     * @access public
     * @param <string> $url
     * @param <string> $api_response
     * @param <string> $format
     * @static
     */
    public static function handleAPIError($url, $api_response, $format='text/xml')
    {
        $error = ($format === 'application/json') ? json_decode($api_response)->error
                                                  : simplexml_load_string($api_response);
        throw new NewsCredException(sprintf('Class:: %s Line:: %s %s URL: %s, Code: %d, Message: %s',
                                    __CLASS__, __LINE__, NewsCredException::EXCEPTION_API_ERROR,
                                    $url, $error->code, $error->message));
    }

    /**
     * Build the HTTP request string from the key,value pairs in $options
     * @param array $options
     * @return string
     * @access public
     * @static     
     */
    public static function getRequestParams($options)
    {
        if(!is_array($options) || empty($options)) return;
        
        $request_params = '';
        
        foreach($options as $key => $value) {
            if (is_array($value) && ($key === 'sources' || $key === 'source_countries')) {
                //sources and source_countries params are to be joined by space
                //and don't need to be parameterized
                $request_params .= '&'. $key . '=' . urlencode(join(' ', $value));
            }
            elseif (is_bool($value)) {
                //PHP turns boolean true/false into 1/0 when casted to string.
                //Need to pass true/false as is.
                $request_params .= ($value === True) ? '&'. $key . '=' . 'true' : 
                                                       '&'. $key . '=' . 'false';
            }
            else {
                
                $request_params .= '&'. $key . '=' . urlencode(NewsCred::parameterize($value));
            }
        }
        
        return $request_params;
    }
    
    /**
     * Format $param as required by the API. ('Football player' => 'football-player')
     * @access public
     * @param string|array $param
     * @return string
     * @static
     */
    public static function parameterize($param)
    {
        $parameterize = create_function('$string', 'return str_replace(" ", "-",'.
                                                   ' strtolower($string));');
      
        if(is_array($param)) {
            
            return join(array_map($parameterize, $param), ' ');
        }
        
        return $parameterize($param);
    }
    
    /**
     * Parses the SimpleXML object and returns an array of NewsCredModule objects
     * @access public
     * @param string $module
     * @param string $key
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    public static function createObjects($module, $key, $xml)
    {
        $objects      = array();        
        $parsed_nodes = NewsCredParser::parse($module, $xml);

        if (!empty($parsed_nodes)) {
            
            foreach($parsed_nodes as $parsed_node) {
                
                switch ($module) {
                    
                    case 'topic'   : $object = new NewsCredTopic(); break;
                    case 'article' : $object = new NewsCredArticle(); break;
                    case 'source'  : $object = new NewsCredSource(); break;
                    case 'author'  : $object = new NewsCredAuthor(); break;
                    case 'image'   : $object = new NewsCredImage(); break;
                    case 'video'   : $object = new NewsCredVideo(); break;
                    case 'tweet'   : $object = new NewsCredTwitter(); break;
                }
                
                $object->key = $key;
                
                foreach(get_object_vars($object) as $property => $value) {
                    
                    if(property_exists($parsed_node, $property))
                        $object->$property = $parsed_node->$property;
                }
                array_push($objects, $object);
            }
        }
        
        return $objects;
    }
}

/**
 * Class for parsing SimpleXML nodes to PHP objects
 */
class NewsCredParser
{
    /**
     * Parses a SimpleXML object and returns an array of NewsCred objects
     * @access public
     * @param string $nodeType
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    public static function parse($node_type, $xml, $key = NULL)
    {
        $objects = array();
        
        $nodes = $xml->xpath('//'.$node_type);
        
        if (empty($nodes)) return;
        
        foreach($nodes as $node) {
            
            if ($node_type === 'cluster'){
                array_push($objects, self::_parseClusterNode($node, $key));
            }                
            else {
                
                $method = '_parse'.ucwords($node_type).'Node';                
                array_push($objects, self::$method($node, $key));
            }
        }

        return $objects;
    }

    /**
     * Parses a <cluster> node and returns a cluster(an array of NewsCredArticle objects)
     * @access private
     * @param SimpleXML $cluster_node
     * @param string $key
     * @return array
     * @static
     */
    
    private static function _parseClusterNode($cluster_node, $key)
    {
        $cluster = array();
        
        if(!isset($cluster_node->article_set)) return;
        
        //parse each cluster->article_set->article and create a NewsCredArticle
        //object
        foreach($cluster_node->article_set->article as $article_node) {
            
            $article = new NewsCredArticle($key);
            
            foreach(get_object_vars($article) as $property => $value) {

                if(property_exists($article_node, $property))
                    $article->$property = (string) $article_node->$property;
            }
            
            //parse the <source> node of the <article> node and create a NewsCredSource object
            $source = new NewsCredSource($key);
            foreach(get_object_vars($source) as $property => $value) {

                if(property_exists($article_node->source, $property))
                    $source->$property = (string) $article_node->source->$property;
            }
            //Assign the NewsCredSource object as a property of the NewsCredArticle object
            $article->source = $source;
            
            //push the article object into the cluster
            array_push($cluster, $article);
        }
        
        return $cluster;
    }
    
    /**
     * Parses the <topic> nodes and returns a PHP object
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseTopicNode($topic_node, $key = NULL)
    {

        $topic = new NewsCredTopic();
        $topic->key = (isset($key)) ? $key : $topic->key;
        
        foreach(get_object_vars($topic) as $property => $value) {
            
            if ($property === 'classification' || $property === 'subclassification') {
                
                $child_node = 'topic_'.$property;
                
                if (isset($topic_node->$child_node->name))
                    $topic->$property = (string) $topic_node->$child_node->name;
                
                elseif (isset($topic_node->$child_node))
                    $topic->$property = (string) $topic_node->$child_node;
                
                else $topic->$property = '';
            }
            else {
                
                if(property_exists($topic_node, $property))
                    $topic->$property = (string) $topic_node->$property;
            }
        }

        return $topic;
    }
    
    /**
     * Parses the <article> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseArticleNode($article_node, $key = NULL)
    {
        $article = new NewsCredArticle();
        $article->key = (isset($key)) ? $key : $article->key;
        
        foreach(get_object_vars($article) as $property => $value) {
            
            switch($property) {
                
                case 'category':
                    $article->category = isset($article_node->category) ? 
                                         (string) $article_node->category->name : '';
                    break;
                
                case 'thumbnail':
                    $article->thumbnail = isset($article_node->thumbnail->link) ?
                                          (string) $article_node->thumbnail->link : '';
                    $article->thumbnail_original = isset($article_node->thumbnail->original_image) ?
                                                   (string) $article_node->thumbnail->original_image : '';
                    break;
                
                case 'source':
                    if (isset($article_node->source)) {
                        $article->source = self::_parseSourceNode($article_node->source, $key);
                    }
                    break;
                
                default:
                    if(property_exists($article_node, $property))
                        $article->$property = (string) $article_node->$property;
            }            
        }
        
        if (isset($article_node->topic_set)) {
            
            $article->topics = array();
            
            foreach($article_node->topic_set->topic as $topic) {
                array_push($article->topics, self::_parseTopicNode($topic, $key));
            }
        }

        return $article;
    }

    /**
     * Parses the <source> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseSourceNode($source_node, $key)
    {
        $source = new NewsCredSource();
        $source->key = (isset($key)) ? $key : $source->key;
        
        foreach(get_object_vars($source) as $property => $value) {
            
            if(property_exists($source_node, $property))
                $source->$property = (string) $source_node->$property;
        }
        
        return $source;
    }
    
    /**
     * Parses the <image> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseImageNode($image_node, $key)
    {
        $image = new NewsCredImage();
        $image->key = (isset($key)) ? $key : $image->key;
        
        foreach(get_object_vars($image) as $property => $value) {
            
            if(property_exists($image_node, $property))
                $image->$property = (string) $image_node->$property;
        }
        
        if (isset($image_node->urls)) {
            
            $image->image_small = isset($image_node->urls->small) ?
                                  (string) $image_node->urls->small : null;
            
            $image->image_medium = isset($image_node->urls->medium) ?
                                  (string) $image_node->urls->medium : null;
            
            $image->image_large = isset($image_node->urls->large) ?
                                  (string) $image_node->urls->large : null;
        }
        
        return $image;
    }
    
    /**
     * Parses the <tweet> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseTweetNode($tweet_node, $key)
    {
        $twitter = new NewsCredTwitter();
        $twitter->key = (isset($key)) ? $key : $twitter->key;
        
        foreach(get_object_vars($twitter) as $property => $value) {
            
            if(property_exists($tweet_node, $property))
                $twitter->$property = (string) $tweet_node->$property;
        }
        
        return $twitter;
    }
    
    /**
     * Parses the <video> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseVideoNode($video_node, $key)
    {
        $video = new NewsCredVideo();
        $video->key = (isset($key)) ? $key : $video->key;
        
        foreach(get_object_vars($video) as $property => $value) {
            
            switch($property) {
                case 'category':
                    $video->catgory = (isset($video_node->category)) ?
                                      (string) $video_node->category->name : '';
                case 'source':
                    if (isset($video_node->source)) {
                        $video->source = self::_parseSourceNode($video_node->source, $key);
                    }
                    
                default:
                    if(property_exists($video_node, $property))
                        $video->$property = (string) $video_node->$property;
            }
            
        }
        
        if (isset($video_node->topic_set)) {
            
            $video->topics = array();
            
            foreach($video_node->topic_set->topic as $topic) {
                array_push($video->topics, self::_parseTopicNode($topic, $key));
            }
        }
        
        return $video;
    }
    
    /**
     * Parses the <author> nodes and returns an array of PHP objects
     * @access private
     * @param SimpleXML $xml
     * @return array
     * @static
     */
    private static function _parseAuthorNode($author_node, $key)
    {
        $author = new NewsCredAuthor();
        $author->key = (isset($key)) ? $key : $author->key;
        
        foreach(get_object_vars($author) as $property => $value) {
            
            if(property_exists($author_node, $property))
                $author->$property = (string) $author_node->$property;
        }
        
        return $author;
    }
}

/**
 * Custom Exception class for NewsCred
 */
class NewsCredException extends Exception
{
    const EXCEPTION_REMOTE_URL_ACCESS_DENIED = "Failed to connect to NewsCred Platform server. Either install PHP cURL extension on your server or set 'allow_url_fopen' flag to 'On'.";
    const EXCEPTION_API_RESPONSE_GET_FAILED  = 'Failed to get API response for the request: ';
    const EXCEPTION_XML_PARSE_ERROR          = 'Error parsing the XML response for the request: ';
    const EXCEPTION_JSON_PARSE_ERROR         = 'Error parsing the JSON response for the request: ';
    const EXCEPTION_NO_ACCESS_KEY            = 'No access key provided.';
    const EXCEPTION_API_ERROR                = 'NewsCred API Error.';


    const EXCEPTION_AUTHENTICATION_FAILED    = 'Authentication Failed. Please check the access key.';
    const EXCEPTION_INVALID_GUID             = 'Invalid GUID provided.';
    
    const EXCEPTION_PLATFORM_RETURNED_ERROR  = 'NewsCred Platform returned Internal Server Error for this request: ';
}


/**
 * Represents a Topic in the NewsCred Platform
 */
class NewsCredTopic extends NewsCred
{
    public $name;
    public $link;
    public $dashed_name;
    public $image_url;
    public $classification;
    public $subclassification;
    public $description;
    
    /**
     * Constructor of the class
     * @access public
     * @param string $key
     * @param string $guid
     */
    public function __construct($key = null, $guid = null)
    {
        //Initialize all properties to empty string
        foreach(get_object_vars($this) as $property => $value) {
            $this->$property = '';
        }
        
        $this->module = 'topic';
        $this->key    = $key;
        $this->guid   = $guid;
        
        if (!empty($key) && !empty($guid)) {
            $this->populate();
        }
    }
    
    
    public function __call($method_name, $arguments)
    {
        switch($method_name) {
            
            case 'getRelatedStories': $name = 'story';  break;
            case 'getRelatedSources': $name = 'source'; break;
            case 'getRelatedImages' : $name = 'image';  break;
            case 'getRelatedVideos' : $name = 'video';  break;
            case 'getRelatedTweets' : $name = 'tweet';  break;

            /*Raise error when no matching method name found.*/
            default:
                trigger_error('Call to undefined method: '.__CLASS__.
                              '::'.$method_name.'() in '.__FILE__.
                              ' on line '.__LINE__, E_USER_ERROR);
        }
        
        if (is_array($arguments) && !empty($arguments))
            $options = $arguments[0];
        
        else $options = NULL;
        
        return $this->getRelatedStuff($name, $options);
    }
    
    /**
     * Get metadata of a topic
     * @access public
     * @param array $options
     * @return stdClass
     */
    public function getMetaData($options = array())
    {

        $this->url = sprintf("%s/%s/%s?access_key=%s", NewsCred::NEWSCRED_DOMAIN,
                                                       $this->module,
                                                       $this->guid,
                                                       urlencode($this->key));
        if ($options)
            $this->url .= NewsCred::getRequestParams($options);

        try {

            $json_response = NewsCred::get($this->url, 'json');

        } catch(NewsCredException $e) {

            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.' '.
                                        $e->getMessage());
        }


        return (isset($json_response->topic->metadata)) ? $json_response->topic->metadata : null;
        
    }

    /**
     * Extract topics from the given $query
     * @access public
     * @param string $key
     * @param string $query
     * @return array
     * @static
     */
    public static function extract($key, $query, $options = array())
    {
        if (empty($key)) {
            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.' '.
                                        NewsCredException::EXCEPTION_NO_ACCESS_KEY);
            return;
        }
                
        $api_method_name = (isset($options['exact']) && ($options['exact'] == True))
                           ? 'extract' : 'related';
        
        $url = NewsCred::NEWSCRED_DOMAIN.'/topics/'.$api_method_name.'?access_key='.$key
                                        .'&query='.urlencode($query);
        
        if (!empty($options)) {
            $url .= NewsCred::getRequestParams($options);
        }

        try {
            
            $xml = NewsCred::get($url);
        
        } catch (NewsCredException $e) {

            throw new NewsCredException('Class:: '.__CLASS__.' Line:: '.__LINE__.
                                        ' '.$e->getMessage());
        }

        return NewsCredParser::parse('topic', $xml, $key);
    }
    
    /**
     * Searches topics with the given $query
     * @access public
     * @param string $key
     * @param string $query
     * @return array
     * @static
     */
    public static function search($key, $query, $options = array())
    {
        return parent::_search($key, 'topic', $query, $options);
    }
}

/**
 * Represents an Article in the NewsCred Platform
 */
class NewsCredArticle extends NewsCred
{
    public $title;
    public $source;
    public $source_guid;
    public $source_website;
    public $created_at;
    public $published_at;
    public $description;
    public $category;
    public $link;
    public $thumbnail;
    public $thumbnail_original;
    public $topics;
    
    /**
     * Constructor of the class
     * @access public
     * @param string $key
     * @param string $guid
     */
    public function __construct($key = null, $guid = null)
    {
        //Initialize all properties to empty string
        foreach(get_object_vars($this) as $property => $value) {
            $this->$property = '';
        }
        
        $this->module = 'article';
        $this->key    = $key;
        $this->guid   = $guid;
        
        if (!empty($key) && !empty($guid)) {            
            
            $this->populate();
            //supporting legacy code
            $this->source_guid = $this->source->guid;
            $this->source_website = $this->source->website;
        }
    }

    /**
     * Get images related to the Article
     * @access public
     * @param array $options
     * @return arrat
     */
    public function getRelatedImages($options = array())
    {
        return $this->getRelatedStuff('image', $options);
    }

    /**
     * Searches article with the $query
     * @access public
     * @param string $key
     * @param string $query
     * @param array $options
     * @return array
     * @static
     */
    public static function search($key, $query, $options = array())
    {
        return parent::_search($key, 'article', $query, $options);
    }
    
    /**
     * Search stories(cluster of articles) based on the $query
     * @access public
     * @param string $key
     * @param string $query
     * @param string $options
     * @return array
     */
    public static function searchStories($key, $query, $options = array())
    {
        return parent::_search($key, 'story', $query, $options);
    }
}

/**
 * Represents an author in the NewsCred Platform
 */
class NewsCredAuthor extends NewsCred
{

    public $last_name;
    public $first_name;

    /**
     * Constructor of the class
     * @access public
     * @param string $key
     * @param string $guid
     */
    public function __construct($key = null, $guid = null)
    {
        //Initialize all properties to empty string
        foreach(get_object_vars($this) as $property => $value) {
            $this->$property = '';
        }
        
        $this->module = 'author';
        $this->key    = $key;
        $this->guid   = $guid;

        if (!empty($key) && !empty($guid)) 
        {
            $this->populate();
        }
    }
    
    /**
     * Search authors with the $query
     * @access public
     * @param string $key
     * @param string $query
     * @return <type>
     * @static
     */
    public static function search($key, $query, $options = array())
    {
        return parent::_search($key, 'author', $query, $options);
    }
}

/**
 * Represents a source in the NewsCred Platform
 */
class NewsCredCategory extends NewsCred
{
    public $name;

    /**
     * Constructor of the class
     * @access public
     * @param string $key
     * @param string $name
     */
    public function __construct($key, $name)
    {
        $this->module = 'category';

        $this->key  = $key;
        $this->name = $name;
    }
    
    public function __call($method_name, $arguments)
    {
        switch($method_name) {
            
            case 'getRelatedStories': $name = 'story';  break;
            case 'getRelatedSources': $name = 'source'; break;
            case 'getRelatedImages' : $name = 'image';  break;
            
            /*Raise error when no matching method name found.*/
            default:
                trigger_error('Call to undefined method: '.__CLASS__.
                              '::'.$method_name.'() in '.__FILE__.
                              ' on line '.__LINE__, E_USER_ERROR);
        }
        
        if (is_array($arguments) && !empty($arguments))
            $options = $arguments[0];
        
        else $options = array();
        
        return $this->getRelatedStuff($name, $options);
    }
}

/**
 * Represents a Source in NewsCred Platform
 */
class NewsCredSource extends NewsCred
{
    public $name;
    public $is_blog;
    public $website;
    public $media_type;
    public $frequency;
    public $country;
    public $description;
    public $circulation;
    public $thumbnail;

    /**
     * Constructor of the class
     * @access public
     * @param string $key
     * @param string $guid
     */
    public function __construct($key = null, $guid = null)
    {
        //Initialize all properties to empty string
        foreach(get_object_vars($this) as $property => $value) {
            $this->$property = '';
        }
        
        $this->module = 'source';
        $this->key    = $key;
        $this->guid   = $guid;
        
        if (!empty($key) && !empty($guid)) {
            
            $this->populate();
        }
    }
    
    public function __toString()
    {
        return $this->name;
    }
    /**
     * Searches an author with the $query
     * @param string $key
     * @param string $query
     * @param array $options
     * @return array
     */
    public static function search($key, $query, $options = array())
    {
        return parent::_search($key, 'source', $query, $options);
    }
}
/*
 * Represents Twitter module in NewsCred API
 */
class NewsCredTwitter extends NewsCred
{
    public $author_link;
    public $author_name;
    public $title;
    public $link;
    public $thumbnail;
    public $created_at;
    
    private $has_related_topics   = FALSE;
    private $has_related_articles = FALSE;
    
    /*
     * Searches tweets with the given $query
     * @param string $key
     * @param string $query
     * @param array $options
     * @return array
     */    
    
    public static function search($key, $query, $options=array())
    {
        return parent::_search($key, 'tweet', $query, $options);
    }
}

/*
 * Represents Image module in NewsCred API
 */
class NewsCredImage extends NewsCred
{
    public $guid;
    public $caption;
    public $description;
    public $height;
    public $width;
    public $attribution_link;
    public $attribution_text;
    public $license;
    public $image_medium;
    public $image_small;
    public $image_large;
    public $published_at;
    public $created_at;
    
    private $has_related_topics   = FALSE;
    private $has_related_articles = FALSE;
    
    /*
     * Searches images with the given $query
     * @param string $key
     * @param string $query
     * @param array $options
     * @return array
     */
    public static function search($key, $query, $options=array())
    {
        return parent::_search($key, 'image', $query, $options);
    }
}

/*
 * Represents Video module in NewsCred API
 */
class NewsCredVideo extends NewsCred
{
    public $title;
    public $caption;
    public $guid;
    public $thumbnail;
    public $embed_code;
    public $published_at;
    public $media_file;
    public $source_name;
    
    private $has_related_topics   = FALSE;
    private $has_related_articles = FALSE;
    
    /*
     * Searches videos with the given $query
     * @param string $key
     * @param string $query
     * @param array $options
     * @return array
     */
    public static function search($key, $query, $options=array())
    {
        return parent::_search($key, 'video', $query, $options);
    }
}
?>