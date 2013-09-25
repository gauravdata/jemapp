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
 * Handle writing to csv file
 * @package GwtPhpFramework
 */
class Gpf_Io_Csv_Writer extends Gpf_Io_File {
    
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
     * @var array
     */
    private $headers = array();
    
    /**
     * Csf files writer
     *
     * @param string $fileName Csv file name
     * @param array $headers
     * @param string $delimiter Set the field delimiter (one character only). Defaults as a comma
     * @param string $enclosure Set the field enclosure character (one character only). Defaults as a double quotation mark. 
     * @param string $escapeChar Set the escape character (one character only). Defaults as a backslash (\)
     */
    public function __construct($fileName, $headers, $delimiter = ';', $enclosure = '"') {
        parent::__construct($fileName);
        
        $this->setHeaders($headers);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->setFileMode('w+');
        $this->writeHeader();
    }
    
    /**
     * Set csv headers
     *
     * @param array $headers
     */
    private function setHeaders($headers) {
        $this->headers = array();
        foreach ($headers as $header) {
            $this->headers[$header] = $header;
        }
    }
    
    /**
     * If it is new file, write header to csv file
     */
    private function writeHeader() {
        $this->writeLine($this->headers);
        
    }
    
    /**
     * Write line to csv file
     *
     * @param array $columns
     */
    public function writeLine($columns) {
        $row = array();
        foreach ($this->headers as $header) {
            if (array_key_exists($header, $columns)) {
        	   $row[] = $columns[$header];
            } else {
                throw new Gpf_Exception($this->_("Column %s not defined in input data for csv file %s.", $header, $this->getFileName()));
            }
        }
        fputcsv($this->getFileHandler(), $row, $this->delimiter, $this->enclosure);
    }

    /**
     * Write line to csv file
     *
     * @param array $columns
     */
    public function writeRawLine($columns, $enclosedColumnsList) {                
        foreach ($this->headers as $header) {
            if (array_key_exists($header, $columns)) {
                if (in_array($header, $enclosedColumnsList)) {
                    $row[] = $this->addEnclosure($columns[$header], $this->enclosure);                    
                } else {
                    $row[] = $columns[$header];
                }
            } else {
                throw new Gpf_Exception($this->_("Column %s not defined in input data for csv file %s.", $header, $this->getFileName()));
            }
        }
        fputs($this->getFileHandler(), implode($this->delimiter, $row)."\n");
    }
    
    public function writeRecord(Gpf_Data_Record $record) {
        $row = array();
        foreach ($this->headers as $header) {
           $row[] = $record->get($header);
        }
        fputcsv($this->getFileHandler(), $row, $this->delimiter, $this->enclosure);
    }

    private function addEnclosure($message, $enclosure) {
        $message = str_replace('"', '""', $message);
        return $enclosure . $message . $enclosure;
    }
}
?>
