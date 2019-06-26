<?php

    class Biztech_Translator_Block_Adminhtml_Translator_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget
    {
    protected $_categoryIds = '';

    public function getTree() {
        
        $store = (int) $this->getRequest()->getParam('store');

        if ($store) {
            $Mystore = Mage::app()->getStore($store);
            $rootId = $Mystore->getRootCategoryId();
        }
        else {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        }    

        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();

        $root = $tree->getNodeById($rootId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
        ->setStoreId($store)
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return "<ul>" . $this->BuildBranch($root) . "</ul>";
    }    

    public function BuildBranch(Varien_Data_Tree_Node $node) {

        $categories = $this->getCategories();
        $arrCategory = explode(',',$categories);

        $buildString = '<li style="padding-left: 16px;">';
        $buildString .= '<div class="tree-level-' . $node->getLevel() . '">';

        if($node->getChildrenCount()!=0) {
            $buildString .= '<div class="opener" id="opener' . $node->getId() .'" OnClick="OpenMe(this)"></div>';
        }
        else {
            $buildString .= '<div class="child"></div>';
        }

        $checked = '';
        if(in_array($node->getId(),$arrCategory))
            $checked .= 'checked="checked"';

        $buildString .= '
        <input type="checkbox" class="inputcb" id="inputcb' . $node->getId() .'" OnClick="Decide(this)" enabled="false" '.$checked.'/>
        <div class="folder"></div>
        <a tabindex="1" href="#" hidefocus="on" id="linkItem"><span unselectable="on" id="extdd-' . $node->getLevel() . '">' . $node->getName() . '</a>
        ';

        $buildString .= '</div>';

        if($node->getChildrenCount()!=0) {
            $buildString .= '<ul id="ToOpen' . $node->getId() .'">';

            foreach ($node->getChildren() as $child) {
                $buildString .=  $this->BuildBranch($child);
            }

            $buildString .= '</ul>';

        }

        $buildString .= '</li>';

        return $buildString;
    }
   
    public function getCategories(){        
        if(!($this->_categoryIds) && Mage::registry('translator_data')){
            $model = Mage::registry('translator_data    ')->getData();
            $this->_categoryIds = $model['category_ids'];
        }
        return $this->_categoryIds;
    }
}