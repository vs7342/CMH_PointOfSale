<?php
	require_once("utility.php");//This file consists of all the helper/utility functions which would be used throughout the application layer.
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CMH - Interview 2</title>
		<script>
			//function to enable the qty text box when corresponding checkbox is checked
			function toggleTextBox(sku)
			{
				var textBoxId = "skuQty" + sku;
				var textBox = document.getElementById(textBoxId);
				if(textBox.disabled==true)
					textBox.disabled=false;
				else
					textBox.disabled=true;
			}
			
		</script>
	</head>
	<body>
		<div id="navigation">
			<?php 
				echo getNavigation();
			?>
		</div>
		<?php
			/*Processing when Add to Cart button is clicked*/
			
			$failureMsg = "";
			$successMsg = "";
			//$successMsg or $failureMsg will be set based on success/failure of adding product/s to cart
			if(isset($_POST["submitAddToCart"]))
			{
				if($_POST["skuSelected"]!=null)
				{
					foreach($_POST["skuSelected"] as $singleSkuSelected)
					{
						$qtyPostVariable = "skuQty".$singleSkuSelected;
						if(isset($_POST[$qtyPostVariable]) && $_POST[$qtyPostVariable]!=null)
						{
							$uid = $_SESSION["uid"];
							$sku = $singleSkuSelected;
							$productQty = $_POST[$qtyPostVariable];
							
							$cart = new BLCart($uid, $sku, $productQty);
							try
							{
								if($cart->insert())
									$successMsg = "Product(s) added successfully";
								else
									$failureMsg = "Please try with reduced quantity.";
							}
							catch(DLException $dle)
							{
								$failureMsg="Something went wrong. Contact Admin!";
							}
						}
					}
				}
				else
					$failureMsg = "You need to check the box as well as enter quantity to add product to cart";
			}
			
			//Setting the warning message if user is not logged in
			$warningMsg="";
			if(!isset($_SESSION["uid"]))
				$warningMsg="You need to login to add products to cart!";
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
		<div class="warning">
			<?php
				echo $warningMsg;
			?>
		</div>
		<div class="search">
			<h3>Search Product</h3>
			<div id="byName">
				<form action='index.php' method='post'>
					By Name: <br/>
					<input type='text' name='prodName'>
					<input type='submit' name='submitSearchByName' value='Go!'>
				</form>
			</div>
			<div id="byCategory">
				<form action='index.php' method='post'>
					By Category: <br/>
					<select name='prodCategory'>
						<!-- Make this dynamic later using distinct category query-->
						<option value="Movie">Movie</option>
						<option value="Music">Music</option>
						<option value="TV Series">TV Series</option>
					</select>
					<input type='submit' name='submitSearchByCategory' value='Go!'>
				</form>
			</div>
			<div id="byPrice">
				<form action='index.php' method='post'>
					By Price Range:<br/> 
					<input type='number' min="0" step="0.01" name='prodPriceMin'> to
					<input type='number' min="0" step="0.01" name='prodPriceMax'>
					<input type='submit' name='submitSearchByPrice' value='Go!'>
				</form>
			</div>
		</div>
		<div id="products">
			<h3>Products Available</h3>
			<?php
				$products = new BLProduct();
				try
				{
					if(isset($_POST["submitSearchByName"]))
					{
						$name = $_POST["prodName"];
						$name = sanitize($name);
						if(!validateText($name))
						{
							$name="";
						}
						$products->setName($name);
						$products = $products->getProductByName();
					}
					else if(isset($_POST["submitSearchByCategory"]))
					{
						$category = $_POST["prodCategory"];
						$category = sanitize($category);
						if(!validateText($category))
						{
							$category="";
						}
						$products->setCategory($category);
						$products = $products->getProductsByCategory();
					}
					else if(isset($_POST["submitSearchByPrice"]))
					{
						if(isset($_POST["prodPriceMin"]) && !empty($_POST["prodPriceMin"]))
						{
							$min = $_POST["prodPriceMin"];
							$min = sanitize($min);
						}
						else
							$min=null;
						
						if(isset($_POST["prodPriceMax"]) && !empty($_POST["prodPriceMax"]))
						{
							$max = $_POST["prodPriceMax"];
							$max = sanitize($max);
						}
						else
							$max = null;
						
						$products = $products->getProductsByPriceRange($min, $max);
					}
					else
					{
						$products = $products->getAllProducts();
					}
				}
				catch(DLException $dle)
				{
					echo "<h3 class='error'>Something went wrong. Contact Admin</h2>";
				}
				
				if(count($products)>0)
				{
					echo "
					<form action='index.php' method='post'>
						<table border='1'>
							<tr>
								<th>Name</th>
								<th>Category</th>
								<th>Available Qty</th>
								<th>Price</th>
								<th>Check to Add</th>
								<th>Order Qty</th>
							</tr>
					";
					foreach($products as $singleProduct)
					{
						$sku = $singleProduct->getSKU();
						echo "<tr>";
						echo "<td>{$singleProduct->getName()}</td>";
						echo "<td>{$singleProduct->getCategory()}</td>";
						echo "<td>{$singleProduct->getQuantity()}</td>";
						echo "<td>\${$singleProduct->getPrice()}</td>";
						echo "
							<td>
								<input 
									type='checkbox' 
									name='skuSelected[]'
									value='$sku'
									onClick='toggleTextBox($sku)'
								>
							</td>
						";
						echo "
							<td>
								<input type='number' min='1' name='skuQty$sku' id='skuQty$sku' disabled>
							</td>
						";
						echo "</tr>";
					}
					echo "</table>";
					echo "*Please Note that you need to check the box and enter quantity for adding a product to cart</br></br>";
					
					//disabling add to cart button if user is not logged in
					$disabled="";
					if(!isset($_SESSION["uid"]))
						$disabled = "disabled";
					
					echo "<input type='submit' name='submitAddToCart' value='Add to Cart!' $disabled>";
					echo "</form>";
				}
				else
					echo "<h4>No Products Found</h4>"
			?>
		</div>
	</body>
</html>