<?php
class Dealer4dealer_Exactonline_Adminhtml_AuthController extends Mage_Adminhtml_Controller_Action
{
    public function grandAction()
    {
        $hasApiKey      = $this->hasApiKey();
        $hasApiUserId   = $this->hasApiUserId();
        $hasApiCountry  = $this->hasApiCountry();
        $hasApiUrl      = $this->hasApiUrl();
        $hasApiClientId = $this->hasApiClientId();
        $hasTokenLifeTime = $this->hasTokenLifeTime();

        if ($hasApiKey && $hasApiUserId && $hasApiCountry && $hasApiUrl && $hasApiClientId && $hasTokenLifeTime) {

            // Get the next URL with an CSRF-token as parameter.
            $returnUrl      = $this->getUrl('*/*/token');
            $apiKey         = $this->getApiKey();
            $userId         = $this->getApiUserId();
            $country        = $this->getApiCountry();
            $apiUrl         = $this->getApiUrl();

            $this->_redirectUrl($apiUrl .'/grand.php?return_url=' . urlencode($returnUrl) . '&api_key=' . urlencode($apiKey) . '&connector_id=' . urlencode($userId) . '&country=' . urlencode($country));

        } else {

            if (!$hasApiUserId) {
                $this->initApiUserId();
            }

            if (!$hasApiKey) {
                $this->initApiKey();
            }

            if (!$hasApiCountry) {
                $this->initApiCountry();
            }

            if (!$hasApiUrl) {
                $this->initApiUrl();
            }

            if (!$hasApiClientId) {
                $this->initApiClientId();
            }

            if (!$hasTokenLifeTime) {
                $this->initTokenLifeTime();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('exactonline')->__('Created the Api Key/Api Key settings. Please fill in the values.'));
            $this->_redirect('*/adminhtml_exactonlinesetting/index');
        }
    }

    public function getApiKey()
    {
        $apiKey = Mage::getModel('exactonline/setting')->load('api_key', 'name');
        $encrypted = $apiKey->getValue();

        return base64_decode(Mage::helper('core')->decrypt($encrypted));
    }

    public function getApiUserId()
    {
        $apiUserId = Mage::getModel('exactonline/setting')->load('api_user_id', 'name');

        return $apiUserId->getValue();
    }

    public function getApiCountry()
    {
        $apiCountry = Mage::getModel('exactonline/setting')->load('api_country', 'name');

        return $apiCountry->getValue();
    }

    public function getApiUrl()
    {
        $apiUrl = Mage::getModel('exactonline/setting')->load('api_url', 'name');

        return $apiUrl->getValue();
    }

    public function tokenAction()
    {
        $accessToken = Mage::helper('core')->encrypt(base64_encode($this->getRequest()->getParam('access_token')));
        $refreshToken = Mage::helper('core')->encrypt(base64_encode($this->getRequest()->getParam('refresh_token')));


        $accessSettingToken = Mage::getModel('exactonline/setting')->load('access_token', 'name');

        $data = array(
            'name'              => 'access_token',
            'visible'           => 1,
            'value'             => $accessToken,
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'Access Token',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        if ($accessSettingToken->getId()) {
            $data['setting_id'] = $accessSettingToken->getId();
        }

        $accessSettingToken
            ->setData($data)
            ->save();

        $data = array(
            'name'              => 'refresh_token',
            'visible'           => 1,
            'value'             => $refreshToken,
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'Refresh Token',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,

        );

        $refreshSettingToken = Mage::getModel('exactonline/setting')->load('refresh_token', 'name');

        if ($refreshSettingToken->getId()) {
            $data['setting_id'] = $refreshSettingToken->getId();
        }

        $refreshSettingToken
            ->setData($data)
            ->save();

        $this->_redirect('*/adminhtml_exactonlinesetting/index');
    }

    public function hasApiKey()
    {
        $apiKey = Mage::getModel('exactonline/setting')->load('api_key', 'name');

        if (!$apiKey->getId()) {
            return false;
        }

        return true;
    }

    public function hasApiUserId()
    {
        $userId = Mage::getModel('exactonline/setting')->load('api_user_id', 'name');

        if (!$userId->getId()) {
            return false;
        }

        return true;
    }

    public function hasApiCountry()
    {
        $apiCountry = Mage::getModel('exactonline/setting')->load('api_country', 'name');

        if (!$apiCountry->getId()) {
            return false;
        }

        return true;
    }

    public function hasApiUrl()
    {
        $apiUrl = Mage::getModel('exactonline/setting')->load('api_url', 'name');

        if (!$apiUrl->getId()) {
            return false;
        }

        return true;
    }

    public function hasApiClientId()
    {
        $apiClientId = Mage::getModel('exactonline/setting')->load('api_client_id', 'name');

        if (!$apiClientId->getId()) {
            return false;
        }

        return true;
    }

    public function hasTokenLifeTime()
    {
        $tokenLifeTime = Mage::getModel('exactonline/setting')->load('token_life_time', 'name');

        if (!$tokenLifeTime->getId()) {
            return false;
        }

        return true;

    }

    public function initApiKey()
    {
        $apiKey = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'api_key',
            'visible'           => 1,
            'value'             => 'APIKey',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'API Key',
            'category_id'       => 1,
            'field_type'        => 3,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiKey->setData($data);
        $apiKey->save();

        return $apiKey;
    }

    public function initApiUserId()
    {
        $apiUserId = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'api_user_id',
            'visible'           => 1,
            'value'             => 'API User ID',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'API User ID',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiUserId->setData($data);
        $apiUserId->save();

        return $apiUserId;
    }

    public function initApiCountry()
    {
        $apiCountry = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'api_country',
            'visible'           => 1,
            'value'             => 'nl',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'API Country',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiCountry->setData($data);
        $apiCountry->save();

        return $apiCountry;
    }

    public function initApiUrl()
    {
        $apiUrl = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'api_url',
            'visible'           => 1,
            'value'             => 'https://connect.dealer4dealer.nl/api',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'API Url',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiUrl->setData($data);
        $apiUrl->save();

        return $apiUrl;
    }

    public function initApiClientId()
    {
        $apiClientId = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'api_client_id',
            'visible'           => 1,
            'value'             => '{8e5cf2b4-dfeb-4ea2-9ba2-8bb1c56226de}',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'API Client ID',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiClientId->setData($data);
        $apiClientId->save();

        return $apiClientId;
    }

    public function initTokenLifeTime()
    {
        $apiClientId = Mage::getModel('exactonline/setting');

        $data = array(
            'name'              => 'token_life_time',
            'visible'           => 1,
            'value'             => '540',
            'timestamp'         => date('Y-m-d H:i:s'),
            'label'             => 'Token Life Time',
            'category_id'       => 1,
            'field_type'        => 1,
            'is_editable_key'   => 1,
            'is_deletable'      => 1,
        );

        $apiClientId->setData($data);
        $apiClientId->save();

        return $apiClientId;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dealer4dealer_menu');
    }
}