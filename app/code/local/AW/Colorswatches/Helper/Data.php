<?php
class AW_Colorswatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     * @param string $sSize
     *
     * @return int
     */
    public static function convertPHPSizeToBytes($sSize)
    {
        if (is_numeric($sSize)) {
            return $sSize;
        }
        $sSuffix = substr($sSize, -1);
        $iValue = substr($sSize, 0, -1);
        switch(strtoupper($sSuffix)){
            case 'P':
                $iValue *= 1024;
            case 'T':
                $iValue *= 1024;
            case 'G':
                $iValue *= 1024;
            case 'M':
                $iValue *= 1024;
            case 'K':
                $iValue *= 1024;
                break;
        }
        return $iValue;
    }

    /**
     * @param int $iValue
     *
     * @return string
     */
    public static function convertBytesToHumanValue($iValue)
    {
        $literalList = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');
        $resultLiteral = null;
        foreach($literalList as $literal) {
            $resultLiteral = $literal;
            if ($iValue/1024 < 1) {
                break;
            }
            $iValue /= 1024;
        }
        return $iValue . ' ' . $resultLiteral;
    }

    /**
     * @return null|string
     */
    public static function getMaximumFileUploadSize()
    {
        $maxFileUploadSizeInBytes = min(
            self::convertPHPSizeToBytes(ini_get('post_max_size')),
            self::convertPHPSizeToBytes(ini_get('upload_max_filesize'))
        );
        return self::convertBytesToHumanValue($maxFileUploadSizeInBytes);
    }
}