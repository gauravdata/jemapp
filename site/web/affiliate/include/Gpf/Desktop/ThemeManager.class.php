<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WindowManager.class.php 19346 2008-07-23 14:30:58Z mbebjak $
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
class Gpf_Desktop_ThemeManager extends Gpf_Object {


    /**
     * @service theme read
     *
     * @return Gpf_Data_RecordSet
     */
    public function getThemes(Gpf_Rpc_Params $params) {
        if ($params->exists('panelName')) {
            return $this->getThemesNoRpc($params->get('panelName'));
        } else {
            return $this->getThemesNoRpc(Gpf_Session::getModule()->getPanelName(),
            $params->get('filterDisabled'));
        }
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    public function getThemesNoRpc($panelName, $filterDisabled = false) {
        $response = new Gpf_Data_RecordSet();
        $response->addColumn(Gpf_Desktop_Theme::ID);
        $response->addColumn(Gpf_Desktop_Theme::NAME);
        $response->addColumn(Gpf_Desktop_Theme::AUTHOR);
        $response->addColumn(Gpf_Desktop_Theme::URL);
        $response->addColumn(Gpf_Desktop_Theme::DESCRIPTION);
        $response->addColumn(Gpf_Desktop_Theme::THUMBNAIL);
        $response->addColumn(Gpf_Desktop_Theme::DESKTOP_MODE);
        $response->addColumn(Gpf_Desktop_Theme::ENABLED);
        $response->addColumn(Gpf_Desktop_Theme::BUILT_IN);

        $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
        foreach ($iterator as $fullName => $themeId) {
            if ($themeId == rtrim(Gpf_Paths::DEFAULT_THEME, '/')) {
                continue;
            }
            try {
                $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                if($filterDisabled && !$theme->isEnabled()){
                    continue;
                }
                $response->addRecord($theme->toRecord($response));
            } catch (Gpf_Exception $e) {
                 Gpf_Log::error($e->getMessage());
            }
        }

        return $response;
    }

    public function getFirstTheme($panelName) {
        $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
        $themeIds = array();

        foreach ($iterator as $fullName => $themeId) {
            try {
                $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                if (strlen($themeId) > 0 && $themeId[0] != '_' && $theme->isEnabled()) {
                    $themeIds[] = $themeId;
                }
            } catch (Gpf_Exception $e) {
                Gpf_Log::debug('Exception during reading theme directory: ' .$e->getMessage());
            }
        }

        if (count($themeIds) > 0) {
            sort($themeIds, SORT_STRING);
            return $themeIds[0];
        }

        throw new Gpf_Exception($this->_("No available theme") . ': ' . Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName);
    }

    /**
     * @service theme write
     *
     * @return Gpf_Rpc_Action
     */
    public function toggleThemeEnabled(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            $panel = $action->getParam('panelName');
            $themeId = $action->getParam('themeId');
            $theme = new Gpf_Desktop_Theme($themeId, $panel);
            if($theme->isEnabled()&& !$this->canDisableTheme($panel)){
                $action->setInfoMessage($this->_('One theme should be enabled'));
            }else{
                $theme->setEnabled(!$theme->isEnabled());
            }
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
            $action->setErrorMessage($e->getMessage());
        }
        return $action;
    }

    private function canDisableTheme($panelName){
        $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
        $enabledCount = 0;
        foreach ($iterator as $fullName => $themeId) {
            if ($themeId == rtrim(Gpf_Paths::DEFAULT_THEME, '/')) {
                continue;
            }
            try {
                $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                if($theme->isEnabled()){
                    $enabledCount++;
                    if($enabledCount == 2){
                        return true;
                    }
                }
            } catch (Gpf_Exception $e) {
                Gpf_Log::error($e->getMessage());
            }
        }
        return false;
    }

    /**
     * @service theme write
     *
     * @return Gpf_Rpc_Action
     */
    public function setTheme(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Error changing theme"));
        $action->setInfoMessage($this->_("Theme changed"));

        try {
            $themeId = $action->getParam('themeId');
            Gpf_Session::getAuthUser()->setTheme($themeId);
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
        }

        return $action;
    }
}
