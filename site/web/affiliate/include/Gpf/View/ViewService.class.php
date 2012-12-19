<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ViewService.class.php 30061 2010-11-22 15:09:13Z mkendera $
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
class Gpf_View_ViewService extends Gpf_Object {

    const DEFAULT_VIEW_ID = "default";
    const DEFAULT_VIEW_NAME = "Default view";
    // translate for constant 
    // Gpf_Lang::_runtime('Default view')

    /**
     * @service
     * @anonym
     *
     * @param $gridcode
     */
    public function getViews(Gpf_Rpc_Params $params) {
        $gridCode = $params->get("gridcode");

        $viewsTable = Gpf_Db_Table_Views::getInstance();
        $viewColumnsTable = Gpf_Db_Table_ViewColumns::getInstance();
        $viewGrid = Gpf::newObj($gridCode);
        $views = $viewsTable->getAllViews($gridCode);

        $views->addColumn("columns");
        $views->addColumn("defaultview");
        $hasDefaultView = false;
        foreach ($views as $view) {
            $columns = $viewGrid->getAllViewColumns();
            $viewColumnsTable->fillViewColumns($columns, $view->get('id'));
            $view->set('columns', $columns->toObject());
            if (($view->get(Gpf_Db_Table_Views::NAME) == self::DEFAULT_VIEW_NAME) && ($view->get(Gpf_Db_Table_Views::ACCOUNTUSERID)=='')) {
                $hasDefaultView = true;
                $view->set('defaultview', Gpf::YES);
            }
        }

        if ($views->getSize() == 0) {
            $views->setHeader(array("id", "name", "rowsperpage", "columns", "defaultview"));
        }
        if (!$hasDefaultView) {
            $view = $views->createRecord();
            $view->set('id', self::DEFAULT_VIEW_ID);
            $view->set('name', $this->_(self::DEFAULT_VIEW_NAME));
            $view->set('rowsperpage', 30);
            $view->set('columns', $viewGrid->getDefaultViewColumns()->toObject());
            $view->set('defaultview', Gpf::YES);
    
            $views->add($view);
        }

        return $views;
    }

    /**
     * @service
     * @anonym
     *
     * @param $gridcode
     */
    public function getActiveView(Gpf_Rpc_Params $params) {
        $view = new Gpf_Data_RecordSet();
        $view->setHeader(array("id"));
        try {
            $view->add(array($this->getActiveViewId($params->get('gridcode'))));
        } catch (Exception $e) {
            $view->add(array("default"));
        }
        return $view;
    }

    public function fillActiveViewData(Gpf_Rpc_Params $params, Gpf_View_GridService $gridService) {
        $sort_col = null;
        $sort_asc = null;
        try {
            $view = new Gpf_Db_View();
            $view->setId($this->getActiveViewId(get_class($gridService)));
            $view->load();
            $columns = Gpf_Db_Table_ViewColumns::getInstance()->fillViewColumns($gridService->getAllViewColumns(), $view->getId())->toObject();
            $viewColumn = new Gpf_Db_ViewColumn();
            $viewColumn->setViewId($view->getId());
            foreach ($viewColumn->loadCollection() as $viewColumn) {
                if ($viewColumn->getSorted() != 'N') {
                    if(!$params->exists('sort_col')) {
                        $sort_col = $viewColumn->getName();
                    }
                    if (!$params->exists('sort_asc') && $viewColumn->getSorted() == 'A') {
                        $sort_asc = true;
                    }
                }
            }
            $this->initPager($params, 0, $view->getRowsPerPage());
        } catch (Exception $e) {
            $defaultColumns = $gridService->getDefaultViewColumns();
            $columns = $defaultColumns->toObject();
            foreach ($defaultColumns as $column) {
                if ($column->get('sorted') != 'N') {
                    if(!$params->exists('sort_col')) {
                        $sort_col = $column->get('id');
                    }
                    if(!$params->exists('sort_asc')) {
                        $sort_asc = ($column->get('sorted') == 'A');
                    }
                }
            }
            $this->initPager($params, 0, 30);
        }
        $params->add("columns", $columns);
        if ($sort_col != null) {
            $params->add('sort_col', $sort_col);
        }
        if ($sort_asc !== null) {
            $params->add('sort_asc', $sort_asc);
        }
    }

    private function initPager(Gpf_Rpc_Params $params, $offset=0, $limit = 30) {
            if (!$params->exists('offset')) {
                $params->add('offset', $offset);
            }
            if (!$params->exists('limit')) {
                $params->add('limit', $limit);
            }
    }

