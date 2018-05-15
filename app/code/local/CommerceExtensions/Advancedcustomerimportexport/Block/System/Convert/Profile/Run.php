<?php
/**
 * Run.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Customers
 * @package    Run
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
class CommerceExtensions_Advancedcustomerimportexport_Block_System_Convert_Profile_Run extends Mage_Adminhtml_Block_System_Convert_Profile_Run
{
    /**
     * Flag for batch model
     * @var boolean
     */
    protected $_batchModelPrepared = false;
    /**
     * Batch model instance
     * @var Mage_Dataflow_Model_Batch
     */
    protected $_batchModel = null;
    /**
     * Preparing batch model (initialization)
     * @return Mage_Adminhtml_Block_System_Convert_Profile_Run
     */
    protected function _prepareBatchModel()
    {
        if ($this->_batchModelPrepared) {
            return $this;
        }
        $this->setShowFinished(true);
        $batchModel = Mage::getSingleton('dataflow/batch');
        $this->_batchModel = $batchModel;
        if ($batchModel->getId()) {
            if ($batchModel->getAdapter()) {
                $this->setBatchModelHasAdapter(true);
                $numberOfRecords = $this->getProfile()->getData('gui_data/import/number_of_records');
                if (!$numberOfRecords) {
                    $batchParams = $batchModel->getParams();
                    $numberOfRecords = isset($batchParams['number_of_records']) ? $batchParams['number_of_records'] : 1;
                }
                $this->setNumberOfRecords($numberOfRecords);
                $this->setShowFinished(false);
                $batchImportModel = $batchModel->getBatchImportModel();
                $importIds = $batchImportModel->getIdCollection();
                $this->setBatchItemsCount(count($importIds));
                $this->setBatchConfig(
                    array(
                        'styles' => array(
                            'error' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif'),
                                'bg'   => '#FDD'
                            ),
                            'message' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif'),
                                'bg'   => '#DDF'
                            ),
                            'loader'  => Mage::getDesign()->getSkinUrl('images/ajax-loader.gif')
                        ),
                        'template' => '<li style="#{style}" id="#{id}">'
                                    . '<img id="#{id}_img" src="#{image}" class="v-middle" style="margin-right:5px"/>'
                                    . '<span id="#{id}_status" class="text">Row #{id} - #{text}</span>'
                                    . '</li>',
                        'text'     => $this->__('Processed <strong>%s%% %s/%d</strong> records', '#{percent}', '#{updated}', $this->getBatchItemsCount()),
                        'successText'  => $this->__('Imported <strong>%s</strong> records', '#{updated}')
                    )
                );
                $jsonIds = array_chunk($importIds, $numberOfRecords);
                $importData = array();
                foreach ($jsonIds as $part => $ids) {
                    $importData[] = array(
                        'batch_id'   => $batchModel->getId(),
                        'rows[]'     => $ids
                    );
                }
                $this->setImportData($importData);
            } else {
                $this->setBatchModelHasAdapter(false);
                $batchModel->delete();
            }
        }
        $this->_batchModelPrepared = true;
        return $this;
    }
}
