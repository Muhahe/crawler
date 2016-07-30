<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette;
use Nette\Database\Table;
use Tracy\Debugger;

/**
 * Description of keywordModel
 *
 * @author SukL
 */
class crawlerModel extends Nette\Object {

    private $database;

    public function __construct(Nette\Database\Context $database) {
	$this->database = $database;
	
    }

    public function insertDomain($path, $location) {
	$recordExists = $this->database->table('domain')->where('path = ?',$path)->where('location = ?', $location);
	$recordExistsCount = $recordExists->count();

	if ($recordExistsCount == 0) {
	    $row = $this->database->table('domain')->insert(array(
		'path' => $path,
		'location' => $location,
                'flagPath' => $flagPath
	    ));
	    $id = $row->id;
	    return $id;
	} else {
	    $row = $recordExists->fetch();
	    return $row->id;
	}
    }

    public function insertKeyword($keywordName, $linkId, $md5Sum, $weight) {
	$recordExists = $this->database->table('keywords')->where('keyword', $keywordName);
	//Debugger::dump();
	$insertedId;
	$recordExistsCount = $recordExists->count();
	if ($recordExistsCount == 0) {
	    
	    $row = $this->database->table('keywords')->insert(array(
		'keyword' => $keywordName
	    ));
	    $insertedId = $row->keywordId;
	} else {
	    $insertedId = $recordExists->fetch()->keywordId;
	}

	$this->database->table('linkkeyword' . $md5Sum[0])->insert(array(
	    'linkId' => $linkId,
	    'keywordId' => $insertedId,
	    'weight' => $weight
	));
    }

    public function getLinkSumAndPath($linkId) {
	$recordExists = $this->database->table('link')->where('linkId = ?', $linkId);
	$recordExistsCount = $recordExists->count;

	if ($recordExistsCount == 0) {
	    return false;
	} else {
	    $row = $recordExists-fetch();
	    $values = array(
		'path' => $row->path,
		'md5Sum' => $row->md5Sum
	    );
	    return $values;
	}
    }

    public function pathExists($path) {
	$recordExists = $this->database->table('link')->where('path = ?', $path);
	$recordExistsCount = $recordExists->count();

	if ($recordExistsCount == 0) {
	    return false;
	} else {
	    $row = $recordExists->fetch();
	    $values = array(
		'linkId' => $row->linkId,
		'md5Sum' => $row->md5sum
	    );
	    return $values;
	}
    }

    public function insertLink($contentPath, $contentTitle, $content, $date, $md5Sum, $level, $type,$domainId) {
	$linkExists = $this->database->table('link')->where('md5sum', $md5Sum)->count();
	//$contentPath = urlencode($contentPath);
	$insertedId = false;

	if ($linkExists == 0) {
	    $row = $this->database->table('link')->insert(array(
		'path' => $contentPath,
		'title' => $contentTitle,
		'linkContent' => $content,
		'indexdate' => $date,
		'size' => '0',
		'md5sum' => $md5Sum,
		'level'=> $level,
		'type' => $type,
		'domainId' => $domainId
	    ));
	    $insertedId = $row->linkId;
	}

	return $insertedId;
    }
    
    public function updateLink($link){
	$this->removeLinkKeywords($link);
	$date = date('Y-m-d');
	$this->database->table('link')->where('linkId = ?',$link->getId())->update(array(		
		'title' => $link->getName(),
		'linkContent' => $link->getContent(),
		'indexdate' => $date,
		'size' => '0',
		'md5sum' => $link->getMd5Sum(),
		'level'=> $link->getLevel(),
		'type' => $link->getType(),
		'domainId' => $link->getDomainId()
	    ));
    }
    
    public function removeLinkKeywords($link){
	$md5Sum = $link->getMd5Sum();
	$tableLetter = $md5Sum[0];
	$this->database->table('linkkeyword'.$tableLetter)->where('linkId = ?',$link->getId())->delete();
    }
    
    public function removeLink($linkId){
	$this->database->table('link')->where('linkId = ?',$linkId)->delete();
    }
    
    public function getAllLinksForDomain($domain){
	$result = $this->database->table('link')->where('domainId = ?', $domain->getId())->fetchAll();
	return $result;
    }

}
