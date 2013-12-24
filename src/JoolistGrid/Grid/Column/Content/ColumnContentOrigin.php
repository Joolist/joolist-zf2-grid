<?php

namespace JoolistGrid\Grid\Column\Content;

use JoolistGrid\Grid\Column\ColumnContent;

class ColumnContentOrigin extends ColumnContent {

	private $_string;
	
	public function __construct($string) {
		$this->setField($string);
	}
	
	public function setField($string) {
		$this->_string = $string;
	}
	
	public function getValue($row) {
		return $this->_string;
	}
	
}