<?php

class Biztech_Translator_Adminhtml_TranslatorController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {

		$this->loadLayout()->_setActiveMenu('translator/items');

		$this->getLayout()
			->getBlock('head')
			->setCanLoadExtJs(true);

		if (Mage::helper('translator')->isEnable()) {
			$this->_addContent($this->getLayout()->createBlock('translator/adminhtml_translator_translate'))
				->_addLeft($this->getLayout()->createBlock('translator/adminhtml_translator_tabs'));

		}
		$this->renderLayout();
	}

	public function calculateAction() {
		$charCount = 0;
		$charCountwithoutHtml = 0;
		$products = Mage::getModel('catalog/product')->getCollection();

		$attributes = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields');
		$translateAll = Mage::getStoreConfig('translator/translator_general/translate_all');
		$finalAttributeSet = array_values(explode(',', $attributes));

		foreach ($products as $p) {
			$product = Mage::getModel('catalog/product')->load($p->getEntityId());
			if (($translateAll == 1 && $product->getTranslated() == 1) || ($translateAll == 1 && $product->getTranslated() == 0) || ($translateAll == 0 && $product->getTranslated() == 0)) {
				foreach ($finalAttributeSet as $attributeCode) {
					if (!isset($product[$attributeCode]) || empty($product[$attributeCode])) {
						continue;
					} else {
						$char = strip_tags($product[$attributeCode]);
						$charCount += mb_strlen($product[$attributeCode]);
						$charCountwithoutHtml += mb_strlen($char);
					}
				}
			}
		}

		$result = array(
			'withouthtml' => $charCountwithoutHtml,
			'withhtml' => $charCount,
		);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	public function translateAction() {
		$data = $this->getRequest()->getPost();
		if ($data['value'] != '') {
			$translate = array();
			$translate['id'] = $data['id'];
			$result = Mage::getModel('translator/translator')->getTranslate($data['value'], $data['langto'], $data['langfrom']);
			if ($data['id'] == 'url_key') {
				$r = array();
				foreach ($result as $key => $value) {
					$r[$key] = $value;
					if ($key == 'text') {
						$urlKey = Mage::getModel('catalog/product_url')->formatUrlKey($value);
						if ($urlKey == '') {
							$r[$key] = $data['value'];
						} else {
							$r[$key] = $urlKey;
						}
					}
				}
				$translate['value'] = $r;
			} else {
				$translate['value'] = $result;
			}
			$translate['status'] = $result['status'];
		} else {
			$result = array(
				'text' => 'There is no data to translate.',
				'status' => 'fail',
			);
			$translate = array(
				'id' => $data['id'],
				'value' => $result,
				'status' => $result['status'],
			);
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($translate));
	}

	public function translatecmspageAction() {
		$data = $this->getRequest()->getPost();
		$find_data = array('="{{', '}}"', '{{', '}}');
		$replace_data = array('="((', '))"', '<span class="notranslate">{{', '}}</span>');
		$newarr = array('="((', '))"');
		$newarr1 = array('="{{', '}}"');
		$data['value'] = str_replace($newarr, $newarr1, str_replace($find_data, $replace_data, $data['value']));
		$translate = array();
		$translate['id'] = $data['id'];
		$result = Mage::getModel('translator/translator')->getTranslateCmsPage($data['value'], $data['langto'], $data['langfrom']);
		$translate['value'] = $result;
		$translate['status'] = $result['status'];
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($translate));
	}

	public function massTranslateAction() {
		$langTo = '';
		$langFrom = '';
		$data = $this->getRequest()->getParams();
		$ids = $this->getRequest()->getParam('product');
		$storeId = $this->getRequest()->getParam('store_id');
		$timescheduled = null;
		if (!$this->_checkErrors($ids, $storeId)) {
			return;
		}

		if (isset($ids) && is_array($ids) && !empty($ids)) {
			sort($ids);
		}

		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		$cronTranslate1 = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', 'pending');

		if ($cronTranslate1->count() > 0) {
			foreach ($cronTranslate1 as $abortCron1) {
				$abortCron = Mage::getModel('translator/cron')->load($abortCron1->getId())->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->setIsAbort(1)->setStatus('abort')->save();
			}
		} elseif (isset($data['is_abort']) && $data['is_abort'] == 1 && $cronTranslate1->count() > 0) {
			foreach ($cronTranslate1 as $abortCron1) {
				$abortCron = Mage::getModel('translator/cron')->load($abortCron1->getId())->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->setIsAbort(1)->setStatus('abort')->save();
			}
		}

		$cronTranslate = Mage::getModel('translator/cron');
		$cronTranslate->setCronName('Cron Translation')
			->setStoreId($storeId)
			->setProductIds(json_encode($ids))
			->setLangFrom($langFrom)
			->setLangTo($langTo)
			->setStatus('pending');
		$cronTranslate->save();

		$jobCode = 'bizgridcrontranslation';
		$cronSet = Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode);

		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Cron Process Registered! Please enable log to view any error occured'));

		/*Mage::getModel('translator/observer')->translateGridCronMassProducts();
			        Mage::getModel('translator/observer')->checkBizTranslateCron();
		*/

		$this->_redirect('adminhtml/catalog_product/index', array('store' => $storeId));

	}

	private function _checkErrors($ids, $storeId) {
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select products.'));
			$this->_redirect('adminhtml/catalog_product/index');
			return false;
		}

		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store view.'));
			$this->_redirect('adminhtml/catalog_product/index');
			return false;
		}

		return true;
	}

	public function checkCronExistsAction() {
		$cronTranslate1 = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', 'pending');

		if ($cronTranslate1->count() > 0) {
			$result = array(
				'status' => 1,
				'msg' => $this->__('Cron already exists!'),
			);
			$this->getResponse()->setBody(json_encode($result));
		} else {
			$result = array(
				'status' => 0,
				'msg' => $this->__('Cron doesn\'t exists!'),
			);
			$this->getResponse()->setBody(json_encode($result));
		}
	}

	public function massCronDeleteAction() {
		$cronIds = $this->getRequest()->getParam('translator');
		if (!is_array($cronIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($cronIds as $cronId) {
					$cronData = Mage::getModel('translator/cron')->load($cronId);
					$cronData->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
						'Total of %d record(s) were successfully deleted', count($cronIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/cron');
	}

	public function ProductTranslateAction() {
		$id = $this->getRequest()->getParam('product_id');
		$storeId = $this->getRequest()->getParam('store_id');
		if ($storeId == Null) {
			$storeId = 0;
		}
		/* Use Default */
		/*$product_edit_form = array();
			        parse_str($this->getRequest()->getPost('product_edit_form'), $product_edit_form);
		*/
		/*end use default*/
		$translatedProductCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		try {
			$productModel = Mage::getModel('catalog/product');
			$product = $productModel->setStoreId($storeId)->load($id);
			//Mage::app()->setCurrentStore($storeId);
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$attributes = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields', $storeId);

			if (!$product || !$attributes) {
				//  continue;
			}
			$finalAttributeSet = array_values(explode(',', $attributes));

			foreach ($finalAttributeSet as $attributeCode) {
				if (!isset($product[$attributeCode]) || empty($product[$attributeCode])) {
					continue;
				}
				$translate = Mage::getModel('translator/translator')->getTranslate($product[$attributeCode], $langTo, $langFrom);
				if ($translate['status'] == 'fail') {
					Mage::log('"' . $product->getName() . '" can\'t be translated for' . ' "Product attribute : ' . $attributeCode . '". Error: ' . $translate['text']);
					Mage::getSingleton('adminhtml/session')->addError('"' . $product->getName() . '" can\'t be translated for' . ' "Product attribute : ' . $attributeCode . '". Error: ' . $translate['text']);
					continue;
				} else {
					// $product->setData($attributeCode, $translate['text']);

					if ($attributeCode == 'url_key') {
						$urlKey = Mage::getModel('catalog/product_url')->formatUrlKey($translate['text']);

						if ($urlKey != '') {
							$action = Mage::getModel('catalog/resource_product_action');
							$action->updateAttributes(array($id), array(
								$attributeCode => $urlKey,
							), $storeId);
							$action->updateAttributes(array($id), array(
								'translated' => true,
							), $storeId);
						}

					} else {

						$action = Mage::getModel('catalog/resource_product_action');
						$action->updateAttributes(array($id), array(
							$attributeCode => $translate['text'],
						), $storeId);

						$action->updateAttributes(array($id), array(
							'translated' => true,
						), $storeId);
					}
				}
			}

			/*if ($useDefaults) {
				                foreach ($useDefaults as $attributeCode) {
				                    if (in_array($attributeCode,$finalAttributeSet)) {
				                        continue;
				                    }
				                    $product->setData($attributeCode, false);
				                }
			*/

			try {
				// $product->save();
				if ($translate['status'] != 'fail') {
					$translatedProductCount++;
				}
			} catch (Exception $e) {
				Mage::log($e->getMessage());
				//continue;
			}

			if ($translatedProductCount == 0) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('No Product has been translated. Detail info see in log.'));
			} else {
				$langTo = $languages[$langTo];
				Mage::getSingleton('adminhtml/session')->addSuccess(
					$translatedProductCount . Mage::helper('translator')->__(' product') . $this->__(' has been translated to
                        ') . $langTo
				);
			}
		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return;
		}

		$result = 1;
		Mage::app()->getResponse()->setBody($result);
	}

	public function translateSearchAction() {

		$cache = $this->getRequest()->getParam('cache');
		if ($cache) {
			$searchResult = unserialize(Mage::getModel('core/cache')->load('translate_search_result'));
		} else {
			$searchResponse = array();
			$string = $this->getRequest()->getParam('searchString');
			$modules = $this->getRequest()->getParam('modules');
			$interface = $this->getRequest()->getParam('interface');
			$locale = $this->getRequest()->getParam('locale');
			$case = NULL;
			$keysearch = NULL;
			$searchResult = Mage::getModel('translator/search')->searchString($string, $locale, $modules, $interface);
			$cache = Mage::getModel('core/cache');
			$cache->save($string, 'translate_search_string', array('translate_cache'), null);
			$cache->save(serialize($searchResult), 'translate_search_result', array('translate_cache'), null);
			$cache->save('asc', 'translate_search_order', array('translate_cache'), null);
			Mage::getSingleton('core/session')->setTranslateSearchCache(true);
		}
		$this->loadLayout();
		if (empty($searchResult)) {
			$searchResponse['data'] = Mage::helper('translator')->__("No data found.");
		} else if (isset($searchResult['warning']) && $searchResult['warning'] == "True") {
			$searchResponse['data'] = Mage::helper('translator')->__("The search returned too many data. Please narrow your search.");
		} else {
			$searchResponse['data'] = $this->getLayout()
				->createBlock('translator/adminhtml_translator_translateGrid')
				->setResults($searchResult)
				->setTemplate('translator/translategrid.phtml')
				->toHtml();
		}
		$searchResponse = Zend_Json::encode($searchResponse);
		$this->getResponse()->setBody($searchResponse);
	}

	public function editAction() {
		$this->loadLayout();
		$this->getLayout()
			->getBlock('head')
			->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('translator/adminhtml_translator_edit'));
		$this->renderLayout();
	}

	public function translateAdminAction() {
		$data = $this->getRequest()->getPost();
		/*	echo "<pre>";
			print_r($data);
		*/
		$string = $data['value'];
		if ($data['value'] != '') {
			if ($string != strip_tags($string)) {
				$find_data = array('="{{', '}}"', '{{', '}}');
				$replace_data = array('="((', '))"', '<span class="notranslate">{{', '}}</span>');
				$newarr = array('="((', '))"');
				$newarr1 = array('="{{', '}}"');
				$string = str_replace($newarr, $newarr1, str_replace($find_data, $replace_data, $string));
			}
			$stringWithoutQuotes = str_replace("'", "\\", $string);
			$langto = explode('_', $data['langto']);
			$result = Mage::getModel('translator/translator')->getTranslate($stringWithoutQuotes, $langto[0], $data['langfrom']);
			$resultwithQuotes = str_replace("\\", "'", $result);
		} else {
			$result = array(
				'text' => 'There is no data to translate.',
				'status' => 'fail',
			);
			$translate = array(
				'id' => $data['id'],
				'value' => $result,
				'status' => $result['status'],
			);
		}
		$result = $this->getResponse()->setBody(json_encode($resultwithQuotes));
		return $result;
	}

	public function saveStringAction() {
		$original = $this->getRequest()->getParam('original_translation');
		$module = $this->getRequest()->getParam('module');
		$customString = $this->getRequest()->getParam('string');
		preg_match('#\((.*?)\)#', $this->getRequest()->getParam('source'), $match); //get search string module name.
		$string_module = $match[1];
		$locale = $this->getRequest()->getParam('locale');
		$storeId = $this->getRequest()->getParam('storeid');
		$original = $string_module . '::' . $original;
		$resource = Mage::getResourceModel('core/translate_string');
		$resource->saveTranslate($original, $customString, $locale, $storeId);
		Mage::app()->getCache()->clean();
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Translation data is saved.'));
		$this->_redirect('*/*/');
	}

	public function massTranslateReviewAction() {
		$ids = $this->getRequest()->getParam('reviews');
		$storeId = $this->getRequest()->getParam('store_id');
		if (!$this->_checkReviewErrors($ids, $storeId)) {
			return;
		}
		$selectedReviewCount = count($ids);
		$translatedReviewCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		try {
			foreach ($ids as $id) {
				$reviewModel = Mage::getModel('review/review');
				$review = $reviewModel->setStoreId($storeId)->load($id);
				//Mage::app()->setCurrentStore($storeId);
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$attributes = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields', $storeId);
				if (!$review || !$attributes) {
					continue;
				}
				$attributeCode = "detail";
				if (!isset($review['detail']) || empty($review['detail'])) {
					continue;
				}
				$translate = Mage::getModel('translator/translator')->getTranslate($review['detail'], $langTo, $langFrom);
				if ($translate['status'] == 'fail') {
					Mage::log('"' . $review['entity_id'] . '" can\'t be translated for' . ' "Review  : ' . $attributeCode . '". Error: ' . $translate['text']);

					Mage::getSingleton('adminhtml/session')->addError('"' . $review['entity_id'] . '" can\'t be translated for' . ' "Review  : ' . $attributeCode . '". Error: ' . $translate['text']);
					continue;
				} else {
					$review->setData($attributeCode, $translate['text']);
				}
				try {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$review->save();
					if ($translate['status'] != 'fail') {
						$translatedReviewCount++;
					}

				} catch (Exception $e) {
					Mage::log($e->getMessage());
					continue;
				}
			}
			if ($translatedReviewCount == 0) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('No review has been translated. Detail info see in log.'));
				$this->_redirect('adminhtml/catalog_product_review/index');
				return;
			} else {
				Mage::getSingleton('adminhtml/session')->addSuccess(
					$translatedReviewCount . Mage::helper('translator')->__(' Review(s) of ') . $selectedReviewCount . $this->__(' has been translated to ') . $languages[$langTo]
				);
			}

		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/catalog_product_review/index');
			return;
		}
		$this->_redirect('adminhtml/catalog_product_review/index', array('store' => $storeId));
	}

	private function _checkReviewErrors($ids, $storeId) {
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Review.'));
			$this->_redirect('adminhtml/catalog_product_review/index');
			return false;
		}
		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store view.'));
			$this->_redirect('adminhtml/catalog_product_review/index');
			return false;
		}
		return true;
	}

	public function massTranslateCmsBlockAction() {

		$ids = $this->getRequest()->getParam('block_id');
		$storeId = $this->getRequest()->getParam('store_id');
		if (!$this->_checkCmsBloclErrors($ids, $storeId)) {
			return;
		}
		$selectedReviewCount = count($ids);
		$translatedReviewCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		try {
			foreach ($ids as $id) {
				$cmsBlockModel = Mage::getModel('cms/block');
				$cmsBlock = $cmsBlockModel->setStoreId($storeId)->load($id);
				//Mage::app()->setCurrentStore($storeId);
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$attributes = array('content', 'title');
				if (!$cmsBlock || !$attributes) {
					continue;
				}
				$finalAttributeSet = $attributes;
				foreach ($finalAttributeSet as $attributeCode) {
					if (!isset($cmsBlock[$attributeCode]) || empty($cmsBlock[$attributeCode])) {
						continue;
					}
					$translate = Mage::getModel('translator/translator')->getTranslate($cmsBlock[$attributeCode], $langTo, $langFrom);
					if ($translate['status'] == 'fail') {
						Mage::log('"' . $cmsBlock['block_id'] . '" can\'t be translated for' . ' "CMS Block  : ' . $attributeCode . '". Error: ' . $translate['text']);

						Mage::getSingleton('adminhtml/session')->addError('"' . $cmsBlock['block_id'] . '" can\'t be translated for' . ' "CMS Block  : ' . $attributeCode . '". Error: ' . $translate['text']);
						continue;
					} else {
						$cmsBlock->setData($attributeCode, $translate['text']);
					}
				}
				try {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$cmsBlock->save();
					if ($translate['status'] != 'fail') {
						$translatedReviewCount++;
					}

				} catch (Exception $e) {
					Mage::log($e->getMessage());
					continue;
				}
			}
			if ($translatedReviewCount == 0) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('No CMS Block has been translated. Detail info see in log.'));
				$this->_redirect('adminhtml/cms_page/index');
				return;
			} else {

				Mage::getSingleton('adminhtml/session')->addSuccess(
					$translatedReviewCount . Mage::helper('translator')->__(' CMS Block(s) of ') . $selectedReviewCount . $this->__(' were translated on ') . $languages[$langTo]
				);
			}

		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/cms_block/index');
			return;
		}

		$this->_redirect('adminhtml/cms_block/index', array('store' => $storeId));
	}

	private function _checkCmsBloclErrors($ids, $storeId) {
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select CMS Block.'));
			$this->_redirect('adminhtml/cms_block/index');
			return false;
		}

		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store view.'));
			$this->_redirect('adminhtml/cms_block/index');
			return false;
		}

		return true;

	}

	public function massTranslateCmsPageAction() {
		$ids = $this->getRequest()->getParam('page_id');
		$storeId = $this->getRequest()->getParam('store_id');
		if (!$this->_checkCmsPageErrors($ids, $storeId)) {
			return;
		}
		$selectedReviewCount = count($ids);
		$translatedReviewCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') !== 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);
		try {
			foreach ($ids as $id) {
				$cmsPageModel = Mage::getModel('cms/page');
				$cmsPage = $cmsPageModel->setStoreId($storeId)->load($id);
				//Mage::app()->setCurrentStore($storeId);
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$attributes = Mage::getStoreConfig('translator/translator_general/massaction_cmspagetranslate_fields', $storeId);
				if (!$cmsPage || !$attributes) {
					continue;
				}
				$finalAttributeSet = array_values(explode(',', $attributes));
				foreach ($finalAttributeSet as $attributeCode) {
					$attributeCode = str_replace("page_", "", $attributeCode);
					if (!isset($cmsPage[$attributeCode]) || empty($cmsPage[$attributeCode])) {
						continue;
					}
					$translate = Mage::getModel('translator/translator')->getTranslate($cmsPage[$attributeCode], $langTo, $langFrom);
					if ($translate['status'] == 'fail') {
						Mage::log('"' . $cmsPage['page_id'] . '" can\'t be translated for' . ' "CMS Page  : ' . $attributeCode . '". Error: ' . $translate['text']);

						Mage::getSingleton('adminhtml/session')->addError('"' . $cmsPage['page_id'] . '" can\'t be translated for' . ' "CMS Page  : ' . $attributeCode . '". Error: ' . $translate['text']);
						continue;
					} else {
						$cmsPage->setData($attributeCode, $translate['text']);
					}
				}
				try {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$cmsPage->save();
					if ($translate['status'] != 'fail') {
						$translatedReviewCount++;
					}

				} catch (Exception $e) {
					Mage::log($e->getMessage());
					continue;
				}
			}
			if ($translatedReviewCount == 0) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('No CMS Page has been translated. Detail info see in log.'));
				$this->_redirect('adminhtml/cms_page/index');
				return;
			} else {

				Mage::getSingleton('adminhtml/session')->addSuccess(
					$translatedReviewCount . Mage::helper('translator')->__(' CMS Block(s) of ') . $selectedReviewCount . $this->__(' were translated on ') . $languages[$langTo]
				);
			}

		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/cms_page/index');
			return;
		}

		$this->_redirect('adminhtml/cms_page/index', array('store' => $storeId));
	}

	private function _checkCmsPageErrors($ids, $storeId) {
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select CMS Page.'));
			$this->_redirect('adminhtml/cms_page/index');
			return false;
		}

		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store view.'));
			$this->_redirect('adminhtml/cms_page/index');
			return false;
		}

		return true;

	}

	public function massTranslateTagAction() {
		$ids = $this->getRequest()->getParam('tag');
		$storeId = $this->getRequest()->getParam('store_id');

		if (!$this->_checkTagErrors($ids, $storeId)) {
			return;
		}

		$selectedTagCount = count($ids);
		$translatedTagCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		try {

			foreach ($ids as $id) {

				$tagModel = Mage::getModel('tag/tag');
				$tag = $tagModel->setStoreId($storeId)->load($id);
				//Mage::app()->setCurrentStore($storeId);
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$attributes = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields', $storeId);
				if (!$tag || !$attributes) {
					continue;
				}
				$attributeCode = "name";
				if (!isset($tag['name']) || empty($tag['name'])) {
					continue;
				}
				$translate = Mage::getModel('translator/translator')->getTranslate($tag['name'], $langTo, $langFrom);
				if ($translate['status'] == 'fail') {
					Mage::log('"' . $tag['tag_id'] . '" can\'t be translated for' . ' "Tag  : ' . $attributeCode . '". Error: ' . $translate['text']);

					Mage::getSingleton('adminhtml/session')->addError('"' . $tag['tag_id'] . '" can\'t be translated for' . ' "Tag  : ' . $attributeCode . '". Error: ' . $translate['text']);
					continue;
				} else {
					$tag->setData($attributeCode, $translate['text']);
				}

				try {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$tag->save();
					if ($translate['status'] != 'fail') {
						$translatedTagCount++;
					}

				} catch (Exception $e) {
					Mage::log($e->getMessage());
					continue;
				}
			}
			if ($translatedTagCount == 0) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('No tag has been translated. Detail info see in log.'));
				$this->_redirect('adminhtml/tag/index');
				return;
			} else {

				Mage::getSingleton('adminhtml/session')->addSuccess(
					$translatedTagCount . Mage::helper('translator')->__(' Tag(s) of ') . $selectedTagCount . $this->__(' has been translated to ') . $languages[$langTo]
				);
			}

		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('adminhtml/tag/index');
			return;
		}

		$this->_redirect('adminhtml/tag/index', array('store' => $storeId));
	}

	private function _checkTagErrors($ids, $storeId) {
		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Tag.'));
			$this->_redirect('adminhtml/tag/index');
			return false;
		}
		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store view.'));
			$this->_redirect('adminhtml/tag/index');
			return false;
		}
		return true;
	}

	public function massCMSDeleteAction() {
		if ($this->getRequest()->getParam('block_id')) {
			$taxIds = $this->getRequest()->getParam('block_id');
		} else {
			$taxIds = $this->getRequest()->getParam('page_id');
		}
		if (!is_array($taxIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select Item(es).'));
		} else {
			try {
				$rateModel = Mage::getModel('cms/block');
				foreach ($taxIds as $taxId) {
					$rateModel->load($taxId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('tax')->__(
						'Total of %d record(s) were deleted.', count($taxIds)
					)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massTranslateCategoryAction() {
		$id = $this->getRequest()->getParam('category_ids');
		$country = $this->getRequest()->getParam('country');
		$ids = explode(",", $id);
		$ids = array_filter($ids);
		$storeId = $this->getRequest()->getParam('store_id');
		if (!$this->_checkMassCategoryErrors($ids, $storeId)) {
			return;
		}
		$selectedTagCount = count($ids);
		$translatedTagCount = 0;
		$languages = Mage::helper('translator/languages')->getLanguages();
		if ($this->getRequest()->getParam('lang_to') != 'locale') {
			$langTo = $this->getRequest()->getParam('lang_to');
		} else {
			$langTo = Mage::helper('translator')->getLanguage($storeId);
		}

		$langFrom = Mage::helper('translator')->getFromLanguage($storeId);

		try {
			foreach ($ids as $id) {
				$categoryModel = Mage::getModel('catalog/category');
				$categorydata = $categoryModel->setStoreId($storeId)->load($id);
				//Mage::app()->setCurrentStore($storeId);
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
				$attributes = Mage::getStoreConfig('translator/translator_general/massaction_categorytranslate_fields', $storeId);
				if (!$categorydata || !$attributes) {
					continue;
				}

				$finalAttributeSet = array_values(explode(',', $attributes));
				foreach ($finalAttributeSet as $attributeCode) {

					$attributeCode = str_replace("group_4", "", $attributeCode);
					if (!isset($categorydata[$attributeCode]) || empty($categorydata[$attributeCode])) {
						continue;
					}
					$translate = Mage::getModel('translator/translator')->getTranslate($categorydata[$attributeCode], $country, $langFrom);
					if ($translate['status'] == 'fail') {
						Mage::log('"' . $categorydata['tag_id'] . '" can\'t be translated for' . ' "Category  : ' . $attributeCode . '". Error: ' . $translate['text']);

						$var1["error"] = $categorydata[$attributeCode] . '" can\'t be translated for' . ' "Category  : ' . $attributeCode . '". Error: ' . $translate['text'];
						continue;
					} else {
						$categorydata->setData($attributeCode, $translate['text']);
					}
				}
				try {
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$categorydata->save();
					if ($translate['status'] != 'fail') {
						$translatedTagCount++;
					}
				} catch (Exception $e) {
					Mage::log($e->getMessage());
					continue;
				}
			}
			if ($translatedTagCount == 0) {
				$var1["error"] = "No category has been translated. Detail info see in log ";
			} else {
				$var1["success"] = $selectedTagCount . ' categories(s) of ' . $selectedTagCount . ' were translated';
			}
		} catch (Exception $e) {
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*/index');
			return;
		}
		$data = json_encode($var1);
		$this->getResponse()->setBody($data);
	}

	private function _checkMassCategoryErrors($ids, $storeId) {
		if (!is_array($ids)) {
			$var1["error"] = "Please select Category ID";
			return false;
		}
		if (!$storeId) {
			$block = $this->getLayout()->getBlock('store_switcher');
			if (!$block) {
				return true;
			}
			$var1["error"] = "Please select Store View";
			return false;
		}
		return true;
	}

	public function cronmasstranslateAction() {
		$storeId = $this->getRequest()->getParam('store_id');

		Mage::getModel('translator/translator')->setTranslateCron($storeId);

		if (Mage::getStoreConfig('dev/log/active')) {
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Cron Process Registered! Please see log for more info!'));
		} else {
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Cron Process Registered! Please enable log to view any error occured'));
		}

		$this->_redirect('adminhtml/catalog_product/index', array('store' => $storeId));
	}

	public function cronAction() {
		// Mage::getModel('translator/observer')->checkBizTranslateCron();
		// exit;
		$this->_title($this->__('Translator'))->_title($this->__('Cron'));
		$this->loadLayout();
		$this->_setActiveMenu('translator/cron');

		$this->getLayout()
			->getBlock('head')
			->setCanLoadExtJs(true);

		if (Mage::helper('translator')->isEnable()) {
			$this->_addContent($this->getLayout()->createBlock('translator/adminhtml_translator_cron'));

		}
		$this->renderLayout();
	}

	public function gridAction() {
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('translator/adminhtml_translator_cron_grid')->toHtml()
		);
	}

	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('translator/items');
	}
}
