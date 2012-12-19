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
class Gpf_CPanel_AccountBandWidth extends Gpf_Object {

    private $limitEnabled = false;

    /**
     * Bandwidth Limit (in KB)
     * @var integer
     */
    private $limit = 0;

    /**
     * Human Readable Bandwidth Usage (Transfer) Limit.
     * @var string
     */
    private $humanLimit;

    /**
     * Human Readable Current Monthly Bandwidth Usage (Transfer).
     * @var string
     */
    private $usedHuman;

    /**
     * Whether or not the account is permitted unlimited bandwidth usage (transfer).
     * @var boolean
     */
    private $isUnlimited = false;



    /**
     * Whether or not the account is permitted unlimited bandwidth usage (transfer).
     * @param $isUnlimited Whether or not the account is permitted unlimited bandwidth usage (transfer). Yes=1, No=0.
     */
    public function setUnlimited($isUnlimited) {
        $this->isUnlimited = $isUnlimited == 1;
    }

    /**
     * Whether or not the account is permitted unlimited bandwidth usage (transfer).
     * @return boolean
     */
    public function isUnlimited() {
        return $this->isUnlimited;
    }

    /**
     * set Human Readable Current Monthly Bandwidth Usage (Transfer).
     * @param $usedHuman Human Readable Current Monthly Bandwidth Usage (Transfer).
     */
    public function setHumanLimitUsed($usedHuman) {
        $this->usedHuman = $usedHuman;
    }

    /**
     * Get Human Readable Current Monthly Bandwidth Usage (Transfer).
     * @return string
     */
    public function getHumanLimitUsed() {
        return $this->usedHuman;
    }

    /**
     * Set Human Readable Bandwidth Usage (Transfer) Limit.
     * @param $humanLimit Human Readable Bandwidth Usage (Transfer) Limit.
     */
    public function setHumanLimit($humanLimit) {
        $this->humanLimit = $humanLimit;
    }

    /**
     * Get Human Readable Bandwidth Usage (Transfer) Limit.
     * @return string
     */
    public function getHumanLimit() {
        return $this->humanLimit;
    }

    /**
     * Bandwidth Limit (in KB)
     * @param $limit
     */
    public function setLimit($limit) {
        $this->limit = $limit;
    }

    /**
     * Bandwidth Limit (in KB)
     * @return integer Bandwidth Limit (in KB)
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Whether or not the bandwidth limit is enforced. Yes=1, No=0.
     *
     * @param $isEnabled Whether or not the bandwidth limit is enforced. Yes=1, No=0.
     */
    public function setLimitEnabled($isEnabled) {
        $this->limitEnabled = $isEnabled == 1;
    }

    /**
     * Whether or not the bandwidth limit is enforced.
     * @return boolean
     */
    public function isLimitEnabled() {
        return $this->limitEnabled;
    }
}

?>
