<?php
if (!isset($_SESSION['auth']))
{
	header('Location: index.php?page=home');
	die();
}
else
{
	if (!($_SESSION['permissions'] & CLIENT))
	{
		header('Location: index.php?page=home');
		die();
	}
}
if (!isset($_GET['id_product']))
{
	header('Location: index.php?page=home');
	die();

}
else
{
	$select = $db->query("SELECT COUNT(*) AS matchId FROM product WHERE id_product = ".$_GET['id_product'])->fetch(PDO::FETCH_ASSOC);
	if (!$select['matchId'])
	{
		header('Location: index.php?page=home');
		die();
	}
}
// ============== Formulaire note


	/*if (isset($_POST["action"])) echo "toto0". $_POST["action"] . "<br>";// . $_POST["action"] . "<br>";
	if (isset($_POST["comment"])) echo "toto1". $_POST["comment"] . "<br>";   
	if (isset($_POST["satisfaction"])) echo "toto2". $_POST["satisfaction"] . "<br>";  	 
	if (isset($_SESSION["id"])) echo "toto3". $_SESSION["id"] . "<br>";    
	if (isset($_GET["id_product"])) echo "toto4". $_GET["id_product"]. "<br>";*/
	$message = ''; // Contient les messages d'erreur à afficher
	if (!empty($_POST['action']) AND ($_POST['action'] == 'publish_note')) // Si le formulaire est validé
	{
		if ( !empty($_POST['comment']) AND ( !empty($_POST['satisfaction'])))  // Si les champs requis sont remplis
		{
			$product_id = $_GET['id_product'];

			// Suppression espaces
			$comment = trim($_POST['comment']);
			$satisfaction = trim($_POST['satisfaction']);

			// Sécurité
			$comment = $db->quote($comment);
			$satisfaction = $db->quote($satisfaction);


			// INSERT SQL
			$query = 'INSERT INTO note (client_id, product_id, satisfaction, comment, time_create) 
					  VALUES ('.$_SESSION["id"].','.$product_id.','.$satisfaction.','. $comment .',NOW())';
			$db->exec($query);
			$_SESSION['message'] = '<div class="alert alert-success" role="alert">Votre avis a été publié avec succès ! <a href="'.$_SERVER['HTTP_REFERER'].'">Retour à la page précédente</a></div>';
			header('Location: index.php?page=process');
		}
		else
		{
			$message = '<div class="alert alert-danger" role="alert">Tous les champs sont requis pour publier un avis.</div>';
		}
	}

	// Requête nombre de notes à afficher
	$select4 = $db->query("SELECT COUNT(*) AS nbRows4 FROM note where product_id = ".$_GET['id_product'])->fetch(PDO::FETCH_ASSOC);

	// On calcule le nombre de pages à créer  :gestion de la pagination
	$itemsPage = 3;
	$nbPage = ceil($select4['nbRows4'] / $itemsPage);

	// Vérifie que la page demandée existe sinon choix première ou dernière page
	if (isset($_GET['pagin']))
	{
		$pagin = $_GET['pagin'];
		if ($pagin > $nbPage)
		{
		$pagin = $nbPage;
		}
		if ($pagin < 1)
		{
		$pagin = 1;
		}
	}
	else
	{
		$pagin = 1;
	}
	// On calcule le numéro de la première ligne qu'on prend pour le LIMIT de MySQL LIMIT".$firstRow.','.$itemsPage
	$firstRow = ($pagin - 1) * $itemsPage;

	// Requête de sélection pour affichage <td>'.$row['pseudo'].'</td>
	//$select5 = $db->query("SELECT note.id as mynote_id, note.client_id as mynote_clientid, note.product_id as mynote_productid, note.satisfaction as mynote_satisfaction, note.comment as mynote_comment, time_create as mynote_timecreate, product.id_product FROM note JOIN product ON product.id_product = note.product_id where note.product_id = " .$_GET['id_product'] . " ORDER BY time_create DESC LIMIT ".$firstRow.','.$itemsPage)->fetchAll(PDO::FETCH_ASSOC);
	$select5 = $db->query("SELECT note.id as mynote_id, note.client_id as mynote_clientid, note.product_id as mynote_productid, note.satisfaction as mynote_satisfaction, note.comment as mynote_comment, time_create as mynote_timecreate, product.name as myproduct_name, client.pseudo as myclient_name, product.id_product FROM note JOIN product ON product.id_product = note.product_id JOIN client ON client.id = note.client_id where note.product_id = " .$_GET['id_product'] . " ORDER BY time_create DESC LIMIT ".$firstRow.','.$itemsPage)->fetchAll(PDO::FETCH_ASSOC);	
	$select6 = $db->query("SELECT product.name  as myproduct_name2 from product where product.id_product = " .$_GET['id_product'] )->fetch(PDO::FETCH_ASSOC);

	//echo "$select5[0] : "; 

	function displayLoop2($select5)
	{
		foreach ($select5 as $row)
		{
			if (strlen($row['mynote_comment'])> 50)
			{   
				$row['mynote_comment'] = limite(50, $row['mynote_comment']);
			}
			// voir pour les images des indices de satisfaction
			$i = 0;
			$etoile = "";
			while ($i < $row['mynote_satisfaction'])
			{
				$etoile = $etoile . "★"; 
				$i++;
			}
			require('./note/displayLoop.phtml');
		}
	}
	require('./note/index.phtml');
?>