<?php

namespace JoolistGrid\Grid;

use JoolistGrid\JoolistGrid;
use JoolistGrid\Grid\View\ViewHeader;
use JoolistGrid\Grid\View\ViewFilter;
use JoolistGrid\Grid\View\ViewPaginator;
use JoolistGrid\Grid\View\ViewBody;

class GridView {

	private $_grid;
	private $_collectionColumn;
	private $_collectionFilter;
	private $_source;
	private $_data;
	private $_page;
	private $_linePerPage;
	private $_linePerPageOptions = array(5  => 5,
										 10 => 10,
										 20 => 20,
										 50 => 50,
										 100 => 100,
										 200 => 200,
										 500 => 500,
										 null => "All");

	private $_translator;

	public function setGrid(JoolistGrid $grid) {
		if(!$grid instanceof JoolistGrid) {
			throw new Zend_Exception("");
		}
		$this->_grid = $grid;

		$this->initParams();
	}

	public function initParams() {
		$grid = $this->_grid;
		$this->_collectionColumn = $grid->getCollectionColumn();
		$this->_collectionFilter = $grid->getCollectionFilter();
		$this->_source 		  	 = $grid->getSource();
		$this->_data			 = $this->_source->getData();
		$this->_page 			 = $grid->getPage();
		$this->_linePerPage 	 = $grid->getLinePerPage();
        $this->_translator       = $grid->getTranslator();
	}

	public function getHtml() {
	    $html  = '<form class="grid-form" method="post">';
	    $html .= '<div class="grid-container" id="'. $this->_grid->getName() .'-container">';
	    $html .= '<h3 class="table-caption">'. $this->_grid->getCaption() .'</h3>';
	    $html .= '<div class="clear"></div>';
	    $html .= '<div class="grid-inner-wrapper">';
		$html .= $this->getStyleSheet();
		$html .= $this->getJavascript();

		$html .= '<div  class="form-element-head">';
		if($this->_grid->getPaginatorVisiable()) {
    		$html .= $this->getLinePerPageOption();
		}

		$html .= "<input type='button' value='Apply Filter' id='applyFilter' class='btn btn-primary' style='margin-right:5px'>";
		$html .= "<input type='button' value='Clear Filter' id='clearFilter' class='btn' style='margin-right:5px'>";
		$html .= $this->getColumnDisplayMap();
		$html .= '</div>';
		$html .= '<div id="grid-content">' . $this->getGridContent() .'</div>';
		$html .= '</div></div></form>';

		return $html;
	}

	public function getGridContent() {
	    $gridContent  = $this->getHeader();
	    $gridContent .= $this->getFilter();
	    $gridContent .= $this->getBody();
	    $gridContent .= $this->getPaginator();
	    $gridContent .= $this->getHtmlInputHidden();
	    $gridContent .= $this->getScriptCheckAll();

	    return $gridContent;
	}

