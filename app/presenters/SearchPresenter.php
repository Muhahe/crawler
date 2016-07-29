<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Crawler as crawler;
use Tracy\Debugger;
use \App\Model\searchModel;
use Nette\Application\UI;

Debugger::enable(Debugger::DEVELOPMENT);

class SearchPresenter extends BasePresenter {

    private $database;
    private $searchModel;
    private $resultSet = array();
    private $resultPerPage = 10;
    private $value;
    private $page = 1;

    public function __construct(Nette\Database\Context $database, searchModel $searchModel) {
	$this->database = $database;
	$this->searchModel = $searchModel;
    }

    public function renderDefault() {
	//$this->renderResults($page);	
    }

    public function handlePagination($page, $value) {
	$this->page = $page;
	$this->value = $value;
	$this->performSearch();
    }

    protected function createComponentSearchForm() {
	$form = new UI\Form;
	$form->addText('Search');
	$form->setMethod('get');
	$form->addSubmit('send', 'Search');
	$form->setDefaults(array(
	    'Search' => $this->value,
	));
	$form->onSuccess[] = array($this, 'searchSent');
	return $form;
    }

    public function searchSent(UI\Form $form, $values) {
	$this->value = $values->Search;
	if (trim($this->value) <> "") {
	    $this->performSearch();
	}
    }

    public function performSearch($page = 1) {
	
	$this->resultSet = $this->searchModel->search($this->value, ($this->page - 1) * $this->resultPerPage, $this->resultPerPage);
	$this->renderResults();
    }

    public function renderResults() {
	$inputs = $this->resultSet;
	if (!empty($inputs)) {

	    usort($inputs, array($this, 'cmpLinkObjectsByWeight'));
	    $this->template->count = $this->searchModel->getCount();
	    $this->template->inputs = $this->resultSet;
	    $this->template->pages = $this->searchModel->getPages();
	    $this->template->value = $this->value;
	    $this->template->currentPage = $this->page;
	    $this->template->resultsPerPage = $this->resultPerPage;
	    
	} else {
	    
	}
    }

    private static function cmpLinkObjectsByWeight($a, $b) {
	return $b->getWeight() - $a->getWeight();
    }

}
