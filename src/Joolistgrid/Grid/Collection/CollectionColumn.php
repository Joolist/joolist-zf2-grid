<?php

namespace JoolistGrid\Grid\Collection;

use JoolistGrid\Grid\GridColumn;

use Zend\Json\Json;
use Zend\Http\Request;

class CollectionColumn {

	private $_name;

	private $_collectionColumn = array();
	private $_displayMap       = array();

	private $_checkboxVisible  = true;
	private $_rankVisible     = true;

	public function __construct($columns=null) {
		if($columns != null) {
			$this->addColumns($columns);
		}
	}

	public function addColumn(GridColumn $column, $position=null) {
		if(!$column instanceof GridColumn) {
			throw new Zend_Exception("Column in not instanceof Grid_Column");
		}

		$position = $position != null ? $position : count($this->_collectionColumn);

		$this->_collectionColumn[$position] = $column;

		$column->setPosition($position);

		//SET DISPLAY COLUMN
		$display = $column->getDisplay();

		$rq = new Request();
		$cookie = $rq->getCookie($this->_name, $default = null);

		if($cookie != null && is_string($cookie)) {

			$displayMap = Json::decode($cookie[$this->_name]);

			if(isset($displayMap[$position])) {
				$display = $displayMap[$position];
			}
		}

		$this->setDisplayColumn($position, $display);
	}

	public function addColumns($columns) {
		if(!is_array($columns)) {
			throw new Zend_Exception("Columns is not an Array");
		}

		foreach ($columns as $key => $column) {
			$this->addColumn($column);
		}
	}

	public function setCollection($collection) {
		$this->_collectionColumn = $collection;
	}

	public function setDisplayColumn($position, $display) {
		$this->_displayMap["$position"] = "$display";
	}

	public function setDisplayMap($displayMap) {
		$this->_displayMap = $displayMap;
	}

	public function setName($name) {
		$this->_name = $name;
	}


	public function getCollection() {
		return $this->_collectionColumn;
	}

	public function getDisplayMap() {
		return $this->_displayMap;
	}

	public function getName() {
		return $this->_name;
	}

	public function setDisableCheckbox() {
	    $this->_checkboxVisible = false;
	}

    public function getCheckboxVisible() {
        return $this->_checkboxVisible;
    }

	public function setDisableRank() {
	    $this->_rankVisible = false;
	}

    public function getRankVisible() {
        return $this->_rankVisible;
    }
}