<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 * 	 @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: String.class.php 21966 2008-10-29 08:34:33Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * class derived from Net_IPv4
 *
 * @package GwtPhpFramework
 */
class Gpf_Net_Ip extends Gpf_Object {
    public $ip = '';
    public $netmask = '';
    public $bitmask = '';
    public $network = '';
    public $broadcast = '';

    public static $validNetmasks = array(
            0 => "0.0.0.0",
            1 => "128.0.0.0",
            2 => "192.0.0.0",
            3 => "224.0.0.0",
            4 => "240.0.0.0",
            5 => "248.0.0.0",
            6 => "252.0.0.0",
            7 => "254.0.0.0",
            8 => "255.0.0.0",
            9 => "255.128.0.0",
            10 => "255.192.0.0",
            11 => "255.224.0.0",
            12 => "255.240.0.0",
            13 => "255.248.0.0",
            14 => "255.252.0.0",
            15 => "255.254.0.0",
            16 => "255.255.0.0",
            17 => "255.255.128.0",
            18 => "255.255.192.0",
            19 => "255.255.224.0",
            20 => "255.255.240.0",
            21 => "255.255.248.0",
            22 => "255.255.252.0",
            23 => "255.255.254.0",
            24 => "255.255.255.0",
            25 => "255.255.255.128",
            26 => "255.255.255.192",
            27 => "255.255.255.224",
            28 => "255.255.255.240",
            29 => "255.255.255.248",
            30 => "255.255.255.252",
            31 => "255.255.255.254",
            32 => "255.255.255.255"
        );

    /**
     * Get array of ip addresses or ip ranges
     *
     * @param $settingName Setting Name
     * @return array - in case list is invalid, return false
     */
    public static function getBannedIPAddresses($settingName) {
        $setting = str_replace(array("\n", ';'), ',', trim(Gpf_Settings::get($settingName)));
        if (!strlen($setting)) {
            return false;
        }
        return explode(',', $setting);
    }

    public static function validateIP($ip)
    {
        if ($ip == long2ip(ip2long($ip))) {
            return true;
        }
        return false;
    }

    /**
     * match array of IPv4 addresses and check if input IP address is not in range
     *
     * @param $ip IP to check
     * @param $ipRanges array of IP address ranges, supported formats:
     *                          1. exact match (e.g. 192.168.1.1)
     *                          2. wildcart notation (e.g. *.*.*.*)
     *                          3. range (e.g. 1.1.1.1-1.2.1.1)
     *                          4. subnet masks in forms:
     *                                       [dot quad ip]/[ bitmask ] (e.g. 192.168.0.0/16)
     *                                       [dot quad ip]/[ dot quad netmask ] (e.g. 192.168.0.0/255.255.0.0)
     *                                       [dot quad ip]/[ hex string netmask ] (e.g. 192.168.0.0/ffff0000)
     * @return boolean
     */
    public static function ipMatchRange($ip, $ipRanges) {
        foreach ($ipRanges as $ipRange) {
            $ipRange = trim($ipRange);
            if (strlen($ipRange)) {

                //exact match
                if ($ip == $ipRange) {
                    return true;

                //wildcard notation *.*.*.*
                } else if (strpos($ipRange, '*') !== false) {
                    $regexp = str_replace ('*', '.+', str_replace ('.', '\\.', $ipRange));
                    if(preg_match("/^$regexp$/", $ip)) {
                        return true;
                    }

                //range 1.1.1.1-1.2.1.1
                } else if (strpos($ipRange, '-') !== false) {
                    list($fromIp, $toIp) = explode('-', $ipRange);
                    $fromIp = trim($fromIp);
                    $toIp = trim($toIp);
                    $toLong = self::ip2double($toIp);
                    $fromLong = self::ip2double($fromIp);
                    $ipLong = self::ip2double($ip);
                    if (long2ip($ipLong) == $ip && long2ip($fromLong) == $fromIp && long2ip($toLong) == $toIp && $fromLong <= $ipLong && $ipLong <= $toLong) {
                        return true;
                    }

                //range defined in form of sub net mask
                } else if (strpos($ipRange, '/') !== false) {
                    try {
                        $parsedIp = self::parseAddress($ipRange);

                        $net = self::ip2double($parsedIp->network);
                        $bcast = self::ip2double($parsedIp->broadcast);
                        $ip = self::ip2double($ip);

                        if ($ip >= $net && $ip <= $bcast) {
                            return true;
                        }
                    } catch (Exception $e) {
                    }
                }

            }
        }
        return false;
    }



