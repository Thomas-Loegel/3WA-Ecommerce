<?php
	$products = $cart->getProducts();
	$i = 0;
	while ($i < count($products))
	{
		$quantity = $cartManager->getQuantity($products[$i], $cart);
		if ($i == 0 || ($i > 0 && $products[$i] != $products[$i-1]))
			require 'views/contents/display_product_oldcart.phtml';
		$i++;
	}
?>