<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: EmailValidator.class.php 25086 2009-07-29 12:21:56Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */


/**
 *
 * Validation of email quality
 *
 * @package GwtPhpFramework
 */
class Gpf_Mail_EmailValidator extends Gpf_Object {
    private $emailAddres = '';
    private $emailDomain = '';
    private $senderEmail = 'support@qualityunit.com';
    private $connectAddress = '';
    private $hostname = 'www.qualityunit.com';
    private $messages = array();
    private $messagesSA = array();
    private $messagesSAIP = array();

    private $validateMX = true;
    private $validateAddress = false;

    public function __construct() {
        if (isset($_SERVER['HTTP_HOST']) && strlen($_SERVER['HTTP_HOST'])) {
            $this->hostname = $_SERVER['HTTP_HOST'];
        }
    }

    private function rfc822_compactible_regexp($_email) {
        $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
        $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
        $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
        '\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
        $quoted_pair = '\\x5c[\\x00-\\x7f]';
        $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
        $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
        $domain_ref = $atom;
        $sub_domain = "($domain_ref|$domain_literal)";
        $word = "($atom|$quoted_string)";
        $domain = "$sub_domain(\\x2e$sub_domain)*";
        $local_part = "$word(\\x2e$word)*";
        $addr_spec = "$local_part\\x40$domain";
        return preg_match("!^$addr_spec$!", $_email) ? 1 : 0;
    }


    private function rfc2822_compactible_regexp($_email) {
        $regexp = "/^(.* )?[<\[]?((?:(?:(?:[a-zA-Z0-9][\.\-\+_]?)*)[a-zA-Z0-9])+)\@(((?:(?:(?:[a-zA-Z0-9][\.\-_]?){0,62})[a-zA-Z0-9])+)\.([a-zA-Z0-9]{2,6}))[>\]]?$/";
        if (preg_match($regexp, $_email, $_matches)) {
            $this->emailAddres = $_matches[2] . '@' . $_matches[3];
            $this->emailDomain = $_matches[3];
            return true;
        } else {
            return false;
        }
    }


    private function validateRegexp($_email) {
        $fchk = explode('@', $_email);
        if (!isset($fchk[1])) {
            return false;
        }
        $this->emailAddres = $_email;
        $this->emailDomain = $fchk[1];
        if (preg_match('/^[0-9]{1,3}[\.]{1,1}[0-9]{1,3}[\.]{1,1}[0-9]{1,3}[\.]{1,1}[0-9]{1,3}$/', $fchk[1])) {
            $regexIP = '(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])';
            if (preg_match("/^$regexIP\\.$regexIP\\.$regexIP\\.$regexIP$/", $fchk[1])) {
                return true;
            }
        } else {
            if ($this->rfc822_compactible_regexp($_email)) {
                return true;
            }
            if ($this->rfc2822_compactible_regexp($_email)) {
                return true;
            }
        }
        return false;
    }


    private function validateMxDns() {
        if (getmxrr($this->emailDomain, $mxHost, $weights)) {
            $this->connectAddress = $mxHost[0];
            return true;
        } else {
            $host = $this->emailDomain;
            if (checkdnsrr($host, 'ANY')) {
                $this->connectAddress = $this->emailDomain;
                return true;
            }
        }
        return false;
    }

    private function validateServerAddress() {
        $result = true;
        $connect = @fsockopen ( $this->connectAddress, 25, $errorno, $errorstr, 1 );
        if ($connect) {
            stream_set_timeout($connect, 1);
            if (preg_match("/^220/", $out = fgets($connect, 1024))) {
                fputs ($connect, "HELO " . $this->hostname . "\r\n");
                $out = fgets ( $connect, 1024 );
                fputs ($connect, "MAIL FROM: <{$this->senderEmail}>\r\n");
                $from = fgets ( $connect, 1024 );
                fputs ($connect, "RCPT TO: <{$this->emailAddres}>\r\n");
                $to = fgets ($connect, 1024);
                fputs ($connect, "QUIT\r\n");
                fclose($connect);

                if (!preg_match("/^250/", $from) || !preg_match("/^250/", $to )) {
                    $_vMessageSABs = 'not passed';
                    $result = false;
                } else {
                    $_vMessageSABs = 'passed';
                }
            } else {
                $_vMessageSABs = 'not passed';
                $result = false;
            }
        }  else {
            $_vMessageSABs = 'can not connect to the email server';
            $result = false;
        }
        $this->messagesSA['_vMessageSABs'] = $_vMessageSABs;
        return $result;
    }

    /**
     * return level of email check, which was ok
     * -1 All levels failed
     * 1 Valid regular email format
     * 2 Valid MX domain
     * 3 Valid email account - sometimes not possible to check on mail server, because not each server allows such check
     *
     * @param string $email Email address to check
     * @param boolean $validateMX Check if domain name of mail address exist and is valid mail domain
     * @param boolean $validateMailAccount Check if mail address exist on mail server
     * @return unknown
     */
    public function validate($email, $validateMX = true, $validateMailAccount = false) {
        $this->validateAddress = $validateMailAccount;
        $this->validateMX = $validateMX;
        return $this->returnValidatedEmailLevel($email);
    }

    /**
     * return level of email check, which was ok
     * -1 All levels failed
     * 1 Valid regular email format
     * 2 Valid MX domain
     * 3 Valid email account - sometimes not possible to check on mail server, because not each server allows such check
     *
     * @param string $email
     * @return int
     */
    function returnValidatedEmailLevel($email) {
        $level = -1;
        if ($this->validateRegexp($email)) {
            $level = 1;
            if ($this->validateMX == true) {
                if ($this->validateMxDns()) {
                    $level = 2;
                    if ($this->validateAddress == true) {
                        if ($this->validateServerAddress()) {
                            $level = 3;
                        }
                    }
                }
            }
        }
        return $level;
    }
}

// support windows platforms
if (!Gpf_Php::isFunctionEnabled('getmxrr') ) {
    function getmxrr($hostname, &$mxhosts, &$mxweight) {
        if (!is_array ($mxhosts) ) {
            $mxhosts = array ();
        }

        if (!Gpf_Php::isFunctionEnabled('exec') || !Gpf_Php::isFunctionEnabled('escapeshellarg')) {
            //It is not possible to validate domain, because exec or escapeshellarg command is not allowed
            return true;
        }

        if (!empty ($hostname) ) {
            $output = "";
            @exec ("nslookup.exe -type=MX " . escapeshellarg($hostname), $output);
            $imx=-1;

            foreach ($output as $line) {
                $parts = "";
                if (preg_match ("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts) ) {
                    $imx++;
                    $mxweight[$imx] = $parts[1];
                    $mxhosts[$imx] = $parts[2];
                }
            }
            return ($imx!=-1);
        }
        return false;
    }
}

if (!Gpf_Php::isFunctionEnabled('checkdnsrr')) {
    function checkdnsrr($hostName, $recType = 'MX')
    {
        if(!empty($hostName)) {
            if (!Gpf_Php::isFunctionEnabled('exec') || !Gpf_Php::isFunctionEnabled('escapeshellarg')) {
                //It is not possible to validate domain, because exec or escapeshellarg command is not allowed
                return true;
            }

            exec("nslookup.exe -type=$recType " . escapeshellarg($hostName), $result);
            // check each line to find the one that starts with the host
            // name. If it exists then the function succeeded.
            foreach ($result as $line) {
                if(preg_match("/^$hostName/i",$line)) {
                    return true;
                }
            }
        }
        return false;
    }
}
?>
