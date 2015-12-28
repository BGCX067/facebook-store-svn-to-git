<?php
include_once 'Govn/govn.php';

class HTS_Govn_Api extends Govn
{
	public function __construct($config)
	{
		parent::__construct($config);
		if (isset($config['proxy.host']) && isset($config['proxy.port']))
		{
			self::$CURL_OPTS[CURLOPT_HTTPPROXYTUNNEL] = 'true';
			self::$CURL_OPTS[CURLOPT_PROXY] = $config['proxy.host'];
			self::$CURL_OPTS[CURLOPT_PROXYPORT] = $config['proxy.port'];
		}
	}
	public function httpRequest($url, $params, $ch=null)
	{
		$this->makeRequest($url, $params, $ch);
	}
}