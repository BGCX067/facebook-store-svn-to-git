<?php

class HTS_Model_User extends HTS_Db_Table_Row
{
	/**
	 * Get user roles
	 * @return array of roles
	 */
	public function getRoles()
	{
		if (empty($this->id))
		{
			return array(Core_Role::ROLE_GUEST);
		}
		else
		{
			return array(Core_Role::ROLE_USER);
		}
	}

	/**
	 * Get config language
	 * @return code of language
	 */
	public function getLanguageCode()
	{
        if ($this->language)
        {
            return $this->language;
        }
        else
        {
            return DEFAULT_LANGUAGE_CODE;
        } 
	}
}
