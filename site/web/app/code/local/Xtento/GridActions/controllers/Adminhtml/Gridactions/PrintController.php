<?php

/**
 * Product:       Xtento_GridActions (1.6.3)
 * ID:            Xqy7GuNQGLaP3Lxk3vRszsnC5xL25cGGoirg49gQ3uk=
 * Packaged:      2013-08-20T21:06:08+00:00
 * Last Modified: 2013-05-31T15:29:32+02:00
 * File:          app/code/local/Xtento/GridActions/controllers/Adminhtml/Gridactions/PrintController.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_GridActions_Adminhtml_GridActions_PrintController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction()
    {
        $orderIds = explode(",", $this->getRequest()->getParam('order_ids'));
        $flag = false;
        if (!empty($orderIds)) {
            //foreach ($orderIds as $orderId) {
            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->setOrderFilter($orderIds) // Be careful: Could be because of PdfCustomizer extension. Should be $orderId - why does the PDF get returned instantly?
                ->load();
            if ($invoices->getSize() > 0) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                } else {
                    $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }
            //}
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'invoices_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('adminhtml/sales_order');
            }
        }
        $this->_redirect('adminhtml/sales_order');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction()
    {
        $orderIds = explode(",", $this->getRequest()->getParam('order_ids'));
        $flag = false;
        if (!empty($orderIds)) {
            //foreach ($orderIds as $orderId) {
            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->setOrderFilter($orderIds) // Be careful: Could be because of PdfCustomizer extension. Should be $orderId - why does the PDF get returned instantly?
                ->load();
            if ($shipments->getSize() > 0) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                } else {
                    $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }
            //}
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslips_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('adminhtml/sales_order');
            }
        }
        $this->_redirect('adminhtml/sales_order');
    }
}