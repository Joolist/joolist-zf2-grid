<?php

namespace JoolistGrid\Grid;


abstract class GridPageable {
	
	abstract function getPage();
	
	abstract function getTotalNumberPage();
	
	abstract function getStartPage();
	
	abstract function getEndPage();
	
	abstract function getCurrentPage();
	
	abstract function getLinePerPage();	
	
	
	abstract function setPage($page);
	
	abstract function setTotalNumberPage($totalNumberPage);
	
	abstract function setStartPage($startPage);
	
	abstract function setEndPage($endPage);
	
	abstract function setCurrentPage($currentPage);
	
	abstract function setLinePerPage($linePerPage);
	
}