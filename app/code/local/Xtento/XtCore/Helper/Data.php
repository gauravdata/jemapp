<?php

/**
 * Product:       Xtento_XtCore (1.0.0)
 * ID:            Xqy7GuNQGLaP3Lxk3vRszsnC5xL25cGGoirg49gQ3uk=
 * Packaged:      2013-08-20T21:06:08+00:00
 * Last Modified: 2012-12-02T16:34:18+01:00
 * File:          app/code/local/Xtento/XtCore/Helper/Data.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getInstallationDate()
    {
        return Mage::getStoreConfig('xtcore/adminnotification/installation_date');
    }
}