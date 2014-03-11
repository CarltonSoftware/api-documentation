<?php

class HmacPlugin implements Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * Parameters to encode
     * 
     * @var array 
     */
    protected $params = array();
    
    /**
     * Api Key
     * 
     * @var string
     */
    protected $key;
    
    /**
     * Api Secret
     * 
     * @var string
     */
    protected $secret;
    
    // ----------------------------- Constructor ---------------------------- //
    
    /**
     * Constructor
     * 
     * @param string $key    Api Key
     * @param string $secret Secret
     * 
     * @return void
     */
    public function __construct($key, $secret)
    {
        $this->setKey($key);
        $this->setSecret($secret);
    }

    // -------------------------- Interface Methods ------------------------- //

    public static function getSubscribedEvents()
    {
        return array('request.before_send' => 'onBeforeSend');
    }

    public function onBeforeSend(Guzzle\Common\Event $event)
    {
        $request = $event['request'];
        $request->getQuery()->set('APIKEY', $this->getKey());
        $request->getQuery()->set('hash', $this->getHash($request));
    }
    
    // -------------------------- Public Functions -------------------------- //
    
    /**
     * Parameters to encode
     * 
     * @param array $params Array of parameters to encode
     * 
     * @return \HmacPlugin
     */
    public function setParams($params)
    {
        $this->params = $params;
        
        return $this;
    }
    
    /**
     * Api Key
     * 
     * @param string $key Api Key
     * 
     * @return \HmacPlugin
     */
    public function setKey($key)
    {
        $this->key = $key;
        
        return $this;
    }
    
    /**
     * Api Secret
     * 
     * @param string $secret Api Secret
     * 
     * @return \HmacPlugin
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        
        return $this;
    }
    
    /**
     * Return the api key
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * Return the secret
     * 
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
    
    /**
     * Return the hash required for the api
     * 
     * @param Guzzle\Http\Message\Request $request Request object
     * 
     * @return string
     */
    public function getHash(Guzzle\Http\Message\Request $request)
    {
        // If post/put use posted fields
        switch ($request->getMethod()) {
        case 'POST':
        case 'PUT':
            $params = array_merge(
                $request->getQuery()->toArray(),
                $request->getPostFields()->toArray()
            );
            break;
        default:
            $params = $request->getQuery()->toArray();
            break;
        }
        $params['APISECRET'] = $this->getSecret();
        ksort($params);
        $params = array_map('strval', $params);
        
        return hash('SHA256', json_encode($params), false);
    }
    
    /**
     * Return the relative route for the url
     * 
     * @param Guzzle\Http\Message\Request $request Request object
     * 
     * @return string
     */
    public final function getRoute(Guzzle\Http\Message\Request $request)
    {
        $route = str_replace(
            $request->getClient()->getBaseUrl(), 
            '', 
            $request->getUrl()
        );
        
        $route = explode('?', $route);
        
        return $route[0];
    }
}