<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
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
abstract class Gpf_IntegrityCheck extends Gpf_Object {
    const PROGRESS_PARAM = 'p';

    private $corruptedFiles = array();
    private $filesCheckedSoFar = 0;
    private $progress = 0;
    private $startTime;
    private $checkOnly;

    public function __construct($checkOnly = false) {
        $this->checkOnly = $checkOnly;
    }

    protected function checkFile($fileName, $checkSum) {
        if (time() - $this->startTime > 24) {
            $_SESSION['corruptedFiles'] = $this->corruptedFiles;
            $this->echoMessage($this->getCheckingMessage(),
            $this->getRedirectUrl($this->filesCheckedSoFar));
            exit();
        }
        $this->filesCheckedSoFar++;
        if ($this->filesCheckedSoFar <= $this->progress) {
            return;
        }
        $file = new Gpf_Io_File('../' . $fileName);
        try {
            if ($file->getCheckSum() != $checkSum) {
                $this->corruptedFiles[ltrim($file->getFileName(), '\.\.\/')] = $this->_('CORRUPTED');
            }
        } catch (Gpf_Exception $e) {
            $this->corruptedFiles[ltrim($file->getFileName(), '\.\.\/')] = $this->_('MISSING');
        }
    }

    protected abstract function checkFiles();

    public final function check() {
        if (!array_key_exists(self::PROGRESS_PARAM, $_GET)) {
            session_name('pinst');
            session_start();
            $_SESSION['corruptedFiles'] = array();
            $_SESSION[self::PROGRESS_PARAM] = array();
            $this->echoMessage($this->getCheckingMessage(),
            $this->getRedirectUrl('0'));
            exit();
        }
        if ($_GET[self::PROGRESS_PARAM] == 'F') {
            return;
        }

        session_name('pinst');
        session_start();
        $this->startTime = time();
        $this->progress = $_GET[self::PROGRESS_PARAM];
        if (isset($_SESSION['corruptedFiles'])) {
            $this->corruptedFiles = $_SESSION['corruptedFiles'];
        } else {
            $this->corruptedFiles = array();
        }

        $this->checkFiles();
        if (count($this->corruptedFiles) > 0) {
            $this->echoMessage($this->getCorruptedFilesMessage());
            exit();
        }
        if ($this->checkOnly) {
            $this->echoMessage('<h3>' . $this->_('Application is ok.') . '</h3>');
        } else {
            $this->echoMessage('<h3>' . $this->_('Application is ok. Starting installation.') . '</h3>',
            $this->getRedirectUrl('F'));
        }
        exit();
    }

    public function getCorruptedFilesMessage() {
        $msg = $this->_('%sFolowing files have not been uploaded correctly%sPlease upload them again and %srefresh%s Read more about this error in our %sknowledgebase%s Corrupted files in affiliate system directory: %s File%sStatus%s',
    			'<h3 style="color: red;">',
    			'</h3><h3 style="color: red;">', 
    			'<a href="index.php">',
    			'</a></h3>',
    			'<a href="http://support.qualityunit.com/knowledgebase/post-affiliate-pro/troubleshooting/folowing-files-have-not-been-uploaded-correctly.html" target="_blank">',
    			'</a><br><br><hr>',
    			'<b>' . realpath('../') . '</b><br><br><table><tr><td><b>',
    			'</b></td><td><b>',
    			'</b></td></tr>');
        foreach ($this->corruptedFiles as $fileName => $error) {
            $msg .= '<tr><td>'.$fileName.'</td><td>'.$error.'</tr>';
        }
        $msg .= '</table>';
        return $msg;
    }
    
    private function getCheckingMessage() {
    	return '<h3>' . $this->_('Checking integrity of application. This may take few minutes ...') . '</h3>';
    }
    
    private function translateOkMessage($message) {
    	return '<h3>' . $this->_($message) . '</h3>';
    }

    private function echoMessage($message, $redirectUrl = null) {
        $redirectTag = '';
        if ($redirectUrl != null) {
            $redirectTag = "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1;URL=$redirectUrl\">";
        }
        echo "<html><head>$redirectTag</head><body>$message<body></html>";
    }

    private function getRedirectUrl($progress) {
        return ($this->checkOnly ? 'check.php' : 'index.php').'?' . self::PROGRESS_PARAM . '=' . $progress;
    }

}

?>
