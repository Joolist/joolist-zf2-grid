<?php

namespace JoolistGrid\Grid\View;

use JoolistGrid\Grid\View\ViewAbstract;

class ViewBody implements ViewAbstract {

	const PARAM_DATA 			  = 'data';
	const PARAM_COLUMN_COLLECTION = 'collectionColumn';

	const PARAM_PAGE 			  = 'page';
	const PARAM_LINE_PER_PAGE 	  = 'linePerPage';

	private $_params;

	public function getHtml() {
		$data 			  = $this->_params[self::PARAM_DATA];
		$collectionColumn = $this->_params[self::PARAM_COLUMN_COLLECTION];
		$page 			  = $this->_params[self::PARAM_PAGE];
		$linePerPage	  = $this->_params[self::PARAM_LINE_PER_PAGE];

		$collection = $collectionColumn->getCollection();

		if(!$data || !$collectionColumn) {
			return '<tr><td colspan="'. count($collection) .'"><h5 class="grid-data-not-found"> Data not found! </h5></td></tr>';
		}

		$displayMap = $collectionColumn->getDisplayMap();

		$checkboxVisiable = $collectionColumn->getCheckboxVisible();
		$rankVisiable     = $collectionColumn->getRankVisible();

		$html  = '<tbody>';
		foreach ($data as $key => $row) {
			$html .= '<tr>';

			if($rankVisiable) {
    			$html .= '<td class="stt center" style="width:4%">' . ($key + ($page -  1) * $linePerPage + 1) . '</td>';
			}

			foreach ($collection as $column) {
				$position  = $column->getPosition();
				$attribute = $column->getAttribute();

				$attrs = '';
				if($attribute) {
				    foreach ($attribute as $key => $attr) {
				        if(is_string($key)) {
                            $attrs .= ' '. $key .'="'. $attr .'" ';
				        }
				        if(is_numeric($key)) {
				            $attrs .= ' '. $attr .' ';
				        }
				    }
				}

				if($displayMap->$position) {
					$html .= '<td'. $attrs .'>'. $column->getValue($row). '</td>';
				}
			}

			if($checkboxVisiable) {
			    $html .= '<td class="item-checkbox center" style="width:4%"><input type="checkbox" name="item_checker[]" value="'. $row['id'] .'"></td>';
			}

			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}

	public function setParams($params) {
		$this->_params = $params;
	}

}