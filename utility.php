<?php

session_start(); //This is required since every page needs a session

//returns true if user is admin
function isAdmin()
{
	if(isset($_SESSION["uid"]) && strtolower($_SESSION["role"])=='admin')
		return true;
	else
		return false;
	
}

function __autoload($file)
{
	@include_once("./BL/$file.php");
	@include_once("./DL/$file.php");
}

//function to sanitize the string
function sanitize($str)
{
	$str = trim($str);
	$str = stripslashes($str);
	$str = strip_tags($str);
	$str = htmlentities($str);
	return $str;
}

//function to validate text input
function validateText($str)
{
	return preg_match("/^[A-Za-z ]+$/" , $str);
}

//function to allow only alphanumeric and space input
function validateAlphaNumeric($str)
{
	return preg_match("/^[A-Za-z0-9 ]+$/", $str);
}

//function to return the navigation bar
function getNavigation()
{
	$adminNav="";
	$loginNav="";
	$logoutNav="";
	
	if(isAdmin())
	{
		$adminNav = "
			<li><a href='adminAddProduct.php'>Admin</a>
				<ul>
					<li><a href='adminAddProduct.php'>Add Product</a></li>
					<li><a href='adminUpdateProduct.php'>Update Product</a></li>
				</ul>
			</li>
		";
	}
	if(!isset($_SESSION["uid"]))
		$loginNav = "<li><a href='login.php'>Login</a></li>";
	else
		$logoutNav = "<li><a href='logout.php'>Logout</a></li>";

	$loginBasedNav = $adminNav.$loginNav.$logoutNav;
	
	$nav = "
		<ul>
			<li><a href='index.php'>Home</a></li>
			<li><a href='cart.php'>Cart</a></li>
			$loginBasedNav
		</ul>
		<link rel='stylesheet' type='text/css' href='css/style.css'>
	";
	/*Bad Place to add link to external css*/
	/*Later, put the css import code in getHeadTag() function which may return some kind of banner as well*/
	
	return $nav;
}

function getProductForm($name, $price, $tax, $quantity)
{
	$form = '
		Product Name:<br/>
		<input type="text" name="prodName" value="'.$name.'"><br/>
		
		Product Category:<br/>
		<select name="prodCategory">
			<option value="Movie">Movie</option>
			<option value="Music">Music</option>
			<option value="TV Series">TV Series</option>
		</select><br/>
		
		Product Price:<br/>
		<input type="number" min="0.01" step="0.01" name="prodPrice" value="'.$price.'"><br/>
		
		Product Tax:<br/>
		<input type="number" min="0.01" step="0.01" name="prodTax" value="'.$tax.'"><br/>
		
		Available Quantity:<br/>
		<input type="number" min="1" step="1" name="prodQuantity" value="'.$quantity.'"><br/>
		
		<br/>
		<input type="submit" name="submitProduct" value="Submit">
	';
	return $form;
}
?>