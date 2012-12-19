<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ChannelsForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Gpf_Common_CodeUtils_CodeGenerator extends Gpf_Common_CodeUtils_CodeBase {

    /**
     * @return String
     */
    public function generate() {
        $code = $this->format;

        foreach ($this->replace[0] as $string) {
            $code = substr_replace($code, $this->generateString($this->removeBrackets($string)), strpos($code, $string), strlen($string));
        }

        return $code;
    }

    private function removeBrackets($string) {
        return substr($string, 1, -1);
    }

    private function generateString($string) {
        $replacedString = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $replacedString .= $this->generateChar($string[$i]);
        }
        return $replacedString;
    }

    private function generateChar($char) {
        if ($char == 'X') {
            $char = $this->generateID();
        }
        return chr(rand($this->min($char), $this->max($char)));
    }

    private function generateID() {
        $rand = rand(1,3);
        return ($rand == 1 ? '9' : ($rand == 2 ? 'z' : 'Z'));
    }
}
?>
