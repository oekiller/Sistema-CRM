<nav>
			<ul>
			
				<li  class="principal">
				
				    <?php
                        if($_SESSION['rol'] ==1 || $_SESSION['rol'] == 2){    
                    ?>
				
				    <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
				    
				    <ul>
				        <li><a href="configUser.php"> <i class="fas fa-user-cog"></i> Configuracion de Usuario</a></li>
				    </ul>
				</li>
				
				<?php } ?>
				
				<?php
                    if($_SESSION['rol'] ==1){    
                ?>
				
				<li class="principal">
				
					<a href="lista_usuarios.php"> <i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"> <i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php"> <i class="fas fa-address-book"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php } ?>
				<li class="principal">
					<a href="lista_clientes.php"> <i class="fas fa-user-tie"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php"> <i class="fas fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"> <i class="fas fa-address-book"></i> Lista de Clientes</a></li>
					</ul>
				</li>
				
				<?php
                    if($_SESSION['rol'] ==1 || $_SESSION['rol'] == 2){    
                ?>
				
				<li class="principal">
					<a href="lista_provedor.php"> <i class="fas fa-building"></i> Proveedores</a>
					<ul>
						<li><a href="registro_provedor.php"> <i class="fas fa-plus-square"></i> Nuevo Proveedor</a></li>
						<li><a href="lista_provedor.php"> <i class="fas fa-address-book"></i> Lista de Proveedores</a></li>
					</ul>
				</li>
				
				<?php } ?>
				
				<li class="principal">
					<a href="lista_producto.php"> <i class="fab fa-product-hunt"></i> Productos</a>
					<ul>
					
					    <?php
                            if($_SESSION['rol'] ==1 || $_SESSION['rol'] == 2){    
                        ?>
					
						<li><a href="registro_producto.php"> <i class="fab fa-product-hunt"></i> Nuevo Producto</a></li>
						<?php } ?>
						<li><a href="lista_producto.php"> <i class="fas fa-address-book"></i> Lista de Productos</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="nueva_venta.php"> <i class="fas fa-file-invoice-dollar"></i> Facturas</a>
					<ul>
						<li><a href="nueva_venta.php"> <i class="fas fa-file-invoice"></i> Nueva Venta</a></li>
						<li><a href="ventas.php"> <i class="fas fa-address-book"></i> Ventas</a></li>
					</ul>
				</li>
			</ul>
		</nav>