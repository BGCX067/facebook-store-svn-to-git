<?php

class Payment_IndexController extends HTS_Controller_Action
{

	function indexAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		Payment_Manager::getInstance()->handlePayment($this->getRequest()->getParams());
	}

}

