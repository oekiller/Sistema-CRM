$(document).ready(function(){

    //--------------------- SELECCIONAR FOTO PRODUCTO ---------------------
    $("#foto").on("change",function(){
    	var uploadFoto = document.getElementById("foto").value;
        var foto       = document.getElementById("foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.getElementById('form_alert');
        
            if(uploadFoto !='')
            {
                var type = foto[0].type;
                var name = foto[0].name;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png')
                {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';                        
                    $("#img").remove();
                    $(".delPhoto").addClass('notBlock');
                    $('#foto').val('');
                    return false;
                }else{  
                        contactAlert.innerHTML='';
                        $("#img").remove();
                        $(".delPhoto").removeClass('notBlock');
                        var objeto_url = nav.createObjectURL(this.files[0]);
                        $(".prevPhoto").append("<img id='img' src="+objeto_url+">");
                        $(".upimg label").remove();
                        
                    }
              }else{
              	alert("No selecciono foto");
                $("#img").remove();
              }              
    });

    $('.delPhoto').click(function(){
    	$('#foto').val('');
    	$(".delPhoto").addClass('notBlock');
    	$("#img").remove();
        
        if($("#foto_actual") && $("#foto_remove")){
            $("#foto_remove").val('img_producto.png');
        }

    });
    
//    MODAL DEL PRODUCTO
    
    $('.add_product').click(function(event){
        
        event.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';
        
        $.ajax({
            url:'ajax.php',
            type:'POST',
            async: true,
            data: {action:action,producto:producto},
            
               success: function(response){
                   
                   if(response != 'error'){
                        
                       var info = JSON.parse(response);
                       
                       $('#producto_id').val(info.codproducto);
                       $('.nameProducto').html(info.descripcion);
                    }
            },
                error: function(error){
            }
            
        });
        
        $('.modal').fadeIn();
    });
    
    
    $('.del_product').click(function(event){
        
        event.preventDefault();
        var producto = $(this).attr('product');
        var action = 'infoProducto';
        
        $.ajax({
            url:'ajax.php',
            type:'POST',
            async: true,
            data: {action:action,producto:producto},
            
               success: function(response){
                   
                   if(response != 'error'){
                        
                       var info = JSON.parse(response);
                       
                      $('.bodyModal').html('<form action="" method="post" name="form_del_product" id="form_del_product" onsubmit="event.preventDefault(); delProduct();">'+
                                            '<h1><i class="fas fa-cubes" style="font-size: 45pt;"></i> <br> Eliminar Producto</h1>'+
                                            '<h2 style="margin-top:30px">¿ESTAS SEGURO DE ELIMINAR ESTE PRODUCTO?</h2>'+
		                                    '<br>'+'<h2 class="nameProducto">'+info.descripcion+'</h2><br>'+
                                            '<input type="hidden" name="producto_id" id="producto_id" value="'+info.codproducto+'" required>'+
                                            '<input type="hidden" name="action" value="delProduct" required>'+
                                            '<div class="alert alertAddProduct"></div>'+
                                           '<button style="font-size:13px;" type="submit" class="btn_ok">Aceptar</button>'+
                                           '<a href="#" class="btn_cancel" onclick="closeModal();">Cerrar</a>'+
                                            '</form>');
                    }
            },
                error: function(error){
            }
            
        });
        
        $('.modal').fadeIn();
    });
    
    
    $('#search_proveedor').change(function(event){
        
        event.preventDefault();
        
        var sistema = getUrl();
        location.href = sistema+'buscar_producto.php?proveedor='+$(this).val();
                                  
    });
    
    
    $('.btn_new_cliente').click(function(event){
        event.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');
        $('#dir_cliente').removeAttr('disabled');
        
        $('#div_registro_cliente').slideDown();
        
    });
    
    
    $('#nit_cliente').keyup(function(event){
       event.preventDefault();
        
        var cl = $(this).val();
        var action = 'searchCliente';
        
        $.ajax({
           url: 'ajax.php',
           type: "POST",
           async: true,
           data:{action:action,cliente:cl},
            
            success: function(response){
                
                if(response == 0){
                   $('#idcliente').val('');
                   $('#nom_cliente').val('');
                   $('#tel_cliente').val('');
                   $('#dir_cliente').val('');
                    
                    $('.btn_new_cliente').slideDown();
                }else{
                    var data = $.parseJSON(response);
                    $('#idcliente').val(data.idcliente);
                    $('#nom_cliente').val(data.nombre);
                    $('#tel_cliente').val(data.telefono);
                    $('#dir_cliente').val(data.direccion);
                    
                    $('.btn_new_cliente').slideUp();
                    
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    
                    $('#div_registro_cliente').slideUp();
                    
                }
            },
            error: function(error){
                
            }
        });
    });
    
    
    $('#form_new_cliente_venta').submit(function(event){
        event.preventDefault();
        
         $.ajax({
           url: 'ajax.php',
           type: "POST",
           async: true,
           data: $('#form_new_cliente_venta').serialize(),
            
            success: function(response){
                
                if(response != 'error'){
                    $('#idcliente').val(response);
                    
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    
                    $('.btn_new_cliente').slideUp();
                    $('#div_registro_cliente').slideUp();
                }
            },
            error: function(error){
                
            }
        });
    });
    
    
    $('#txt_cod_producto').keyup(function(event){
        event.preventDefault();
        
        var producto = $(this).val();
        var action = 'infoProducto';
        
        if(producto != ''){
            
            $.ajax({
               url: 'ajax.php',
               type: "POST",
               async: true,
               data: {action:action,producto:producto},

                success: function(response){
                    
                    if(response != 'error'){
                       
                        var info = JSON.parse(response);
                        
                        $('#txt_descripcion').html(info.descripcion);
                        $('#txt_existencia').html(info.existencia);
                        $('#txt_cant_producto').val('1');
                        $('#txt_precio').html(info.precio);
                        $('#txt_precio_total').html(info.precio);
                        
                        $('#txt_cant_producto').removeAttr('disabled');
                        
                        $('#add_product_venta').slideDown();
                    }else{
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');
                        
                        $('#txt_cant_producto').attr('disabled','disabled');
                        
                        $('#add_product_venta').slideUp();
                    }

                },
                error: function(error){

                }
            });
        }
        
       
    });
    
    
    $('#txt_cant_producto').keyup(function(event){
       event.preventDefault();
        
        var precio_total = $(this).val() * $('#txt_precio').html();
        var existencia = parseInt($('#txt_existencia').html());
        
        $('#txt_precio_total').html(precio_total);
        
        if(($(this).val() < 1 || isNaN($(this).val())) || ($(this).val() > existencia)){
           $('#add_product_venta').slideUp();
        }else{
            $('#add_product_venta').slideDown();
        }
    });
    
    
    $('#add_product_venta').click(function(event){
       event.preventDefault();
        
        if($('#txt_cant_producto').val() > 0){
            
            var codproducto = $('#txt_cod_producto').val();
            var cantidad = $('#txt_cant_producto').val();
            var action = 'addProductoDetalle';
            
               $.ajax({
                   url: 'ajax.php',
                   type: "POST",
                   async: true,
                   data: {action:action,producto:codproducto,cantidad:cantidad},

                    success: function(response){
                        
                        if(response != 'error'){
                            var info = JSON.parse(response);
                            
                            $('#detalle_venta').html(info.detalle);
                            $('#detalle_totales').html(info.totales);
                            
                            $('#txt_cod_producto').val('');
                            $('#txt_descripcion').html('-');
                            $('#txt_existencia').html('-');
                            $('#txt_cant_producto').val('0');
                            $('#txt_precio').html('0.00');
                            $('#txt_precio_total').html('0.00');
                            
                            $('#txt_cant_producto').attr('disabled','disabled');
                            
                            $('#add_product_venta').slideUp();
                            
                            
                           
                        }else{
                            
                        }
                        
                        viewProcesar();


                    },
                    error: function(error){

                    }
                });
        }
    });
    
    
    
     $('#btn_anular_venta').click(function(event){
       event.preventDefault();
         
         var rows = $('#detalle_venta tr').length;
        
        if(rows > 0){
            var action = 'anularVenta';
            
               $.ajax({
                   url: 'ajax.php',
                   type: "POST",
                   async: true,
                   data: {action:action},

                    success: function(response){
                       
                        if(response != 'error'){
                           location.reload();
                        }
                    },
                    error: function(error){

                    }
                });
        }
    });
    
    
    
     $('#btn_facturar_venta').click(function(event){
       event.preventDefault();
         
         var rows = $('#detalle_venta tr').length;
        
        if(rows > 0){
            
            var action = 'procesarVenta';
            var codcliente = $('#idcliente').val();
            
               $.ajax({
                   url: 'ajax.php',
                   type: "POST",
                   async: true,
                   data: {action:action,codcliente:codcliente},

                    success: function(response){
                       
                        if(response != 'error'){
                            
                            var info = JSON.parse(response);
                           
                        Swal.fire({
                               icon: 'success',
                               title: 'FACTURA PROCESADA'
                              }).then(function(){
                              
                                generarPDF(info.codcliente,info.nofactura)
                                location.reload(); 

                                });
                                      
                        }else{
                            
                            console.log('no data');
                        }
                    },
                   
                    error: function(error){

                    }
                });
        }
    });
    
    
    
     $('.anular_factura').click(function(event){
        
        event.preventDefault();
        var nofactura = $(this).attr('fac');
        var action = 'infoFactura';
        
        $.ajax({
            url:'ajax.php',
            type:'POST',
            async: true,
            data: {action:action,nofactura:nofactura},
            
               success: function(response){
                   
                   if(response != 'error'){
                        
                        var info = JSON.parse(response);
                      
                       
                      $('.bodyModal').html('<form action="" method="post" name="form_anular_factura" id="form_anular_factura"                               onsubmit="event.preventDefault(); anularFactura();">'+
                                            '<h1><i class="fas fa-file-invoice-dollar" style="font-size: 43pt;"></i> <br> Anular Factura</h1>'+
                                            '<h2 style="margin-top:30px">¿ESTAS SEGURO DE ANULAR LA FACTURA?</h2>'+
                                           
                                           '<p><strong>No. '+info.nofactura+'</strong></p>'+
                                           '<p><strong>Monto. $ '+info.totalfactura+'</strong></p>'+
                                           '<p><strong>Fecha. '+info.fecha+'</strong></p>'+
                                           '<input type="hidden" name="action" value="anularFactura">'+
                                           '<input type="hidden" name="no_factura" id="no_factura" value="'+info.nofactura+'"required>'+
                                           
                                           
                                            '<div class="alert alertAddProduct"></div>'+
                                           '<button style="font-size:13px;" type="submit" class="btn_ok">Anular</button>'+
                                           '<a href="#" class="btn_cancel" onclick="closeModal();">Cerrar</a>'+
                                            '</form>');
                    }
            },
                error: function(error){
            }
            
        });
        
        $('.modal').fadeIn();
    });
    
    
    
    $('.view_factura').click(function(event){
        event.preventDefault();
        var codCliente = $ (this).attr('cl');
        var noFactura = $ (this).attr('f');
        generarPDF(codCliente,noFactura);
        
    });
    
    
    
    $('.newPass').keyup(function(){
        
        validPass();
    });
    
    
    $('#frmChangePass').submit(function(event){
       
        event.preventDefault();
        
        var passActual = $('#txtPassUser').val();
        var passNuevo = $('#txtNewPassUser').val();
        var confirmPassNuevo = $('#txtPassConfirm').val();
        var action = "changePassword";
        
        if(passNuevo != confirmPassNuevo){
            $('.alertChangePass').html('<p style="color:red;">Las contraseñas no coinciden.</p>');   
            $('.alertChangePass').slideDown(); 
            return false;
        }
        
        if(passNuevo.length < 6){
           $('.alertChangePass').html('<p style="color:red;">Las contraseñas deben tener al menos 6 caracteres.</p>'); 
           $('.alertChangePass').slideDown(); 
            return false;
        }
        
        $.ajax({
           url: 'ajax.php',
           type: "POST",
           async: true,
           data: {action:action,passActual:passActual,passNuevo:passNuevo},
            
            success: function(response){
                
                if(response != 'error'){
                   
                    var info = JSON.parse(response);
                    
                    if(info.cod == '00'){
                        $('.alertChangePass').html('<p style="color:green;">'+info.msg+'</p>'); 
                        $('#frmChangePass')[0].reset();
                    }else{
                        $('.alertChangePass').html('<p style="color:red;">'+info.msg+'</p>'); 
                    }
                    $('.alertChangePass').slideDown(); 
                }
                
             
            },
            error: function(error){
                
            }
        });
        
    });
    
    
});

    function validPass(){
        var passNuevo = $('#txtNewPassUser').val();
        var confirmPassNuevo = $('#txtPassConfirm').val();
        
        if(passNuevo != confirmPassNuevo){
            $('.alertChangePass').html('<p style="color:red">Las contraseñas no coinciden.</p>');   
            $('.alertChangePass').slideDown(); 
            return false;
        }
        
        if(passNuevo.length < 6){
           $('.alertChangePass').html('<p style="color:red">Las contraseñas deben tener al menos 6 caracteres.</p>'); 
           $('.alertChangePass').slideDown(); 
            return false;
        }
        
        $('.alertChangePass').html('');
        $('.alertChangePass').slideUp();
        
    }


    function anularFactura(){
        var noFactura = $('#no_factura').val();
        var action = 'anularFactura';
        
        $.ajax({
           
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,noFactura:noFactura},
            
            success:function(response){
                
                if(response == 'error'){
                    
                        $('.alertAddProduct').html('<p style="color:red;">ERROR AL ANULAR LA FACTURA.</p>');    
                }else{
                    $('#row_'+noFactura+' .estado').html('<span class="anulada">Anulada</span>');
                    $('#form_anular_factura .btn_ok').remove();
                    $('#row_'+noFactura+' .div_factura').html('<button type="button" class="btn_anular inactive"> <i class="fas fa-ban"></i> </button>');
                    $('.alertAddProduct').html('<p>FACTURA ANULADA.</p>');
                }
                
            },
            error:function(error){
                
            }
        });
    }


    function generarPDF(cliente,factura){
        var ancho = 1000;
        var alto = 800;
        
        var x = parseInt((window.screen.width/2) - (ancho / 2));
        var y = parseInt((window.screen.height/2) - (alto / 2));
        
        $url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;
        window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
    }


    

    function del_product_detalle(correlativo){
        
        var action = 'delProductoDetalle';
        var id_detalle = correlativo;
        
         $.ajax({
                   url: 'ajax.php',
                   type: "POST",
                   async: true,
                   data: {action:action,id_detalle:id_detalle},

                    success: function(response){
                        
                        if(response != 'error'){
                            var info = JSON.parse(response);
                            
                            $('#detalle_venta').html(info.detalle);
                            $('#detalle_totales').html(info.totales);
                            
                            $('#txt_cod_producto').val('');
                            $('#txt_descripcion').html('-');
                            $('#txt_existencia').html('-');
                            $('#txt_cant_producto').val('0');
                            $('#txt_precio').html('0.00');
                            $('#txt_precio_total').html('0.00');
                            
                            $('#txt_cant_producto').attr('disabled','disabled');
                            
                            $('#add_product_venta').slideUp();
                            
                            
                        }else{
                            $('#detalle_venta').html('');
                            $('#detalle_totales').html('');
                        }
                        
                        viewProcesar();
                        
                    },
                    error: function(error){

                    }
                });
    }

    function viewProcesar(){
        if($('#detalle_venta tr').length > 0){
           $('#btn_facturar_venta').show();
        }else{
            $('#btn_facturar_venta').hide();
        }
    }


    function serchForDetalle(id){
        var action = 'serchForDetalle';
        var user = id;
        
         $.ajax({
                   url: 'ajax.php',
                   type: "POST",
                   async: true,
                   data: {action:action,user:user},

                    success: function(response){
                        
                            if(response != 'error'){
                            var info = JSON.parse(response);
                            
                            $('#detalle_venta').html(info.detalle);
                            $('#detalle_totales').html(info.totales);
                           
                        }else{
                            
                        }
                        
                        viewProcesar();
                        
                    },
                    error: function(error){

                    }
                });
    }


    function getUrl(){
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.lenght - ((loc.pathname + loc.search + loc.hash).lenght - pathName.lenght));
    
    }


    function sendDataProduct(){
        $('.alertAddProduct').html('');
        
        
         $.ajax({
            url:'ajax.php',
            type:'POST',
            async: true,
            data: $('#form_add_product').serialize(),
            
               success: function(response){
                   if(response == 'error'){
                      $('.alertAddProduct').html('<p style="color:red;">ERROR AL AGREGAR EL PRODUCTO.</p>');
                    
                   }else{
                       var info = JSON.parse(response);
                       
                       $('.row'+ info.producto_id+' .cellPrecio').html(info.nuevo_precio);
                       $('.row'+ info.producto_id+' .cellExistencia').html(info.nueva_existencia);
                       $('#txtCantidad').val('');
                       $('#txtPrecio').val('');
                       
                       $('.alertAddProduct').html('<p>PRODUCTO ACTUALIZADO CORRECTAMENTE.</p>');
                   }
            },
                error: function(error){
            }
            
        });
    
    }


    function delProduct(){
        
        var pr = $('#producto_id').val();
        
        $('.alertAddProduct').html('');
        
        
         $.ajax({
            url:'ajax.php',
            type:'POST',
            async: true,
            data: $('#form_del_product').serialize(),
            
               success: function(response){
                   if(response == 'error'){
                      $('.alertAddProduct').html('<p style="color:red;">ERROR AL ELIMINAR EL PRODUCTO.</p>');
                    
                   }else{
                       
                       $('.row'+ pr).remove();
                       $('#form_del_product .btn_ok').remove();
                       $('.alertAddProduct').html('<p>PRODUCTO ELIMINADO CORRECTAMENTE.</p>');
                   }
            },
                error: function(error){
            }
            
        });
    
    }


function closeModal(){
    
    $('.alertAddProduct').html('');
    $('#txtCantidad').val('');
    $('#txtPrecio').val('');
    
    $('.modal').fadeOut();
    location.reload();
}