    /**
     * Parse a formatted IP address
     *
     * Given a network qualified IP address, attempt to parse out the parts
     * and calculate qualities of the address.
     *
     * The following formats are possible:
     *
     * [dot quad ip]/[ bitmask ]
     * [dot quad ip]/[ dot quad netmask ]
     * [dot quad ip]/[ hex string netmask ]
     *
     * The first would be [IP Address]/[BitMask]:
     * 192.168.0.0/16
     *
     * The second would be [IP Address] [Subnet Mask in dot quad notation]:
     * 192.168.0.0/255.255.0.0
     *
     * The third would be [IP Address] [Subnet Mask as Hex string]
     * 192.168.0.0/ffff0000
     *
     * Usage:
     *
     * $cidr = '192.168.0.50/16';
     * $net = Net_IPv4::parseAddress($cidr);
     * echo $net->network; // 192.168.0.0
     * echo $net->ip; // 192.168.0.50
     * echo $net->broadcast; // 192.168.255.255
     * echo $net->bitmask; // 16
     * echo $net->long; // 3232235520 (long/double version of 192.168.0.50)
     * echo $net->netmask; // 255.255.0.0
     *
     * @param  string $ip IP address netmask combination
     * @return Gpf_Net_Ip     true if syntax is valid, otherwise false
     */
    public static function parseAddress($address)
    {
        $myself = new Gpf_Net_Ip();
        if (strchr($address, "/")) {
            $parts = explode("/", $address);
            if (! self::validateIP($parts[0])) {
                throw new Exception('Invalid IP address');
            }
            $myself->ip = $parts[0];

            // Check the style of netmask that was entered
            /*
            *  a hexadecimal string was entered
            */
            if (preg_match("/^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i", $parts[1], $regs)) {
                // hexadecimal string
                $myself->netmask = hexdec($regs[1]) . "." .  hexdec($regs[2]) . "." .
                hexdec($regs[3]) . "." .  hexdec($regs[4]);

                /*
                 *  a standard dot quad netmask was entered.
                 */
            } else if (strchr($parts[1], ".")) {
                if (! self::validateNetmask($parts[1])) {
                    throw new Exception("invalid netmask value");
                }
                $myself->netmask = $parts[1];

                /*
                 *  a CIDR bitmask type was entered
                 */
            } else if (ctype_digit($parts[1]) && $parts[1] >= 0 && $parts[1] <= 32) {
                // bitmask was entered
                $myself->bitmask = $parts[1];

                /*
                 *  Some unknown format of netmask was entered
                 */
            } else {
                throw new Exception("invalid netmask value");
            }
            $myself->calculate();
            return $myself;
        } else if (self::validateIP($address)) {
            $myself->ip = $address;
            return $myself;
        } else {
            throw new Exception("invalid IP address");
        }
    }

    /**
     * Validate the syntax of a four octet netmask
     *
     * There are 33 valid netmask values.  This function will compare the
     * string passed as $netmask to the predefined 33 values and return
     * true or false.  This is most likely much faster than performing the
     * calculation to determine the validity of the netmask.
     *
     * @param  string $netmask Netmask
     * @return bool       true if syntax is valid, otherwise false
     */
    public static function validateNetmask($netmask)
    {
        if (! in_array($netmask, self::$validNetmasks)) {
            return false;
        }
        return true;
    }


    /**
     * Calculates network information based on an IP address and netmask.
     *
     * Fully populates the object properties based on the IP address and
     * netmask/bitmask properties.  Once these two fields are populated,
     * calculate() will perform calculations to determine the network and
     * broadcast address of the network.
     *
     * @return mixed     true if no errors occured, otherwise PEAR_Error object
     */
    public function calculate()
    {
        /* Find out if we were given an ip address in dot quad notation or
         * a network long ip address.  Whichever was given, populate the
         * other field
         */
        if (strlen($this->ip)) {
            if (! self::validateIP($this->ip)) {
                throw new Exception("invalid IP address");
            }
            $this->long = self::ip2double($this->ip);
        } else if (is_numeric($this->long)) {
            $this->ip = long2ip($this->long);
        } else {
           throw new Exception("ip address not specified");
        }

        /*
         * Check to see if we were supplied with a bitmask or a netmask.
         * Populate the other field as needed.
         */
        if (strlen($this->bitmask)) {
            $this->netmask = self::$validNetmasks[$this->bitmask];
        } else if (strlen($this->netmask)) {
            $validNM_rev = array_flip(self::$validNetmasks);
            $this->bitmask = $validNM_rev[$this->netmask];
        } else {
            throw new Exception("netmask or bitmask are required for calculation");
        }
        $this->network = long2ip(ip2long($this->ip) & ip2long($this->netmask));
        $this->broadcast = long2ip(ip2long($this->ip) |
                (ip2long($this->netmask) ^ ip2long("255.255.255.255")));
        return true;
    }

    public static function ip2double($ip)
    {
        return (double)(sprintf("%u", ip2long($ip)));
    }
}

?>
