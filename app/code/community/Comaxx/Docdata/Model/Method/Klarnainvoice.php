<?php

/**
 * Payment method class for Klarna Invoice
 */
class Comaxx_Docdata_Model_Method_Klarnainvoice extends Comaxx_Docdata_Model_Method_Fee {
	protected $_code = 'docdata_klarna_invoice';
	protected $_pm_name = 'Klarna Invoice'; // Used for translatable fields
}