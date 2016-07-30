<?php

namespace App\Crawler;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Model;
use App\Model\crawlerModel;
use Tracy\Debugger;

/**
 * Description of commonFileClass
 *
 * @author SukL
 */
class indexClass {

    protected $path;
    protected $name;
    protected $content = "";
    protected $keywords = Array();
    protected $model;
    protected $md5Sum;
    protected $domainId;
    protected $level;
    protected $type;
    protected $id;
    protected $state;
    
    protected function extractKeywords() {

	$toReplace = Array('/', '\\', '-', '_', '', "");
	$title = str_replace($toReplace, " ", $this->path);
	$content = str_replace($toReplace, " ", $this->content);
	$wordsInContent = array_count_values(explode(" ", $content));
	$wordsInTitle = array_count_values(explode(" ", $title));
	foreach ($wordsInTitle as $key => $value) {
	    $wordsInTitle[$key] = $value * 5;
	}

	$this->keywords = array_merge($wordsInContent, $wordsInTitle);
    }

    protected function fillKeywordsToDatabase() {
	foreach ($this->keywords as $key => $value) {
	    $this->model->insertKeyword($key, $this->id, md5($key), $value);
	}
    }

    protected function checkRecordExists() {
	$result = $this->model->pathExists($this->path);

	if ($result == false) {
	    $this->id = $this->insertLink();
	    $this->state = $this->path . " was inserted into database";
	} else {
	    $this->id = $result['linkId'];
	    if ($result['md5Sum'] != $this->md5Sum) {
		$this->model->updatelink($this);
		$this->state = $this->path . " was updated in database";
	    }else{
		$this->state = $this->path . " wasnt changed";
	    }
	}
    }

    protected function pathExists() {
	return file_exists($this->path);
    }

    protected function insertLink() {
	$date = date('Y-m-d');
	$this->id = $this->model->insertLink($this->path, $this->name, $this->content, $date, $this->md5Sum, $this->level, $this->type, $this->domainId);

	if ($this->id !== false) {
	    $this->extractKeywords();
	    $this->fillKeywordsToDatabase();
	}
    }

    public function getType() {
	return $this->type;
    }

    public function getMd5Sum() {
	return $this->md5Sum;
    }

    public function getPath() {
	return $this->path;
    }

    public function getName() {
	return $this->name;
    }

    public function getContent() {
	return $this->content;
    }

    public function getDomainId() {
	return $this->domainId;
    }

    public function getId() {
	return $this->id;
    }

    public function getLevel() {
	return $this->level;
    }

    function getState() {
	return $this->state;
    }

}
