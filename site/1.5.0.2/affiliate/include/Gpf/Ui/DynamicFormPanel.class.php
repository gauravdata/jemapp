<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Widget.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */


abstract class Gpf_Ui_DynamicFormPanel_Builder extends Gpf_Object {
    protected $fieldCode;
    protected $fieldName;
    protected $fieldStatus;
    protected $availableValues;

    public function build($fieldCode, $fieldName, $fieldStatus, $availableValues) {
        $this->fieldCode = $fieldCode;
        $this->fieldName = $this->_localize($fieldName);
        $this->fieldStatus = $fieldStatus;
        $this->availableValues = $availableValues;

        $elementHtml = "<label>";
        if ($this->fieldStatus == Gpf_Db_FormField::STATUS_MANDATORY) {
            $elementHtml .= "<strong>$this->fieldName</strong>";
        } else {
            $elementHtml .= $this->fieldName;
        }
        $elementHtml .= $this->createInputElement();
        $elementHtml .= "</label>";
        return $elementHtml;
    }

    public function getCode() {
        return $this->fieldCode;
    }

    abstract protected function createInputElement();
}

class Gpf_Ui_DynamicFormPanel_TextBuilder extends Gpf_Ui_DynamicFormPanel_Builder {
    protected function createInputElement() {
        return '<input type="text" name="' . $this->fieldCode . '">';
    }
}

class Gpf_Ui_DynamicFormPanel_PasswordBuilder extends Gpf_Ui_DynamicFormPanel_Builder {
    protected function createInputElement() {
        return '<input type="password" name="' . $this->fieldCode . '">';
    }
}

class Gpf_Ui_DynamicFormPanel_NumberBuilder extends Gpf_Ui_DynamicFormPanel_Builder {
    protected function createInputElement() {
        return '<input type="text" name="' . $this->fieldCode . '">';
    }
}

class Gpf_Ui_DynamicFormPanel_CheckBoxBuilder extends Gpf_Ui_DynamicFormPanel_Builder {
    protected function createInputElement() {
        return '<input type="checkbox" name="' . $this->fieldCode . '" value="' . Gpf::YES . '">';
    }
}

class Gpf_Ui_DynamicFormPanel_ListBoxBuilder extends Gpf_Ui_DynamicFormPanel_Builder {

    protected function createInputElement() {
        $this->element = '<select name="' . $this->fieldCode . '">';
        $this->buildOptions();
        $this->element .= '</select>';
        return $this->element;
    }

    /**
     * @return $defaultOptionId or null
     */
    protected function getDefaultOptionId() {
        return null;
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    protected function getOptions() {
        $json = new Gpf_Rpc_Json();
        $options = new Gpf_Data_RecordSet();
        $rows = $json->decode($this->availableValues);
        if (is_array($rows)) {
            $options->loadFromArray($rows);
        }

        return $options;
    }

    private function buildOptions() {
        foreach ($this->getOptions() as $option) {
            if ($this->getDefaultOptionId() !== null && $option->get('id') == $this->getDefaultOptionId()) {
                $this->addItem($option->get('id'), $option->get('name'), true);
                continue;
            }
            try {
                $this->addItem($option->get('id'), $option->get('name'));
            } catch (Gpf_Exception $e) {
                $this->addItem($option->get('id'), $option->get('value'));
            }
        }
    }

    private function addItem($value, $name, $selected = false) {
        $this->element .= '<option value="'.$value.'"'.$this->setSelected($selected).'>'.Gpf_Lang::_localizeRuntime($name).'</option>';
    }

    private function setSelected($selected) {
        return $selected ? ' selected="selected"' : '';
    }
}

class Gpf_Ui_DynamicFormPanel_CountryListBoxBuilder extends Gpf_Ui_DynamicFormPanel_ListBoxBuilder {

    protected function getDefaultOptionId() {
        return Gpf_Settings::get(Gpf_Settings_Gpf::DEFAULT_COUNTRY);
    }

