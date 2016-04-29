<?php

class DLUser
{
	/*Attributes*/
	private $uid;
	private $email;
	private $password;
	private $role;
	
	/*Constructor*/
	function __construct($uid=null, $email=null, $password=null, $role=null)
	{
		if($uid!==null)
			$this->uid = $uid;
		if($email!==null)
			$this->email = $email;
		if($password!==null)
			$this->password = $password;
		if($role!==null)
			$this->role = $role;
	}
	
	/*Getters and Setters*/
	function getUid()
	{
		return $this->uid;
	}
	function getEmail()
	{
		return $this->email;
	}
	function getPassword()
	{
		return $this->password;
	}
	function getRole()
	{
		return $this->role;
	}
	
	function setUid($uid)
	{
		$this->uid = $uid;
	}
	function setEmail($email)
	{
		$this->email = $email;
	}
	function setPassword($password)
	{
		$this->password = $password;
	}
	function setRole($role)
	{
		$this->role = $role;
	}
	
	/*Methods*/
	
	//function for user signup
	//Basically inserts a user and returns true for a successful insertion
	function signup()
	{
		$query = "INSERT INTO users (email, password, role) VALUES (?, md5(?), ?)";
		$values = array($this->email, $this->password, $this->role);
		$types = array('s','s','s');
		
		try
		{	
			$db = new DBPdo();
			$results = $db->setData($query, $values, $types);
			if($results["RowsAffected"]>0)
			{
				$this->uid = $results["InsertId"];
				return true;
			}	
			else
				return false;
		}
		catch(DLException $dle)
		{
			throw $dle;
		}
		
	}
	
	//function for user login
	//Basically fetches data for email/password pair
	//Returns a single DLUser object
	function login()
	{
		$query = "SELECT * FROM users WHERE email = ? AND password = md5(?)";
		$values = array($this->email, $this->password);
		$types = array('s','s');
		
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLUser");
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
	
	//Function to check if email already exists
	//Returns true if email exists
	function ifEmailExists()
	{
		$query = "SELECT email from users WHERE email = ?";
		$values = array($this->email);
		$types = array('s');
		
		try
		{
			$db = new DBPdo();
			$result = $db->getData($query, $values, $types, "DLUser");
			if(count($result)>0)
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