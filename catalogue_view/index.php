<?php
$select = $db->query("SELECT COUNT(*) AS matchId FROM product WHERE id_product = ".$_GET['id_product'])->fetch(PDO::FETCH_ASSOC);
if (!$select['matchId'])
{
	header('Location: index.php?page=home');
	die();
}
function displayAvis($id)
{
	if (isset($_SESSION['auth']) AND ($_SESSION['permissions'] & CLIENT))
	{
		require('./catalogue_view/displayAvis.phtml');
	}
}
/*$amount = "";
if (isset($_POST['amount'])) { $login = $_POST["amount"];}*/
//echo $_GET['id_product']; 
$amount = 0;
if (isset($_POST['amount']))
{ 
	$amount = $_POST["amount"];
}
// si on a choisis un produit, il faudras aller sur la fiche produit
if (!empty($_GET['id_product'])) 
{
	$selectsc3 = $db->query("SELECT id_product as myproduct3_idproduct,
									product.name        as myproduct3_name,
									product.description as myproduct3_description,
									sub_category_id     as myproduct3_subcategoryid,
									price               as myproduct3_price,
									image               as myproduct3_image,
									origine             as myproduct3_origine, 
									stock_quantity      as myproduct3_stockquantity,
									note_id             as myproduct3_noteid,
									sub_category.name   as myproduct3_subcategoryname
							FROM    sub_category, product 
							WHERE   id_sub_category = sub_category_id
							AND	    id_product = ".$_GET['id_product'] )->fetch(PDO::FETCH_ASSOC);
							    // id_sub_category = ".$_GET['id_subcategory'])->fetch(PDO::FETCH_ASSOC);
}
require('./catalogue_view/index.phtml');	
?>