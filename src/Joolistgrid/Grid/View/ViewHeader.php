<?php

namespace JoolistGrid\Grid\View;

use JoolistGrid\Grid\View\ViewAbstract;

class ViewHeader implements ViewAbstract {

	const PARAM_COLUMN_COLLECTION = "collectionColumn";

	private $_params;
	private $_translator;

	public function getHtml() {
		$collectionColumn = $this->_params[self::PARAM_COLUMN_COLLECTION];

		if(!$collectionColumn) {
			return null;
		}

		$collection = $collectionColumn->getCollection();
		$displayMap = $collectionColumn->getDisplayMap();

		$html  = "<table class='table table-bordered table-condensed table-striped'>";
		$html .= "<thead>";
		$html .= "<tr>";

		if($collectionColumn->getRankVisible()) {
    		$html .= "<th class='stt'>TT</th>";
		}

		foreach ($collection as $column) {
			$position = $column->getPosition();
			if($displayMap->$position) {
			    $label = $column->getLabel();
				$html .= "<th>";
				if($column->getOrder()) {
					$html .= "<a href='javascript:void(0)' class='order-able' data-order-column='". $position ."'>". $label ."</a>";
				} else {
					$html .= $label;
				}
				$html .= "</th>";
			}
		}

		if($collectionColumn->getCheckboxVisible()) {
		    $html .= '<th class="tr-check-all"><input type="checkbox" class="check-all"></th>';
		}

		$html .= "</tr>";
		$html .= "</thead>";

		return $html;
	}

	public function setParams($params) {
		$this->_params = $params;
	}

	public function setTranslator($translator) {
		$this->_translator = $translator;
	}

}