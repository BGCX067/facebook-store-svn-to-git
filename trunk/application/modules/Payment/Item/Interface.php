<?php

interface Payment_Item_Interface
{
	function getItem();
	function getItemId();
	function getItemPrice();
	function getItemTitle();
	function getDefaultTime();
	function getPageItem($id_page);
	function getTimeExpired($id_page);
	function isValid($id_page);
	function updatePageItem($id_page);
}
