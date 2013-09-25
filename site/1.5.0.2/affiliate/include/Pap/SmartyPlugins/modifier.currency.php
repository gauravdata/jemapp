<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty |currency modifier
 *
 * Type:     function<br>
 * Name:     localize<br>
 *
 * Examples:
 * <pre>
 * {$allCommission|currency}
 * </pre>
 * @author   Michal Bebjak
 * @param    string
 * @return   string
 */
function smarty_modifier_currency($number)
{
    $number = number_format($number, Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision(), Gpf_Settings_Regional::getInstance()->getDecimalSeparator(), Gpf_Settings_Regional::getInstance()->getThousandsSeparator());
    return Pap_Common_Utils_CurrencyUtils::stringToCurrencyFormat($number);
}
?>
