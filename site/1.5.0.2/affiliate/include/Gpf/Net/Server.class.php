<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Server.class.php 23410 2009-02-06 08:12:30Z vzeman $
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
abstract class Gpf_Net_Server extends Gpf_Object {
    protected $daemonSocket;

    /**
     * Active socket
     *
     * @var Gpf_Net_Server_Socket
     */
    protected $socket;

    /**
     * port to listen
     */
    protected $port = 10000;

    /**
     * domain to bind to
     */
    protected $domain = "localhost";

    /**
     * The connection protocol:
     * AF_INET, AF_INET6, AF_UNIX
     *
     * @access private
     * @var integer
     */
    private $protocol = AF_INET;

    /**
     * maximum amount of clients
     * @access private
     * @var    integer    $maxClients
     */
    protected $maxClients = -1;

    /**
     * buffer size for socket_read
     */
    protected $readBufferSize = 128;

    /**
     *
     * @var Gpf_Net_Server_Handler
     */
    protected $listener;

    /**
     * maximum of backlog in queue
     */
    protected $maxQueue = 500;

    /**
     * empty array, used for socket_select
     * @var    array    $null
     */
    protected $null = array();

    public  function __construct($domain = "localhost", $port = 10000, $protocol = AF_INET) {
        $this->domain = $domain;
        $this->port = $port;
        $this->protocol = (int)$protocol;
        $this->socket = new Gpf_Net_Server_NoSocket();
    }

    public function setAddress($host, $port) {
        $this->domain = $host;
        $this->port = $port;
    }

    /**
     * Read from socket up to stopString within maxBuffer
     *
     * @param string $stopString
     * @param number $maxBuffer null is unliited
     * @return striing
     */
    public function read($stopString = null, $maxBuffer = null) {
        if(!$this->socket->isValid()) {
            throw new Gpf_Net_Server_SocketException("Trying to read data from invalid socket");
        }

        $maxReached = false;
        $noData = false;
        $data = $this->socket->getLeftOverBuffer();

        $this->socket->setLeftOverBuffer('');

        while(true) {
            if($maxBuffer !== null) {
                if(strlen($data) >= $maxBuffer) {
                    $this->socket->setLeftOverBuffer(substr($data, $maxBuffer));
                    $data = substr($data, 0, $maxBuffer);
                    $maxReached = true;
                }
            }

            if($stopString !== null) {
                $dataUpToStopSring = $this->getDataUpToStopSring($stopString, $data);
                if($dataUpToStopSring !== false) {
                    return $dataUpToStopSring;
                }
                if($maxReached || $noData) {
                    $this->socket->setLeftOverBuffer($data);
                    throw new Gpf_Net_Server_SocketException('Stop character not found. Buffer len: ' . strlen($buffer));
                }
            }

            if($noData || $maxReached) {
                return $data;
            }
            try {
                $buffer = $this->readOnce();
            } catch (Exception $e) {
                $this->socket->setLeftOverBuffer($data);
                throw $e;
            }
            $data .= $buffer;

            if(strlen($buffer) < $this->readBufferSize) {
                $noData = true;
            }
        }
    }

    /**
     * send data to a client
     * @throws Gpf_Net_Server_SocketException
     */
    public function send($data) {
        if(!$this->socket->isValid()) {
            throw new Gpf_Net_Server_SocketException("Trying to send data to invalid socket");
        }

        $toSend = $data;
        while(($len = strlen($toSend)) > 0) {
            $written = @socket_write($this->socket->getSocket(), $toSend);
            if($written === false) {
                $message = 'Could not write data to: ' . $this->getClientInfo()
                . "\n" . 'Reason: ' . $this->getLastSocketErrorMessage();
                $this->debug($message);
                throw new Gpf_Net_Server_SocketException($message);
            }
            if($written == $len) {
                return;
            }
            $toSend = substr($toSend, $written);
        }
    }

    public function setListener(Gpf_Net_Server_Handler $listener) {
        $this->listener = $listener;
    }

    /**
     * set maximum amount of simultaneous connections
     *
     * @param    int    $maxClients
     */
    public function setMaxClients($maxClients) {
        $this->maxClients = $maxClients;
    }

    public function releaseConnection() {
    }

    public function closeConnection() {
        if(!$this->socket->isValid()) {
            return;
        }
        $this->listener->onDisconnect();
        $this->debug('Closed connection from: ' . $this->getClientInfo());
        $this->socket->close();
        $this->unsetSocket($this->socket);
    }

