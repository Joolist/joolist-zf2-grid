# joolist-zf2-grid
Version 0.0.1 Created by Nguyen Tien Minh and the Joolist team

## Introduction
Joolist ZF2 Grid provides a suite of common classes used across several ZF2 modules.
You probably don't need to install this module unless either A) you are
installing a module which have any Grid that depends on joolist-zf2-grid.

## Requirements
* Zend Framework 2
* Bootstrap theme

## Installation

#### By cloning project
Simply clone this project into your `./vendor/` directory and enable it in your
`./config/application.config.php` file.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "joolist/joolist-zf2-grid": "dev-master"
    }
    ```

2. Now tell composer to download joolist-zf2-grid by running the command:

    ```bash
    $ php composer.phar update
    ```


## How to use

#### 1. Set up Global Adapter for Zend Framework 2
follow: http://framework.zend.com/manual/2.1/en/tutorials/tutorial.dbadapter.html

#### 2. Load ServiceConfig
in Module.php of your module load Service Config like this:

    
     public function getServiceConfig() {
         return array(
             'factories' => array(
                 'Application\Model\Table\AlbumTable' =>  function($sm) {
                     $tableGateway = $sm->get('AlbumTableGateway');
                     $table = new AlbumTable($tableGateway);
                     return $table;
                 },
                 'AlbumTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new Album();
                     return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
    

#### 3. Create a Model extends BaseModel like this:

    
	<?php
		namespace Application\Model;
		
		use JoolistGrid\Model\BaseModel;
		
		class Album extends BaseModel {
	
	}
    

#### 4. Create Model Table extends BaseTable like this: 

    
	<?php
	namespace Application\Model\Table;
	
	use Zend\Db\TableGateway\TableGateway;
	use JoolistGrid\Model\Table\BaseTable;
	use Zend\Db\Sql\TableIdentifier;
	use Zend\Db\Sql\Select;
	
	class AlbumTable extends BaseTable {
	    protected $_name = 'album';
	    
	    public function getSelectedGrid() {
	        $select = new Select();
	        $select->from('album')->join(array('cat' => 'categories'), 'cat.id = album.category_id', 
	                                array(
	                                    'cat_id' => 'id',
	                                    'cat_title' => 'title',
	                                ))
	                                ->columns(array(
	                                    'id' => 'id',
	                                    'title'=> 'title',
	                                    'artist' => 'artist',
	                                ));
	       return $select;
	    }
	}
    


#### 5. Create a Grid like this:

    
	<?php
	namespace Application\Grid;
	
	use JoolistGrid\JoolistGrid;
	use JoolistGrid\Grid\Collection\CollectionColumn;
	use JoolistGrid\Grid\GridColumn;
	use JoolistGrid\Grid\Column\Content\ColumnContentTemplate;
	use JoolistGrid\Grid\Column\Content\ColumnContentCallBack;
	
	class GridAlbum extends JoolistGrid {
	    public function init() {
	        $collectionColumn = new CollectionColumn();
	
	        $idColumn = new GridColumn('id');
	        $idColumn->setFilter('Combobox');
	        $idColumn->addAttribute(array('class' => 'center', 'width' => '4%'));
	
	        $catId = new GridColumn('cat_id');
	        $catId->setFilter('Combobox');
	        $catId->setLabel('Category Id');
	
	        $cat = new GridColumn('cat_title');
	        $cat->setFilter('Combobox');
	        $cat->setLabel('Category');
	
	        $titleColumn = new GridColumn('title');
	        $titleColumn->setFilter('Text');
	        $titleColumn->setLabel('Title');
	
	        $artistColumn = new GridColumn('artist');
	        $artistColumn->setFilter('Text');
	        $artistColumn->setLabel('Artist');
	
	        $linkDeleteColumn = new GridColumn();
	        $linkDeleteColumn->setLabel('Delete');
	        $linkDelete = '<a href="/admin/contact/delete/id/${id}" class="must-confirm"><i class="icon-remove"></i></a>';
	        $linkDeleteColumn->setContent(new ColumnContentTemplate($linkDelete));
	        $linkDeleteColumn->addAttribute(array('class' => 'center', 'width' => '4%'));
	
	        $collectionColumn->addColumns(
	                  array(
	                        $idColumn,
	                        $catId,
	                        $cat,
	                        $titleColumn,
	                        $artistColumn,
	                        $linkDeleteColumn
	                       ));
	
	        $this->setCollectionColumn($collectionColumn);
	    }
	}
    
	
#### 6. In Your controller 
 Load model table, grid like this: 
 
    
    public function indexAction() {
        $sm = $this->getServiceLocator();
        $albumTable = $sm->get('Application\Model\Table\AlbumTable');

        $select = $albumTable->getSelectedGrid();

        $grid = new GridAlbum('boat-grid', $this->getRequest(), $sm);
        $selectGrid = $grid->getGridQuery($select);
        $grid->setCaption('Bateaux');
        $grid->setSource(new ZendDbSelect($this->_dbAdapter, $selectGrid));
        $grid->setEnableCookie();
        $grid->setView(new GridView());
        $grid->deploy();

        return new ViewModel( array('grid' => $grid));
    }
    
#### 7. In your view
 Render the grid:
    `
	<?php echo $grid->getView()->getHtml(); ?>
    `
