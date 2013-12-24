<?php

namespace JoolistGrid\Grid\Filter;

use JoolistGrid\Grid\GridFilter;
use JoolistGrid\Grid\GridFilterable;
use JoolistGrid\Grid\GridSource;

class FilterRange extends GridFilter {

	const MODE_EQUAL     = "EQUAL";
	const MODE_NOT_EQUAL = "NOT_EQUAL";

	const VALUE_START = 0;
	const VALUE_END   = 1;

	private $_key;
	private $_value;
	private $_attribute=array();
	private $_class;
	private $_mode;

	private $_decorator;

	public function __construct($key=null, $value=null, $mode=null, $decorator='-') {
		$this->setKey($key);
		$this->setValue($value);
		$this->setMode($mode);
		$this->setDecorator($decorator);
	}

	public function filters(GridFilterable $source) {
		$value = $this->_value;

		if(!$value[self::VALUE_START] && !$value[self::VALUE_END]) {
			return null;
		}

		$this->addClass("grid-filtering");

		switch ($this->_mode) {
			case self::MODE_EQUAL:
				$source->greaterOrEqual($this->_key, $value[self::VALUE_START]);
				$source->lowerOrEqual($this->_key, $value[self::VALUE_END]);
			break;
			case self::MODE_NOT_EQUAL:
				$source->greater($this->_key, $value[self::VALUE_START]);
				$source->lower($this->_key, $value[self::VALUE_END]);
			break;
			default:
				$source->greater($this->_key, $value[self::VALUE_START]);
				$source->lower($this->_key, $value[self::VALUE_END]);
			break;
		}

		return $source;
	}

	public function getHtml(GridSource $source=null) {
		$attribute = "";
		foreach ($this->_attribute as $key => $value) {
			$attribute .= $key . ' = "' . $value . '" ';
		}

		$html = "<input name='grid-filter[". $this->_key . '_' . self::VALUE_START ."]'  id='grid-filter[". $this->_key . '_' . self::VALUE_START ."]'
						type='text' value='". $this->_value[self::VALUE_START] ."'
						class='filter-input filter-range filter-range-start ". $this->_class ."'". $attribute .">";

		$html .= $this->getDecorator();

		$html .= "<input name='grid-filter[". $this->_key . '_' . self::VALUE_END ."]'  id='grid-filter[". $this->_key . '_' . self::VALUE_END ."]'
				 		  type='text' value='". $this->_value[self::VALUE_END] ."'
				 		  class='filter-input filter-range filter-range-end ". $this->_class ."' ". $attribute .">";

		return $html;
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

	public function setMode($mode=true) {
		$this->_mode = $mode;
	}

	public function setDecorator($decorator='-') {
		$this->_decorator = $decorator;
	}

	public function getDecorator() {
		return $this->_decorator;
	}


	function setAttribute($attribute) {
		$this->_attribute = $attribute;
	}

	function addClass($class) {
		$this->_class = $class;
	}
}