    public function getRemoteIp() {
        return $this->socket->getRemoteIp();
    }

    public function getRemoteHost() {
        return $this->socket->getRemoteHost();
    }

    public abstract function start();

    public abstract function handleRequest();

    public function shutDown() {
        $this->listener->onShutdown();
        @socket_shutdown($this->daemonSocket, 2);
        @socket_close($this->daemonSocket);
        $this->info('Shutdown server.');
        exit();
    }

    protected function debug($message) {
        Gpf_Log::debug($message, 'TCP');
    }

    protected function info($message) {
        Gpf_Log::info($message, 'TCP');
    }

    protected function dieExit($message) {
        Gpf_Log::critical($message, 'TCP');
        die($message);
    }

    protected function getLastSocketErrorMessage($lastError = null) {
        if($lastError === null) {
            $lastError = socket_last_error();
            socket_clear_error();
        }
        return socket_strerror($lastError) . ' / Code: '. $lastError;
    }

    protected function getLastSocketError() {
        $lastError = socket_last_error();
        socket_clear_error();
        return $lastError;
    }

    protected function checkRequiredExtensions() {
        if (!Gpf_Php::isFunctionEnabled('socket_create')) {
            $this->dieExit('Sockets extension not available.');
        }
    }

    protected function startDaemonSocket() {
        if(is_resource($this->daemonSocket)) {
            throw new Gpf_Exception('Server already started');
        }

        $this->checkRequiredExtensions();

        $this->daemonSocket = @socket_create($this->protocol, SOCK_STREAM, SOL_TCP);

        if (false === $this->daemonSocket) {
            $this->dieExit('Could not create socket: ' . $this->getLastSocketErrorMessage());
        }
        @socket_set_option($this->daemonSocket, SOL_SOCKET, SO_REUSEADDR, 1);

        if (false === @socket_bind($this->daemonSocket, $this->domain, $this->port)) {
            $error = $this->getLastSocketErrorMessage();
            @socket_close($this->daemonSocket);
            $this->dieExit('Could not bind socket to ' . $this->domain
            . ':' . $this->port . ' (' . $error .').');
        }

        if (false === @socket_listen($this->daemonSocket, $this->maxQueue)) {
            $error = $this->getLastSocketErrorMessage();
            @socket_close($this->daemonSocket);
            $this->dieExit('Could not listen on ' . $this->domain
            . ':' . $this->port . ' (' . $error . ').');
        }

        $this->info('Server started listening on ' . $this->domain . ':' . $this->port);
        $this->listener->onStart();
    }

    protected function getClientInfo() {
        return $this->socket->getClientInfo();
    }

    protected function setNewConnection() {
        $this->socket->setClientInfo();
        $this->debug('New connection from ' . $this->getClientInfo());
        $this->listener->onConnect();
    }

    protected function unsetSocket(Gpf_Net_Server_Socket $socket) {
        unset($socket);
        $this->socket = new Gpf_Net_Server_NoSocket();
    }

    protected function isSocketConnected() {
        try {
            $data = $this->readOnce();
            $this->socket->setLeftOverBuffer($this->socket->getLeftOverBuffer() . $data);
        } catch (Exception $e) {
            $data = '';
        }
        if($data == '') {
            $this->closeConnection();
            return false;
        }
        return true;
    }

    private function readOnce() {
        $buffer = @socket_read($this->socket->getSocket(), $this->readBufferSize);
        if ($buffer === false) {
            $errorCode = $this->getLastSocketError();
            $errorMessage = 'Could not read from ' . $this->getClientInfo()
            . "\n" . 'Reason: ' . $this->getLastSocketErrorMessage($errorCode);
            $this->debug($errorMessage);
            throw new Gpf_Net_Server_SocketException($errorMessage, $errorCode);
        }
        return $buffer;
    }

    /**
     * Return data up to stopString or false
     *
     * @param string $stopString
     * @param string $data
     * @return string or false
     */
    private function getDataUpToStopSring($stopString, $data) {
        $posStopString = strpos($data, $stopString);

        if ($posStopString !== false) {
            $posStopString += strlen($stopString);

            $this->socket->setLeftOverBuffer(substr($data, $posStopString)
            . $this->socket->getLeftOverBuffer());
            $data = substr($data, 0, $posStopString);
            return $data;
        }
        return false;
    }
}
?>
