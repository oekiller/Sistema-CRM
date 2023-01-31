<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<?php include "include/script.php"; ?>


	<title>SISTEMA CRM</title>
</head>

<body>

	<?php

	include "include/header.php";
	include "../conexion.php";


	$query_dash = mysqli_query($conexion, "CALL dataDashboard();");

	// $query_dash = mysqli_query($conexion, "SELECT COUNT(*) as cantidad_registros from cliente c INNER JOIN usuario u on c.usuario_id=u.idusuario INNER JOIN proveedor pr on u.idusuario=pr.usuario_id WHERE c.estatus > 0 and u.estatus > 0 and pr.estatus > 0");
 
	$result_dash = mysqli_num_rows($query_dash);

	if ($result_dash > 0) {
		$data_dash = mysqli_fetch_assoc($query_dash);
	}

	?>


	<section id="container">



		<div class="divContainer">
			<?php

			if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {


			?>
				<div>
					<h1 class="titleDatos">DATOS DEL SISTEMA</h1>
				</div>

				<div class="dashboard">

					<a href="lista_clientes.php">

						<img class="img1" src="img/clientes.png" alt="clientes">
						<p>
							<strong>Clientes</strong>

						</p>
						<span><?php echo $data_dash['clientes']; ?></span>

					</a>

					<a href="lista_provedor.php">

						<img class="img2" src="img/proveedores.png" alt="proveedores">
						<p>
							<strong>Proveedores</strong>

						</p>
						<span><?php echo $data_dash['proveedores']; ?></span>

					</a>

					<a href="lista_producto.php">

						<img class="img3" src="img/productos.png" alt="productos">
						<p>
							<strong>Productos</strong>

						</p>
						<span><?php echo $data_dash['productos']; ?></span>

					</a>

					<a href="ventas.php">

						<img class="img4" src="img/facturas.png" alt="facturas">
						<p>
							<strong>Facturas</strong>

						</p>
						<span><?php echo $data_dash['ventas']; ?></span>

					</a>



				</div>

		</div>
	<?php } ?>
	</section>

	<?php include "include/footer.php"; ?>
</body>

</html>