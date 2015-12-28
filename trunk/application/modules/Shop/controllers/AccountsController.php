<?php

class Tab_TabController extends HTS_Controller_Action
{
	private $_id_page;
	public function init()
	{
		parent::init();
		$this->_helper->layout->setLayout('tab_facebook');
	}

	public function preDispatch()
	{
		parent::preDispatch();
		$this->_id_page = $this->_social->getIdPage();
	}

	private function _getCache()
	{
		return HTS_Util::getObjectFileCache();
	}


	private function _orderRssNews($rss_list)
	{
		$feeds = array();
		$errors = array();
		$id_source = 0;
		if (isset($_COOKIE['id_' . $this->_id_page]))
		{
			$id_source = $_COOKIE['detail_source_' . $this->_id_page];
		}
		$flag = false;
		$cache = $this->_getCache();
		foreach ($rss_list as $rss_key => $rss)
		{
			try {
				$cache_key = PREFIXED_CACHE_RSS_URL . md5($rss['link']);

				$rss_news = $cache->load($cache_key);
				if (!$rss_news)
				{
					$feed = Zend_Feed_Reader::import($rss['link']);
						
					$arrFeeds = array();
					foreach ($feed as $entry)
					{
						$itemRSS = array (
							"title" => $entry->getTitle(),
							"dateModified" => $entry->getDateModified(),
							"description" => $entry->getDescription(),
							"link" => $entry->getLink(),
							"commentLink" => $entry->getCommentLink()
						);
						$arrFeeds[] = $itemRSS;
					}
						
					$rss_news = array(
							'link'  => $feed->getLink(),
							'entries' => $arrFeeds,
					);
						
					$cache->save($rss_news, $cache_key, array(), TIMELIFE_CACHE_RSS_URL);
				}
					
				if(!$flag && ($id_source == $rss['id']))
				{
					$flag = true;
				}

				$entries = array();
				$index = 0;
				foreach ($rss_news["entries"] as $entry)
				{
					if ($index >= $rss['num_stories'])
					{
						break;
					}
						
					$news = array(
						"title" => $entry["title"],
						"dateModified" => $entry["dateModified"],
						"description" => $entry["description"],
						"link" => $entry["link"],
						"commentLink" => $entry["commentLink"]
					);
						
					$entries[$index] = $news;
					$index++;
				}

				$rss_feeds = array(
						'title' => $rss['title'],
						'link'  => $rss_news["link"],
						'entries' => $entries,
						'is_allow_comment' => $rss["is_allow_comment"],
				);

				$feeds[$rss['id']] = $rss_feeds;

			} catch (Exception $e) {
				$feeds[$rss['id']] = array(
                    'title' => $rss['title'],
                    'entries' => $this->translate('ERR_CANNOT_LOAD_DATA_FROM_RSS_LINK')// "There is no data from this RSS link.",
				);

				$errors[] = $rss['link'];
			}
		}
		if (!$flag)
		{
			setcookie('detail_source_' . $this->_id_page, "all", time() + 7*24*60*60, '/');
		}
		return $feeds;
	}

	private function _checkValidCategory($id_category, $categories)
	{
		$is_valid = false;
		if (count($categories) > 0)
		{
			foreach ($categories as $category)
			{
				if ($id_category == $category->id)
				{
					$is_valid = true;
					break;
				}
			}
		}
		return $is_valid;
	}

	private function _getFirstCategory($categories)
	{
		if ($categories->count())
		{
			return $categories->getRow(0);
		}
		return array();
	}

	public function listAction()
	{
		$categories_db = HTS_Util::getDbTable('categories');
		$categories = $categories_db->getEnableByIdPage($this->_id_page);

		if (empty($this->_params['id_category']))
		{
			if (isset($_COOKIE['id_' . $this->_id_page]) && $this->_checkValidCategory($_COOKIE['id_' . $this->_id_page], $categories))
			{
				$this->_params['id_category'] = $_COOKIE['id_' . $this->_id_page];
			}
			else
			{
				$current_category = $this->_getFirstCategory($categories);
				if (!empty($current_category))
				{
					$this->_params['id_category'] = $current_category->id;
					//TODO Zend_Cookie
					setcookie('detail_source_' . $this->_id_page, "all", time() + 7*24*60*60, '/');
				}
			}
		}
		else
		{
			if ($this->_checkValidCategory($this->_params['id_category'], $categories))
			{
				setcookie('id_' . $this->_id_page, $this->_params['id_category'], time() + 7*24*60*60, '/');
				setcookie('detail_source_' . $this->_id_page, "all", time() + 7*24*60*60, '/');
			}
			else
			{
				$current_category = $this->_getFirstCategory($categories);
				if (!empty($current_category))
				{
					$this->_params['id_category'] = $current_category->id;
					//TODO Zend_Cookie
					setcookie('detail_source_' . $this->_id_page, "all", time() + 7*24*60*60, '/');
				}
			}
		}
		if (!empty($this->_params['id_category']))
		{
			$rss_db = HTS_Util::getDbTable('rss');
			$rss_list = $rss_db->getEnableByCategory($this->_params['id_category']);
			$this->view->rss_list = $rss_list;
			$this->view->feeds = $this->_orderRssNews($rss_list);
			$this->view->id_category = $this->_params['id_category'];
		}

		if ($this->_social->isPageAdmin())
		{
			$this->view->owner = 'owner';
		}

		$this->view->id_page = $this->_id_page;
		$this->view->categories = $categories;
	}
}