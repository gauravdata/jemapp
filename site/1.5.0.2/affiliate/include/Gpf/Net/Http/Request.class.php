<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Request.class.php 19101 2008-07-11 12:47:55Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Net_Http_Request extends Gpf_Object {
	const CRLF = "\r\n";

	private $method = 'GET';
	private $url;

	//proxy server
	private $proxyServer = '';
	private $proxyPort = '';
	private $proxyUser = '';
	private $proxyPassword = '';

	//URL components
	private $scheme = 'http';
	private $host = '';
	private $port = 80;
	private $http_user = '';
	private $http_password = '';
	private $path = '';
	private $query = '';
	private $fragment = '';
	private $cookies = '';

	private $body = '';
	private $headers = array();

	public function setCookies($cookies) {
		$this->cookies = $cookies;
	}

	public function getCookies() {
		return $this->cookies;
	}

    public function getCookiesString() {
        $cookies = '';
        if (!is_array($this->cookies)) {
            return $cookies;
        }
        foreach ($this->cookies as $key => $value) {
            $cookies .= "$key=$value; ";
        }
        return $cookies;
    }

	public function getCookiesHeader() {
		return "Cookie: " . $this->getCookiesString();
	}

	public function setUrl($url) {
		$this->url = $url;
		$this->parseUrl();
	}

	public function getUrl() {
		return $this->url;
	}

	private function parseUrl() {
		$components = parse_url($this->url);
		if (array_key_exists('scheme', $components)) {
			$this->scheme = $components['scheme'];
		}
		if (array_key_exists('host', $components)) {
			$this->host = $components['host'];
		}
		if (array_key_exists('port', $components)) {
			$this->port = $components['port'];
		}
		if (array_key_exists('user', $components)) {
			$this->http_user = $components['user'];
		}
		if (array_key_exists('pass', $components)) {
			$this->http_password = $components['pass'];
		}
		if (array_key_exists('path', $components)) {
			$this->path = $components['path'];
		}
		if (array_key_exists('query', $components)) {
			$this->query = $components['query'];
		}
		if (array_key_exists('fragment', $components)) {
			$this->fragment = $components['fragment'];
		}
	}

	public function getScheme() {
		return $this->scheme;
	}

	public function getHost() {
		if (strlen($this->proxyServer)) {
			return $this->proxyServer;
		}
		return $this->host;
	}

	public function getPort() {
		if (strlen($this->proxyServer)) {
			return $this->proxyPort;
		}

		if (strlen($this->port)) {
			return $this->port;
		}
		return 80;
	}

	public function getHttpUser() {
		return $this->http_user;
	}

    public function setHttpUser($user) {
        $this->http_user = $user;
    }

	public function getHttpPassword() {
		return $this->http_password;
	}

    public function setHttpPassword($pass) {
        $this->http_password = $pass;
    }

	public function getPath() {
		return $this->path;
	}

	public function getQuery() {
		return $this->query;
	}

	public function addQueryParam($name, $value) {
		if (is_array($value)) {
			foreach($value as $key => $subValue) {
				$this->addQueryParam($name."[".$key."]", $subValue);
			}
			return;
		}
		$this->query .= ($this->query == '') ? '?' : '&';
		$this->query .= $name.'='.urlencode($value);
	}

	public function getFragment() {
		return $this->fragment;
	}

	/**
	 * Set if request method is GET or POST
	 *
	 * @param string $method possible values are POST or GET
	 */
	public function setMethod($method) {
		$method = strtoupper($method);
		if ($method != 'GET' && $method != 'POST') {
			throw new Gpf_Exception('Unsupported HTTP method: ' . $method);
		}
		$this->method = $method;
	}

	/**
	 * get the request method
	 *
	 * @access   public
	 * @return   string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * In case request should be redirected through proxy server, set proxy server settings
	 * This function should be called after function setHost !!!
	 *
	 * @param string $server
	 * @param string $port
	 * @param string $user
	 * @param string $password
	 */
	public function setProxyServer($server, $port, $user, $password) {
		$this->proxyServer = $server;
		$this->proxyPort = $port;
		$this->proxyUser = $user;
		$this->proxyPassword = $password;
	}

	public function getProxyServer() {
		return $this->proxyServer;
	}

	public function getProxyPort() {
		return $this->proxyPort;
	}

	public function getProxyUser() {
		return $this->proxyUser;
	}

	public function getProxyPassword() {
		return $this->proxyPassword;
	}

	public function setBody($body) {
		$this->body = $body;
	}

	public function getBody() {
		return $this->body;
	}

	/**
	 * Set header value
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
	}

	/**
	 * Get header value
	 *
	 * @param string $name
	 * @return string
	 */
	public function getHeader($name) {
		if (array_key_exists($name, $this->headers)) {
			return $this->headers[$name];
		}
		return null;
	}

	/**
	 * Return array of headers
	 *
	 * @return array
	 */
	public function getHeaders() {
		$headers = array();
		foreach ($this->headers as $headerName => $headerValue) {
			$headers[] = "$headerName: $headerValue";
		}
		return $headers;
	}

	private function initHeaders() {
		if ($this->getPort() == '80') {
			$this->setHeader('Host', $this->getHost());
		} else {
			$this->setHeader('Host', $this->getHost() . ':' . $this->getPort());
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
		}
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			$this->setHeader('Accept', $_SERVER['HTTP_ACCEPT']);
		}
		if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
			$this->setHeader('Accept-Charset', $_SERVER['HTTP_ACCEPT_CHARSET']);
		}
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$this->setHeader('Accept-Language', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		}
		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->setHeader('Referer', $_SERVER['HTTP_REFERER']);
		}
		if ($this->getMethod() == 'POST' && !strlen($this->getHeader("Content-Type"))) {
			$this->setHeader("Content-Type", "application/x-www-form-urlencoded");
		}
		if ($this->getHttpPassword() != '' && $this->getHttpUser() != '') {
            $this->setHeader('Authorization', 'Basic ' . base64_encode($this->getHttpUser() . ':' . $this->getHttpPassword()));
		}

		$this->setHeader('Content-Length', strlen($this->getBody()));
		$this->setHeader('Connection', 'close');

		if (strlen($this->proxyUser)) {
			$this->setHeader('Proxy-Authorization',
            'Basic ' . base64_encode ($this->proxyUser . ':' . $this->proxyPassword));
		}

	}

	public function getUri() {
		$uri = $this->getPath();
		if (strlen($this->getQuery())) {
			$uri .= '?' . $this->getQuery();
		}
		return $uri;
	}

	public function toString() {
		$this->initHeaders();
		$out = sprintf('%s %s HTTP/1.0' . self::CRLF, $this->getMethod(), $this->getUri());
		$out .= implode(self::CRLF, $this->getHeaders()) . self::CRLF . $this->getCookiesHeader() . self::CRLF;
		$out .= self::CRLF . $this->getBody();
		return $out;
	}

}
?>
