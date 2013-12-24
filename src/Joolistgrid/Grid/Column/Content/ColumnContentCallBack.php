<?php

namespace JoolistGrid\Grid\Column\Content;

use JoolistGrid\Grid\Column\ColumnContent;

class ColumnContentCallBack extends ColumnContent {

	private $_function;
	
	public function __construct($function) {
		$this->setFunction($function);
	}

	public function setFunction($function) {
		$this->_function = $function;
	}
	
	public function getValue($row) {
		$value = null;
		$function = $this->_function;
		
		if(is_callable($function)) {
			$value = $function($row);
		}
		return $value;
	}
	
}