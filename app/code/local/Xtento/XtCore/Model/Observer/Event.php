<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            Xqy7GuNQGLaP3Lxk3vRszsnC5xL25cGGoirg49gQ3uk=
 * Packaged:      2013-08-20T21:06:08+00:00
 * Last Modified: 2013-06-09T19:18:53+02:00
 * File:          app/code/local/Xtento/XtCore/Model/Observer/Event.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Model_Observer_Event
{
    protected static $hasRun;

    public function cronExecution()
    {
        if (!self::$hasRun) {
            self::$hasRun = 1;
        } else {
            return $this;
        }

        // Called by event, save last cron execution for XTENTO modules
        Mage::getConfig()->saveConfig('xtcore/crontest/last_execution', time(), 'default', 0)->reinit();

        // Save last cron execution for TBT_Testweet
        Mage::getConfig()->saveConfig('testsweet/crontest/timestamp', time(), 'default', 0)->reinit();

        return $this;
    }
}