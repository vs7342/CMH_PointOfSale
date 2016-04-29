<?php

class DLProduct
{
	/*Attributes*/
	private $SKU;
	private $name;
	private $category;
	private $price;
	private $tax;
	private $quantity;
	
	/*Constructor*/
	function __construct($SKU=null, $name=null, $category=null, $price=null, $tax=null, $quantity=null)
	{
		if($SKU!==null)
			$this->SKU = $SKU;
		if($name!==null)
			$this->name = $name;
		if($category!==null)
			$this->category = $category;
		if($price!==null)
			$this->price = $price;
		if($tax!==null)
			$this->tax = $tax;
		if($quantity!==null)
			$this->quantity = $quantity;
	}
	/*Getters and Setters*/
	function getSKU()
	{
		return $this->SKU;
	}
	function getName()
	{
		return $this->name;
	}
	function getCategory()
	{
		return $this->category;
	}
	function getPrice()
	{
		return $this->price;
	}
	function getTax()
	{
		return $this->tax;
	}
	function getQuantity()
	{
		return $this->quantity;
	}
	
	function setSKU($SKU)
	{
		$this->SKU = $SKU;
	}
	function setName($name)
	{
		$this->name = $name;
	}
	function setCategory($category)
	{
		$this->category = $category;
	}
	function setPrice($price)
	{
		$this->price = $price;
	}
	function setTax($tax)
	{
		$this->tax = $tax;
	}
	function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}
	
	
	/*Methods*/
	
	//Inserts a product and sets the SKU attribute for that product object
	function insert()
	{
		$query = "INSERT INTO products (name, category, price, tax, quantity) VALUES (?,?,?,?,?);";
		$values = array(
			$this->name,
			$this->category,
			$this->price,
			$this->tax,
			$this->quantity
		);
		$types = array('s','s','i','i','i');
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			$this->SKU = $results["InsertId"];
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns an array of DLProduct objects with the given name
	function getProductByName()
	{
		$query = "SELECT * FROM products WHERE name LIKE ?";
		$values = array("%".$this->name."%");
		$types = array('s');
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLProduct");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns an array of DLProduct objects with the given category
	function getProductsByCategory()
	{
		$query = "SELECT * FROM products WHERE category = ?";
		$values = array($this->category);
		$types = array('s');
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLProduct");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns an array of DLProduct objects within a given price range
	//If min is null, then minimum price in the entire table is considered as lower value
	//If max is null, then maximum price in the entire table is considered as upper value
	function getProductsByPriceRange($min, $max)
	{
		$query = "SELECT * FROM products WHERE price BETWEEN ";
		$values = array();
		$types = array();
		
		if($min===null)
			$query.="(SELECT min(price) FROM products) ";
		else
		{
			$query.="? ";
			$values[] = $min;
			$types[] = 'i';
		}
		$query.="AND ";
		if($max===null)
			$query.="(SELECT max(price) FROM products) ";
		else
		{
			$query.="? ";
			$values[] = $max;
			$types[] = 'i';
		}
		
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLProduct");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns a DLProduct object with the given SKU ID
	function getProductBySku()
	{
		$query = "SELECT * FROM products WHERE SKU = ?";
		$values = array($this->SKU);
		$types = array('i');
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLProduct");
			
			if(count($result)>0)
				return $result[0];
			else
				return null;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Returns an array of DLProduct objects present in Database
	function getAllProducts()
	{
		$query = "SELECT * FROM products";
		$values = array();
		$types = array();
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLProduct");
			return $result;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Updates the product based on which attributes of the DLProduct are set
	//Returns number of rows affected
	function update()
	{
		$query = "UPDATE products SET ";
		$values = array();
		$types = array();
		
		if($this->name!==null)
		{
			$query.="name = ?,";
			$values[] = $this->name;
			$types[] = 's';
		}
		if($this->category!==null)
		{
			$query.="category = ?,";
			$values[] = $this->category;
			$types[] = 's';
		}
		if($this->price!==null)
		{
			$query.="price = ?,";
			$values[] = $this->price;
			$types[] = 'i';
		}
		if($this->tax!==null)
		{
			$query.="tax = ?,";
			$values[] = $this->tax;
			$types[] = 'i';
		}
		if($this->quantity!==null)
		{
			$query.="quantity = ?,";
			$values[] = $this->quantity;
			$types[] = 'i';
		}
		
		$query = trim($query,',');
		$query.= " WHERE SKU = ?";
		$values[] = $this->SKU;
		$types[] = "i";
		
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			return $results["RowsAffected"];
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Deletes the product corresponding to SKU
	//Returns number of rows affected
	function delete()
	{
		$query = "DELETE FROM  products WHERE SKU = ?;";
		$values = array($this->SKU);
		$types = array('i');
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			return $results["RowsAffected"];
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
	}
	
	//Reduces the availabe quantity of a product by a specified count
	//Returns true/false based on success of reduction
	function reduceQuantity($reduceBy)
	{
		//First we need to check if quantity does not go below zero
		$product = new BLProduct($this->SKU);
		$product = $product->getProductBySku();
		$qtyAvailable = $product->getQuantity();
		if($reduceBy > $qtyAvailable)
			return false;
		
		$query = "UPDATE products SET quantity = quantity - ? WHERE SKU = ?";
		$values = array($reduceBy, $this->SKU);
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