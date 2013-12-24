<?php

namespace JoolistGrid\Grid\View;

use JoolistGrid\Grid\View\ViewAbstract;

class ViewPaginator implements ViewAbstract {

	const PARAM_SOURCE 		    = "source";
	const PARAM_PAGE 			= "page";
	const PARAM_LINE_PER_PAGE 	= "linePerPage";
	const PARAM_PAGE_RANGE 		= 10;

	private $_params;

	public function getHtml() {
		$source      = $this->_params[self::PARAM_SOURCE];
		$page 		 = $this->_params[self::PARAM_PAGE];
		$linePerPage = $this->_params[self::PARAM_LINE_PER_PAGE];
		$pageRange   = self::PARAM_PAGE_RANGE;

		$totalRow    = $source->getTotalRow();

		if(!$totalRow || $totalRow <= $linePerPage || $linePerPage == 0) {
			return null;
		}

		$totalPage = ceil($totalRow / $linePerPage);

		if($totalPage <= $pageRange) {
			$firstInRange = 1;
			$lastInRange  = $totalPage;
		} else {
			if($page <= $pageRange - 2) {
				$firstInRange = 1;
				$lastInRange  = $pageRange;
			} else if(($totalPage - round($pageRange / 2)) <= $page && $page <= $totalPage) {
				$firstInRange = $totalPage - $pageRange;
				$lastInRange  = $totalPage;
			} else {
				$firstInRange = $page - round($pageRange / 2);
				$lastInRange  = $page + round($pageRange / 2) - 1;
			}
		}


		$html  = "<ul class='grid-paginator'>";
		$class = "class='grid-paginator-page' ";
		$href  = "href='javascript:void(0)' ";

		if(1 < $page && $page < $pageRange - 1) {
			$html .= "<li><a ". $class . $href ." data-page='". 1 . "'>1</a></li>";
		} else if($page == 1) {
			$html .= "<li><a class='paginator-active' href='#'>". 1 . "</a></li>";
		} else {
			$html .= "<li><a ". $class . $href ." data-page='". ($page - 1) . "'>Prev</a></li>";
			$html .= "<li><a ". $class . $href ." data-page='". 1 . "'>1</a></li>...";
		}

		for ($i = $firstInRange + 1; $i < $lastInRange; $i++) {
			if($i != $page) {
				$html .= "<li><a ". $class . $href ." data-page='". $i . "'> ". $i ."</a></li>";
			} else {
				$html .= "<li><a class='paginator-active' href='#'>". $i . "</a></li>";
			}
		}

		if($totalPage - $pageRange + round($pageRange / 2) - 1 < $page && $page < $totalPage) {
			$html .= "<li><a ". $class . $href ." data-page='". $totalPage . "'>". $totalPage ."</a></li>";
		} else if($page == $totalPage) {
			$html .= "<li><a class='paginator-active' href='#'>". $totalPage . "</a></li>";
		} else {
			$html .= "...<li><a ". $class . $href ." data-page='". $totalPage . "'>". $totalPage ."</a></li>";
			$html .= "<li><a ". $class . $href ." data-page='".($page + 1)  . "'>Next</a></li>";
		}

		$html .= "</ul>";

		return $html;
	}

	public function setParams($params) {
		$this->_params = $params;
	}

}