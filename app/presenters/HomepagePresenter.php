<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Crawler as crawler;
use Tracy\Debugger;
use \App\Model\crawlerModel;

Debugger::enable(Debugger::DEVELOPMENT);

class HomepagePresenter extends BasePresenter {

    private $database;
    private $crawlerModel;

    public function __construct(Nette\Database\Context $database, crawlerModel $crawlerModel) {
	$this->database = $database;
	$this->crawlerModel = $crawlerModel;
    }

    public function renderDefault() {
	$this->template->anyVariable = 'any value';
    }
    
    public function renderIndex(){
	//$path = "\\\\sjabcz-vyv-fs2\\cae\\04_Knowledge-base\\testReindex";
	$path = "c:\\\\Users\\Lubos\\Documents\\testLanguage";
        $location = "testLocation";
	$domain = new crawler\domainClass($path, $location, $this->crawlerModel);

	$this->template->log = $domain->getLog();
	$this->template->directories = $domain->getNumOfDirs();
	$this->template->files = $domain->getNumOfFiles();
    }
    
    public function renderSearch(){
	$this->template->testVar = "vyv";
    }

}
