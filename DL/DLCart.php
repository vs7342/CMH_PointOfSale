<?php

class DLCart
{
	/*Attributes*/
	private $uid;
	private $SKU;
	private $quantity;
	
	/*Constructor*/
	function __construct($uid=null, $SKU=null, $quantity=null)
	{
		if($uid!==null)
			$this->uid=$uid;
		if($SKU!==null)
			$this->SKU=$SKU;
		if($quantity!==null)
			$this->quantity=$quantity;
	}
	
	/*Getters and Setters*/
	function getUid()
	{
		return $this->uid;
	}
	function getSKU()
	{
		return $this->SKU;
	}
	function getQuantity()
	{
		return $this->quantity;
	}
	
	function setUid($uid)
	{
		$this->uid = $uid;
	}
	function setSKU($SKU)
	{
		$this->SKU = $SKU;
	}
	function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}
	
	/*Methods*/
	
	//Inserts a row in the cart table
	//Returns true for successful insertion
	function insert()
	{
		$query = "INSERT INTO cart (uid, SKU, quantity) VALUES (?,?,?);";
		$values = array(
			$this->uid,
			$this->SKU,
			$this->quantity
		);
		$types = array('i','i','i');
		
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			if($results["RowsAffected"]>0)
				return true;
			else
				return false;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns the quantity for corresponding uid and sku in the cart
	function getQuantityForCart()
	{
		$query = "SELECT * from cart where uid = ? and SKU = ?";
		$values = array($this->uid, $this->SKU);
		$types = array('i','i');
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLCart");
			if(count($result)>0)
				return $result[0]->quantity;
			else
				return 0;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Updates the quantity of cart(uid and SKU)
	function updateQuantityForCart()
	{
		$query = "UPDATE cart SET quantity = ? WHERE uid = ? AND SKU = ?";
		$values = array($this->quantity, $this->uid, $this->SKU);
		$types = array('i','i','i');
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			if($results["RowsAffected"]>0)
				return true;
			else
				return false;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns an array of cart objects for a particular user id
	function getCartForUser()
	{
		$query = "SELECT * FROM cart WHERE uid = ?";
		$values = array($this->uid);
		$types = array('i');
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLCart");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//deletes a product from the cart for a particular user id
	function deleteCart()
	{
		$query = "DELETE FROM cart WHERE uid = ? AND sku = ?";
		$values = array($this->uid, $this->SKU);
		$types = array('i','i');
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			if($results["RowsAffected"]>0)
				return true;
			else
				return false;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
}

?>