<?php

namespace JoolistGrid\Grid\Column\Content;

use JoolistGrid\Grid\Column\ColumnContent;

class ColumnContentField extends ColumnContent {

	private $_field;
	private $_format;

	public function __construct($field, $format=self::FORMAT_TEXT) {
		$this->setField($field);
        $this->setFormat($format);
	}

	public function setField($field) {
		$this->_field = $field;
	}

	public function setFormat($format) {
		$this->_format = $format;
	}

	public function getValue($row) {
		return $row[$this->_field];
	}

}