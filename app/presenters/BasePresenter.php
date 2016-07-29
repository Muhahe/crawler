<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Caching\Cache;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
    
 

    public function beforeRender() {
	parent::beforeRender();
	$this->template->menuItems = array(
	    'Search' => 'Search:',
	    'Index' => 'Homepage:Index',
	);
	$this->template->title = "CAE Knowledge Base";
    }

}
