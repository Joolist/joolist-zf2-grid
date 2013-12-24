<?php

namespace JoolistGrid\Grid\Column\Content;

use JoolistGrid\Grid\Column\ColumnContent;

class ColumnContentTemplate extends ColumnContent {

	private $_template;

	public function __construct($template) {
		$this->setTemplate($template);
	}

	public function setTemplate($template) {
		$this->_template = $template;
	}

	public function getValue($row) {
		$value = $this->_template;

		$matches = array();
		preg_match_all('/\${([^}]*)}/', $this->_template, $matches);

		$params = array();
		foreach ($matches[1] as $k => $v) {
			if(isset($row[$v])) {
				$value = str_replace($matches[0][$k], $row[$v], $value);
			}
		}

		return $value;
	}

}