<?php

namespace JoolistGrid\Grid;


interface GridFilterable {
	
	public function like($key, $value);
	
	public function equal($key, $value);
	
	public function greater($key, $value);
	
	public function lower($key, $value);
	
	public function greaterOrEqual($key, $value);
	
	public function lowerOrEqual($key, $value);
	
}