<?php

namespace JoolistGrid\Grid;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\PhpEnvironment\Request as EnvironmentRequest;
use Zend\Http\Request;

use JoolistGrid\Grid\Column\Content\ColumnContentField;
use JoolistGrid\Grid\Column\ColumnContent;
use JoolistGrid\Grid\Filter\FilterCombobox;
use JoolistGrid\Grid\Filter\FilterText;
use JoolistGrid\Grid\Filter\FilterRanger;
use JoolistGrid\OeZendWidgetGrid;

class GridColumn extends AbstractPlugin {

	private $_key;
	private $_order=true;
	private $_label;
	private $_display=1;
	private $_position;

	private $_filter;
	private $_columnContent;

	private $_value;
	private $_attribute=array();

	public function __construct($key=null) {
		$this->setKey($key);
	}

	public function setKey($key) {
		$this->_key = $key;

		if($this->_label == null) {
			$this->_label = $key;
		}

		//SET DEFAULT CONTENT is FIELD CONTENT;
		$content = new ColumnContentField($this->_key);
		$this->setContent($content);
	}

	public function setOrder($order=true) {
		$this->_order = $order;
	}

	public function setLabel($label) {
		$this->_label = $label;
	}

	public function setDisplay($display=1) {
		$this->_display = ($display  == 1 ? 1 : 0);
	}

	/**
	 *
	 * Set Filter for Column
	 * @param JoolistGrid\Grid\Filter $filter
	 * @param String: 'Combobox'|'Range'| Default is 'Text' $filter
	 *
	 * Check key,value in Filter Element
	 * If (key == null) key = columnKey
	 * If (value == null) value = requestParam[$filter->getKey]
	 *
	 * */
	public function setFilter($filter) {

	    if(is_string($filter)) {
	        switch ($filter) {
	        	case 'Combobox': $filter = new FilterCombobox();
	        	break;
	        	case 'Range': $filter = new FilterRange();
	        	break;
	        	default: $filter = new FilterText();
	        	break;
	        }
	    }

		if(!$filter instanceof GridFilter) {
			throw new Zend_Exception('Filter is not instanceof Grid_Filter');
		}

		if($filter->getKey() == null) {
			$filter->setKey($this->_key);
		}

		if($filter->getValue() == null) {
            $params = $_POST['grid-filter'];

			$value = null;
			if($filter instanceof FilterRange) {
				if(isset($params[$filter->getKey() . '_' . $filter::VALUE_START])
						&& isset($params[$filter->getKey() . '_' . $filter::VALUE_END])) {
					$value = array(0 => $params[$filter->getKey() . '_' . $filter::VALUE_START], 1 => $params[$filter->getKey(). '_' . $filter::VALUE_END]);
				}
			} else {
				$value = isset($params[$filter->getKey()]) ? $params[$filter->getKey()] : null;
			}

			$filter->setValue($value);
		}

		$this->_filter = $filter;
	}

	public function setContent(ColumnContent $columnContent) {
		if(!$columnContent instanceof ColumnContent) {
			throw new Zend_Exception('$columnContent in not instanceof Grid_Column_Content');
		}
		$this->_columnContent = $columnContent;
	}

	public function setPosition($position) {
		$this->_position = $position;
	}

	/**
	 * Add params attribute set to html column
	 *
	 * @param : Array()|String
	 *
	 * */
	public function addAttribute($attribute) {
	    if(is_string($attribute)) {
	        array_push($this->_attribute, $attribute);
	    }
	    if(is_array($attribute)) {
	        $this->_attribute = array_merge($this->_attribute, $attribute);
	    }
	}

	public function getAttribute() {
	    return $this->_attribute;
	}

	public function getValue($row) {
		return $this->_columnContent->getValue($row);
	}

	public function getLabel() {
		return $this->_label;
	}

	public function getKey() {
		return $this->_key;
	}

	public function getFilter() {
		return $this->_filter;
	}

	public function getOrder() {
		return $this->_order;
	}

	public function getDisplay() {
		return $this->_display;
	}

	public function getPosition() {
		return $this->_position;
	}
}