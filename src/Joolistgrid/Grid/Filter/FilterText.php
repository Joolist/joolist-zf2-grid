<?php

namespace JoolistGrid\Grid\Filter;

use JoolistGrid\Grid\GridFilter;
use JoolistGrid\Grid\GridFilterable;
use JoolistGrid\Grid\GridSource;

class FilterText extends GridFilter {
	
	const MODE_EQUAL    		= "EQUAL";
	const MODE_LIKE     		= "LIKE";
	const MODE_GREATER  		= "GREATER";
	const MODE_LOWER    		= "LOWER";
	const MODE_GREATER_OR_EQUAL = "GREATER_OR_EQUAL";
	const MODE_LOWER_OR_EQUAL   = "LOWER_OR_EQUAL";
	
	private $_key;
	private $_value;
	private $_attribute=array();
	private $_class;
	private $_mode;
	
	public function __construct($key=null, $value=null, $mode=null) {
		$this->setKey($key);
		$this->setValue($value);
		$this->setMode($mode);
	}
	
	public function filters(GridFilterable $source) {
		
		if($this->_value == null) {
			return null;
		}
		
		$this->addClass("grid-filtering");
		
		switch ($this->_mode) {
			case self::MODE_EQUAL:
				$source->equal($this->_key, $this->_value);
			break;
			case self::MODE_LIKE:
				$source->like($this->_key, $this->_value);
			break;
			case self::MODE_GREATER:
				$source->greater($this->_key, $this->_value);
			break;
			case self::MODE_LOWER:
				$source->lower($this->_key, $this->_value);
			break;
			case self::MODE_GREATER_OR_EQUAL:
				$source->greaterOrEqual($this->_key, $this->_value);
			break;
			case self::MODE_LOWER_OR_EQUAL:
				$source->lowerOrEqual($this->_key, $this->_value);
			break;
			default:
				$source->like($this->_key, $this->_value);
			break;
		}
		
		return $source;
	} 
	
	function getHtml(GridSource $source=null, $selected=null) {
		$attribute = "";
		foreach ($this->_attribute as $key => $value) {
			$attribute .= $key . ' = "' . $value . '" ';
		}
		
		return "<input class='filter-input". ' ' .$this->_class ."' ". $attribute ." value='". $this->_value ."' 
				type='input' name='grid-filter[". $this->_key ."]'  id='grid-filter[". $this->_key ."]'>";
	}

	public function getValue() {
		return $this->_value;
	}
	
	public function getKey() {
		return $this->_key;
	}
	
	public function setKey($key) {
		$this->_key = $key;
	}
	
	public function setValue($value) {
		$this->_value = $value;
	}
	
	public function setMode($mode=true) {
		$this->_mode = $mode;
	}
	
	function setAttribute($attribute) {
		$this->_attribute = $attribute;
	}
	
	function addClass($class) {
		$this->_class = $class;
	}
}