<?php

namespace JoolistGrid;

use JoolistGrid\Grid\Collection\CollectionColumn;
use JoolistGrid\Grid\GridColumn;
use JoolistGrid\Grid\GridSource;
use JoolistGrid\Grid\Collection\CollectionFilter;

use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Debug\Debug;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class JoolistGrid {
    /**
     * @var Base Path
     */
    public static $_basePath;
    protected $_dbAdapter;
    

    //PROCCESS METHOD
    const PROCESS_METHOD_CLASSIC = 1;
    const PROCESS_METHOD_AJAX    = 0;

	private $_source;
	private $_collectionColumn;
	private $_collectionFilter;

	private $_data;

	private $_router; // Zend\Mvc\Router\Http\RouteMatch
    private $_request; // Zend\Http\Request
	private $_baseUrl;

	private $_name;
	private $_caption;

	private $_orderBy;
	private $_orderColumn;
	private $_page;
	private $_linePerPage;
	private $_startPage;
	private $_endPage;

	private $_columnDisplayMap;

	//DEFAULT PAGINATOR PARAMS
	private $_linePerPageDefault = 10;
	private $_orderByDefault 	 = "desc";
	private $_orderColumnDefault = null;
	private $_pageDefault   	 = 1;

	//VIEWS
    private $_view;

	private $_checkboxVisiable  = true;
	private $_rankVisiable      = true;
	private $_paginatorVisiable = true;

    //Translator
	private $_translator;
	private static $_translatorDefault;

	//FOR DEVELOPMENT
    private $_disableCookie = true;

	private $_processMethod = self::PROCESS_METHOD_AJAX;


	/**
	 * Init request params
	 *
	 * @param String $name
	 * @return void
	 */
	public function __construct($name, $request, ServiceLocatorInterface $sm) {
		$this->setName($name);
		$this->setRouter($router);
        $this->setRequest($request);

		//init request param;
		$this->setLinePerPage($_POST["grid-line-per-page"])
             ->setPage($_POST["grid-page"])
             ->setOrderBy($_POST["grid-order-by"])
             ->setOrderColumn($_POST["grid-order-column"]);
             
             if(isset($_POST['grid-column-display-map'])) {
                 $this->setColumnDisplayMap($_POST['grid-column-display-map']);
             }

		$this->init();
        $this->_dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $renderer = $sm->get('Zend\View\Renderer\RendererInterface');
        self::$_basePath = $renderer->basePath();
	}

	public function init(){}

	/**
	 * Set request params to source, collectionColumn
	 * Init filter, view
	 *
	 * @return void
	 */
	public function deploy() {
		if(!$this->_paginatorVisiable) {
		    $this->setLinePerPage(0);
		}

		$this->_source->setLinePerPage($this->getLinePerPage());
		$this->_source->setPage($this->getPage());
		$this->_source->setOrderBy($this->getOrderBy());

		//ORDER COLUMN
		$this->_source->setOrderColumn($this->getOrderColumnKey());

		//SORT OR SHOW/HIDE COLUMN
		if(!$this->_disableCookie) {
    		$this->processColumnCollection();
		}

		//INIT FILTER
		$this->initFilter();

		//INIT VIEW
		if($view = $this->getView()) {
            $view->setGrid($this);

            // Ajax request
    		if($this->getRequest()->isXmlHttpRequest()) {
                echo $view->getGridContent();die;
    		}
		}
	}

	/**
	 * Set sort and display column map to cookie
	 *
	 * @return void
	 */
	public function processColumnCollection() {
	    $collectionColumn = $this->getCollectionColumn();

		//SORT COLUMN DISPLAY
		$collectionColumnArray = $collectionColumn->getCollection();
		$columnMapCookieObj = $this->getRequest()->getCookie();
        $columnMapCookieString = $columnMapCookieObj[$collectionColumn->getName()];

		if($columnMapCookieString) {
			$columnMapCookie = Json::decode($columnMapCookieString, Json::TYPE_OBJECT);

			foreach ($columnMapCookie as $position => $value) {
				if(isset($collectionColumnArray[$position])
						&& $collectionColumnArray[$position] instanceof GridColumn)
				{
					$collectionColumnNew[$position] = clone $collectionColumnArray[$position];
				}
			}

			$collectionColumn->setDisplayMap($columnMapCookie);
			$collectionColumn->setCollection($collectionColumnNew);

		} else {
			//SET COLUMN DISPLAY MAP TO COOKIE
			$displayMap = $collectionColumn->getDisplayMap();

			$columnMapString = '{';
			foreach ($displayMap as $key => $value) {
				$columnMapString .= ($columnMapString != "{") ? (',"'. $key .'":' . $value) :  ('"'.$key . '":' . $value);
			}
			$columnMapString .= '}';

			setcookie($this->getName(), $columnMapString);
		}
	}

	/**
	 * Init and add filter to collection filter
	 *
	 * @return void
	 */
	public function initFilter() {
	    $collectionColumn = $this->getCollectionColumn();


		$collection = $collectionColumn->getCollection();

		if(!$collection) {
			// throw new Zend_Exception("Collection Column is not null");
		}

		$collectionFilter = new CollectionFilter();

		if($this->_rankVisiable) {
    		$collectionFilter->addFilter(null);
		}

		$displayMap = $collectionColumn->getDisplayMap();
		foreach ($collection as $key => $column) {
			if($displayMap->$key) {
				$collectionFilter->addFilter($column->getFilter());
			}
		}

		if($this->_checkboxVisiable) {
    		$collectionFilter->addFilter(null);
		}

		$collectionFilter->filters($this->getSource());

		$this->setCollectionFilter($collectionFilter);
	}

//SETER

	/**
	 * @param String $name
	 * @return JoolistGrid $this
	 */
	public function setName($name) {
	    $name = trim($name);
	    $name = str_replace(' ', '-', $name);
	    $name = strtolower($name);

		$this->_name = $name;

		return $this;
	}

	/**
	 * @param String $caption
	 * @return JoolistGrid $this
	 */
	public function setCaption($caption) {
		$this->_caption = $caption;

		return $this;
	}

	/**
	 * @param const $processMethod
	 * @return JoolistGrid $this
	 */
	public function setProcessMethod($processMethod) {
		$this->_processMethod = $processMethod;

		return $this;
	}

	/**
	 * @param JoolistGrid_Collection_Column $collectionColumn
	 * @return JoolistGrid $this
	 */
	public function setCollectionColumn(CollectionColumn $collectionColumn) {
		if(!$collectionColumn instanceof CollectionColumn) {
			throw new Zend_Exception("CollectionColumn in not instacneof Grid_CollectionColumn");
		}
		$this->_collectionColumn = $collectionColumn;
		$this->_collectionColumn->setName($this->_name);

		return $this;
	}

	/**
	 * @param JoolistGrid_Collection_Filter $collectionFilter
	 * @return JoolistGrid $this
	 */
	public function setCollectionFilter(CollectionFilter $collectionFilter) {
		if(!$collectionFilter instanceof CollectionFilter) {
			throw new Zend_Exception("Collection Filter in not instanceof JoolistGrid_Collection_Filter");
		}
		$this->_collectionFilter = $collectionFilter;

		return $this;
	}

	/**
	 * @param JoolistGrid_Source $source
	 * @return JoolistGrid $this
	 */
	public function setSource(GridSource $source) {
		if(!$source instanceof GridSource) {
			throw new Zend_Exception("Source in not instacneof JoolistGrid_Source");
		}
		$this->_source = $source;

		return $this;
	}

	/**
	 * @param JoolistGrid_View $view
	 * @return JoolistGrid $this
	 */
	public function setView($view) {
        $view->setGrid($this);
	    $this->_view = $view;

	    return $this;
	}

	/**
	 * @return JoolistGrid $this
	 */
	public function setDisableCheckbox() {
	    $this->_checkboxVisiable = false;
	    if($this->_collectionColumn instanceof CollectionColumn) {
	        $this->_collectionColumn->setDisableCheckbox();
	    }

	    return $this;
	}

	/**
	 * @return JoolistGrid $this
	 */
	public function setDisableRank() {
	    $this->_rankVisiable = false;
	    if($this->_collectionColumn instanceof CollectionColumn) {
	        $this->_collectionColumn->setDisableRank();
	    }

	    return $this;
	}

	/**
	 * @return JoolistGrid $this
	 */
	public function setDisablePaginator() {
	    $this->_paginatorVisiable = false;

	    return $this;
	}

    /**
     * @return void
     */
    public function setDisableCookie() {
        $this->_disableCookie = true;
    }

    /**
     * @return void
     */
    public function setEnableCookie() {
        $this->_disableCookie = false;
    }

	/**
	 * @param $request
	 * @return JoolistGrid
	 */
	public function setRequest($request) {
	    $this->_request = $request;

	    return $this;
	}

    /**
     * @param $request
     * @return JoolistGrid
     */
    public function setRouter($router) {
        $this->_router = $router;

        return $this;
    }

	/**
	 * @param String $columnDisplayMap
	 * @return JoolistGrid
	 */
	public function setColumnDisplayMap($columnDisplayMap) {
		$this->_columnDisplayMap = $columnDisplayMap;

		return $this;
	}

	/**
	 * @param String $orderBy : asc|desc
	 * @return JoolistGrid
	 */
	public function setOrderBy($orderBy) {
		$this->_orderBy = $orderBy == "desc" ? "desc" : "asc";

		return $this;
	}

	/**
	 * @param int $orderColumn
	 * @return JoolistGrid
	 */
	public function setOrderColumn($orderColumn) {
		$this->_orderColumn = $orderColumn != null ? intval($orderColumn) : $this->_orderColumnDefault;

		return $this;
	}

	/**
	 * @param int $linePerPage
	 * @return JoolistGrid
	 */
	public function setLinePerPage($linePerPage) {
		$this->_linePerPage = intval(isset($linePerPage) ? $linePerPage : $this->_linePerPageDefault);

		return $this;
	}

	/**
	 * Set curent page
	 *
	 * @param int $page
	 * @return JoolistGrid
	 */
	public function setPage($page) {
	    $page = intval($page);
		$this->_page = $page != 0 ? $page : $this->_pageDefault;

		return $this;
	}


//GETER

	/**
	 * @return CollectionColumn $_collectionColumn
	 */
	public function getCollectionColumn() {
		return $this->_collectionColumn;
	}

	/**
	 * @return CollectionFilter $_collectionFilter
	 */
	public function getCollectionFilter() {
		return $this->_collectionFilter;
	}

	/**
	 * @return GridSource $_source
	 */
	public function getSource() {
		return $this->_source;
	}

	/**
	 * @return the $_request
	 */
	public function getRequest() {
		return $this->_request;
	}

    /**
     * @return the $_request
     */
    public function getRouter() {
        return $this->_router;
    }

	/**
	 * @return JoolistGrid_View $_view
	 */
	public function getView() {
	    return $this->_view;
	}

	 /**
	 * @return the $_name
	 */
	public function getName() {
		return $this->_name;
	}

	 /**
	 * @return the $_caption
	 */
	public function getCaption() {
	    return $this->_caption;
	}

	/**
	 * @return the $_orderBy
	 */
	public function getOrderBy() {
		return $this->_orderBy;
	}

	/**
	 * @return the $_orderColumn
	 */
	public function getOrderColumn() {
		return $this->_orderColumn;
	}

	/**
	 * @return String orderColumnKey
	 */
	public function getOrderColumnKey() {
	    $orderColumn = $this->getOrderColumn();

		$collectionColumn = $this->getCollectionColumn()->getCollection();
		$orderColumn = isset($collectionColumn[$orderColumn])
					   ? $collectionColumn[$orderColumn]
					   : $collectionColumn[0] ;

		return $orderColumn->getKey();
	}

	/**
	 * @return the $_page
	 */
	public function getPage() {
		return $this->_page;
	}

	/**
	 * @return the $_linePerPage
	 */
	public function getLinePerPage() {
		return $this->_linePerPage;
	}

	/**
	 * @return the $_processMethod
	 */
	public function getProcessMethod() {
		return $this->_processMethod;
	}

	/**
	 * @return the $_baseUrl
	 */
	public function getBaseUrl() {
		return $this->_baseUrl;
	}

	/**
	 * @return the $_paginatorVisable
	 */
	public function getPaginatorVisiable() {
	    return $this->_paginatorVisiable;
	}


// Localization:
// See: Zend_Form

	/**
	 * Set translator object
	 *
	 * @param  Zend_Translate|Zend_Translate_Adapter|null $translator
	 * @return JoolistGrid
	 */
	public function setTranslator($translator = null) {
	    if (null === $translator) {
	        $this->_translator = null;
	    } elseif ($translator instanceof Zend_Translate_Adapter) {
	        $this->_translator = $translator;
	    } elseif ($translator instanceof Zend_Translate) {
	        $this->_translator = $translator->getAdapter();
	    } else {
	        throw new Zend_Exception('Invalid translator specified');
	    }

	    return $this;
	}

	/**
	 * Retrieve translator object
	 *
	 * @return Zend\Translate|null
	 */
	public function getTranslator() {
	    if (null === $this->_translator) {
	        return self::getDefaultTranslator();
	    }

	    return $this->_translator;
	}

	/**
	 * Get global default translator object
	 *
	 * @return null|Zend\Translate
	 */
	public static function getDefaultTranslator() {
	    // if (null === self::$_translatorDefault) {
	        // require_once 'Zend/Registry.php';
	        // if (Zend_Registry::isRegistered('Zend_Translate')) {
	            // $translator = Zend_Registry::get('Zend_Translate');
	            // if ($translator instanceof Zend_Translate_Adapter) {
	                // return $translator;
	            // } elseif ($translator instanceof Zend_Translate) {
	                // return $translator->getAdapter();
	            // }
	        // }
	    // }
	    return self::$_translatorDefault;
	}

    public function getGridQuery(Select $select) {
        $sql = new Sql($this->_dbAdapter);
        return $sql->select()->from(array('data' => $select));
    }
    
    public static function getBasePath() {
        return self::$_basePath;
    }
}