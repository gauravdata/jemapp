<?php

class Biztech_Translator_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getFromLanguage($storeId) {
		$fromConf = Mage::getStoreConfig('translator/translator_general/from_lang', $storeId);
		if ($fromConf == "auto") {
			$fromLanguage = "";
		} else {
			$fromLanguage = $fromConf;
		}
		return $fromLanguage;
	}

	public function getLanguageFullNameByCode($code, $storeId) {
		$languagesList = Mage::helper('translator/languages')->getLanguages();
		if ($code == 'locale') {
			$lang = $this->getLanguage($storeId);
			if (is_array($lang)) {
				return false;
			} else {
				return $languagesList[$lang];
			}
		}
		return $languagesList[$code];
	}

	public function getLanguage($storeId) {
		$localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
		$configLang = Mage::getStoreConfig('translator/translator_general/languages', $storeId);
		$lang = '';
		if ($configLang == 'locale') {
			$arr = explode('_', $localeCode);
			$language = $arr[0];
			if (in_array($language, array_keys(Mage::helper('translator/languages')->getLanguages()))) {
				$lang = $language;
			} else {
				$lang['message'] = $this->__('Select language for this store in System->Configuration->Translator');
			}
		} else {
			$lang = $configLang;
		}
		return $lang;
	}

	public function getTranslateRequestValues($request, $store) {
		$values = array();
		$values['module'] = ucfirst($request->getParam('modules'));
		$translation = explode('::', base64_decode($request->getParam('translation')));
		$values['string'] = (isset($translation[1])) ? htmlspecialchars_decode($translation[1]) : htmlspecialchars_decode(base64_decode($request->getParam('translation')));
		$values['original_translation'] = htmlspecialchars_decode(base64_decode($request->getParam('original')));
		$original = explode("::", $values['original_translation']);
		$values['original'] = (isset($original[1]) ? $original[1] : $original[0]);
		$values['source'] = base64_decode($request->getParam('source'));
		$values['source_label'] = base64_decode($request->getParam('source'));
		$values['interface'] = ucfirst($request->getParam('interface'));
		$values['locale'] = $request->getParam('locale');
		$values['storeid'] = $store->getId();
		$values['store_name'] = $store->getId() != 0 ? $store->getName() : "Main Website";
		$values['translate_url'] = Mage::helper('adminhtml')->getUrl('adminhtml/translator/translateAdmin');
		return $values;
	}

	public function checkKey($k, $s = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, sprintf('http://www.appjetty.com/extension/licence.php'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&domains=' . urlencode(implode(',', $this->getAllStoreDomains())) . '&sec=translator');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$content = curl_exec($ch);
		$res = json_decode($content);
		$modulestatus = new Mage_Core_Model_Config();
		$enc = Mage::helper('core');
		if (empty($res)) {
			$modulestatus->saveConfig('translator/activation/key', "");
			$modulestatus->saveConfig('translator/translator_general/enabled', 0);
			$data = Mage::getStoreConfig('translator/activation/data');
			$groups = array(
				'activation' => array(
					'fields' => array(
						'data' => array(
							'value' => $data,
						),
						'websites' => array(
							'value' => '',
						),
					),
				),
			);
			Mage::getModel('adminhtml/config_data')
				->setSection('translator')
				->setGroups($groups)
				->save();
			Mage::getConfig()->reinit();
			Mage::app()->reinitStores();
			return;
		}
		$data = '';
		$web = '';
		$en = '';

		if (isset($res->dom) && intval($res->c) > 0 && intval($res->suc) == 1) {
			$data = $enc->encrypt(base64_encode(json_encode($res)));
			if (!$s) {
				$params = Mage::app()->getRequest()->getParam('groups');
				if (isset($params['activation']['fields']['websites']['value'])) {
					$s = $params['activation']['fields']['websites']['value'];
				} else if (Mage::app()->getRequest()->getParam('website')) {
					$s = explode(',', str_replace($data, '', Mage::helper('core')->decrypt(Mage::getStoreConfig('translator/activation/websites'))));
				}
			}
			$en = $res->suc;
			if (isset($s) && $s != null) {
				$web = $enc->encrypt($data . implode(',', $s) . $data);
			} else {
				$web = $enc->encrypt($data . $data);
			}
		} else {
			$modulestatus->saveConfig('translator/activation/key', "");
			$modulestatus->saveConfig('translator/translator_general/enabled', 0);
		}
		$groups = array(
			'activation' => array(
				'fields' => array(
					'data' => array(
						'value' => $data,
					),
					'websites' => array(
						'value' => (string) $web,
					),
					'en' => array(
						'value' => $en,
					),
					'installed' => array(
						'value' => 1,
					),
				),
			),
		);
		Mage::getModel('adminhtml/config_data')
			->setSection('translator')
			->setGroups($groups)
			->save();
		Mage::getConfig()->reinit();
		Mage::app()->reinitStores();
	}

	public function getAllStoreDomains() {
		$domains = array();
		foreach (Mage::app()->getWebsites() as $website) {
			$url = $website->getConfig('web/unsecure/base_url');
			if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
				$domains[] = $domain;
			}
			$url = $website->getConfig('web/secure/base_url');
			if ($domain = trim(preg_replace('/^.*?\/\/(.*)?\//', '$1', $url))) {
				$domains[] = $domain;
			}
		}
		return array_unique($domains);
	}

	public function getFormatUrl($url) {
		$input = trim($url, '/');
		if (!preg_match('#^http(s)?://#', $input)) {
			$input = 'http://' . $input;
		}
		$urlParts = parse_url($input);
		$path = isset($urlParts['path']) ? $urlParts['path'] : '';

		$domain = preg_replace('/^www\./', '', $urlParts['host'] . $path);
		return $domain;
	}

	public function getDomains() {
		$domains = array();
		foreach (Mage::app()->getWebsites() as $website) {
			$url = $website->getConfig('web/unsecure/base_url');
			if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
				$domains[] = $domain;
			}
			$url = $website->getConfig('web/secure/base_url');
			if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
				$domains[] = $domain;
			}
		}
		return array_unique($domains);
	}

	public function getDataInfo() {
		$websiteId = Mage::app()->getRequest()->getParam('website');
		$data = Mage::app()->getWebsite($websiteId)->getConfig('translator/activation/data');
		return json_decode(base64_decode(Mage::helper('core')->decrypt($data)));
	}

	public function isEnable() {
		$websiteId = Mage::app()->getWebsite()->getId();
		$isenabled = Mage::getStoreConfig('translator/translator_general/enabled');
		if ($isenabled) {
			if ($websiteId) {
				$websites = $this->getAllWebsites();
				$key = Mage::getStoreConfig('translator/activation/key');
				if ($key == null || $key == '') {
					return false;
				} else {
					$en = Mage::getStoreConfig('translator/activation/en');
					if ($isenabled && $en && in_array($websiteId, $websites)) {
						return true;
					} else {
						return false;
					}
				}
			} else {
				$en = Mage::getStoreConfig('translator/activation/en');
				if ($isenabled && $en) {
					return true;
				}
			}
		}
	}

	public function getAllWebsites() {
		if (!Mage::getStoreConfig('translator/activation/installed')) {
			return array();
		}
		$data = Mage::getStoreConfig('translator/activation/data');
		$web = Mage::getStoreConfig('translator/activation/websites');
		$websites = explode(',', str_replace($data, '', Mage::helper('core')->decrypt($web)));
		$websites = array_diff($websites, array(""));
		return $websites;
	}

	public function isUrlKeyAttribute() {
		$flag = false;
		$translateAll = Mage::getStoreConfig('translator/translator_general/translate_all');
		$finalAttributeSet = array_values(explode(',', $translateAll));

		foreach ($finalAttributeSet as $attributeCode) {
			if ($attributeCode == 'url_key') {
				$flag = true;
				break;
			}
		}
		return $flag;
	}
}