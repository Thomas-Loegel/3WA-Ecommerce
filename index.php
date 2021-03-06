<?php
	session_start();

	//Connection à la BDD
	require('config.php');
	$link = mysqli_connect($localhost, $login, $pass, $database);

	if (!$link) 
	{
	    require('views/bigerror.phtml');
	    exit;
	}

	//Autoload des classes 
	function __autoload($className)
	{
			require ("models/".$className.".class.php");
	}

	$access = array('search','search_result', 'logout', 'login', 'register', 'confirm_delete','change_password', 'address', 'add_edit_address',  'home', 'shop', 'product', 'current_cart', 'profile', 'cart', 'edit_contact', 'feedback', 'cat_admin', 'add_edit_cat', 'add_edit_sub_cat', 'product_admin', 'old_cart', 'add_edit_feedback');
	$page = 'home'; /*page courante : home par default*/ 
	$error = '';
	
	if (isset($_SESSION['success']))
	{
		$success = $_SESSION['success'];
		$_SESSION['success'] = '';
	}
	else
		$success = '';

	if (isset($_GET['page']))
	{
		if (in_array($_GET['page'], $access))
			$page = $_GET['page'];
	}

	$access_traitement = array(
								"login" => "user", 
								"logout" => "user", 
								"register" => "user", 
								"edit_contact" => "user",
								"change_password" => "user",
								"profile" => "user",
								"current_cart" => "cart",
								"product" => "cart",
								"cart" => "cart",  
								"feedback" => "feedback",
								"add_edit_feedback" => "feedback",
								"cat_admin" => "cat",
								"add_edit_cat" => "cat",
								"add_edit_sub_cat" => "cat",
								"address" => "address",
								"add_edit_address" => "address",
								"product_admin" => "product"
								);
	
	if (array_key_exists($page, $access_traitement))
		require('apps/treatments/traitement_'.$access_traitement[$page].'.php');
	if (isset($_GET['ajax']))
		require('apps/contents/search_result.php');
	else
		require 'apps/skel.php';
?>