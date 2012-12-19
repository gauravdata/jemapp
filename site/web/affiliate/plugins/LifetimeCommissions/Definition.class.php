<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class LifetimeCommissions_Definition extends Gpf_Plugins_Definition  {
	public function __construct() {
		$this->codeName = 'LifetimeCommissions';
		$this->name = $this->_('Lifetime commissions');
		$this->description = $this->_('Enables lifetime referrals');
		$this->version = '1.0.0';
		$this->configurationClassName = 'LifetimeCommissions_Config';
		$this->help = $this->_('%sThis plugin enables lifetime referrals.%s
Requirements:%s
in order to make it work, you have to pass some identifier that uniquely identifies the customer (for example his email)
in the data1 parameter in the sale/lead tracking code.
%s
For example:%s
How it works:
%s
%sIf customer is referred by the affiliate for the first time, the plugin will store the relation between customer (his identifier) and affiliate into the database%s
%sNext time, when the same customer is referred (with the same identifier), it will find the affiliate who originally referred him, and give him commission%s
%sIf no affiliate is found, the process goes normally, so it tries to recognize affiliate from cookie, etc.%s',
        '<strong>',
        '</strong><br/>',
        '<br/>',
        '<br/><br/>',
        '<br/>
<pre>
&lt;script id="pap_x2s6df8d" src="http://www.yoursite.com/affiliate/scripts/salejs.php" type="text/javascript"&gt;<br/>
&lt;/script&gt;<br/>
&lt;script type="text/javascript"&gt;<br/>
var sale = PostAffTracker.createSale();<br/>
sale.setTotalCost(\'120.50\');<br/>
sale.setOrderID(\'ORD_12345XYZ\');<br/>
sale.setProductID(\'test product\');<br/>
<b>sale.setData1(\'some@customeremail.com\');</b><br/>
PostAffTracker.register();<br/>
&lt;/script&gt;<br/>
</pre>
<br/>',
        '<ol>',
        '<li>',
        '</li>',
        '<li>',
        '</li>',
        '<li>',
        '</li>');

		$this->addRequirement('PapCore', '4.0.4.6');
		$this->addRefuse('SplitCommissions');

		$this->addImplementation('Tracker.action.recognizeParametersStarted', 'LifetimeCommissions_Main', 'checkLifetimeReferral', 5);
		$this->addImplementation('Tracker.action.afterSaveCommissions', 'LifetimeCommissions_Main', 'saveLifetimeReferral', 5);
		$this->addImplementation('PostAffiliate.merchant.menu', 'LifetimeCommissions_Main', 'addToMenu', 5);
		$this->addImplementation('Tracker.action.beforeSaveCommissions', 'LifetimeCommissions_Main', 'checkLifitimeLimit');
		$this->addImplementation('Core.defineSettings', 'LifetimeCommissions_Main', 'initSettings');
	}
}
?>
