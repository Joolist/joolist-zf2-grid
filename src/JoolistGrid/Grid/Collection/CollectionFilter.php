<?php

namespace JoolistGrid\Grid\Collection;

use JoolistGrid\Grid\GridSource;

class CollectionFilter {
	
	private $_collection = array();
	
	public function addFilter($filter, $position=null) {
		if(null != $position) {
			$this->_collection[$position] = $filter;
		} else {
			array_push($this->_collection, $filter);
		}
	}
	
	public function getCollection() {
		return $this->_collection;
	}
	
	public function filters(GridSource $source) {
		foreach ($this->_collection as $filter) {
			if($filter) {
				$filter->filters($source);
			}
		}
	}
}