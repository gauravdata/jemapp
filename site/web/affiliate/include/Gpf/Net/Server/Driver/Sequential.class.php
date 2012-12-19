<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Sequential.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Net_Server_Driver_Sequential extends Gpf_Net_Server {
    /**
     * Seconds until the idle handler is called.
     * If set to NULL, the idle handler is deactivated.
     * @var integer
     */
    private $idleTimeout = null;

    private $clients = array();


    /**
     * Set the number of seconds until the idle handler is called (if defined).
     * If the timeout is set to NULL (or 0), the timeout is deactivated.
     *
     * The idle handler function is "onIdle" and takes no parameters.
     *
     * Please take care when using timeout handlers, as the PHP manual states:
     *  You should always try to use socket_select() without timeout. Your program
     *  should have nothing to do if there is no data available. Code that depends
     *  on timeouts is not usually portable and difficult to debug.
     *
     * @param int  $idleTimeout   Number of seconds until the timeout handler
     *                               is called.
     */
    public function setIdleTimeout($idleTimeout = null) {
        if ($idleTimeout == 0) {
            $idleTimeout = null;
        }
        $this->idleTimeout = $idleTimeout;
    }

    /**
     * start the server
     *
     */
    public function start() {
        $this->startDaemonSocket();

        if ($this->idleTimeout !== null) {
            $idleLast = time();
        }

        while (true) {
            $watchedSockets = array();
            array_push($watchedSockets, $this->daemonSocket);

            foreach ($this->clients as $socket) {
                array_push($watchedSockets, $socket->getSocket());
            }

            $ready = @socket_select($watchedSockets, $this->null, $this->null, $this->idleTimeout);
            if ($ready === false) {
                $this->dieExit('Socket_select failed:' . $this->getLastSocketErrorMessage());
            }

            if ($ready == 0 && $this->idleTimeout !== null && ($idleLast + $this->idleTimeout) <= time()) {
                $idleLast = time();
                $this->debug('Calling onIdle() handler.');
                $this->listener->onIdle();
                continue;
            }

            if (in_array($this->daemonSocket, $watchedSockets)) {
                $this->acceptConnection();
            }

            foreach ($watchedSockets as $socket) {
                if ($socket == $this->daemonSocket) {
                    continue;
                }
                $this->socket = $this->clients[$socket];
                $this->handleRequest();
            }
        }
    }
    
    public function handleRequest() {
        if(!$this->isSocketConnected()) {
            return;
        }
        $this->listener->onReceiveData();
    }

    /**
     * close connection to a client
     *
     */
    public function closeConnection() {
        if(!$this->socket->isValid()) {
            return;
        }
        $this->releaseConnection();
        $this->debug('Open connection count: ' . count($this->clients));
        parent::closeConnection();
    }
    
    public function releaseConnection() {
        unset($this->clients[$this->socket->getSocket()]);
    }
    
    /**
     * shutdown server
     *
     */
    public function shutDown() {
        foreach ($this->clients as $socket) {
            $this->socket = $socket;
            $this->closeConnection();
        }
        parent::shutDown();
    }

    /**
     * accept a new connection
     *
     */
    private function acceptConnection() {
        $socket = socket_accept($this->daemonSocket);
        if($socket === false) {
            $this->debug('Could not accept new connection: ' . $this->getLastSocketErrorMessage());
            return;
        }
        
        $this->socket = new Gpf_Net_Server_Socket($socket);
        
        if ($this->maxClients > 0 && count($this->clients) >= $this->maxClients) {
            $this->debug('Too many connections. Closing socket.' . $this->getClientInfo());
            $this->listener->onConnectionRefused();
            $this->socket->close();
            $this->unsetSocket($this->socket);
            return;
        }
        $this->clients[$socket] = $this->socket;
        $this->setNewConnection();
    }
}
?>
