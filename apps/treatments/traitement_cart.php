<?php
	if (isset($_POST['action']))
	{
		if (isset($_SESSION['id_user']))
		{
			if ($_SESSION['admin'] == 0)
			{
				$cartManager = new CartManager($link);
				$productsManager = new ProductsManager($link);
				$currentCart = $cartManager->findCurrentCart($_SESSION['user']);
				if ($_POST['action'] == 'addProduct')
				{
					$id_product = intval($_POST['id_product']);
					$product = $productsManager->findById($id_product);
					if (!isset($_POST['size']))
						$error = "Enter a size";
					else if (!isset($_POST['quantity']))
						$error = "Enter a quantity";
					else if ($_POST['quantity'] == '')
						$error = "Enter a quantity";
					else if ($_POST['quantity'] < 0)
						$error = "Enter a positive quantity ;)";
					else if ($_POST['quantity'] > $product->getStock())
						$error = "Sorry, the stock is the stock!";
					if (empty($error))
					{
						try
						{
							$quantity = intval($_POST["quantity"]);
							$currentCart->setNbProducts($quantity);
							$currentCart->addProduct($product, $quantity);
							$product->changeStock(-$quantity);
							$UpdateProducts = $currentCart->getUpdateProducts();
							$i=0;
							$price = 0;
							$weight = 0;
							while ($i < count($UpdateProducts))
							{
								$price = $price + $UpdateProducts[$i]->getPrice();
								$weight = $weight + $UpdateProducts[$i]->getWeight();
								$i++;
							}
							$currentCart->setPrice($price);
							$currentCart->setWeight($weight);
							$cartManager->update($currentCart);
							$productsManager->update($product);
							$_SESSION['success'] = "This product has been added in your cart";
							header('Location: index.php?page=product&id_product='.$id_product);
							exit;
						}
						catch (Exception $exception)
						{
							$error = $exception->getMessage();
						}
					}
				}
				if ($_POST['action'] == 'removeProduct')
				{
					if (!isset($_POST['quantity']))
						$error = "Enter a quantity";
					if (empty($error))
					{
						try
						{
							$product = $productsManager->findById($_POST['id_product']);
							$quantity = intval($_POST["quantity"]);
							$product->changeStock($quantity);
							$currentCart->removeProduct($product);
							$currentCart->setNbProducts(-$quantity);
							$UpdateProducts = $currentCart->getUpdateProducts();
							$i=0;
							$price = 0;
							$weight = 0;
							while ($i < count($UpdateProducts))
							{
								$price = $price + $UpdateProducts[$i]->getPrice();
								$weight = $weight + $UpdateProducts[$i]->getWeight();
								$i++;
							}
							$currentCart->setPrice($price);
							$currentCart->setWeight($weight);
							$cartManager->update($currentCart);
							$productsManager->update($product);
							$_SESSION['success'] = "This product has been removed of your cart";
							header('Location: index.php?page=current_cart');
							exit;
						}
						catch (Exception $exception)
						{
							$error = $exception->getMessage();
						}
					}
				}
				if ($_POST['action'] == 'valid')
				{
					$products = $currentCart->getProducts();
					if ($products == null)
						$error = "You can't check out an empty cart";
					$addressManager = new AddressManager($link);
					$address = $addressManager->findByUser($_SESSION['user']);
					if (empty($error) &&$adress == null)
					{
						$_SESSION['success'] = 'Please, Add an address before!';
						header('Location: index.php?page=address');
						exit;
					}			
					if (empty($error))
					{
						try
						{
						$currentCart->setStatus(1);
						$cartManager->update($currentCart);
						$cartManager->create();
						$_SESSION['success'] = "You have validate your cart. Waiting for validation by an admin";
						header('Location: index.php?page=profile');
						exit;
						}
						catch (Exception $exception)
						{
							$error = $exception->getMessage();
						}
					}

				}
			}
			else if ($_SESSION['admin'] == 1)
			{
				$cartManager = new CartManager($link);
				$productsManager = new ProductsManager($link);
				if ($_POST['action'] == 'addProduct')
					$error = "An admin can't buy product";
				if ($_POST['action'] == 'valid')
				{
					if (isset($_POST['id_cart']))
					{
						try
						{		
							$id_cart = intval($_POST['id_cart']);
							$cart = $cartManager->findById($id_cart);
							$cart->getProducts();
	 						$cart->setStatus(2);
	 						$cartManager->update($cart);
	 						$_SESSION['success'] = "This cart has been checked";
	 						header('Location: index.php?page=profile');
	 						exit;
	 					}
	 					catch (Exception $exception)
						{
							$error = $exception->getMessage();
						}
					}
				}
				if ($_POST['action'] == 'refuse')
				{
					if (isset($_POST['id_cart']))
					{
						try
						{	
							$productManager = new ProductsManager($link);
							$id_cart = intval($_POST['id_cart']);
							$cart = $cartManager->findById($id_cart);
							$products = $cart->getProducts();
	 						$cart->setStatus(3);
	 						$cartManager->update($cart);
	 						$i = 0;
	 						while ($i < count($products))
							{
								if ($i == 0 || ($i > 0 && $products[$i] != $products[$i-1]))
								{
									$quantity = $cartManager->getQuantity($products[$i], $cart);
									$products[$i]->changeStock($quantity);
									$productManager->update($products[$i]);
									$_SESSION['success'] = "This cart has been refused";
									header('Location: index.php?page=profile');
									exit;
								}
								$i++;
							}
	 					}
	 					catch (Exception $exception)
						{
							$error = $exception->getMessage();
						}
					}
				}
			}
		}
		else 
		{
			header('Location: index.php?page=login');
			exit;
		}
	}
?>