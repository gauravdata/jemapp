<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak, Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
interface Gpf_Tasks_Task  {

    public function setProgressMessage($message);

    public function getProgressMessage();

    public function setName($name);

    public function getName();

    public function setProgress($progress);

    public function getProgress();

    public function getWorkingAreaFrom();

    public function getWorkingAreaTo();

    public function setWorkingAreaFrom($from);

    public function setWorkingAreaTo($to);

    public function setClassName($className);

    public function getClassName();

    public function setParams($params);

    public function getParams();

    public function isFinished();

    public function setPid($pid);

    public function insertTask();

    public function updateTask();

    public function loadTask($className, $params);

    public function finishTask();

    public function lockTask($isExecuting);

    public function setSleepTime($sleepSeconds);

}

?>
