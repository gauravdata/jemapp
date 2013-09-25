<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */

class Pap_Tests_Plugins_TopLevelAffiliateCommision_MainTest extends Gpf_Tests_TestCaseBase {

    public function setUp() {
        Gpf_Tests_Mock_PluginsEngine::init();
        Gpf_Plugins_Engine::getInstance()->activate('TopLevelAffiliateCommision', true);

        Gpf_Session::set(Gpf_Tests_Mock_Session::create());
        Gpf_Tests_Mock_Settings::create(new Pap_Settings());
    }

    public function testNoTopLevel(){
        $trackingContext =  $this->mockAll('Pap_Contexts_Action');
        $user =  $this->mockAll('Pap_Common_User', 'getParentUserId', 'someId');
        $context =  $this->mockAll('Pap_Common_SaveCommissionCompoundContext');

        $this->expectOnce($context, 'getContext', $trackingContext);
        $this->expectOnce($context, 'getUser', $user);
        $trackingContext->expects($this->exactly(2))->method('debug');

        $plugin = new TopLevelAffiliateCommision_Main();
        $plugin->modifyCommission($context);
    }

    public function test70PercentCommision(){
        Gpf_Settings::set(TopLevelAffiliateCommision_Config::COMMISSION_KEY, '70');

        $context =  $this->mockAll('Pap_Common_SaveCommissionCompoundContext');
        $trackingContext =  $this->mockAll('Pap_Contexts_Action');
        $user =  $this->mockAll('Pap_Common_User', 'getParentUserId', '');
        $transaction =  $this->mockAll('Pap_Common_Transaction');

        $this->expectOnce($context, 'getContext', $trackingContext);
        $this->expectOnce($context, 'getUser', $user);
        $this->expectOnce($context, 'getTier', 1);

        $this->expectOnce($trackingContext, 'getTransaction', $transaction);
        $this->expectOnce($transaction, 'setCommission', null, array($this, 'checkTransactionExpect70Commission'));
        $this->expectOnce($transaction, 'getCommission', 100);

        $plugin = new TopLevelAffiliateCommision_Main();
        $plugin->modifyCommission($context);
    }
     
    function checkTransactionExpect70Commission(){
        $commission =   func_get_arg(0);
        $this->assertEquals(70, $commission);
    }
     
    public function testNoSaveContext(){
        $trackingContext =  $this->mockAll('Pap_Contexts_Tracking');
        $context =  $this->mockAll('Pap_Common_SaveCommissionCompoundContext');
        $context->expects($this->once())->method('getContext')->will($this->returnValue($trackingContext));
        $trackingContext->expects($this->once())->method('debug');
        $pluginMain = new TopLevelAffiliateCommision_Main();
        $pluginMain->modifyCommission($context);
    }
}

?>
