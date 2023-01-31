<?php

session_start();

include "../conexion.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php include "include/script.php"; ?>
    <?php include "include/header.php"; ?>


    <title>Lista de Productos</title>
</head>

<body>

    <section id="container">

        <?php

        $busqueda = '';
        $search_proveedor = '';

        if (empty($_REQUEST['busqueda']) && empty($_REQUEST['proveedor'])) {
            header("location: lista_producto.php");
        }

        if (!empty($_REQUEST['busqueda'])) {
            $busqueda = strtolower($_REQUEST['busqueda']);
            $where = "(p.codproducto LIKE '%$busqueda%' OR p.descripcion LIKE '%$busqueda%') AND p.estatus=1";
            $buscar = 'busqueda=' . $busqueda;
        }

        if (!empty($_REQUEST['proveedor'])) {
            $search_proveedor = $_REQUEST['proveedor'];
            $where = "p.proveedor LIKE $search_proveedor AND p.estatus = 1";
            $buscar = 'proveedor=' . $search_proveedor;
        }

        ?>

        <h1> <i class="fas fa-cubes"></i> LISTA DE PRODUCTOS</h1>
        <a href="registro_producto.php" class="btn_new"> CREAR PRODUCTO</a>

        <form action="buscar_producto.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar producto" value="<?php echo $busqueda; ?>">

            <input type="submit" value="Buscar" class="btn_search">

        </form>

        <table>

            <tr>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio</th>
                <th>Exixtencia</th>
                <th>
                    <?php

                    $pro = 0;

                    if (!empty($_REQUEST['proveedor'])) {
                        $pro = $_REQUEST['proveedor'];
                    }

                    $query_proveedor = mysqli_query($conexion, "SELECT * FROM proveedor WHERE estatus = 1 ORDER BY proveedor ASC");

                    $result_proveedor = mysqli_num_rows($query_proveedor);

                    ?>
                    <select name="proveedor" id="search_proveedor">

                        <option value="" selected disabled>PROVEEDOR</option>

                        <?php
                        if ($result_proveedor > 0) {
                            while ($proveedor = mysqli_fetch_array($query_proveedor)) {

                                if ($pro == $proveedor["codproveedor"]) {
                        ?>
                                    <option value="<?php echo $proveedor['codproveedor']; ?>" selected><?php echo $proveedor['proveedor']; ?></option>

                                <?php
                                } else {
                                ?>

                                    <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>

                        <?php
                                }
                            }
                        }


                        ?>

                    </select>

                </th>
                <th>Foto</th>
                <th>Acciones</th>
            </tr>

            <?php

            $sql_registe = mysqli_query($conexion, "SELECT COUNT(*) as total_registro FROM producto as p WHERE $where");

            $result_register = mysqli_fetch_array($sql_registe);
            $total_registro = $result_register['total_registro'];


            $por_pagina = 3;

            if (empty($_GET['pagina'])) {
                $pagina = 1;
            } else {
                $pagina = $_GET['pagina'];
            }

            $desde = ($pagina - 1) * $por_pagina;
            $total_paginas = ceil($total_registro / $por_pagina);

            $query = mysqli_query($conexion, "SELECT p.codproducto, p.descripcion, p.precio, p.existencia, pr.proveedor, p.foto FROM  producto p INNER JOIN proveedor pr ON p.proveedor = pr.codproveedor WHERE $where ORDER BY p.codproducto DESC LIMIT $desde, $por_pagina");


            $result = mysqli_num_rows($query);

            if ($result > 0) {

                while ($data = mysqli_fetch_array($query)) {

                    if ($data['foto'] != 'img_producto.png') {
                        $foto = 'img/uploads/' . $data['foto'];
                    } else {
                        $foto = 'img/' . $data['foto'];
                    }

            ?>
                    <tr class="row<?php echo $data["codproducto"]; ?>">
                        <td><?php echo $data["codproducto"]; ?></td>
                        <td><?php echo $data["descripcion"]; ?></td>
                        <td class="cellPrecio"><?php echo $data["precio"]; ?></td>
                        <td class="cellExistencia"><?php echo $data["existencia"]; ?></td>
                        <td><?php echo $data["proveedor"]; ?></td>
                        <td class="img_producto"><img src="<?php echo $foto; ?>" alt="<?php echo $data["descripcion"]; ?>"></td>



                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>

                            <td>
                                <a class="link_add add_product" product="<?php echo $data["codproducto"]; ?>" href="">Agregar</a>
                                <a class="link_edit" href="editar_producto.php?id=<?php echo $data["codproducto"]; ?>">Editar</a>
                                <a class="link_delete del_product" href="#" product="<?php echo $data["codproducto"]; ?>">Eliminar</a>

                            </td>

                        <?php } ?>
                    </tr>
            <?php

                }
            }

            ?>


        </table>

        <?php if ($total_paginas != 0) { ?>

            <div class="paginador">
                <ul>

                    <?php
                    if ($pagina != 1) {
                    ?>

                        <li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>">|<< /a>
                        </li>
                        <li><a href="?pagina=<?php echo $pagina - 1; ?>&<?php echo $buscar; ?>">
                                <<< /a>
                        </li>

                    <?php

                    }

                    for ($i = 1; $i <= $total_paginas; $i++) {

                        if ($i == $pagina) {
                            echo '<li class="selecte">' . $i . '</li>';
                        } else {
                            echo '<li><a href="?pagina=' . $i . '&' . $buscar . '">' . $i . '</a></li>';
                        }
                    }

                    if ($pagina != $total_paginas) {
                    ?>

                        <li><a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar; ?>">>></a></li>
                        <li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>">>>|</a></li>

                    <?php } ?>
                </ul>
            </div>

        <?php } ?>

    </section>

    <?php include "include/footer.php"; ?>
</body>

</html>