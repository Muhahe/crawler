<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "directoryClass.php";

/**
 * Description of crawler
 *
 * @author SukL
 */
class crawler {

    private $startPath;
    private $ignoreArray = array('.', '..');

    public function __construct($startPath) {
        $this->startPath = $startPath;
        $this->crawl($this->startPath);
    }

    private function crawl($path) {
        $dirHandler = opendir($path);
        if ($dirHandler !== false) {
            
            while ((($file = readdir($dirHandler)) !== false) && file_exists($path)) {                
                if (!in_array($file, $this->ignoreArray)) {
                    $this->dirContent .= basename($file) . " ";
                    if (is_Dir($path . "/" . $file)) {
                        $thisDir = new directoryClass($path . "/" . $file);
                        $this->crawl($path . "/" . $file);
                    } else {
                        
                    }
                }
            }
        } else {
            echo "<br>wrong directory path<br>";
        }
    }

}

$testCrawler = new crawler("\\\\sjabcz-vyv-fs2\\cae\\04_Knowledge-base\\testReindex");