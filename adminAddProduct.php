<?php
	require_once("utility.php");//This file consists of all the helper/utility functions which would be used throughout the application layer.
	
	if(!isAdmin())
	{
		//user not authorized
		header("location: index.php");
		die();
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CMH - Interview 2 - Admin</title>
	</head>
	<body>
		<div id="navigation">
			<?php 
				echo getNavigation();
			?>
		</div>
		<?php
		
			$failureMsg="";
			$successMsg="";
			
			$name = "";
			$category = "";
			$price = "";
			$quantity = "";
			$tax = "";
			
			/*Processing Adding of product*/
			if(isset($_POST["submitProduct"]))
			{
				/**duplicate code start - fix later**/
				$name = $_POST["prodName"];
				$category = $_POST["prodCategory"];
				$price = $_POST["prodPrice"];
				$quantity = $_POST["prodQuantity"];
				$tax = $_POST["prodTax"];
				
				//Sanitize and Validate Inputs
				$validationSuccess = true;
				
				$name = sanitize($name);
				if(!validateText($name) || strlen($name)<=0)
				{
					$validationSuccess = false;
					$failureMsg.="Please input a valid name - only Alphabets allowed. ";
				}
				if($price==null)
				{
					$validationSuccess = false;
					$failureMsg.="Please input a valid price. ";
				}
				if($tax==null)
				{
					$validationSuccess = false;
					$failureMsg.="Please input a valid tax price. ";
				}
				if($quantity==null)
				{
					$validationSuccess = false;
					$failureMsg.="Please input a valid quantity. ";
				}
				/**duplicate code end - fix later**/
				
				if($validationSuccess)
				{
					$product = new BLProduct(
						null,
						$name,
						$category,
						$price,
						$tax,
						$quantity
					);
					
					try
					{
						$product->insert();
						$successMsg = "Product inserted successfully with SKU = ".$product->getSKU();
						
						//clearing the form
						$name = "";
						$price = "";
						$quantity = "";
						$tax = "";
					}
					catch(DLException $dle)
					{
						$failureMsg = "Something went wrong. Please contact admin.";
					}
				}
			}
		?>
		<div class="success">
			<?php
				echo $successMsg;
			?>
		</div>
		<div class="error">
			<?php
				echo $failureMsg;
			?>
		</div>
		<div class="addProduct">
			<h3>Add a Product</h3>
			<form action="adminAddProduct.php" method="post">
				<?php
					echo getProductForm($name, $price, $tax, $quantity);
				?>
			</form>
		</div>
		
	</body>
</html>