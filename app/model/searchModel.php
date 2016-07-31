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
use App\Search as search;

/**
 * Description of searchModel
 *
 * @author SukL
 */
class searchModel extends Nette\Object {

    private $database;
    private $resultData;
    private $resultPerPage = 10;
    private $pages = 0;
    private $searchValue;
    private $cache;
    private $storage;

    public function __construct(Nette\Database\Context $database, Nette\Caching\IStorage $storage) {
        $this->database = $database;
        $this->storage = $storage;
        $this->cache = new \Nette\Caching\Cache($this->storage, 'searchModelResults');
    }

    public function search($searchValue, $offset, $limit) {
        $this->resultData = $this->cache->load($searchValue);
        if ($this->resultData === null) {
            $this->searchValue = $searchValue;
            if (substr($searchValue, 0, 1) == '"' && substr($searchValue, -1) == '"') {
                $this->searchPhrase();
            } else {
                $this->searchKeywords();
            }
            $this->cache->save($searchValue, $this->resultData);
        }

        if ($this->getCount() > 0) {
            return array_slice(array_values($this->resultData), $offset, $limit);
        } else {
            return null;
        }
    }

    private function searchKeywords() {
        $searchValues = $this->searchValue;
        $searchValues = str_replace("*", "%", $searchValues);
        $searchValues = str_replace("!", "_", $searchValues);

        $this->resultData = array();
        foreach (explode(" ", $searchValues) as $keywordName) {
            $weight = -1;
            $recordExists = $this->database->table('keywords')->where('keyword LIKE ?', $keywordName);
            $recordExistsCount = $recordExists->count();

            if ($recordExistsCount != 0) {
                $keywords = $recordExists->fetchAll();
                foreach ($keywords as $keyword) {
                    $currentKeyword = $keyword->keyword;
                    $md5 = md5($currentKeyword);
                    $keywordJoinLink = $keyword->related('linkkeyword' . $md5[0]);
                    $keywordJoinLink->fetchAll();

                    foreach ($keywordJoinLink as $link) {
                        $weight = $link->weight;
                        $linkId = $link->linkId;
                        if (array_key_exists($linkId, $this->resultData)) {
                            $this->resultData[$linkId]->updateWeight($weight);
                        } else {
                            $linkData = $link->ref('link', 'linkId');
                            $domainData = $linkData->ref('domain', 'domainId');
                            Debugger::dump($domainData);
                            //$linkData = $domainData[]
                            $linkClass = new search\linkClass($linkData,$domainData->location,$domainData->flagPath);
                            $linkClass->updateWeight($weight);
                            $this->resultData[$linkId] = $linkClass;
                        }
                    }
                }
            }
        }
    }

    private function searchPhrase() {
        $searchValue = $this->searchValue;
        $phrase = str_replace('"', '', $searchValue);
        $recordExists = $this->database->table('link')->where('linkContent LIKE ? OR path LIKE ?', '% ' . $phrase . "%", '% ' . $phrase . "%");
        $recordExistsCount = $recordExists->count();

        if ($recordExistsCount != 0) {
            $links = $recordExists->fetchAll();

            foreach ($links as $link) {
                $linkId = $link->linkId;
                if (array_key_exists($linkId, $this->linksArray)) {
                    Debugger::dump("exists");
                } else {
                    $linkClass = new search\linkClass($link);
                    $this->resultData[$linkId] = $linkClass;
                }
            }
        }
    }

    function getCount() {
        return count($this->resultData);
    }

    private function calculatePages() {
        $this->pages = ceil($this->getCount() / $this->resultPerPage);
    }

    function getPages() {
        $this->calculatePages();
        return $this->pages;
    }

}
