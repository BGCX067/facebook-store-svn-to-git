<?php
class HTS_Form_Element_File extends Zend_Form_Element_File
{
	/**
	 * Get transfer adapter
	 *
	 * Lazy loads YouNet transfer adapter when no adapter registered.
	 *
	 * @return Zend_File_Transfer_Adapter_Abstract
	 */
	public function getTransferAdapter()
	{
		if (null === $this->_adapter) {
			$this->setTransferAdapter(new HTS_File_Transfer_Adapter_Http());
		}
		return $this->_adapter;
	}
}