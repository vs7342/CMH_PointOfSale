<?php
	require_once("utility.php");//This file consists of all the helper/utility functions which would be used throughout the application layer.
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CMH - Interview 2</title>
		<script>
			function onPurchaseSubmit()
			{
				var popUp = confirm("Are you sure you want to purchase these items?");
				if (popUp == true)
				{
					return true;
				} 
				else 
				{
					return false;
				} 
			}
		</script>
	</head>
	<body>
		<div id="navigation">
			<?php 
				echo getNavigation();
				
				$successMsg="";
				$failureMsg="";
				$warningMsg="";
			?>
		</div>
		<?php
			/*Processing purchase of items*/
			if(isset($_POST["submitPurchase"]))
			{
				$uid = $_SESSION["uid"];
				$cart = new BLCart($uid);
				try
				{
					$cart = $cart->getCartForUser();
				}
				catch(DLException $dle)
				{
					$failureMsg = "Something went wrong. Contact Admin!";
				}
				if(count($cart)>0)
				{
					$purchaseSuccess = true;
					foreach($cart as $singleCart)
					{
						$prodSku = $singleCart->getSKU();
						$product = new BLProduct($prodSku);
						$reduceQuantity = $singleCart->getQuantity();
						try
						{
							//Reducing the quantity only when purchased
							$purchaseSuccess = $purchaseSuccess && $product->reduceQuantity($reduceQuantity);
							
							//deleting that particular cart item from cart
							$purchaseSuccess = $purchaseSuccess && $singleCart->deleteCart();
						}
						catch(DLException $dle)
						{
							$failureMsg = "Something went wrong. Contact Admin!";
						}
						if($purchaseSuccess)
							$successMsg = "Congratulations. You have successfully placed the order";
					}
				}
			}
		
			/*Processing Removal of item from cart*/
			if(isset($_POST["submitDeleteProduct"]))
			{
				$sku = $_POST["sku"];
				$uid = $_SESSION["uid"];
				$cart = new BLCart($uid, $sku);
				try
				{
					if($cart->deleteCart())
						$successMsg = "Product successfully removed from cart";
					else
						$failureMsg = "Error removing product from cart";
				}
				catch(DLException $dle)
				{
					$failureMsg = "Something went wrong. Contact Admin!";
				}
			}
		?>
		<div class="warning">
			<?php
				echo $warningMsg;
			?>
		</div>
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
		<div id="products">
			<h3>Cart Details</h3>
			<?php
				if(isset($_SESSION["uid"]))
				{
					$uid = $_SESSION["uid"];
					$cart = new BLCart();
					$cart->setUid($uid);
					try
					{
						$cart = $cart->getCartForUser(); //returns array of cart object
					}
					catch(DLException $dle)
					{
						echo "<h3>Something went wrong. Contact Admin</h3>";
						die();
					}
					if(count($cart)>0)
					{
						echo "
							<table border='1'>
								<tr>
									<th>Product Name</th>
									<th>Category</th>
									<th>Product Quantity</th>
									<th>Price per qty.</th>
									<th>Tax per qty.</th>
									<th>Price</th>
									<th>Remove Item</th>
								</tr>
						";
						$cartPrice = 0;
						
						$productQtyAvailable = true;
						foreach($cart as $singleCart)
						{
							$sku = $singleCart->getSKU();
							$product = new BLProduct($sku);
							try
							{
								$product = $product->getProductBySku();
							}
							catch(DLException $dle)
							{
								echo "<h3>Something went wrong. Contact Admin</h3>";
								die();
							}
							
							//Product Price Calculation (Tax is applied at this stage)
							$productQuantity = $singleCart->getQuantity();
							$productRate = $product->getPrice();
							$productTax = $product->getTax();
							$productPrice = $productQuantity *($productRate + $productTax);
							
							//Checking the inventory 
							//If enough qty is present, then fine. If not, then the row will have a different color.
							//User will be prompted to delete and add the item again with lower quantity.
							$prodAvailableQty = $product->getQuantity();
							if($prodAvailableQty>=$productQuantity)
							{
								$prodErrorStyle = "";
							}
							else
							{
								$prodErrorStyle = "style='color:red'";
								$productQtyAvailable = false;
							}
							echo "
								<tr $prodErrorStyle>
									<td>{$product->getName()}</td>
									<td>{$product->getCategory()}</td>
									<td>$productQuantity</td>
									<td>\$$productRate</td>
									<td>\$$productTax</td>
									<td>\$$productPrice</td>
									<td>
										<form action='cart.php' method='post'>
											<input type='hidden' name='sku' value='$sku'>
											<input type='submit' name='submitDeleteProduct' value='Remove Item'>
										</form>
									</td>
								</tr>
							";
							$cartPrice+=$productPrice;
						}
						echo "</table>";
						echo "<h3>Total Cost: \$$cartPrice</h3>";
						if($productQtyAvailable)
						{	
							echo "
								<form action='cart.php' onsubmit='return onPurchaseSubmit()' method='post'>
									<input type='submit' name='submitPurchase' value='Purchase items'>
								</form>
							";
						}
						else
							echo "<h4>Please note that highlighted products must be re-inserted to the cart with lesser Qty. Not enough stock present.</h4>";
					}
					else
					{
						echo "<h4>No Products Found. Visit Home page to add products to cart</h4>";
					}
				}
				else
				{
					echo "<h4>Please Login to access the contents of cart</h4>";
				}
			?>
		</div>
	</body>
</html>