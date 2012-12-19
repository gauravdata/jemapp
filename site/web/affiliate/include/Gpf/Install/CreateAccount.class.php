<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_CreateAccount extends Gpf_Install_Step {
    const USERNAME = 'Username';
    const PASSWORD = 'Password';
    const FIRSTNAME = 'Firstname';
    const LASTNAME = 'Lastname';

    public function __construct() {
        parent::__construct();
        $this->code = 'Create-Account';
        $this->name = $this->_('Create Account');
    }

    public function load(Gpf_Rpc_Params $params) {
    }

    protected function execute(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $task = $this->getTask($params);
        try {
            $task->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $this->setResponseType($form, self::PART_DONE_TYPE);
            $form->setInfoMessage($e->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }
        $this->setNextStep($form);
        return $form;
    }

    public function createTestAccount($email = 'user@example.com', $password = 'password', $firstName = 'Max', $lastName = 'Musterman') {
        $account = Gpf_Application::getInstance()->createAccount();
        $account->createTestAccount($email, $password, $firstName, $lastName);
    }

    /**
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Install_CreateAccountTask
     */
    private function getTask(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $username = $form->getFieldValue(self::USERNAME);
        $password = $form->getFieldValue(self::PASSWORD);
        $firstname = $form->getFieldValue(self::FIRSTNAME);
        $lastname = $form->getFieldValue(self::LASTNAME);

        $account = Gpf_Application::getInstance()->createAccount();
        $account->setDefaultId();
        $account->setEmail($username);
        $account->setPassword($password);
        $account->setFirstname($firstname);
        $account->setLastname($lastname);

        return $account->getCreateTask();
    }

}
?>
