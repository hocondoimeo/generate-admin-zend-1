<?php

/**
 * Model for Application_Model_DbTable_Crawler_Abstract
 *
 * @version     $Id$
 */

class Base_Db_Table_Abstract extends Zend_Db_Table {

    public $searchFields;
    public $sortFields;
    private $_messenger;
    
    public function init()
    {
    	$this->_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * Enter description here ...
     * @param Zend_Db_Select $select
     * @param array $params
     * @return Zend_Db_Select | null
     * @author Tung Ly
     */
    public function createSQL($select, $params) {
        $andWhere = array();
        $orWhere = array();
        foreach ($params as $field => $value) {
            if ($field === 'fieldsearch'){
                $andWhere[] = str_replace('{{param}}', addslashes(trim($params['keywords'])), $this->searchFields[$value]);
            }
            elseif ($field !== 'keywords' && $value !== 'none'){
                if(isset($this->searchFields[$field])){
                    $orWhere[] = str_replace('{{param}}', $params[$field], $this->searchFields[$field]);
                } elseif(isset($this->sortFields[$field])){
                    $select->order(str_replace('{{param}}', $params[$field], $this->sortFields[$field]));
                }
            }
        }
        if(count($andWhere)) {
            $select->where(implode(' AND ',$andWhere));
        }
        if(count($orWhere)) {
            $select->where(implode(' OR ',$orWhere));
        }
        //echo '<pre>'; print_r($params); echo $select;
        return $select;
    }
    

/********************************************************************
* Function basic
********************************************************************/
    /**
     * Query get all data in table SiteLinks
     * @param array $params
     * @param array $columns
     * @return string select
     */
    public function getQuerySelectAll($params = array(), $columns = array()) {
        if (sizeof($columns)) {
            $_select = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
                           ->from($this->_name,array())
                           ->columns($columns);
        } else {
            $_select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        }
        $params = array_reverse($params);
        $select = $this->createSQL($_select, $params);
        //$select->join("Sites", "Sites.SiteId = {$this->_name}.SiteId");
        //$select->join("SiteLinks", "SiteLinks.SiteLinkId = {$this->_name}.ParentSiteLinkId");
        $select->setIntegrityCheck(false);
        return $select;
    }

    /**
     * Insert record in table SiteLinks
     * @param array $dataSiteLink
     * @return bool
     */
    public function add($data) {
        try {
            $newRow = $this->createRow($data);
            return $newRow->save();
        } catch (Exception $exc) {
            if ( in_array(APPLICATION_ENV, array('development')) ){
                $this->_messenger->addMessage('danger/'.$exc->getMessage());
            }
            return false;
        }
    }

    /**
     * Update record exist in table SiteLinks

     * @param array $dataSiteLink     * @return bool
     */
    public function edit($data) {
        try {
            $oldRow = $this->find($data[current($this->_primary)])->current();
            $oldRow->setFromArray($data);
            return $oldRow->save();
        } catch (Exception $exc) {
            if ( in_array(APPLICATION_ENV, array('development')) ){
                $this->_messenger->addMessage('danger/'.$exc->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Delete record exist in table SiteLinks
    
     * @param array $dataSiteLink     * @return bool
     */
    public function deleteRow($id) {
    	try {
    	    $row = $this->find($id)->current();
    		return $row->delete();
    	} catch (Exception $exc) {
    		if ( in_array(APPLICATION_ENV, array('development')) ){
    			$this->_messenger->addMessage('danger/'.$exc->getMessage());
    		}
    		return false;
    	}
    }
    
    /**
     * Update toggle boolean
     * @param record id success else is false
     */
    function updateBoolean($field, $id) {
        return $this->update(array($field => new Zend_Db_Expr("! $field")), "{$this->_primary} = $id");
    }
    
}