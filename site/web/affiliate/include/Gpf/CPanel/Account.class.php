<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.11
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
class Gpf_CPanel_Account extends Gpf_Object {

    private $diskLimit = 0;
    private $diskUsed = 0;
    private $domain;
    private $email;
    private $ip;
    private $owner;
    private $partition;
    private $package;
    private $startDate;
    private $suspended = false;
    private $suspendReason;
    private $theme = 'x3';
    private $unixStartTimestamp;
    private $userName;

    public function getUser() {
        return $this->userName;
    }

    public function setUser($username) {
        $this->userName = $username;
    }

    public function getUnixStartDate() {
        return $this->unixStartTimestamp;
    }

    public function setUnixStartDate($timestamp) {
        $this->unixStartTimestamp = $timestamp;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function setTheme($theme) {
        $this->theme = $theme;
    }

    public function setSuspendReason($reason) {
        $this->suspendReason = $reason;
    }

    public function getSuspendReason() {
        return $this->suspendReason;
    }

    public function isSuspended() {
        return $this->suspended;
    }

    public function setSuspended($isSuspended) {
        $this->suspended = $isSuspended == 1 || $isSuspended == true;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setPackage($package) {
        $this->package = $package;
    }

    public function getPackage() {
        return $this->package;
    }

    public function setPartition($partition) {
        $this->partition = $partition;
    }

    public function getPartition() {
        return $this->partition;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function setDiskUsed($diskUsed) {
        $this->diskUsed = $diskUsed;
    }

    public function getDiskUsed() {
        return $this->diskUsed;
    }

    public function setDiskLimit($diskLimit) {
        $this->diskLimit = $diskLimit;
    }

    public function getDiskLimit() {
        return $this->diskLimit;
    }
}

?>
