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
use App\Crawler;

/**
 * Description of domainClass
 *
 * @author SukL
 */
class domainClass {

    private $path;
    private $location;
    private $id;
    private $model;
    private $ignoreArray = array('.', '..');
    private $level;
    private $numOfDirs = 0;
    private $numOfFiles = 0;
    private $log = "";

    public function __construct($path, $location, crawlerModel $keywordModel) {
        
        
        $this->model = $keywordModel;
        $this->path = $path;
        $this->location = $location;
        $this->id = $this->model->insertDomain($path, $location);
        $this->checkDeadLinks();
        $this->crawl($this->path);
        Debugger::dump("indexing finished");
    }

    public function reIndex() {
        $this->checkDeadLinks();
        $this->crawl($this->path);
    }

    public function checkDeadLinks() {
        $links = $this->model->getAllLinksForDomain($this);
        foreach ($links as $link) {
            if (!$this->pathExists($link->path)) {

                $this->model->removeLink($link->linkId);
            } else {
                
            }
        }
    }

    private function crawl($path) {
        $this->level++;
        $dirHandler = opendir($path);
        if ($dirHandler !== false) {

            while ((($file = readdir($dirHandler)) !== false) && file_exists($path)) {
                if (!in_array($file, $this->ignoreArray)) {
                    if (file_exists($path . "\\" . $file)) {
                        if (is_Dir($path . "\\" . $file)) {
                            Debugger::dump("dir");
                            Debugger::dump($path . "\\" . $file);
                            $this->numOfDirs++;
                            $dirClass = new directoryClass($path . "\\" . $file, $this->level, $this, $this->model);
                            $this->log .= $dirClass->getState();
                            $this->crawl($path . "\\" . $file);
                        } else {
                            Debugger::dump("file");
                            Debugger::dump($path . "\\" . $file);
                            $this->numOfFiles++;
                            $fileClass = new fileClass($path . "\\" . $file, $this->level, $this, $this->model);
                            $this->log .= $fileClass->getState();
                        }
                    }else{
                        Debugger::dump("cannot access ". $path . "\\" .$file);
                    }
                }
            }
        } else {
            //directory not found
        }
        $this->level--;
    }

    private function pathExists($path) {
        return file_exists($path);
    }

    public function getLocation() {
        return $this->location;
    }

    public function getPath() {
        return $this->path;
    }

    public function getId() {
        return $this->id;
    }

    function getNumOfDirs() {
        return $this->numOfDirs;
    }

    function getNumOfFiles() {
        return $this->numOfFiles;
    }

    function getLog() {
        return $this->log;
    }

}
