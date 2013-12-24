<?php

namespace JoolistGrid\Grid\Filter;

use JoolistGrid\Grid\GridFilter;
use JoolistGrid\Grid\GridFilterable;
use JoolistGrid\Grid\GridSource;

class FilterCombobox extends GridFilter {

	private $_key;
	private $_value;
	private $_equal;
	private $_attribute=array();
	private $_class;

	public function __construct($key=null, $value=null) {
		$this->setKey($key);
		$this->setValue($value);
	}

	public function filters(GridFilterable $source) {
		if($this->_value) {
			$source->equal($this->_key, $this->_value);
			$this->addClass("grid-filtering");
		}
		return $source;
	}

	public function getHtml(GridSource $source=null) {
		$key = $source->getKeyByAlias();
		$rs  = $source->distinctColumn($this->_key);
        
		$htmlFilter  = "<select  name='grid-filter[". $this->_key ."]'  id='grid-filter[". $this->_key ."]' class='filter-combobox ". $this->_class ."'>";

		if(!$rs) {
    		$htmlFilter .= "<option value=''>-- Data not found --</option>";
			$htmlFilter . '</select>';
			return $htmlFilter;
		}

		$htmlFilter .= "<option value=''>-- All --</option>";
		foreach ($rs as $value) {
			$selectedStr = ($value['KEY_COLUMN_DISTINCT'] == $this->_value) ? "selected = 'selected'" : "";

			$htmlFilter .= "<option value='". $value['KEY_COLUMN_DISTINCT'] ."' ". $selectedStr ."> "
							. $value['KEY_COLUMN_DISTINCT']
							." </option>";
		}
		$htmlFilter .= "</select>";

		return $htmlFilter;
	}

	public function getKey() {
		return $this->_key;
	}

	public function getValue() {
		return $this->_value;
	}

	public function setKey($key) {
		$this->_key = $key;
	}

	public function setValue($value) {
		$this->_value = $value;
	}

	public function setEqual($equal=true) {
		$this->_equal = $equal;
	}

	public function setAttribute($attribute) {
		$this->_attribute = $attribute;
	}

	public function addClass($class) {
		$this->_class = $class;
	}

}