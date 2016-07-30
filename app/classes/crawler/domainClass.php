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
    private $itemCounter = 0;
    private $flagPath = "";

    public function __construct($path, $location, crawlerModel $keywordModel, $flagPath = "") {

        $this->insertToLog("Indexing started");
        $this->model = $keywordModel;
        $this->path = $path;
        $this->location = $location;
        $this->flagPath = $flagPath;
        $this->id = $this->model->insertDomain($path, $location,$this->flagPath);
        $this->checkDeadLinks();
        $this->crawl($this->path);
        $this->insertToLog("Indexing finished");
    }

    public function reIndex() {
        $this->insertToLog("Reindexing started");
        $this->checkDeadLinks();
        $this->crawl($this->path);
        $this->insertToLog("Reindexing finished");
    }

    public function checkDeadLinks() {
        $links = $this->model->getAllLinksForDomain($this);
        foreach ($links as $link) {
            if (!$this->pathExists($link->path)) {

                $this->model->removeLink($link->linkId);
                $this->insertToLog("link " . $link->path . " no longer exist and was removed");
            }
        }
    }

    private function crawl($path) {
        $this->level++;
        $dirHandler = opendir($path);
        if ($dirHandler !== false) {

            while ((($file = readdir($dirHandler)) !== false) && file_exists($path)) {
                $filePath = $path . "\\" . $file;
                $this->itemCounter++;
                if (!in_array($file, $this->ignoreArray)) {
                    if (file_exists($filePath)) {
                        if (mb_detect_encoding($filePath) <> "UTF-8") {
                            $filePath = utf8_encode($filePath);
                        }
                        if (is_Dir($filePath)) {
                            $this->numOfDirs++;
                            $dirClass = new directoryClass($filePath, $this->level, $this, $this->model);
                            $this->insertToLog($this->itemCounter . ". Directory " . $filePath . " indexed");
                            $this->crawl($filePath);
                        } else {
                            $this->numOfFiles++;
                            $fileClass = new fileClass($filePath, $this->level, $this, $this->model);
                            $this->insertToLog($this->itemCounter . ". File " . $filePath . " indexed");
                        }
                    } else {
                        $this->insertToLog("Cannot access " . $filePath);
                    }
                }
            }
        } else {
            //directory not found
        }
        $this->level--;
    }

    private function pathExists($path) {
        if (file_exists($path)) {
            return true;
        } elseif (file_exists(iconv('utf-8', 'cp1252', $path))) {
            return true;
        } else {
            return false;
        }
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

    public function insertToLog($text) {
        $this->log .= date('d/m/Y h:i:s a', time()) . " " . $text . " \r\n";
    }

    function getLog() {
        return $this->log;
    }

}
