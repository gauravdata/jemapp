<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 * 	 @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: File.class.php 19994 2008-08-19 20:25:46Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Io_Csv_Reader extends Gpf_Io_File implements Iterator {

    /**
     * Column delimiter
     *
     * @var string
     */
    private $delimiter;

    /**
     * Set the field enclosure character (one character only). Defaults as a double quotation mark.
     *
     * @var string
     */
    private $enclosure;

    /**
     * array of column names
     *
     * @var Gpf_Data_RecordHeader
     */
    private $headers;

    /**
     * Current row data
     *
     * @var array
     */
    private $currentRow;

    /**
     * Current row number
     *
     * @var int
     */
    private $rowId = 0;

    /**
     * Default headers of csv file
     *
     * @var array if false, default headers are not defined
     */
    private $defaultHeaders = false;

    /**
     * Read max defined number of rows
     *
     * @var integer
     */
    private $maxLinesToRead = 0;
    
    /**
     * Csf files handler
     *
     * @param string $fileName Csv file name
     * @param string $delimiter Set the field delimiter (one character only). Defaults as a comma
     * @param string $enclosure Set the field enclosure character (one character only). Defaults as a double quotation mark.
     * @param string $escapeChar Set the escape character (one character only). Defaults as a backslash (\)
     * @param array $defaultHeaders Array of defaylt headers. If false, read headers from first line of csv file.
     */
    public function __construct($fileName, $delimiter = ';', $enclosure = '"', $defaultHeaders = false) {
        parent::__construct($fileName);
        if(!$this->isExists()) {
            throw new Gpf_Exception($this->_('Could not open csv file.'));
        }
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        if ($defaultHeaders) {
            $this->headers = new Gpf_Data_RecordHeader($defaultHeaders);
            $this->defaultHeaders = $defaultHeaders;
        } else {
            $this->headers = new Gpf_Data_RecordHeader();
        }
        $this->rewind();
    }

    public function rewind($readHeader = true) {
        $this->close();
        $this->rowId = 0;
        if ($readHeader) {
            $this->readHeader();
        }
    }

    /**
     * Read header of csv file.
     * File pointer has to be rewinded before calling this method !
     */
    private function readHeader() {
        if ($this->defaultHeaders === false) {
            // No default header defined, use first row from csv as header
            $this->headers = new Gpf_Data_RecordHeader($this->getNextRow());

            if ($this->headers->getSize() == 0) {
                throw new Gpf_Exception($this->_("Failed to read headers of CSF file %s", $this->getFileName()));
            }
        } else {
            //compare default header with first row from csv file
            $row = $this->getNextRow();
            foreach ($this->defaultHeaders as $id => $headerName) {
                if ($headerName != $row[$id]) {
                    //This is value, headers are missing in this csv file, as headers will be used default headers
                    $this->rewind(false);
                    return;
                }
            }
            //first row was header (same as default header), skip it
        }
        $this->getNextRow();
    }

    private function getNextRow() {
        $this->rowId++;
        $this->currentRow = fgetcsv($this->getFileHandler(), 0, $this->delimiter, $this->enclosure);
        return $this->currentRow;
    }

    /**
     * Create record object from data
     *
     * @param array $data data loaded from row
     * @return Gpf_Data_Record
     */
    private function getRecord($data) {
        return new Gpf_Data_Record($this->headers, $data);
    }

    /**
     * Get current record from csv
     *
     * @return Gpf_Data_Record
     */
    public function current() {
        return $this->getRecord($this->currentRow);
    }

    /**
     * Get current row number
     *
     * @return int
     */
    public function key() {
        return $this->rowId;
    }

    /**
     * Get next record from csv file
     *
     * @return Gpf_Data_Record
     */
    public function next() {
        if (($data = $this->getNextRow()) === false || 
        ($this->maxLinesToRead > 0 && $this->maxLinesToRead < $this->rowId)) {
            return false;
        }

        return $this->getRecord($data);
    }

    /**
     * Is file valid ?
     *
     * @return boolean
     */
    public function valid() {
        return !$this->isEof();
    }
    
    /**
     * Set maximum number of lines, which will be read from file - 0 as default is unlimited
     *
     * @param int $maxLines
     */
    public function setMaxLinesToRead($maxLines = 0) {
        $this->maxLinesToRead = $maxLines;
    }
}
?>