	public function getColumnDisplayMap() {
		$html  = '<script>';
		$html .= '$(function() { $(".grid-column-map-sort").sortable(); });';
		$html .= '</script>';
		$html .= '<div class="grid-div-column-map">';
		$html .= '<a href="javascript:void(0)" class="btn btn-danger" id="grid-column-map-dropdown-toggle"><i class="column-map-toggle glyphicon glyphicon-chevron-down"></i></a>';
		$html .= '<ul id="grid-column-map" data-cookie="'. $this->_collectionColumn->getName() .'">';
		$html .= '<li><span>Tích vào ô checkbox để chọn hiển thị/ẩn - Kéo thả cột để sắp xếp thứ tự hiển thị.</span></li>';
		$html .= '<li><input type="button" class="btn btn-primary" id="grid-button-save-column-map" value="Save"></li>';
		$html .= '<li>';
		$html .= '<ul class="grid-column-map-sort">';
		$displayMap = $this->_collectionColumn->getDisplayMap();

		$columnCoolection = $this->_collectionColumn->getCollection();

		foreach ($displayMap as $key => $value) {
			$column = $columnCoolection[$key];

			$html .= '<li>';
			$html .= '<input type="checkbox" name="grid-column-display[]"
					  		 class="column-display" '. ($value==1? " checked='checked' " : "") .'
							 value="'. intval($key) .'"
							 data-display-value="'. intval($value) .'">';

			$html .= '<span class="column-map-label">'. $column->getLabel() . '</span>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</li>';
		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}

	public function getHeader() {
		$viewHeader = new ViewHeader();
        $viewHeader->setTranslator($this->_translator);

		$headerParams = array($viewHeader::PARAM_COLUMN_COLLECTION => $this->_collectionColumn);

		$viewHeader->setParams($headerParams);

		return $viewHeader->getHtml();
	}

	public function getBody() {
		$viewBody = new ViewBody();

		$viewBody->setParams(array(
					$viewBody::PARAM_DATA 			   => $this->_data,
					$viewBody::PARAM_COLUMN_COLLECTION => $this->_collectionColumn,
					$viewBody::PARAM_PAGE 	 	 	   => $this->_page,
					$viewBody::PARAM_LINE_PER_PAGE     => $this->_linePerPage)
				);

		return $viewBody->getHtml();
	}

	public function getFilter() {
		$viewFilter = new ViewFilter();

		$viewFilter->setParams(array(
						$viewFilter::PARAM_DATA_SOURCE 	  	 => $this->_source,
						$viewFilter::PARAM_COLLECTION_FILTER => $this->_collectionFilter,
						$viewFilter::PARAM_COLLECTION_COLUMN => $this->_collectionColumn)
					);

		return $viewFilter->getHtml();
	}

	public function getPaginator() {
		$viewPaginator = new ViewPaginator();

		$viewPaginator->setParams(array(
					$viewPaginator::PARAM_SOURCE 	    => $this->_source,
					$viewPaginator::PARAM_PAGE 	 	 	=> $this->_page,
					$viewPaginator::PARAM_LINE_PER_PAGE => $this->_linePerPage)
				);

		if(!$viewPaginator->getHtml()) {
		   return null;
		}

		return "<div class='paginator'>". $viewPaginator->getHtml() ."</div><div class='clear'></div>";
	}

	public function getLinePerPageOption() {
		$html = '<select id="grid-line-per-page" name="grid-line-per-page" class="input-mini" style="height:25px;margin-right:5px">';

		foreach ($this->_linePerPageOptions as $key => $value) {
			if($this->_linePerPage == $key) {
				$html .= "<option value='". $key ."' selected='selected'>";
			} else {
				$html .= "<option value='". $key ."'>";
			}
			$html .= $value;
			$html .= "</option>";
		}
		$html .= "</select>";

		return $html;
	}

	public function getHtmlInputHidden() {
		$html  = "<input type='hidden' value='". $this->_grid->getOrderBy() ."' 	 name='grid-order-by' 		id='grid-order-by'>";
		$html .= "<input type='hidden' value='". $this->_grid->getorderColumn() ."'  name='grid-order-column' 	id='grid-order-column'>";
		$html .= "<input type='hidden' value='". $this->_page ."' 		 			 name='grid-page' 			id='grid-page'>";

		return $html;
	}

	public function getJavascript() {
	    $js = '<script src="'.JoolistGrid::getBasePath().'/common/js/jquery.cookie.js"></script>';

		$js .= "<script type='text/javascript'>";

		$js .= "$(function() {
					function submitFormAjax() {
						$.ajax({
							url : '". $this->_grid->getBaseUrl() ."',
							type: 'POST',
							data: $('.grid-form').serialize(),
							success :  function(html) {
								$('#grid-content').html('');
								$('#grid-content').html(html);
							},
							error : function(html) {
								alert('ERROR: submit form ajax');
								console.log(html);
							}
						})
					}

					$('.order-able').live('click', function(e) {
						orderColumn 	   = $(this).attr('data-order-column');
						orderColumnCurrent = $('#grid-order-column').val();
						orderBy 		   = $('#grid-order-by').val();

						if(orderColumn !== orderColumnCurrent) {
							$('#grid-order-column').val(orderColumn);
							$('#grid-order-by').val('asc');
						} else {
							$('#grid-order-by').val(orderBy === 'desc' ? 'asc' : 'desc');
						}

						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});

					$('#grid-line-per-page').live('change', function() {
						$('#grid-page').val(1);
						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});

					$('#clearFilter').bind('click', function() {
						linePerPage = $('#grid-line-per-page').val();
						$('#grid-content').find(':input').each(function() {
							switch(this.type) {
								case 'select-one':
								case 'text':
									$(this).removeClass('grid-filtering');
									$(this).val('');
								break;
								case 'checkbox':
								case 'radio':
									$(this).removeClass('grid-filtering');
									this.checked = false;
							}
						});
						$('#grid-line-per-page').val(linePerPage);
						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});


					$('#applyFilter').bind('click', function() {
						$('#grid-page').val(1);
						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});


					$('.filter-combobox').live('change', function() {
						$('#grid-page').val(1);
						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});


					$('.grid-paginator-page').live('click', function() {
						page = $(this).attr('data-page');
						$('#grid-page').val(page);
						". ( $this->_grid->getProcessMethod() == JoolistGrid::PROCESS_METHOD_CLASSIC
								? "$('.grid-form').submit();"
								: "submitFormAjax();" ) ."
					});

					function createCookie(name, value, days) {
						if(days) {
							var date = new Date();
							date.setTime(date.getTime()+(days*24*60*60*1000));
							var expires = '; expires='+date.toGMTString();
						}
						else var expires = '';
					    $.cookie(name, value);
					}

					$('.column-display').live('click', function() {
						$(this).attr('data-display-value', $(this).attr('data-display-value') == 0 ? 1 : 0) ;
					});

					$('#grid-button-save-column-map').live('click', function() {
						var cookie = '{';
						$.each($('input[name=". '"grid-column-display[]"' ."]'), function() {
							position = $(this).val();
							displayValue = $(this).attr('data-display-value');
							cookie += (cookie != '{') ? (',". '"' ."'" . " + position +"  ."'". '"' .":'+ displayValue) : ('". '"' ."' + position + '". '"' .":' + displayValue) ;
						});
						cookie += '}';
						cookieName = $('#grid-column-map').attr('data-cookie');
						createCookie(cookieName, cookie);
						submitFormAjax();

						$('#grid-column-map').hide();
						$('.column-map-toggle').removeClass('icon-chevron-up');
						$('.column-map-toggle').addClass('icon-chevron-down');
					});

					$('.filter-input ').live('keydown', function(e){
						key = e.keyCode || e.which;
						if(key == 13) {
							submitFormAjax();
						}
					});

					$('#grid-column-map-dropdown-toggle').live('click', function() {
						if($('.column-map-toggle').hasClass('icon-chevron-down')) {
							$('#grid-column-map').show();
							$('.column-map-toggle').removeClass('icon-chevron-down');
							$('.column-map-toggle').addClass('icon-chevron-up');
						} else {
							$('#grid-column-map').hide();
							$('.column-map-toggle').removeClass('icon-chevron-up');
							$('.column-map-toggle').addClass('icon-chevron-down');
						}
					});
				});";

		$js .= "</script>";

		return $js;
	}

	public function getScriptCheckAll() {
        return "<script type='text/javascript'>
	                // CHECKBOX
                    var big_check = $('.check-all'); // Big Checkbox
                    var small_check = $('input[name=\"item_checker[]\"]'); // Small Checkbox Items
                    var rows_class = 'tr-active'; // BG Rows Class
                    big_check.change(function(){
                        var status = $(this).attr('checked');
                        if(status){small_check.attr('checked',status).parent().parent().addClass(rows_class);} else {small_check.removeAttr('checked').parent().parent().removeClass(rows_class);}
                    });

                    // CHANGE BG ROWS ACTIVE
                    small_check.change(function(){
                        if ($(this).attr('checked')) {
                            $(this).parent().parent().addClass(rows_class);
                        } else {
                            big_check.removeAttr('checked');
                            $(this).parent().parent().removeClass(rows_class);
                        }
                    });
                </script>";
	}

	public function getStyleSheet() {
	    return '<style type="text/css">
                    body {font-size: 12px;background-color: #fff;}
                    table thead {background-color: #777;font-weight: bold;}
                    table thead tr td a {color: #fff;}
                    .table {font-size: 12px;}
                    .colection-filter {padding: 20px;}
                    .form-element-head {width: 100%;height: 30px;}

                    .grid-paginator {margin-top: 20px;text-align: left;}
                    .grid-paginator li {list-style: none;display: inline-block;}
                    .grid-paginator li a {display: block;border: 1px solid #ddd;padding: 2px 5px 2px 5px;margin: 0px 2px 0px 2px;text-decoration: none;}
                    .grid-paginator .paginator-active {background-color: #ddd;}
                    #applyFilter, #clearFilter, #grid-line-per-page{float: left;}
                    .filter-collection {background-color: #ddd;height: 20px;}
                    .filter-collection td input,select {margin-bottom: 0px;}
                    .filter-input {width: 120px;height: auto;}
                    .filter-range {width: 80px;}
                    .filter-combobox {width: 80px;}
                    .grid-filtering {border: 1px solid red;}
                    .grid-div-column-map {min-width:220px;width:auto;float: right;position: relative;}
                    #grid-column-map {width: auto;position: absolute;margin: 30px 0px 0px -7px;padding: 5px 0px 5px 5px;display: none;border: 1px solid #ccc;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;background-color: #fff;z-index: 10000;box-shadow: 0 4px 10px; overflow: scroll; max-height: 400px}
                    #grid-column-map li {list-style: none;padding: 3px;margin: 3px 0px 3px 0px;overflow: hidden;border: 1px solid #ddd;border-bottom-right-radius: 5px;background-color: #eee;color: #000;}
                    .grid-column-map-sort {margin: 0px;}
                    .grid-column-map-sort li {cursor: move;}
                    .column-map-label {margin-left: 5px;}
                    .display-block {display: block;}
                    #grid-column-map-dropdown-toggle {float: right;}
                    #grid-button-save-column-map {float: left;margin-bottom: 10px;}
                    .dropdown-toggle-open {background-image: ;}
              </style>';
	}

	public function getGrid() {
	    return $this->_grid;
	}
}