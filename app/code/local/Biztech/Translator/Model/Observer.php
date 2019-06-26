<?php

class Biztech_Translator_Model_Observer {

	public function translateMassProducts(Varien_Event_Observer $observer) {
		$block = $observer->getEvent()->getBlock();
		if (Mage::helper('translator')->isEnable()) {

			if ($block->getRequest()->getControllerName() == 'catalog_product' || $block->getRequest()->getControllerName() == 'catalog_product_review' || $block->getRequest()->getControllerName() == 'tag') {

				if (!isset($block)) {
					return $this;
				}

				$storeId = Mage::app()->getRequest()->getParam('store', 0);
				$configLang = Mage::getStoreConfig('translator/translator_general/languages', Mage::app()->getRequest()->getParam('store', 0));

				$language = Mage::helper('translator')->getLanguage($storeId);

				$localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
				$lang = '';

				$arr = explode('_', $localeCode);
				$language = $arr[0];
				if (in_array($language, array_keys(Mage::helper('translator/languages')->getLanguages()))) {
					$lang = $language;
				} else {
					$lang['message'] = __('Select language for this store in System->Configuration->Translator');
				}

				$fullNameLanguage = Mage::helper('translator')->getLanguageFullNameByCode($language, $storeId);
				$languages = Mage::helper('translator/languages')->getLanguages(true);
				if ($fullNameLanguage) {
					$languages = array($lang => $fullNameLanguage) + $languages;
				} else {
					$languages = array('' => Mage::helper('translator')->__('Select language or adjust in config')) + $languages;
				}

				if ( /*get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' &&*/
					$block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction &&
					$block->getRequest()->getControllerName() == 'catalog_product'
				) {
					$_cronProducts = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', 'pending');
					$block->addItem('translator', array(
						'label' => Mage::helper('catalog')->__('Translate Selected Products'),
						'url' => $block->getUrl('adminhtml/translator/masstranslate'),
						'additional' => array(
							'store_id' => array(
								'name' => 'store_id',
								'type' => 'hidden',
								'class' => 'required-entry',
								'value' => $storeId,
							),
							'lang_to' => array(
								'name' => 'lang_to',
								'type' => 'select',
								'label' => Mage::helper('catalog')->__('Translate To'),
								'values' => $languages,
							),
							'checkCron' => array(
								'name' => 'checkCron',
								'type' => 'hidden',
								'value' => $block->getUrl('adminhtml/translator/checkCronExists'),
							),
							'is_abort' => array(
								'name' => 'is_abort',
								'type' => 'hidden',
								'value' => 0,
							),
							'gridUrl' => array(
								'name' => 'gridUrl',
								'type' => 'hidden',
								'value' => $block->getUrl('adminhtml/catalog_product/grid', array('store', $storeId)),
							),
						),
					));
				}

				if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction &&
					$block->getRequest()->getControllerName() == 'catalog_product_review'
				) {

					$block->addItem('translator', array(
						'label' => Mage::helper('review')->__('Translate Selected Reviews'),
						'url' => $block->getUrl('adminhtml/translator/masstranslatereview'),
						'additional' => array(
							'store_id' => array(
								'name' => 'store_id',
								'type' => 'hidden',
								'class' => 'required-entry',
								'value' => $storeId,
							),
							'lang_to' => array(
								'name' => 'lang_to',
								'type' => 'select',
								'label' => Mage::helper('catalog')->__('Translate To'),
								'values' => $languages,
							),
						),

					));

				}

				if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction &&
					$block->getRequest()->getControllerName() == 'tag'
				) {

					$block->addItem('translator', array(
						'label' => Mage::helper('tag')->__('Translate Selected Tags'),
						'url' => $block->getUrl('adminhtml/translator/masstranslatetag'),
						'additional' => array(
							'store_id' => array(
								'name' => 'store_id',
								'type' => 'hidden',
								'class' => 'required-entry',
								'value' => $storeId,
							),
							'lang_to' => array(
								'name' => 'lang_to',
								'type' => 'select',
								'label' => Mage::helper('catalog')->__('Translate To'),
								'values' => $languages,
							),
						),

					));
				}
			}
		}

		return $this;
	}

	public function checkKey($observer) {
		$key = Mage::getStoreConfig('translator/activation/key');
		Mage::helper('translator')->checkKey($key);

	}

	public function checkErrorMessage($observer) {
		$getControllerName = Mage::app()->getRequest()->getControllerName();
		$getActionName = Mage::app()->getRequest()->getActionName();
		if (($getControllerName == 'adminhtml_translator' || $getControllerName == 'translator') && ($getActionName == 'index' || $getActionName == 'cron')) {
			if (!Mage::helper('translator')->isEnable()) {
				Mage::getSingleton('adminhtml/session')->addError('Language Translator extension is not enabled. Please enable it from System → Configuration → BIZTECH EXTENSIONS → Translator.');
			}
		}
	}

	public function translateGridCronMassProducts() {
		$jobCode = 'bizgridcrontranslation';

		if (!Mage::helper('translator')->isEnable()) {
			throw new \Exception("Language Translator extension is not enabled. Please enable it from System → Configuration → BIZTECH EXTENSIONS → Translator.", 1);
			return;
		}

		$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();
		$_charCutLimit = Mage::getStoreConfig('translator/translator_general/google_daily_cut_before_limit');

		if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate())) && $_logCron->getRemainLimit() <= 0) {
			throw new \Exception("Daily Limit Reached! Please try again later!", 1);
			return;
		}

		Mage::log('=======================================================', null, 'translatecron.log');
		Mage::log(' Start Translation ' . date('d-m-Y', time()), null, 'translatecron.log');

		$batchSize = Mage::getStoreConfig('translator/translator_general/product_batch_size') ? Mage::getStoreConfig('translator/translator_general/product_batch_size') : 20;

		$_cronProducts = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', 'pending');
		$characterLimit = Mage::getStoreConfig('translator/translator_general/google_daily_limit') - $_charCutLimit;

		foreach ($_cronProducts as $cronProductData) {
			$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();

			if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate())) && $_logCron->getRemainLimit() <= 0) {
				throw new \Exception("Daily Limit Reached! Please try again later!", 1);
				return;
			}

			if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate())) && $_logCron->getRemainLimit() > 0) {
				$characterLimit = $_logCron->getRemainLimit();
			}

			if ($characterLimit > 0) {

				if ($cronProductData->getLangTo() == '') {
					$langTo = Mage::helper('translator')->getFromLanguage($cronProductData->getStoreId());
				} else {
					$langTo = $cronProductData->getLangTo();
				}

				if ($cronProductData->getLangFrom() == '') {
					$langFrom = Mage::helper('translator')->getFromLanguage($cronProductData->getStoreId());
				} else {
					$langFrom = $cronProductData->getLangFrom();
				}

				$_productIds = json_decode($cronProductData->getProductIds());

				foreach (array_chunk($_productIds, $batchSize) as $productId) {

					if ($cronProductData->getIsAbort() == 0) {
						$c = Mage::getModel('translator/cron')->load($cronProductData->getId());
						if ($c->getIsAbort() == 1) {
							break;
						}
					}

					//if ($characterLimit > $_charCutLimit) {
					if ($characterLimit > 0) {
						$batchCount = count($productId);
						Mage::log("     Store {$cronProductData->getStoreId()} Batch Product Count {$batchCount}", null, 'translatecron.log');
						$this->batchproductTranslate($cronProductData->getStoreId(), $langTo, $langFrom, $productId, $characterLimit, $jobCode, $cronProductData->getId());
					} else {
						$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();

						if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate()))) {
							break;
						} else {
							$_logCron = Mage::getModel('translator/logcron')->setCronJobCode($jobCode)->setStatus(1)->setStoreId($cronProductData->getStoreId())->save();
						}
					}
				}

				$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();
				if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate()))) {
					$_cronupdate = Mage::getModel('translator/cron')->load($cronProductData->getId());
					if ($_cronupdate->getIsAbort() == 1) {
						$_cronupdate->setStatus('abort1')->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->save();
					} else {
						$_cronupdate->setStatus('pending')->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->save();
					}
				} else {
					$_cronupdate = Mage::getModel('translator/cron')->load($cronProductData->getId());
					if ($_cronupdate->getIsAbort() == 1) {
						$_cronupdate->setStatus('abort1')->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->save();
					} else {
						$_cronupdate->setStatus('success')->setUpdateCronDate(Mage::getSingleton('core/date')->gmtDate())->save();
					}
				}

				Mage::log(' End Translation for Store' . $cronProductData->getStoreId(), null, 'translatecron.log');

			}
		}

		/* Run Indexer After URL Key Update */
		if (Mage::helper('translator')->isUrlKeyAttribute()) {
			try {
				$processor = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_url');
				$processor->reindexAll();
			} catch (Exception $e) {

				Mage::log($e->getMessage(), null, 'translatecron.log');

			}
		}
		/* End Indexer */

		Mage::log(' End Translation ' . date('d-m-Y', time()), null, 'translatecron.log');

	}

	protected function batchproductTranslate($storeId, $langTo, $langFrom, $batchProducts, &$characterLimit, $jobCode, $cronId = null) {
		$_lastSuccessProductId = 0;
		$_failCount = 0;
		$_skipCount = 0;
		$_successCount = 0;
		$remainChar = 0;
		$i = 0;
		$charCount = 0;
		$listCSV = array();

		$logFileName = Mage::getBaseDir('log') . '/' . Mage::getSingleton('core/date')->gmtDate('d-m-Y') . '_Translator.csv';
		if (!is_file($logFileName)) {
			$csvHeader = array('Currenet ProductID', 'Attribute Code', 'Translation Status', 'Character Limit', 'Remaining Characterlimit');
			Mage::log(',' . print_r(implode(",", $csvHeader), true), null, Mage::getSingleton('core/date')->gmtDate('d-m-Y') . '_Translator.csv');

		}

		foreach ($batchProducts as $batchProduct) {
			$i++;

			if (isset($batchProduct['entity_id'])) {
				$productId = $batchProduct['entity_id'];
			} else {
				$productId = $batchProduct;
			}

			if ($i == 10) {
				if ($cronId) {
					$_checkCron = Mage::getModel('translator/cron')->load($cronId);
					if ($_checkCron->getIsAbort() == 1) {
						$_logCron = Mage::getModel('translator/logcron')->setCronJobCode($jobCode)->setStatus(1)->setStoreId($storeId)->setRemainLimit($characterLimit)->setProductId($productId)->save();
						return $this;
					}
				}
			}

			/*if ($productId != 399) {
				                continue;
			*/
			Mage::log("         Translation For Batch Product {$productId} to {$langTo}", null, 'translatecron.log');
			Mage::log("             : characterlimit {$characterLimit}", null, 'translatecron.log');

			$productModel = Mage::getModel('catalog/product');
			$product = $productModel->setStoreId($storeId)->load($productId);
			$attributes = Mage::getStoreConfig('translator/translator_general/massaction_translate_fields', $storeId);
			$translateAll = Mage::getStoreConfig('translator/translator_general/translate_all');
			$finalAttributeSet = array_values(explode(',', $attributes));

			if (($translateAll == 1 && $product->getTranslated() == 1) || ($translateAll == 1 && $product->getTranslated() == 0) || ($translateAll == 0 && $product->getTranslated() == 0)) {

				$charCount = 0;
				foreach ($finalAttributeSet as $attributeCode) {
					if (!isset($product[$attributeCode]) || empty($product[$attributeCode])) {
						continue;
					} else {
						$charCount += mb_strlen($product[$attributeCode]);
					}
				}

				$remainChar = $characterLimit - $charCount;

				if ($remainChar > 0) {
					$_lastSuccessProductId = $productId;
					$listCSV[] = $productId;

					foreach ($finalAttributeSet as $attributeCode) {

						if (!isset($product[$attributeCode]) || empty($product[$attributeCode])) {
							continue;
						}

						try {
							$translate = Mage::getModel('translator/translator')->getTranslate($product[$attributeCode], $langTo, $langFrom);
							$listCSV[] = $product[$attributeCode];
							$listCSV[] = $translate['status'];
							$listCSV[] = $characterLimit;

							if (isset($translate['status']) && $translate['status'] == 'fail') {

								Mage::log(',' . print_r(implode(",", $listCSV), true), null, Mage::getSingleton('core/date')->gmtDate('d-m-Y') . '_Translator.csv');

								$msg = '"' . $product->getName() . '" can\'t be translated for' . ' "Product attribute : ' . $attributeCode . '". Error: ' . $translate['text'];
								Mage::log('         ' . $msg, null, 'translatecron.log');
								Mage::log("                 : characterlimit {$characterLimit}", null, 'translatecron.log');
								$_failCount++;
								continue;
							} else {
								if (isset($translate['status']) && $translate['status'] == 'success') {
									if ($attributeCode == 'url_key') {
										$urlKey = Mage::getModel('catalog/product_url')->formatUrlKey($translate['text']);

										if ($urlKey != '') {
											$action = Mage::getModel('catalog/resource_product_action');
											$action->updateAttributes(array($productId), array(
												$attributeCode => $urlKey,
											), $storeId);
											$action->updateAttributes(array($productId), array(
												'translated' => true,
											), $storeId);
										}

									} else {
										if (isset($translate['text']) && $translate['text'] != '') {
											$action = Mage::getModel('catalog/resource_product_action');
											$action->updateAttributes(array($productId), array(
												$attributeCode => $translate['text'],
											), $storeId);
											$action->updateAttributes(array($productId), array(
												'translated' => true,
											), $storeId);
										}
									}
									$_successCount++;
									Mage::log("             beforesuccess translate {$productId} : characterlimit {$characterLimit}", null, 'translatecron.log');
									$characterLimit -= mb_strlen($product[$attributeCode]);
									$listCSV[] = $characterLimit;

									Mage::log("             aftersuccess translate {productId} : characterlimit {$characterLimit}", null, 'translatecron.log');
									Mage::log("         Save Translation For Batch Product {$productId} and attribute {$attributeCode} ", null, 'translatecron.log');
								} else {
									$_failCount++;
									Mage::log("             on fail {$productId} : characterlimit {$characterLimit}", null, 'translatecron.log');
								}
							}

						} catch (Exception $e) {
							Mage::log($e->getMessage(), null, 'translatecron.log');
							Mage::log("             Exception on translate : characterlimit {$characterLimit}", null, 'translatecron.log');
						}
						Mage::log(',' . print_r(implode(",", $listCSV), true), null, Mage::getSingleton('core/date')->gmtDate('d-m-Y') . '_Translator.csv');
						unset($listCSV);
						$listCSV = array();

						sleep(1);
					}
				} else {

					$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();

					if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate()))) {
						$_failCount++;
						continue;
					} else {
						$_logCron = Mage::getModel('translator/logcron')->setCronJobCode($jobCode)->setStatus(1)->setStoreId($storeId)->setRemainLimit($characterLimit)->setProductId($_lastSuccessProductId)->save();
					}
				}
			} else {
				$_skipCount++;
				// $characterLimit += $charCount;
			}
		}

		if (($_failCount + $_skipCount) == count($batchProducts)) {
			$_charCutLimit = Mage::getStoreConfig('translator/translator_general/google_daily_cut_before_limit');
			$characterLimit1 = Mage::getStoreConfig('translator/translator_general/google_daily_limit') - $_charCutLimit;
			if ($characterLimit1 == $characterLimit) {
				$remainChar = $characterLimit;
			} else {
				$remainChar = $remainChar > 0 ? $remainChar : 0;
			}
			$_logCron = Mage::getModel('translator/logcron')->setCronJobCode($jobCode)->setStatus(0)->setStoreId($storeId)->setRemainLimit($remainChar)->setProductId($_lastSuccessProductId)->save();

		} else {
			$_logCron = Mage::getModel('translator/logcron')->setCronJobCode($jobCode)->setStatus(1)->setStoreId($storeId)->setRemainLimit($characterLimit)->setProductId($_lastSuccessProductId)->save();
		}

	}

	public function checkBizTranslateCron() {

		if (!Mage::helper('translator')->isEnable()) {
			throw new \Exception("Language Translator extension is not enabled. Please enable it from System → Configuration → BIZTECH EXTENSIONS → Translator.", 1);
			return;
		}
		$jobCode = 'bizgridcrontranslation';
		$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();
		$_charCutLimit = Mage::getStoreConfig('translator/translator_general/google_daily_cut_before_limit');
		$timescheduled = null;
		$storeId = 0;
		//$characterLimit = Mage::getStoreConfig('translator/translator_general/google_daily_limit') - $_charCutLimit;

		if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate()))) {

			if ($_logCron->getRemainLimit() <= 0) {
				$timescheduled = date('Y-m-d H:i:s', strtotime($_logCron->getCronDate() . '+1day +1hours'));
			}

			if ($_logCron->getCronJobCode() == 'bizgridcrontranslation') {

				Mage::log('Translator Check Start', null, 'translatecheck.log');

				$storeId = $_logCron->getStoreId();
				$_cronProducts = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', array('eq' => 'pending')) /*->addFieldToFilter('store_id', array('eq' => $storeId))*/;
				$_cronAbourt1Products = Mage::getModel('translator/cron')->getCollection()->getLastItem();

				if ($_cronProducts->count() > 0) {

					// $productModel = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)->addAttributeToSort('entity_id', Varien_Data_Collection::SORT_ORDER_DESC)->addFieldToFilter('entity_id', array('gt' => $_logCron->getProductId()));

					$productModel = Mage::getModel('catalog/product')->getCollection()
						->addFieldToFilter('entity_id', array('in' => json_decode($_cronProducts->getFirstItem()->getProductIds())))
						->addStoreFilter($storeId)
						->addAttributeToSort('entity_id', Varien_Data_Collection::SORT_ORDER_DESC);

					$_pTranslate = array();

					foreach ($productModel as $product) {
						$p = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
						if ($p->getTranslated() == 1) {
							continue;
						} else {
							$_pTranslate[] = $p->getId();
						}
					}
					sort($_pTranslate);
					if (is_array($_pTranslate) && !empty($_pTranslate)) {

						$_checkCronProducts = Mage::getModel('translator/cron')->getCollection()
							->addFieldToFilter('product_ids', array('eq' => json_encode($_pTranslate)))
							->addFieldToFilter('status', 'pending');

						if ($_checkCronProducts->count() == 0) {
							foreach ($_cronProducts as $_cronProduct) {
								$_abortTranslate[] = Mage::getModel('translator/cron')->load($_cronProduct->getId())->setIsAbort(1)->setStatus('abort1')->save();
							}

							$langFrom = $_cronProducts->getFirstItem()->getLangFrom() ? $_cronProducts->getFirstItem()->getLangFrom() : null;

							Mage::log('Translator Check count of products : ' . count($_pTranslate), null, 'translatecheck.log');
							Mage::log('Translator Check: translate to : ' . $_cronProducts->getFirstItem()->getLangTo(), null, 'translatecheck.log');
							Mage::log('Translator Check: translate from : ' . $langFrom, null, 'translatecheck.log');

							try {

								$schedule = Mage::getModel('cron/schedule')->getCollection()->addFieldToFilter('job_code', $jobCode)->load();

								$_cronUpdate = Mage::getModel('translator/cron');
								$_cronUpdate->setCronName('Cron Translation')
									->setStoreId($storeId)
									->setProductIds(json_encode($_pTranslate))
									->setLangFrom($langFrom)
									->setLangTo($_cronProducts->getFirstItem()->getLangTo())
									->setStatus('pending')
									->save();

								Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode, $timescheduled);
							} catch (Exception $e) {
								Mage::log($e->getMessage(), null, 'translatecheck.log');
							}
						} else {
							Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode, $timescheduled);
						}
					} else {
						if (empty($_pTranslate)) {
							foreach ($_cronProducts as $_cronProduct) {
								$_successTranslate[] = Mage::getModel('translator/cron')->load($_cronProduct->getId())->setStatus('success')->save();
							}
						}
					}
				} else if ($_cronAbourt1Products->getStatus() == 'abort1') {

					$productModel = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)->addAttributeToSort('entity_id', Varien_Data_Collection::SORT_ORDER_DESC);

					if ($_cronAbourt1Products->count()) {
						$productModel->addFieldToFilter('entity_id', array('in' => json_decode($_cronAbourt1Products->getFirstItem()->getProductIds())));
					}

					$_pTranslate = array();

					foreach ($productModel as $product) {
						$p = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
						if ($p->getTranslated() == 1) {
							continue;
						} else {
							$_pTranslate[] = $p->getId();
						}
					}
					sort($_pTranslate);

					if (is_array($_pTranslate) && !empty($_pTranslate)) {
						$_checkCronProducts = Mage::getModel('translator/cron')->getCollection()
							->addFieldToFilter('product_ids', array('eq' => json_encode($_pTranslate)))
							->addFieldToFilter('status', 'pending');

						if ($_checkCronProducts->count() == 0) {
							foreach ($_cronAbourt1Products as $_cronProduct) {
								$_abortTranslate[] = Mage::getModel('translator/cron')->load($_cronProduct->getId())->setIsAbort(1)->setStatus('abort1')->save();
							}

							$langFrom = $_cronAbourt1Products->getFirstItem()->getLangFrom() ? $_cronAbourt1Products->getFirstItem()->getLangFrom() : null;

							Mage::log('Abort1 Translator Check count of products : ' . count($_pTranslate), null, 'translatecheck.log');
							Mage::log('Abort1 Translator Check: translate to : ' . $_cronAbourt1Products->getFirstItem()->getLangTo(), null, 'translatecheck.log');
							Mage::log('Abort1 Translator Check: translate from : ' . $langFrom, null, 'translatecheck.log');

							try {

								$_cronUpdate = Mage::getModel('translator/cron');
								$_cronUpdate->setCronName('Cron Translation')
									->setStoreId($storeId)
									->setProductIds(json_encode($_pTranslate))
									->setLangFrom($langFrom)
									->setLangTo($_cronAbourt1Products->getFirstItem()->getLangTo())
									->setStatus('pending')
									->save();
								Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode, $timescheduled);
							} catch (Exception $e) {
								Mage::log($e->getMessage(), null, 'translatecheck.log');
							}
							Mage::log('Translator Check End', null, 'translatecheck.log');
						} else {
							Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode, $timescheduled);
						}
					}
				}

			}

			Mage::log('Translator Check End', null, 'translatecheck.log');

		} else {

			$jobCode = 'bizgridcrontranslation';
			$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();
			$_cronProducts = Mage::getModel('translator/cron')->getCollection()->addFieldToFilter('status', array('eq' => 'pending'));

			if ($_cronProducts->count() > 0) {
				if (!$_logCron->getData() && $_cronProducts->count() > 0) {
					if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate())) && $_logCron->getRemainLimit() <= 0) {
						$timescheduled = date('Y-m-d H:i:s', strtotime($_logCron->getCronDate() . '+1day +1hours'));
					}
				} elseif ($_logCron->getData() && $_cronProducts->count() > 0) {
					$timescheduled = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
				} else {
					$timescheduled = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
				}
				Mage::getModel('translator/translator')->setTranslateCron($storeId, $jobCode, $timescheduled);
			}

			$this->_error[] = Mage::helper('translator')->__('No Translation cron set');
		}

		return $this;

	}

}