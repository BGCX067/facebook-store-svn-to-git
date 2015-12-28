<?php

class Payment_Manager
{

	private static $_instance;
	private $_handlers = array();

	private $_options;

	/**
	 *
	 * @return Payment_Manager
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function registerHandler($handler)
	{
		//$this->_handlers[$handler::CODE] = $handler;
	}

	/**
	 *
	 * @param int $code
	 * @return Payment_Item_Abstract
	 */
	public function getHandlerByCode($code)
	{
		return $this->_handlers[$code];
	}

	public function isExistHandlerByCode($code)
	{
		return array_key_exists($code, $this->_handlers);
	}

	public function handlePayment($data)
	{
		if (array_key_exists('test_ipn', $data)
		&& 1 === (int) $data['test_ipn']
		)
		{
			$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		else
		{
			$url = 'https://www.paypal.com/cgi-bin/webscr';
		}

		$req = 'cmd=_notify-validate';

		//collect the variables sent by PayPal
		foreach ($data as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		list($status, $response) = $this->curlPost($url, $req);

		if ($status == 200 && $response == 'VERIFIED')
		{
			try
			{
				if (!isset($data['mc_gross']) || !isset($data['txn_id']))
				{
					throw new Exception('Payment_Manager::handlePayment() either mc_gross or txn_id is empty');
				}

				// get list of payment items
				// calculate price to be paid
				$items = array();
				$price_to_be_paid = 0.0;

				foreach ($data as $key => $value)
				{
					if (preg_match('/^item_number/', $key))
					{

						$item = $this->getHandlerByCode($value);

						if (!empty($item))
						{
							$items[] = $item;
							$price_to_be_paid+= $item->getItemPrice();
						}
					}
				}

				if ($data['mc_gross'] < $price_to_be_paid)
				{
					throw new Exception('Payment_Manager::handlePayment() total payment (' . $data['mc_gross'] . ') is less than price to be paid (' . $price_to_be_paid . ')');
				}

				// all good! proceed...
				$orderModel = Payment_Model_DbTable_Orders::getInstance();
				$id_order = $orderModel->createOrderFromParams($data, $items);
			}
			catch (Exception $ex)
			{
				file_put_contents('payment-error.log', json_encode($ex) . PHP_EOL, FILE_APPEND);
			}
		}
		else
		{
			// not good. ignore, or log for investigation...
			file_put_contents('payment-error.log', json_encode($data) . PHP_EOL, FILE_APPEND);
		}
	}

	public function curlPost($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		// execute request and get response and status code
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// close connection
		curl_close($ch);
		return array($status, $response);
	}

	public function getItemPrice($code)
	{
		return $this->getHandlerByCode($code)->getItemPrice();
	}

	public function getItemTitle($code)
	{
		return $this->getHandlerByCode($code)->getItemTitle();
	}

	public function getBusinessEmail()
	{
		return $this->_options['payment']['email'];
	}

	public function getPayPalUrl()
	{
		return $this->_options['payment']['url'];
	}

	public function getTimeExpired($id_page, $code)
	{
		return $this->getHandlerByCode($code)->getTimeExpired($id_page);
	}

	public function setOptions($options)
	{
		$this->_options = $options;
	}
}

