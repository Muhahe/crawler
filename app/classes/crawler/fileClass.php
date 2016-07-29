<?php

namespace App\Crawler;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Nette;
use App\Model;
use App\Model\crawlerModel;
use Tracy\Debugger;

/**
 * Description of pdfFile
 *
 * @author SukL
 */
class fileClass extends indexClass {

    private $fileExtension;
    protected $type = "file";

    public function __construct($path, $level, $domain, crawlerModel $keywordModel) {
	$this->model = $keywordModel;
	$this->level = $level;
	$this->path = $path;
	$pathParts = pathinfo($path);
        if(array_key_exists("extension",$pathParts)){
	$this->fileExtension = strtolower($pathParts['extension']);
        }else{
            $this->fileExtension = "N/A";
        }
	$this->name = $pathParts['filename'];
	$this->readFileContent();
	$this->md5Sum = md5($this->path . $this->content);
	$this->domainId = $domain->getId();
	$this->checkRecordExists();
    }

    private function readFileContent() {
	switch ($this->fileExtension) {
	    case "pdf":
		$this->readPDFContent();
		$this->type = "pdf";
		break;
	    case "txt":
		$this->readTxtContent();
		$this->type = "txt";
		break;
	    default:
		$this->content = "No content to read";
	}
    }

    private function readPDFContent() {
	if (realpath("\\\\sjabcz-vyv-bck\\cae\\output.txt")) {
	    unlink("\\\\sjabcz-vyv-bck\\cae\\output.txt");
	}

	$command = "\\\\sjabcz-vyv-bck\\cae\\tools\\pdftotext.exe -enc UTF-8 \"" . $this->path . "\" \\\\sjabcz-vyv-bck\\cae\\output.txt";

	$result = exec($command, $output, $return);

	@$text = file_get_contents("\\\\sjabcz-vyv-bck\\cae\\output.txt", FILE_TEXT);
	if ($text === FALSE) {
	    $this->content = "File content isnt avaliable. Probably because of file reading protection.";
	} else {
	    $this->content = $text;
	}
    }

    private function readTxtContent() {
	$this->content = file_get_contents($this->path);
    }

    public function getName() {
	return $this->name . $this->fileExtension;
    }

}
