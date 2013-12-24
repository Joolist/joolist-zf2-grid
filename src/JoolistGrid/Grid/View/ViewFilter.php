<?php

namespace JoolistGrid\Grid\View;

use JoolistGrid\Grid\View\ViewAbstract;

class ViewFilter implements ViewAbstract {

	const PARAM_DATA_SOURCE 	  = 'dataSource';
	const PARAM_COLLECTION_FILTER = 'collectionFilter';
	const PARAM_COLLECTION_COLUMN = 'collectionColumn';

	private $_params;

	public function getHtml() {
		$source 	  	  = $this->_params[self::PARAM_DATA_SOURCE];
		$collectionFilter = $this->_params[self::PARAM_COLLECTION_FILTER];
		$collectionColumn = $this->_params[self::PARAM_COLLECTION_COLUMN];

		if(!$source || !$collectionFilter) {
			return null;
		}

		$collection = $collectionFilter->getCollection();

		$html = '<tr class="filter-collection">';
		foreach ($collection as $filter) {
			$html .= '<td>';
			if($filter) {
				$html .= $filter->getHtml($source);
			}
			$html .= '</td>';
		}
		$html .= '</tr>';

		return $html;
	}

	public function setParams($params) {
		$this->_params = $params;
	}

}