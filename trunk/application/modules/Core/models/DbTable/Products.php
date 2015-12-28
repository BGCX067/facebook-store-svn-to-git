<?php

class Core_Model_DbTable_Products extends HTS_Db_Table
{
    protected static $_instance;
    protected $_name = 'products';
	protected $_rowClass = 'Core_Model_Product';

    public static function getInstance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function findAllByIdCategory($id_category)
	{
		return $this->fetchAll('id_category = ' . $this->_db->quote($id_category));
	}

    public function getSqlQuery($params)
    {
        $sql = $this->select()->setIntegrityCheck(false)
                ->from($this->_name);

        if(!empty($params['id_category']))
        {
            $sql->where('id_category = ' . $this->_db->quote($params['id_category']));
        }
        if(!empty($params['id_store']))
        {
            $sql->where('id_store = ' . $this->_db->quote($params['id_store']));
        }
        return $sql;
    } 

    public function getAllProducts($params)
    {
        $sql = $this->getSqlQuery($params);
        $result = $this->fetchAll($sql);
        Zend_Debug::dump($result);
    }
}