	private function getActiveViewId($gridCode) {
        $activeView = new Gpf_Db_ActiveView();
        $activeView->set("viewtype", $gridCode);
        $activeView->set("accountuserid", Gpf_Session::getAuthUser()->getAccountUserId());
        $activeView->load();
        return $activeView->get("activeviewid");
    }

    /**
     * @service grid_view write
     *
     * @param $gridcode
     * @param $viewid
     * @return Gpf_Rpc_Action
     */
    public function saveActiveView(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Active view changed"));
        $action->setErrorMessage($this->_("Error while changing active view"));

        $viewId = $action->getParam("viewid");
        if ($viewId != self::DEFAULT_VIEW_ID) {
            try {
                $view = new Gpf_Db_View();
                $view->set(Gpf_Db_Table_Views::ID, $viewId);
                $view->load();
            } catch (Gpf_DbEngine_NoRowException $e) {
                $action->setErrorMessage($this->_("View does not exist"));
                $action->addError();
                return $action;
            }
        }

        $activeViewsTable = Gpf_Db_Table_ActiveViews::getInstance();
        $activeViewsTable->saveActiveView($action->getParam('gridcode'), $viewId);

        $action->addOk();
        return $action;
    }

    /**
     * @service grid_view add
     *
     * @param $Id
     * @param $gridcode
     * @param $name
     * @param $rowsperpage
     * @param $columns
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }
    
    protected function getUserId() {
        return Gpf_Session::getAuthUser()->getAccountUserId();
    }
    
    protected function saveView(Gpf_Db_View $view, Gpf_Rpc_Form $form) {
        $view->set('accountuserid', $this->getUserId());
        $view->set('viewtype', $form->getFieldValue('gridcode'));
        $view->save();
        $view->load();
        
        $viewId = $view->get(Gpf_Db_Table_Views::ID);
        $viewColumns = Gpf_Db_Table_ViewColumns::getInstance();
        $viewColumns->deleteAll($viewId);
        $rorder = 0;
        $columns = new Gpf_Data_RecordSet();
        $columns->loadFromArray($form->getFieldValue('columns'));

        foreach ($columns as $column) {
            $viewColumn = new Gpf_Db_ViewColumn();
            $viewColumn->set('viewid', $viewId);
            $viewColumn->set('name', $column->get('id'));
            $viewColumn->set('sorted', $column->get('sorted'));
            $viewColumn->set('width', $column->get('width'));
            $viewColumn->set('rorder', $rorder++);
            $viewColumn->save();
        }
        $form->setInfoMessage($this->_('View %s saved',$form->getFieldValue('name')));
        $form->setField('Id',$view->getId());
        $activeViewsTable = Gpf_Db_Table_ActiveViews::getInstance();
        $activeViewsTable->saveActiveView($view->get("viewtype"), $viewId);
    }
    
    /**
     *
     * @param $viewId
     * @return Gpf_Db_View
     */
    protected function getView($viewId) {
        $view = new Gpf_Db_View();
        $view->setPrimaryKeyValue($viewId);
        try {
            $view->load();
        } catch (Exception  $e) {
            $view = new Gpf_Db_View();
        }
        return $view;
    }

    /**
     * @service grid_view write
     *
     * @param $Id
     * @param $gridcode
     * @param $name
     * @param $rowsperpage
     * @param $columns
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $viewId = $form->getFieldValue('Id');
        if ($viewId == self::DEFAULT_VIEW_ID) {
            $form->setErrorMessage($this->_("Default view can not be modified"));
            return $form;
        }

        $view = $this->getView($viewId);
        try {
            $form->fill($view);
            if (($view->getName() == $this->_(self::DEFAULT_VIEW_NAME))) {
                throw new Gpf_Exception($this->_("View name must be unique"));
            }
            $this->saveView($view, $form);
        } catch (Exception $e) {
            $form->setErrorMessage($this->_("Error while saving view ") . $form->getFieldValue('name') . " (" . $e->getMessage() . ")");
        }

        return $form;
    }

    /**
     * @service grid_view delete
     *
     * @param $Id
     */
    public function delete(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $viewId = $form->getFieldValue('viewId');
        if ($viewId == self::DEFAULT_VIEW_ID) {
            $form->setErrorMessage($this->_("Default view can not be deleted"));
            return $form;
        }

        $view = new Gpf_Db_View();
        $view->set(Gpf_Db_Table_Views::ID, $viewId);
        try {
            $view->load();
            $view->delete();
        } catch (Exception  $e) {
            $form->setErrorMessage($this->_("Error while deltting view"));
        }

        $form->setInfoMessage($this->_("View deleted"));

        return $form;

    }
}
?>
