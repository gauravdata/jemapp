<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Rpc_Annotation_CommentParser extends Gpf_Object {
    /**
     * @var Gpf_Io_File
     */
    private $file;
    
    public function __construct(Gpf_Io_File $file) {
        $this->file = $file;
    }
    
    public function getMethodComment($methodName) {
        try {
            $this->file->open();
            $source = $this->file->getContents();
        } catch (Exception $e) {
            return '';
        }

        $pattern = '|\s+function\s*' . $methodName . '\s*\(|ims';
        if(preg_match_all($pattern, $source, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) > 0) {
            foreach ($matches as $match) {
                $comment = $this->getComment(substr($source, 0, $match[0][1]));
                if($comment !== false) {
                    return $comment;
                }
            }
        }
        return '';
    }

    private function getComment($source) {
        $endCommentPosition = strrpos($source, '*/');
        if(false === $endCommentPosition) {
            return false;
        }
        if(strpos(substr($source, $endCommentPosition), '(') !== false) {
            return false;
        }
        
        $startCommentPosition = strrpos($source, '/**');
        if(false === $startCommentPosition) {
            return false;
        }
        return substr($source, $startCommentPosition, $endCommentPosition - $startCommentPosition + 2);
    }
}
?>
