<?php

namespace JoolistGrid\Grid;
use JoolistGrid\Grid\GridFilterable;
use JoolistGrid\Grid\GridSource;

abstract class GridFilter {
	
	abstract function filters(GridFilterable $source);
	
	abstract function getHtml(GridSource $source=null);
	
	abstract function setKey($key);
	
	abstract function setValue($value);
	
	abstract function getKey();
	
	abstract function getValue();
	
	abstract function setAttribute($attribute);

	abstract function addClass($class);
}
