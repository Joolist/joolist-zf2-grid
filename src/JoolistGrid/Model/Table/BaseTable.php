<?php

namespace JoolistGrid\Model\Table;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class BaseTable {
    protected $_name;
    protected $_tableGateway;
    protected $_select;
    protected $_sql;
    
    public function __construct(TableGateway $_tableGateway) {
        $this->_tableGateway = $_tableGateway;
        $this->_sql = $this->_tableGateway->getSql();
        $this->_select = $this->_sql->select();
    }
    
    public function select() {
        $resultSet = $this->_tableGateway->selectWith($this->_select);
        return $resultSet;
    }
    
    public function fetchAll() {
        $resultSet = $this->_tableGateway->select();
        return $resultSet;
    }
    
    /*
     * 
    public function selectWith($sql) {
        echo '<br/>'.$sql->getSqlString();
        $resultSet = $this->_tableGateway->selectWith($sql);
        return $resultSet;
    }
    


    public function fetchRow($id) {
        $id = (int)$id;
        $rowset = $this->_tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function delete($id) {
        $this->_tableGateway->delete(array('id' => (int)$id));
    }
    
    public function getTableGateway() {
        return $this->_tableGateway;
    }
    
    public function getQueryString() {
        return $this->_sqlQuery;
    }
    
    public function getSql() {
        return $this->_sql;
    }
    
    public function getSelect() {
        return $this->_select;
    }
    
    public function reset($part) {
        $this->_select->reset($part);
        return $this;
    }
    
    public function columns($columns) {
        $this->_select->columns($columns);
        return $this;
    }
    
    public function quantifier($quantifier) {
        $this->_select->quantifier($quantifier);
        return $this;
    }
    
    public function limitPage($page, $linePerPage) {
        $this->_select->limit($linePerPage);
        $this->_select->offset(($page-1)*$linePerPage);
        return $this;
    }
    
    public function order($orderColumn, $orderBy) {
        $order = $orderColumn .' '. $orderBy;
        $this->_select->order($order);
        return $this;
    }
    
    public function getPart($part) {
        return $this->_select->getRawState($part);
    }
    
    public function where($where) {
        $this->_select->where($where);
        return $this;
    }
    
    public function __toString() {
        return $this->_select->getSqlString();
    }
     * 
     */

}
