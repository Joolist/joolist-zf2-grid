<?php

namespace JoolistGrid\Grid\Source;

use JoolistGrid\Grid\GridSource;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use JoolistGrid\Model\adapter\Baseadapter;
use Zend\Db\Adapter\Adapter;

class ZendDbSelect extends GridSource {

	private $_source;//Baseadapter
	private $_sourceOrigin;//Baseadapter
	private $_adapter;

	private $_orderBy;
	private $_orderColumn;

	private $_page;
	private $_totalNumberPage;
	private $_startPage;
	private $_endPage;
	private $_currentPage;
	private $_linePerPage;

	public function __construct(Adapter $adapter, Select $select) {
		$this->setSource($select);
        $this->_adapter = $adapter;
	}

	public function setSource($source) {
		if(!$source instanceof Select) {
			throw new Zend_Exception("Source in not instanceof Select");
		}
		$this->_source 		 = $source;
		$this->_sourceOrigin = clone $source;
	}

	public function getData() {
		$orderBy 	 = $this->_orderBy;
		$orderColumn = $this->_orderColumn;

		if($this->_linePerPage != 0) {
            $this->_source->limit($this->_linePerPage);
            $this->_source->offset(($this->_page-1)*$this->_linePerPage);
		}
		if($orderColumn && $orderBy) {
			$this->_source->reset(Select::ORDER);
            $order = $orderColumn .' '. $orderBy;
			$this->_source->order($order);
		}
        
        $sql = new Sql($this->_adapter);  
        $selectString = $sql->getSqlStringForSqlObject($this->_source);
        $result = $this->_adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $result;
	}

	public function distinctColumn($key) {
	    $key = $this->getKeyByAlias($key);
		if(!$key) {
			return $this->_sourceOrigin;
		}

		$this->_sourceOrigin->reset(Select::COLUMNS)
						    ->reset(Select::LIMIT)
						    ->reset(Select::OFFSET)
						    ->reset(Select::ORDER)
						    ->reset(Select::GROUP)
						    ->reset(Select::HAVING)
    					    ->columns(array('KEY_COLUMN_DISTINCT' => $key))
    					    ->quantifier(Select::QUANTIFIER_DISTINCT);
                            
        $sql = new Sql($this->_adapter);  
        $selectString = $sql->getSqlStringForSqlObject($this->_sourceOrigin);
        $result = $this->_adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $r = array();
        foreach ($result as $a => $value) {
           $r[$a] = $value; 
        }
        return $r;
	}

	public function getTotalRow() {
		$this->_source->reset(Select::COLUMNS)
							->reset(Select::LIMIT)
							->reset(Select::OFFSET)
							->reset(Select::ORDER)
							->reset(Select::GROUP)
							->reset(Select::HAVING)
                            ->reset(Select::HAVING)
							->columns(array('id', 'count' => new \Zend\Db\Sql\Expression('COUNT(*)')))
                            ->quantifier(Select::QUANTIFIER_DISTINCT);
        
        $sql = new Sql($this->_adapter);
        $statement = $sql->prepareStatementForSqlObject($this->_source);
        $result = $statement->execute()->current();
        return isset($result[0]["count"]) ? $result[0]["count"] : 0;
	}

	public function getPage() {
		return $this->_page;
	}

	public function getTotalNumberPage() {
		return $this->_totalNumberPage;
	}

	public function getStartPage() {
		return $this->_startPage;
	}

	public function getEndPage() {
		return $this->_endPage;
	}

	public function getCurrentPage() {
		return $this->_currentPage;
	}

	public function getLinePerPage() {
		return $this->_linePerPage;
	}

	public function setPage($page) {
		$this->_page = $page;
	}

	public function setTotalNumberPage($totalNumberPage) {
		$this->_totalNumberPage = $totalNumberPage;
	}

	public function setStartPage($startPage) {
		$this->_startPage = $startPage;
	}

	public function setEndPage($endPage) {
		$this->_endPage = $endPage;
	}

	public function setCurrentPage($currentPage) {
		$this->_currentPage = $currentPage;
	}

	public function setLinePerPage($linePerPage) {
		$this->_linePerPage = $linePerPage;
	}

	function setOrderBy($orderBy) {
		$this->_orderBy = $orderBy;
	}

	function setOrderColumn($orderColumn) {
		$this->_orderColumn = $orderColumn;
	}

	public function like($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key LIKE '%". $value ."%'");
		}
	}

	public function equal($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key = '". $value. "'");
		}
	}

	public function greater($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key > ". $value);
		}
	}

	public function lower($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key < ". $value);
		}
	}

	public function greaterOrEqual($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key >= ". $value);
		}
	}

	public function lowerOrEqual($key, $value) {
		if($value) {
			$key = $this->getKeyByAlias($key);
			$this->_source->where(" $key <= ". $value);
		}
	}

	public function getKeyByAlias($key) {
		$columns = $this->_source->getRawState(Select::COLUMNS);

		foreach ($columns as $value) {
			if($value == $key) {
				return $value;
			}
		}
        
		return $key;
	}
    
}