<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ImportFromSource.class.php 19984 2008-08-19 15:08:09Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Represents translation of one sentese
 *
 * @package GwtPhpFramework
 */
class Gpf_Lang_Parser_Translation extends Gpf_Object {
    const STATUS_NOT_TRANSLATED = 'N';
    const STATUS_TRANSLATED = 'T';
    const STATUS_DEPRECATED = 'D';

    const TYPE_CLIENT = 'C';
    const TYPE_SERVER = 'S';
    const TYPE_BOTH = 'B';
    const TYPE_METADATA = 'M';

    /**
     * Source message of translation
     *
     * @var string
     */
    private $source;

    /**
     * Destination message of translation
     *
     * @var string
     */
    private $destination;

    /**
     * Type of translation
     *
     * @var string possible values are: C - client, S - server, B - both, M - metadata
     */
    private $type;

    /**
     * Modules, where was translation used
     * Each application can overwrite mechanism how will be computed module name
     *
     * @var array
     */
    private $modules = array();

    /**
     * Flag if is customer specific translation or general translation
     *
     * @var boolean
     */
    private $isCustomerSpecific = false;

    /**
     * Status of translation
     *
     * @var string Allowed values are N - not translated, T - translated, D - depreceated
     */
    private $status = 'N';

    /**
     * Set source message of translation
     *
     * @param string $sourceMessage
     */
    public function setSourceMessage($sourceMessage) {
        $this->source = $sourceMessage;
    }

    /**
     * Get source message of translation
     *
     * @return string
     */
    public function getSourceMessage() {
        return $this->source;
    }

    /**
     * Set destination message of translation
     *
     * @param string $destinationMessage
     */
    public function setDestinationMessage($destinationMessage) {
        $this->destination = $destinationMessage;
    }

    /**
     * Get destination message of translation
     *
     * @return string
     */
    public function getDestinationMessage() {
        return $this->destination;
    }

    /**
     * Set translation type (Client/Server/Both)
     *
     * @param string $translationType
     */
    public function setType($translationType) {
        if (!in_array($translationType,
                        array(self::TYPE_CLIENT, self::TYPE_SERVER, self::TYPE_BOTH, self::TYPE_METADATA))) {
            throw new Gpf_Exception('Translation type [' . $translationType . '] is not supported');
        }
        if ($this->type == null) {
            $this->type = $translationType;
        }
        //if translation was set to another type before, set it to type both
        if ($this->type != $translationType && $this->type != self::TYPE_METADATA) {
            $this->type = self::TYPE_BOTH;
        }
    }

    /**
     * Get translation type
     *
     * @return string possible values are C - client, S - server, B - both or M - metadata
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Add module name, where was used translation
     *
     * @param string $module
     */
    public function addModule($module) {
        $this->modules[$module] = true;
    }

    /**
     * Get array of module names, where was translation used
     *
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }

    public function setModules($modules) {
        $this->modules = $modules;
    }

    /**
     * If translation found in more modules, return empty string, else return module name
     *
     * @return string
     */
    public function getModule() {
        $modules = array_keys($this->modules);
        if (count($modules) == 1) {
            return array_shift($modules);
        }
        return '';
    }

    /**
     * Set if translation is customer specific
     *
     * @param boolean $isCustomerSpecific
     */
    public function setCustomerSpecific($isCustomerSpecific) {
        if ($isCustomerSpecific == Gpf::YES || $isCustomerSpecific === true) {
            $this->isCustomerSpecific = true;
        } else {
            $this->isCustomerSpecific = false;
        }
    }

    /**
     * Is customer spefic translation ?
     *
     * @return string
     */
    public function isCustomerSpecific() {
        return $this->isCustomerSpecific ? Gpf::YES : Gpf::NO;
    }

    public function getId() {
        return md5($this->source);
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($newStatus) {
        if (!in_array($newStatus,
        array(
        self::STATUS_NOT_TRANSLATED,
        self::STATUS_TRANSLATED,
        self::STATUS_DEPRECATED))) {
            throw new Gpf_Exception('Not supported translation status ' . $newStatus);
        }
        $this->status = $newStatus;
    }


    public function loadFromRecord(Gpf_Data_Record $record) {
        $this->setSourceMessage($record->get('source'));
        $this->setDestinationMessage($record->get('translation'));
        $this->setType($record->get('type'));
        $this->setStatus($record->get('status'));
        $this->addModule($record->get('module'));
        $this->setCustomerSpecific($record->get('customer'));
    }

    public function set($attribute, $value) {
        switch ($attribute) {
        	case "translation":
        	    $this->setDestinationMessage($value);
        	break;
            case "customer":
                $this->setCustomerSpecific($value);
            break;

        	default:
        		throw new Gpf_Exception('Not supported attribute');
        	break;
        }
    }
}

?>
