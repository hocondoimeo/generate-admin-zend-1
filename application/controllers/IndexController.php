<?php

/**
 * Controller Index
 *
 * @author      code generate
 * @package   	KiSS
 * @version     $Id$
 * @todo remove
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {}

    /**
     * Home page - Main Panel
     */
    public function indexAction ()
    {
        // die('dfdf');
        $form = new Application_Form_Sites();
        
        /* Proccess data post */
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                
                if(null !== $dir = $this->_request->getParam('Site',null)){
                	if (is_dir(PROJECT_OUTPUT_PATH.$dir)) {
                		exec('rm -rf '.PROJECT_OUTPUT_PATH.$dir);
                	}
                }
                
                $genSite = new Gen_Site($formData);
                
                switch ($formData['SubmitGen']) {
                    case 'Model':
                        $genSite->genModels();
                        break;
                    case 'Controller':
                        $genSite->genControllers();
                        break;
                    case 'Form':
                        $genSite->genForms();
                        break;
                    case 'View':
                        $genSite->genViews();
                        break;
                    case 'ApplicationIni':
                        $genSite->genApplicationIni();
                        break;
                    case 'NavigationIni':
                        $genSite->genNavigationIni();
                        break;
                    case 'All':
                        $genSite->genAll();
                        break;
                    case 'Deploy':
                        $genSite->deploy();
                        break;
                    //
                    // default:
                    // $genSite->genAll();
                    // break;
                }
            }
        }
        // $form->populate($this->view->form->getValues());
        $this->view->form = $form;
    }
    
    /**
     * Delete record %%TABLE_NAME%%.
     * @param $id
     * @author
     */
    public function ajaxCheckFolderAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
    	/* Check valid data */
    	if(null !== $dir = $this->_request->getParam('Site',null)){
    	    if (is_dir(PROJECT_OUTPUT_PATH.$dir)) {
    	    	die($this->_helper->json(array('success' => true)));
    	    }
    	}    	
    	die($this->_helper->json(array('success' => false)));
    }
}
