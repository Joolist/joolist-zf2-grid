<?php

namespace JoolistGrid\Grid;


abstract class GridSource extends GridPageable 
	implements GridFilterable 
{
	abstract function getData();
	
	abstract function distinctColumn($key);
	
	abstract function getTotalRow();
	
	abstract function getKeyByAlias($key);
	
	abstract function setSource($source);
	
	abstract function setOrderBy($orderBy);
	
	abstract function setOrderColumn($orderColumn);
}