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
			
			$sku = "";
			$name = "";
			$category = "";
			$price = "";
			$quantity = "";
			$tax = "";
			
			/*Processing Seletion of a product to update*/
			if(isset($_POST["submitSelectProduct"]))
			{
				$sku = $_POST["selectProduct"];
				$prod = new BLProduct($sku);
				try
				{
					$prod = $prod->getProductBySku();
				}
				catch(DLException $dle)
				{
					$failureMsg = "Something went wrong. Please contact admin.";
				}
				
				$name = $prod->getName();
				$category = $prod->getCategory();
				$price = $prod->getPrice();
				$quantity = $prod->getQuantity();
				$tax = $prod->getTax();
			}
			
			/*Processing Deletion of a product*/
			if(isset($_POST["deleteSelectedProduct"]))
			{
				$sku = $_POST["toDeleteSku"];
				$prod = new BLProduct($sku);
				try
				{
					if(($prod->delete())>0)
						$successMsg = "Product Successfully deleted";
					else
						$failureMsg = "Some problem encountered. Please Try again.";
				}
				catch(DLException $dle)
				{
					$failureMsg = "Something went wrong. Please contact admin.";
				}
			}
			
			/*Processing Updation of product*/
			if(isset($_POST["submitProduct"]))
			{
				$sku=$_POST["selectedSku"];
				
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
						$sku,
						$name,
						$category,
						$price,
						$tax,
						$quantity
					);
					
					try
					{
						if(($product->update())>0)
						{
							$successMsg = "Product Updated successfully!";
							
							//Clearing the form
							$sku = "";
							$name = "";
							$price = "";
							$quantity = "";
							$tax = "";
						}
						else
							$failureMsg = "Kindly make some changes and then submit.";
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
		<div id="selectProd">
			<form action="adminUpdateProduct.php" method="post">
				Select a Product:
				<select name="selectProduct">
					<?php
						$products = new BLProduct();
						try
						{
							$products = $products->getAllProducts();
						}
						catch(DLException $dle)
						{
							echo "<h3 class='error'>Something went wrong. Contact Admin</h2>";
						}
						foreach($products as $singleProduct)
						{
							$optionValue = $singleProduct->getSKU();
							$optionDisplay = $singleProduct->getSKU()." - ".$singleProduct->getName()."(".$singleProduct->getCategory().")";
							echo "<option value='$optionValue'>$optionDisplay</option>";
						}
					?>
				</select>
				<input type="submit" name="submitSelectProduct" value="Select">
			</form>
		</div>
		<div class="updateProduct">
			<h3>Update a Product</h3>
			<form action="adminUpdateProduct.php" method="post">
				<input type="hidden" value="<?=$sku?>" name="selectedSku">
				<?php
					echo getProductForm($name, $price, $tax, $quantity);
				?>
			</form>
		</div>
		<div class="deleteProduct">
			<?php
				if(isset($_POST["submitSelectProduct"]))
				{
					$sku = $_POST["selectProduct"];
					echo '
						</br>
						<form action="adminUpdateProduct.php" method="post">
							<input type="hidden" value="'.$sku.'" name="toDeleteSku">
							<input type="submit" value="Delete Product" name="deleteSelectedProduct">
						</form>
					';
				}
			?>
		</div>
	</body>
</html>