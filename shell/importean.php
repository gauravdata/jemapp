<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Importean extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
/*
        $data = [];
        $csv = file('ean.csv');
        foreach ($csv as $row)
        {
            $d = explode(',', $row);
            $data[trim($d[0])] = trim($d[1]);
        }

        $processedSKUS = [];
        foreach ($data as $sku => $ean)
        {
            $product = Mage::getSingleton('catalog/product');
            $product = $product->load($product->getIdBySku($sku));
            if ($product->getId() !== null)
            {
                echo 'Assigning EAN (' . $ean . ') to ' . $product->getName() . ': ';
                $product->setData('ean_code', $ean);
                $product->save();
                echo 'OK' . PHP_EOL;

                $processedSKUS[] = $ean;
            }
        }
*/
        $collection = Mage::getModel('catalog/product');
        foreach ($collection->getCollection() as $col)
        {
            if (empty($col->getData('ean_code')) && $col->getTypeId() == 'simple')
                echo 'SKU '.$col->getSku().' has no EAN assigned!'.PHP_EOL;
        }


    }

}

$shell = new Mage_Shell_Importean();
$shell->run();
