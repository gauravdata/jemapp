<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Gpf_Common_ClassMerger {
    private $outputName;
    private $classes = array();
    protected $output;

    public final function addClass($className) {
        $this->classes[] = $className;
    }

    public final function setOutputName($outputClass) {
        $this->outputName = $outputClass;
    }

    public final function build() {
        if(count($this->classes) < 1) {
            throw new Gpf_Exception("You have to specify at least one class");
        }

        $this->startClass();

        $this->addAdditionalClasses();
        foreach($this->classes as $class) {
            $this->importClass($class);
        }
        $this->addExtraClasses();

        $this->addVersionCheckSum();
        $this->finishClass();
    }

    protected function importClass($classname){
        $fileName = Gpf::existsClass($classname);
        if(!$fileName) {
            echo "Class '$classname' was not found<br>";
        }

        $this->processClass($fileName, $classname);
    }

    protected function getHeader() {
        return "/**".
               " *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o.".
               " *   @author Quality Unit".
               " *   @package Core classes".
               " *   @since Version 1.0.0".
               " *   ".
               " *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,".
               " *   Version 1.0 (the \"License\"); you may not use this file except in compliance".
               " *   with the License. You may obtain a copy of the License at".
               " *   http://www.qualityunit.com/licenses/gpf".
               " *   ".
               " */\n";
    }

    protected function addAdditionalClasses() {
    }

    protected function addExtraClasses() {
    }

    private function startClass() {
        $this->output = fopen($this->outputName, "w");
        fwrite($this->output, "<?php\n");
        fwrite($this->output, $this->getHeader());
    }

    private function finishClass() {
        fwrite($this->output, "\n?>");
        fclose($this->output);
    }

    private function addVersionCheckSum() {
        fwrite($this->output, "/*\nVERSION\n" . md5_file($this->outputName) . "\n*/");
    }

    private function processClass($classFile, $class) {
        $lines = file($classFile);

        $success = false;
        if(!is_array($lines) || count($lines) < 1) {
            echo "Error - Class '$class' has no lines?<br>";
        }

        $success = $this->copyLinesFromTag('class', $lines, $class);
        if(!$success) {
            // maybe it is not a class, but interface
            $success = $this->copyLinesFromTag('interface', $lines, $class);
        }
        if(!$success) {
            // maybe it is not a class, but interface
            $success = $this->copyLinesFromTag('abstract class', $lines, $class);
        }

        if(!$success) {
            // maybe it is final class
            $success = $this->copyLinesFromTag('final class', $lines, $class);
        }

        if(!$success) {
            die("Failed to add '$class'");
        }
    }

    private function copyLinesFromTag($tag, $lines, $className) {
        $start = false;
        foreach ($lines as $line_num => $line) {
            // start only at the beginning of the class
            if(strpos($line, $tag) === 0 && !$start) {
                $start = true;
                if ($tag == 'interface') {
                    fwrite($this->output, "\nif (!interface_exists('$className', false)) {\n");
                } else {
                    fwrite($this->output, "\nif (!class_exists('$className', false)) {\n");
                }
                //fwrite($this->output, "\n");
            }
            // skip PHP closing tag
            if(trim($line) == '?>') {
                continue;
            }
            if($start) {
                fwrite($this->output, '  '.$line);
            }
        }
        if ($start) {
            fwrite($this->output, "\n} //end $className\n");
        }
        return $start;
    }
}
?>
