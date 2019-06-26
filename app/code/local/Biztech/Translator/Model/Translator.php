<?php

class Biztech_Translator_Model_Translator extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('translator/translator');
	}

	public function getTranslate($text, $langto, $fromLanguage = '') {
		$yourApiKey = Mage::getStoreConfig('translator/translator_general/google_api');
		$sourceData = $text;
		$source = $langto;
		$target = $fromLanguage;
		$translator = new Biztech_Translator_Model_Languagetranslator($yourApiKey);
		try {
			$targetData = $translator->translate(nl2br($sourceData), $source, $target);
		} catch (Exception $e) {
			Mage::log($e->getMessage(), null, 'translator.log');
		}

		if ($targetData != '') {
			if (!is_array($targetData) || !array_key_exists('data', $targetData)) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			if (!array_key_exists('translations', $targetData['data'])) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			if (!is_array($targetData['data']['translations'])) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			foreach ($targetData['data']['translations'] as $translation) {
				$result['text'] = preg_replace('#<br\s*?/?>#i', "\n", $translation['translatedText']);
				$result['status'] = 'success';
			}

			return $result;
		}
	}

	public function getTranslateCmsPage($text, $langto, $fromLanguage = '') {
		$yourApiKey = Mage::getStoreConfig('translator/translator_general/google_api');
		$sourceData = $text;
		$source = $langto;
		$target = $fromLanguage;

		$translator = new Biztech_Translator_Model_Languagetranslator($yourApiKey);
		try {
			$targetData = $translator->translate(nl2br($sourceData), $source, $target);
		} catch (Exception $e) {
			Mage::log($e->getMessage(), null, 'translator.log');
		}

		if ($targetData != '') {
			if (!is_array($targetData) || !array_key_exists('data', $targetData)) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			if (!array_key_exists('translations', $targetData['data'])) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			if (!is_array($targetData['data']['translations'])) {
				$result['text'] = $targetData['error']['message'];
				$result['status'] = 'fail';
				return $result;
			}

			foreach ($targetData['data']['translations'] as $translation) {
				$result['text'] = preg_replace('#<br\s*?/?>#i', "\n", $translation['translatedText']);
				$result['status'] = 'success';
			}

			return $result;
		}
	}

	public function setTranslateCron($storeId, $jobCode = 'bizcrontranslation', $timescheduled = null) {
		// $jobCode = 'bizcrontranslation';
		$schedule = Mage::getModel('cron/schedule')->getCollection()->addFieldToFilter('job_code', $jobCode)->load();
		$result = false;
		if ($schedule) {
			$result = $this->createCronJob($jobCode, $timescheduled);
			// Mage::getModel('translator/observer')->translateCronMassProducts();
		} else {
			try {
				$result = $this->createCronJob($jobCode, $timescheduled);
				// Mage::getModel('translator/observer')->translateCronMassProducts();
			} catch (Exception $e) {
				throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
			}
		}
		return $result;
	}

	protected function createCronJob($jobCode, $timescheduled = null) {
		$_charCutLimit = Mage::getStoreConfig('translator/translator_general/google_daily_cut_before_limit');
		$_logCron = Mage::getModel('translator/logcron')->getCollection()->getLastItem();

		if (Mage::getSingleton('core/date')->gmtDate('d-m-Y') == date('d-m-Y', strtotime($_logCron->getCronDate())) && $_logCron->getRemainLimit() <= 0) {
			$_cronModel = Mage::getModel('cron/schedule')->getCollection()
				->addFieldToFilter('job_code', $jobCode);

			if (!is_null($timescheduled) || $timescheduled != '') {
				$_cronModel->addFieldToFilter('created_at', array('like' => '%' . date('Y-m-d', strtotime($timescheduled)) . '%'));
			}

			if ($_cronModel->count() != 0) {
				$timecreated = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
				if (is_null($timescheduled) || $timescheduled == '') {
					$timescheduled = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
				}

				try {
					$schedule = Mage::getModel('cron/schedule');
					$schedule->setJobCode($jobCode)
						->setCreatedAt($timecreated)
						->setScheduledAt($timescheduled)
						->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
						->save();
				} catch (Exception $e) {
					throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
				}
				return false;
			}
		}

		$_cronModel = Mage::getModel('cron/schedule')->getCollection()
			->addFieldToFilter('job_code', $jobCode)
			->addFieldToFilter('status', 'pending');

		if (!is_null($timescheduled) || $timescheduled != '') {
			$_cronModel->addFieldToFilter('created_at', array('like' => '%' . date('Y-m-d', strtotime($timescheduled)) . '%'));
		}

		if ($_cronModel->count() != 0) {
			return false;
		} else {
			$timecreated = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
			if (is_null($timescheduled) || $timescheduled == '') {
				$timescheduled = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
			}

			try {
				$schedule = Mage::getModel('cron/schedule');
				$schedule->setJobCode($jobCode)
					->setCreatedAt($timecreated)
					->setScheduledAt($timescheduled)
					->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
					->save();
			} catch (Exception $e) {
				throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
			}

			return true;
		}

	}
}
