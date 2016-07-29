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
class pdfFile {
    
    private $filePath;
    private $fileContent;
    private $pdfToTextPath = "";
    
    public function __construct($filePath) {
        $this->filePath = $filePath;
    }
    
    public function setPdfToTextPath($pdfToTextPath){
        $this->pdfToTextPath = $pdfToTextPath; 
    }
    
    private function readFileContent(){
        
    }
    
    public function getContent(){
        return $this->fileContent;
    }
    //put your code here
}
