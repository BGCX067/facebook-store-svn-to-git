<?php

class Core_Model_DbTable_Stores extends HTS_Db_Table
{
	protected static $_instance;
    protected $_name        = 'stores';
	protected $_rowClass    = 'Core_Model_Store';
    
    const STATUS_SHOW       = 10;
    const STATUS_HIDE       = 11;
    
    public static function getInstance()
    {
        if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
    }

    public static function getBaseCurrencyList()
    {
        $currency = HTS_Util::translate('CURRENCY');
        return  $base_currency = array(
            "1"         => "1 " . $currency,
            "10"        => "10 " . $currency,
            "100"       => "100 " . $currency,
            "1000"      => "1000 " . $currency,
            "10000"     => "10.000 " . $currency,
            "100000"    => "100.000 " . $currency
        );
    }        

    public function findOneByIdPage($id_page)
	{
		return $this->fetchRow('id_page = ' . $this->_db->quote($id_page));
	}
    
    public function getSqlQuery($params)
    {
        $sql = $this->select()->setIntegrityCheck(false)
                ->from($this->_name);
        
        if(!empty($params['id_page']))
        {
            $sql->where('id_page = ' . $this->_db->quote($params['id_page']));
        }
        return $sql;
    }   
    
    public function getAllStores($params)
    {
        $sql = $this->getSqlQuery($params);
        return $this->fetchAll($sql);
    }        
}
