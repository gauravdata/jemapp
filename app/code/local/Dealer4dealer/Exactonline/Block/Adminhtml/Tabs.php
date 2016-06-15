<?php
class Dealer4dealer_Exactonline_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();

		$this->setId('exactonline_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('exactonline')->__('Exact Online Connector'));
	}

	protected function _beforeToHtml()
	{
		// Get the categoryId from the request
		$categoryId = $this->getRequest()->getParam('category',1);

		// Load all categories that are active
		$categories = Mage::getModel('exactonline/category')
						->getCollection()
						->setOrder('sort_order', 'ASC')
						->addFieldToFilter('is_active',1);

		foreach($categories as $category) {
			$this->addTab($category->getId(), array(
				'category_id'=>$category->getId(),
				'label' => Mage::helper('exactonline')->__($category->getCategoryName()),
				'title' => Mage::helper('exactonline')->__($category->getCategoryName()),
			));
		}

		// Set the active tab
		$this->setActiveTab($categoryId);

		return parent::_beforeToHtml();
	}

	public function getTabUrl($tab)
	{
		return $this->getUrl('*/*/index', array('category' => $tab->getCategoryId()));
	}
}