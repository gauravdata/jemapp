<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Fork.class.php 23410 2009-02-06 08:12:30Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Forking server class.
 *
 * This class will fork a new process for each connection.
 * This allows you to build servers, where communication between
 * the clients is no issue.
 *
 */
class Gpf_Net_Server_Driver_Fork extends Gpf_Net_Server {
    /**
     * flag to indicate whether this is the parent
     */
    private $isParent = true;

    /**
     * set maximum amount of simultaneous connections
     *
     * @param    int    $maxClients
     */
    public function setMaxClients($maxClients) {
        throw new Gpf_Exception('Unsupported to set max. client count');
    }

    /**
     * start the server
     *
     */
    public function start() {
        $this->startDaemonSocket();

        // Dear children, please do not become zombies
        pcntl_signal(SIGCHLD, SIG_IGN);

        while (true) {
            if(false !== ($socket = @socket_accept($this->daemonSocket))) {
                $this->socket = new Gpf_Net_Server_Socket($socket);
                $pid = pcntl_fork();
                if($pid == -1) {
                    $this->dieExit('Could not fork child process.');
                } elseif($pid == 0) {
                    $this->isParent = false;
                    $this->setNewConnection();
                    $this->handleRequest();
                    exit();
                }
            } else {
                $this->debug('Could not accept new connection: ' . $this->getLastSocketErrorMessage());
            }
        }
    }

    public function handleRequest() {
        while(true) {
            $watchedSockets = array($this->socket->getSocket());

            $ready = @socket_select($watchedSockets, $this->null, $this->null, null);

            if($ready === false) {
                $this->dieExit('Socket_select() failed:' . $this->getLastSocketErrorMessage());
            }
            $this->listener->onReceiveData();
        }
    }

    /**
     * shutdown server
     *
     */
    public function shutDown() {
        if(!$this->isParent) {
            $this->closeConnection();
            exit();
        }
        parent::shutDown();
    }

    protected function getClientInfo() {
        if(!$this->socket->isValid()) {
            return  '(pid: ' . getmypid() . ')';
        }
        return parent::getClientInfo() .  '(pid: ' . getmypid() . ')';
    }

    protected function checkRequiredExtensions() {
        parent::checkRequiredExtensions();

        if (!Gpf_Php::isFunctionEnabled('pcntl_fork')) {
            throw new Gpf_Exception('Needs pcntl extension to fork processes.');
        }
    }
}
?>
