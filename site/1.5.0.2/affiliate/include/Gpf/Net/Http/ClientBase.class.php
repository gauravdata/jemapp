<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Server.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
abstract class Gpf_Net_Http_ClientBase extends Gpf_Object {
    const CONNECTION_TIMEOUT = 20;

    //TODO: rename this method to "send()"
    /**
     * @param Gpf_Net_Http_Request $request
     * @return Gpf_Net_Http_Response
     */
    public function execute(Gpf_Net_Http_Request $request) {

        if (!$this->isNetworkingEnabled()) {
            throw new Gpf_Exception($this->_('Network connections are disabled'));
        }

        if (!strlen($request->getUrl())) {
            throw new Gpf_Exception('No URL defined.');
        }

        $this->setProxyServer($request);
        if (Gpf_Php::isFunctionEnabled('curl_init')) {
            return $this->executeWithCurl($request);
        } else {
            return $this->executeWithSocketOpen($request);
        }
    }

    protected abstract function isNetworkingEnabled();

    /**
     * @param Gpf_Net_Http_Request $request
     * @return Gpf_Net_Http_Response
     */
    private function executeWithSocketOpen(Gpf_Net_Http_Request $request) {
        $scheme = ($request->getScheme() == 'ssl' || $request->getScheme() == 'https') ? 'ssl://' : '';
        $proxySocket = @fsockopen($scheme . $request->getHost(), $request->getPort(), $errorNr,
        $errorMessage, self::CONNECTION_TIMEOUT);

        if($proxySocket === false) {
            $gpfErrorMessage = $this->_sys('Could not connect to server: %s:%s, Failed with error: %s', $request->getHost(), $request->getPort(), $errorMessage);
            Gpf_Log::error($gpfErrorMessage);
            throw new Gpf_Exception($gpfErrorMessage);
        }

        $requestText = $request->toString();

        $result = @fwrite($proxySocket, $requestText);
        if($result === false || $result != strlen($requestText)) {
            @fclose($proxySocket);
            $gpfErrorMessage = $this->_sys('Could not send request to server %s:%s', $request->getHost(), $request->getPort());
            Gpf_Log::error($gpfErrorMessage);
            throw new Gpf_Exception($gpfErrorMessage);
        }

        $result = '';
        while (false === @feof($proxySocket)) {
            try {
                if(false === ($data = @fread($proxySocket, 8192))) {
                    Gpf_Log::error($this->_sys('Could not read from proxy socket'));
                    throw new Gpf_Exception("could not read from proxy socket");
                }
                $result .= $data;
            } catch (Exception $e) {
                Gpf_Log::error($this->_sys('Proxy failed: %s', $e->getMessage()));
                @fclose($proxySocket);
                throw new Gpf_Exception($this->_('Proxy failed: %s', $e->getMessage()));
            }
        }
        @fclose($proxySocket);

        $response = new Gpf_Net_Http_Response();
        $response->setResponseText($result);

        return $response;
    }


    /**
     * @param Gpf_Net_Http_Request $request
     * @return Gpf_Net_Http_Response
     *      */
    private function executeWithCurl(Gpf_Net_Http_Request $request) {
        $session = curl_init($request->getUrl());

        if ($request->getMethod() == 'POST') {
            @curl_setopt ($session, CURLOPT_POST, true);
            @curl_setopt ($session, CURLOPT_POSTFIELDS, $request->getBody());
        }

        $cookies = $request->getCookiesString();
        if($cookies) {
            @curl_setopt($session, CURLOPT_COOKIE, $cookies);
        }

        @curl_setopt($session, CURLOPT_HEADER, true);
        @curl_setopt($session, CURLOPT_CONNECTTIMEOUT, self::CONNECTION_TIMEOUT);
        @curl_setopt($session, CURLOPT_HTTPHEADER, $request->getHeaders());
        @curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
        @curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        if ($request->getHttpPassword() != '' && $request->getHttpUser() != '') {
        	@curl_setopt($session, CURLOPT_USERPWD, $request->getHttpUser() . ":" . $request->getHttpPassword());
        	@curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
        @curl_setopt ($session, CURLOPT_SSL_VERIFYHOST, 0);
        @curl_setopt ($session, CURLOPT_SSL_VERIFYPEER, 0);

        $this->setupCurlProxyServer($session, $request);

        // Make the call
        $result = curl_exec($session);
        $error = curl_error($session);

        curl_close($session);

        if (strlen($error)) {
            throw new Gpf_Exception("Curl error: " . $error);
        }

        $response = new Gpf_Net_Http_Response();
        $response->setResponseText($result);

        return $response;
    }

    protected function setProxyServer(Gpf_Net_Http_Request $request) {
        try {
            $proxyServer = Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_SERVER_SETTING_NAME);
            $proxyPort = Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_PORT_SETTING_NAME);
            $proxyUser = Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_USER_SETTING_NAME);
            $proxyPassword = Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_PASSWORD_SETTING_NAME);
            $request->setProxyServer($proxyServer, $proxyPort, $proxyUser, $proxyPassword);
        } catch (Gpf_Exception $e) {
            $request->setProxyServer('', '', '', '');
        }
    }

    private function setupCurlProxyServer($curlSession, Gpf_Net_Http_Request $request) {
        if (strlen($request->getProxyServer()) && strlen($request->getProxyPort())) {
            @curl_setopt($curlSession, CURLOPT_PROXY, $request->getProxyServer() . ':' . $request->getProxyPort());
            if (strlen($request->getProxyUser())) {
                @curl_setopt($curlSession, CURLOPT_PROXYUSERPWD, $request->getProxyUser() . ':' . $request->getProxyPassword());
            }
        }
    }
}
?>
