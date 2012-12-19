<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Socket.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Net_Server_Socket extends Gpf_Object {
    private $socket;
    private $host;
    private $port;
    private $connectTime;
    
    /**
     * data left after reading
     *
     * @var string
     */
    private $leftOverBuffer = '';
    
    public function __construct($socket) {
        $this->socket = $socket;
        $this->connectTime = time();
    }
    
    public function __destruct() {
        $this->socket = null;
    }
    
    public function setClientInfo() {
        $peerHost = '';
        $peerPort = '';
        socket_getpeername($this->socket, $peerHost, $peerPort);
        $this->host = $peerHost;
        $this->port = $peerPort;
    }
    
    public function getSocket() {
        return $this->socket;
    }

    public function isValid() {
        return is_resource($this->socket);
    }
    
    public function logMetadata() {
        $sendBuffer = socket_get_option($this->socket, SOL_SOCKET, SO_SNDBUF);
        $rcvBuffer = socket_get_option($this->socket, SOL_SOCKET, SO_RCVBUF);
        $linger = socket_get_option($this->socket, SOL_SOCKET, SO_LINGER);
        $keep = socket_get_option($this->socket, SOL_SOCKET, SO_KEEPALIVE);
        
        Gpf_Log::debug(var_export(array('send'=>$sendBuffer, 'rcv'=>$rcvBuffer, 
        'linger'=>$linger, 'keepalive'=>$keep), true));
    }
    
    public function getClientInfo() {
        return $this->host . ':' . $this->port;
    }
    
    public function getLeftOverBuffer() {
        return $this->leftOverBuffer;
    }
    
    public function setLeftOverBuffer($data) {
        $this->leftOverBuffer = $data;
    }
    
    public function close() {
//        @socket_shutdown($this->socket, 2);
//        @socket_close($this->socket);
        $this->gracefulShutdown();
    }
    
    public function getRemoteIp() {
        return $this->host;    
    }
    
    public function getRemoteHost() {
        return @gethostbyaddr($this->host);    
    }
    
    private function gracefulShutdown() {
        @socket_shutdown($this->socket, 1);

        @socket_set_nonblock($this->socket);
        while(true) {
            $read = @socket_read($this->socket, 8000);
            if($read <= 0) {
                break;
            }
        }
        @socket_shutdown($this->socket, 2);
        @socket_close($this->socket);
    }
}
?>
