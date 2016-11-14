<?php

class MT_Email_Model_Core_Email_Template_Filter
    extends  Mage_Core_Model_Email_Template_Filter
{

    private $__processVarFlag = true;

    public function filter($origValue)
    {
        //process .phtml files
        $value = parent::filter($origValue);
        //process variables like {{var store.id}} in html content
        if ($this->__processVarFlag) {
            $value = $this->prepareTemplateBefore($value, $origValue);
            $value = $this->applyCustomFilter($value);
            $value = $this->prepareTemplateAfter($value, $origValue);
            $value = parent::filter($value);
        }
        return $value;
    }

    public function setProcessVarFlag($flag = true)
    {
        $this->__processVarFlag = $flag;
    }

    /**
     * These filters will be applied after template rendering.
     *
     * @param $value
     * @return mixed
     */
    public function applyCustomFilter($value)
    {
        $value = $this->applyCustomBlockFilter($value);
        return $value;
    }

    /**
     * Custom changes in template before filter.
     *
     * @param $value
     * @param $origValue
     * @return mixed
     */
    public function prepareTemplateBefore($value, $origValue)
    {
        if (!$this->isSubjectFilter($origValue)) {
            $value = $this->addCssTag($value);
        }
        return $value;
    }

    /**
     * Last changes in the content before output.
     *
     * @param $value
     * @param $origValue
     * @return mixed
     */
    public function prepareTemplateAfter($value, $origValue)
    {
        if (!$this->isSubjectFilter($origValue)) {
            $value = $this->addBodyTag($value);
        }
        return $value;
    }

    /**
     * This method will add <body> tag to the email content
     *
     * @param $value
     * @return mixed
     */
    public function addBodyTag($value)
    {
        if (substr_count($value, '<body>') == 0) {
            $value = '<body>'.$value;
        }

        if (substr_count($value, '<html>') == 0) {
            $value = '<html>'.$value;
        }

        if (substr_count($value, '</body>') == 0) {
            $value = $value.'</body>';
        }

        if (substr_count($value, '</html>') == 0) {
            $value = $value.'</html>';
        }

        return $value;
    }

    /**
     * This method will add variable {{var non_inline_styles}} to the email content
     *
     * @param $value
     * @return mixed
     */
    public function addCssTag($value)
    {
        //this variable is available only for email content processor
        if (substr_count($value, '<style>') == 0 && substr_count($value, '{{var non_inline_styles}}') == 0) {
            $value = '{{var non_inline_styles}}'.$value;
        }

        return $value;
    }

    public function applyCustomBlockFilter($value)
    {
        if (substr_count($value, '{{block ') > 0) {
            $tmpString = explode('{{block ', $value);
            $i = 0;
            foreach ($tmpString as $tmpValue) {
                //skip the first
                if ($i == 0) {
                    $i = 1;
                    continue;
                }

                if (substr_count($tmpValue, '}}') > 0) {
                    $tmpString2 = explode('}}', $tmpValue);
                    if (substr_count($tmpString2[0], 'type=') == 1 && substr_count($tmpString2[0], 'block_id=') == 1) {
                        $find = '{{block '.$tmpString2[0].'}}';

                        $tmpString3 = explode(' ', $tmpString2[0]);
                        foreach ($tmpString3 as $params) {
                            if (substr_count($params, 'type=') == 1) {
                                $blockType = str_replace(array('type=', '"', "'"), '', $params);
                            } else if (substr_count($params, 'block_id=') == 1) {
                                $blockId = str_replace(array('block_id=', '"', "'"), '', $params);
                            }
                        }

                        if (!empty($blockType)) {
                            $replaceBlock = Mage::app()->getLayout()->createBlock($blockType);
                            if (!empty($blockId)) {
                                $replaceBlock->setBlockId($blockId);
                            }
                            $replace = $replaceBlock->toHtml();
                            $value = str_replace($find, $replace, $value);
                        };

                    }
                }
            }
        }

        return $value;
    }

    public function isSubjectFilter($origValue)
    {
        if (substr_count($origValue, '{{layout') > 0){
            return false;
        }

        return true;
    }
}