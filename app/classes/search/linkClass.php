<?php

namespace App\Search;

use Nette;
use App\Model;
use Tracy\Debugger;

/**
 * Description of linkClass
 *
 * @author SukL
 */
class linkClass {

    private $linkId;
    private $path;
    private $folder;
    private $title;
    private $content;
    private $type;
    private $weight = 0;

    public function __construct(Nette\Database\Table\ActiveRow $linkData) {
        $this->linkId = $linkData->linkId;
        $this->path = $linkData->path;
        $this->title = $linkData->title;
        $this->content = $linkData->linkContent;
        $this->type = $linkData->type;
        $this->setFolder();
    }

    private function setFolder() {
        if (!$this->isDir()) {
            $pathParts = pathinfo($this->path);
            $this->folder = $pathParts['dirname'];
        }
    }

    public function isDir() {
        if ($this->type == "dir") {
            return true;
        } else {
            return false;
        }
    }

    public function getFolder() {
        return $this->folder;
    }

    public function getLinkId() {
        return $this->linkId;
    }

    public function getPath() {
        return $this->path;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getType() {
        return $this->type;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function updateWeight($weight) {
        $this->weight += $weight;
    }

}
