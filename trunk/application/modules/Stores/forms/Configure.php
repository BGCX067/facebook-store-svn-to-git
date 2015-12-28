<?php

class Stores_Form_Add extends HTS_Form
{
	protected $_element_decorators = array(
		'ViewHelper',
	array('Label', array('tag' => 'div', 'class' => 'form_label')),
	array('HtmlTag', array('tag' => 'div', 'class' => 'form_element'))
	);

	protected $_hidden_decorators = array(
		'ViewHelper',
	array('HtmlTag', array('tag' => 'div'))
	);

	public function init()
	{
		parent::init();

		$this->setName('add_rss');
		$this->setMethod('post');

		//get drop down list for autopost config
		$autopost_configs_db = HTS_Util::getDbTable("autopostconfigs");
		$autopost_configs = $autopost_configs_db->getList();
		$autopost_configs_drop_down = array();
		foreach ($autopost_configs as $config)
		{
			$autopost_configs_drop_down[$config["id"]] = $config["name"];
		}
		 
		$id_category = new Zend_Form_Element_Select('id_category');
		$id_category->setLabel('CATEGORY');
		 
		$new_category = new Zend_Form_Element_Text('new_category');
		$new_category->setLabel('');
		if (!empty($this->_params['id_category']))
		{
			$new_category->setValue("");
		} else {
			$new_category->setValue($this->getTranslator()->translate('CATEGORY_GENERAL'));
		}
		$new_category->addValidator('StringLength', false, array(
            'max'       => 200,
            'encoding'  => 'UTF-8'
            ));

            $title = new Zend_Form_Element_Text('title');
            $title->setLabel('TITLE');
            $title->addValidator('StringLength', false, array(
            'max'       => 200,
            'encoding'  => 'UTF-8'
            ));

            $id_rss = new Zend_Form_Element_Hidden('id_rss');

            $link = new Zend_Form_Element_Text('link');
            $link->setLabel('RSS_URL')
            ->setRequired(true)
            ->addValidator('NotEmpty', true);

            $num_stories = new Zend_Form_Element_Text('num_stories');
            $num_stories->setLabel('NUM_OF_ITEMS')
            ->setValue(5)
            ->addValidator(new Zend_Validate_Digits())
            ->addValidator(new Zend_Validate_Between(array('min' => 5, 'max' => 100)))
            ->setRequired(true);
            $is_allow_comment = new Zend_Form_Element_Checkbox("is_allow_comment");
            $is_allow_comment->setLabel("Allow comment")
            ->setCheckedValue(1)
            ->setUncheckedValue(0)
            ->setValue(1);

            $is_autopost = new Zend_Form_Element_Checkbox("is_autopost");
            $is_autopost->setLabel("Allow auto post")
            ->setCheckedValue(1)
            ->setUncheckedValue(0);

            $id_autopost_config = new Zend_Form_Element_Select("id_autopost_config");
            $id_autopost_config->setLabel("Frequency")
            ->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array(Zend_Validate_NotEmpty::IS_EMPTY => 'ERR_AUTO_POST_EMPTY')))
            ->addMultiOptions($autopost_configs_drop_down);

            $max_news_post = new Zend_Form_Element_Text("max_news_post");
            $max_news_post->setLabel("Max news post")
            ->setValue(5)
            ->setRequired(true)
            ->addValidator('lessThan', true, array(
                    'max' => 20,
                    'messages' => array(Zend_Validate_LessThan::NOT_LESS => 'ERR_MAX_NEW_POSTED_NOT_BETWEEN')));
             
            $this->addElements(array($id_rss, $link, $title, $id_category, $new_category, $num_stories, $is_allow_comment,
            $is_autopost, $id_autopost_config, $max_news_post));
            $this->setElementDecorators($this->_element_decorators);
            $id_rss->setDecorators($this->_hidden_decorators);
	}

	public function isValid($data) {
		if ($data['is_autopost'] == 0)
		{
			$this->getElement('max_news_post')->removeValidator('lessThan');
		}
		else
		{
			$this->getElement('max_news_post')->addValidator('lessThan', true, array(
                    'max' => 20,
                    'messages' => array(Zend_Validate_LessThan::NOT_LESS => 'ERR_MAX_NEW_POSTED_NOT_BETWEEN')));
		}
		return parent::isValid($data);
	}
}