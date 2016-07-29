<?php

namespace App\Crawler;

use Nette;
use App\Model;
use App\Model\crawlerModel;
use Tracy\Debugger;

/**
 * Description of pdfFile
 *
 * @author SukL
 */
class directoryClass extends indexClass {

    private $ignoreArray = array('.', '..');
    protected $type = "dir";

    public function __construct($path, $level, $domain, crawlerModel $keywordModel) {
	$this->model = $keywordModel;
	$this->path = $path;
	$pathParts = pathinfo($path);
	$this->name = $pathParts['filename'];
	$this->level = $level;
	$this->readDirectoryContent();
	$this->md5Sum = md5($this->path . $this->content);
	$this->domainId = $domain->getId();
	$this->checkRecordExists();
    }

    private function readDirectoryContent() {
	$dirHandler = opendir($this->path);
	if ($dirHandler !== false) {
	    while ((($file = readdir($dirHandler)) !== false) && file_exists($this->path)) {

		if (!in_array($file, $this->ignoreArray)) {
		    $this->content .= basename($file) . " ";
		}
	    }
	}
    }



}
