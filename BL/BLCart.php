<?php

class BLCart extends DLCart
{
	//First we need to check if product already exists in user's cart. If yes, then just add qty(update)
	//whenever a product is inserted, we need to make sure the quantity is lesser than available quantity
	function insert()
	{
		$cart = new DLCart($this->getUid(), $this->getSKU());
		$qtyPresent = $cart->getQuantityForCart();
		
		$insertQty = $this->getQuantity() + $qtyPresent; //updated quantity
		$insertSKU = $this->getSKU();
		
		$product = new BLProduct($insertSKU);
		$product = $product->getProductBySku();
		$qtyAvailable = $product->getQuantity();
		
		if($insertQty <= $qtyAvailable)
		{
			try
			{
				if($qtyPresent===0)
				{
					$insertSuccess = parent::insert();
					return $insertSuccess;
				}
				else
				{
					$this->setQuantity($insertQty);
					$updateSuccess = parent::updateQuantityForCart();
					return $updateSuccess;
				}
			}
			catch(DLException $dle)
			{
				throw $dle;
			}
		}
		else
			return false;
	}
}

?>