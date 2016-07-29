<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of pdfFile
 *
 * @author SukL
 */
class directoryClass {

    private $dirPath;
    private $dirContent = "";
    private $itemCounter = 0;
    private $ignoreArray = array('.', '..');

    public function __construct($dirPath) {

        $this->dirPath = $dirPath;
        $this->readDirectoryContent();
    }

    private function readDirectoryContent() {
        $this->itemCounter = 0;
        $dirHandler = opendir($this->dirPath);
        if ($dirHandler !== false) {
            while ((($file = readdir($dirHandler)) !== false) && file_exists($this->dirPath)) {
                $this->itemCounter++;
                if (!in_array($file, $this->ignoreArray)) {
                    $this->dirContent .= basename($file) . " ";
                }
            }
        }        
    }
    
    private function indexDirectory(){
        
    }

    public function getDirContent() {
        return $this->dirContent;
    }

}
