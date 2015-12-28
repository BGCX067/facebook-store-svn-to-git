<?php

class Core_Model_DbTable_Categories extends HTS_Db_Table
{
    protected static $_instance;
    protected $_name = 'categories';
	protected $_rowClass = 'Core_Model_Category';

    public static function getInstance()
    {
        if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
    }

	public function findAllByIdStore($id_store)
	{
		return $this->fetchAll('id_store = ' . $this->_db->quote($id_store));
	}

    public function getSqlQuery($params)
    {
        $sql = $this->select()->setIntegrityCheck(false)
                ->from($this->_name);

        if(!empty($params['id_store']))
        {
            $sql->where('id_store = ' . $this->_db->quote($params['id_store']));
        }
        return $sql;
    }

    protected function _reOrderCategory($list_categories)
    {
        if(!empty ($list_categories))
        {
            $root = array();
            $temp = array();
            foreach ($list_categories as $category)
            {
                if(empty ($category['parent']))
                {
                    $root[$category['id']] = $category;
                }
                else
                {
                    $temp[$category['parent']][$category['id']] = $category;
                }
            }

            foreach ($temp as $key => $children)
            {
                foreach ($children as $child_key => $child)
                {
                    if(isset ($temp[$child['id']]))
                    {
                        $temp[$key][$child_key]['children'] = $temp[$child['id']];
                        unset ($temp[$child['id']]);
                    }
                }
            }

            foreach ($root as $key => $element)
            {
                if(!empty ($temp[$key]))
                {
                    $root[$key]['children'] = $temp[$key];
                }
            }
            return $root;
        }
        else
        {
            return array();
        }
    }

    public function getAllCategories($params)
    {
        $sql = $this->getSqlQuery($params);
        $list_categories = $this->_db->fetchAll($sql);
        $list_categories = $this->_reOrderCategory($list_categories);
        return $list_categories;
    }
}
