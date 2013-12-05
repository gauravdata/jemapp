<?php
/**
 * KiTT boostrap for non-autoloaders
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    David Keijser <david.keijser@klarna.com>
 * @copyright 2013 Klarna AB (http://klarna.com)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache license v2.0
 * @link      http://integration.klarna.com/
 */

define('KITT_DIR', dirname(__FILE__));

require_once KITT_DIR . '/ErrorInterface.php';
require_once KITT_DIR . '/Error.php';
require_once KITT_DIR . '/ErrorInterface.php';
require_once KITT_DIR . '/MissingFieldsError.php';
require_once KITT_DIR . '/HTTPContext.php';
require_once KITT_DIR . '/Addresses.php';
require_once KITT_DIR . '/Exception.php';
require_once KITT_DIR . '/Addresses.php';
require_once KITT_DIR . '/String.php';
require_once KITT_DIR . '/Ajax.php';
require_once KITT_DIR . '/Translator.php';
require_once KITT_DIR . '/TemplateException.php';
require_once KITT_DIR . '/Mustache/Exception.php';
require_once KITT_DIR . '/Mustache.php';
require_once KITT_DIR . '/Mustache/Wrapper.php';
require_once KITT_DIR . '/Template.php';
require_once KITT_DIR . '/TemplateLoader.php';
require_once KITT_DIR . '/CountryLogic.php';
require_once KITT_DIR . '/Locator.php';
require_once KITT_DIR . '/CountryDeducer.php';
require_once KITT_DIR . '/Exception.php';
require_once KITT_DIR . '/InvalidLocaleException.php';
require_once KITT_DIR . '/Locale.php';
require_once KITT_DIR . '/PClassCollection.php';
require_once KITT_DIR . '/LanguagePack.php';
require_once KITT_DIR . '/JSONLanguagePack.php';
require_once KITT_DIR . '/XMLLanguagePack.php';
require_once KITT_DIR . '/Widget.php';
require_once KITT_DIR . '/Installment/Widget.php';
require_once KITT_DIR . '/Installment/ControllerInterface.php';
require_once KITT_DIR . '/Installment/Controller/Abstract.php';
require_once KITT_DIR . '/Product/Controller.php';
require_once KITT_DIR . '/ErrorMessage.php';
require_once KITT_DIR . '/VFS.php';
require_once KITT_DIR . '/Cart/Controller.php';
require_once KITT_DIR . '/Formatter.php';
require_once KITT_DIR . '/DefaultFormatter.php';
require_once KITT_DIR . '/MissingConfigurationException.php';
require_once KITT_DIR . '/Config.php';
require_once KITT_DIR . '/SimpleConfig.php';
require_once KITT_DIR . '/Session.php';
require_once KITT_DIR . '/Checkout/ControllerInterface.php';
require_once KITT_DIR . '/InputValues.php';
require_once KITT_DIR . '/Payment/Widget.php';
require_once KITT_DIR . '/Baptizer.php';
require_once KITT_DIR . '/DefaultBaptizer.php';
require_once KITT_DIR . '/Payment/Title.php';
require_once KITT_DIR . '/Payment/OptionInterface.php';
require_once KITT_DIR . '/TemplateFieldsInterface.php';
require_once KITT_DIR . '/TemplateFields.php';
require_once KITT_DIR . '/Payment/Option.php';
require_once KITT_DIR . '/Checkout/Controller.php';
require_once KITT_DIR . '/Lookup.php';
require_once KITT_DIR . '/Dispatcher.php';
require_once KITT_DIR . '/API.php';
require_once KITT_DIR . '/Locale.php';
require_once KITT_DIR . '/../KiTT.php';