    protected function getOptions() {
        $countryForm = new Gpf_Country_CountryForm();
        return $countryForm->loadCountries();
    }
}



/**
 * @package GwtPhpFramework
 */
class Gpf_Ui_DynamicFormPanel extends Gpf_Ui_TemplatePanel {
    private $formName;
    private $builders;
    private $dynamicFormFields = "";
    const DYNAMIC_FIELDS = "DynamicFields";

    public function __construct($templateName, $formName, $panelName='') {
        parent::__construct($templateName, $panelName);
        $this->formName = $formName;
        $this->initBuilders();
    }

    private function initBuilders() {
        $this->builders = array();
        $this->builders["L"] = new Gpf_Ui_DynamicFormPanel_ListBoxBuilder();
        // standard listbox
        $this->builders["C"] = new Gpf_Ui_DynamicFormPanel_CountryListBoxBuilder();
        // gwt listbox
        $this->builders["S"] = new Gpf_Ui_DynamicFormPanel_CountryListBoxBuilder();
        $this->builders["N"] = new Gpf_Ui_DynamicFormPanel_NumberBuilder();
        $this->builders["T"] = new Gpf_Ui_DynamicFormPanel_TextBuilder();
        $this->builders["P"] = new Gpf_Ui_DynamicFormPanel_PasswordBuilder();
        $this->builders["B"] = new Gpf_Ui_DynamicFormPanel_CheckBoxBuilder();
        $this->builders["E"] = new Gpf_Ui_DynamicFormPanel_TextBuilder();
    }

    public function render() {
        $this->renderFormFields();
        $this->add($this->dynamicFormFields, self::DYNAMIC_FIELDS);
        $this->add('<input type="submit" value="' . $this->_('Signup') . '">', "SignupButton");
        return parent::render();
    }

    /**
     * Checks template
     * - check if it contains all mandatory fields
     *
     * @throws Gpf_Ui_DynamicFormPanelCheckException
     */
    public function checkTemplate() {
        $formFields = $this->getFormFields();
        $missingFields = array();

        foreach ($formFields as $field) {
            if ($field->get("status") != Gpf_Db_FormField::STATUS_MANDATORY) {
                continue;
            }
            $fieldCode = $field->get("code");
            $fieldName = $this->_localize($field->get("name"));

            if (!$this->containsId($fieldCode) && !$this->containsId(self::DYNAMIC_FIELDS)) {
                $missingFields[$fieldCode] = $fieldName;
            }
        }

        if (count($missingFields) > 0) {
            throw new Gpf_Ui_DynamicFormPanelCheckException($this->templateName, $missingFields);
        }
    }

    private function getFormFields() {
        return Gpf_Db_Table_FormFields::getInstance()->getFieldsNoRpc(
        $this->formName,
        array(Gpf_Db_FormField::STATUS_MANDATORY, Gpf_Db_FormField::STATUS_OPTIONAL));
    }

    private function renderFormFields() {
        $formFields = $this->getFormFields();

        foreach ($formFields as $field) {
            $fieldCode = $field->get("code");
            $fieldName = $field->get("name");
            $fieldType = $field->get("type");
            $fieldStatus = $field->get("status");
            $availableValues = $field->get("availablevalues");

            $builder = $this->builders[$fieldType];
            $elementHtml = $builder->build($fieldCode, $fieldName, $fieldStatus, $availableValues);
            if ($this->containsId($builder->getCode())) {
                $this->add($elementHtml, $builder->getCode());
            } else {
                $this->dynamicFormFields .= $elementHtml;
            }
        }
    }

    public function addStaticField($fieldCode, $fieldName, $fieldType, $fieldStatus, $availableValues = '') {
        $builder = $this->builders[$fieldType];
        $elementHtml = $builder->build($fieldCode, $fieldName, $fieldStatus, $availableValues);
        $this->add($elementHtml, $builder->getCode());
    }

}

?>
