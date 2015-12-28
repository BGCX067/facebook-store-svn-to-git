<?php
class HTS_Util
{
	/**
	 * Enter description here ...
	 * @param string $tableName
	 * @param string $moduleName
	 * @return HTS_Db_Table
	 */
	public static function getDbTable($tableName, $moduleName = 'Core')
	{
		$className = ucfirst($moduleName) . '_Model_DbTable_' . ucfirst($tableName);
		return new $className();
	}

	public static function getDbRow($tableName, $moduleName = 'Core')
	{
		$dbTable = self::getDbTable($tableName, $moduleName);
		return $dbTable->createRow();
	}

	public static function redirect($url)
	{
		header("Location: $url");
		exit(0);
	}

	public static function parentRedirect($url)
	{
		echo "<script language='javascript'>top.location.href='" . $url . "'</script>";
		exit(0);
	}

	public static function translate($text)
	{
		$translator = Zend_Registry::get('Zend_Translate');
		return $translator->translate($text);
	}

	public static function escapeForJs($text)
	{
		//TODO look urgly :|
		$value = str_replace("\r\n", '\n', htmlentities($text, ENT_QUOTES, 'utf-8'));
		return str_replace("\n", '\n', $value);
	}

	public static function buildUrlFromArray($arr)
	{
		foreach ($arr as $name => $value)
		{
			return urlencode(implode('&', $name . '=' . $value));
		}
	}

	public static function assignPageValues(&$view, $page_count, $current_page, $query = array())
	{
		$view->pageCount = $page_count;
		$view->query = $query;
		$view->current = $current_page;
		$view->page = $current_page;
		if ($current_page > 1) $view->previous = $current_page - 1;
		if ($current_page < $page_count) $view->next = $current_page + 1;
		$pagesInRange = array();
		for ($i=1; $i<=$page_count; $i++)
		$pagesInRange[] = $i;
		$view->pagesInRange = $pagesInRange;
	}

	/**
	 * @return Zend_Cache_Core
	 */
	public static function getObjectMemCache()
	{
		$cacheManager = Zend_Registry::get('cache_manager');
		return $cacheManager->getObjectMemcached();
	}

	/**
	 * @return Zend_Cache_Core
	 */
	public static function getObjectFileCache()
	{
		$cacheManager = Zend_Registry::get('cache_manager');
		return $cacheManager->getObjectFileCache();
	}
    /**
     * @return boolean 
     */
	public static function isFacebookRequest()
	{
		$is_fb_request = false;

		if ($_SERVER['HTTP_USER_AGENT'] == "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)")
		{
			$is_fb_request = true;
		}

		return $is_fb_request;
	}
    /**
     * @param type $view
     * @param type $paginator
     * @param type $params 
     */
    public static function renderPagination(&$view, $paginator, $params)
    {
        echo $view->paginationControl($paginator , 'Sliding', 'paginatorControl.phtml', $params);
    }
    /**
     * @return string 
     */
    public static function getPatternRegexUri()
    {
        return '/http:\/\/([A-Z a-z 0-9 - _]+.)+[A-Z a-z]{2,}\//';
    }
    
    /**
	 * @return string
	 */
	public static function createFullUrl($url, $prefix = 'http')
	{
		if (substr($url, 0, 2) == '//')
		{
			return $prefix . ':' . $url;
		}
		
		return $url;
	}
    
    public static function checkPageAdmin($id_page, $page_list)
    {
        foreach ($page_list as $key => $name)
        {
            if ($key == $id_page)
            {
                return true;
            }
        }
        return false;
    }

    public static function getCreatePageUrl()
    {
        return 'http://www.facebook.com/pages/create.php?ref_type=sitefooter';
    }
}
