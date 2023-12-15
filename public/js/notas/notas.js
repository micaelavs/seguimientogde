var filtros_dataTable = null;
$(document).ready(function () {
	if ($('.notas').length) { //para que actue Datatable sólo donde exista esta tabla, osea en el index
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Notas...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $url_base + '/index.php/notas/ajax_notas',
	            contentType: "application/json",
	            data: function (d) {
                filtros_dataTable = $.extend({}, d, {
                    id_area            		: $('#id_area').val(),
                    remitente               : $('#remitente').val(),
                    fecha_vencimiento  		: $('#fecha_vencimiento').val(),
                });
                return filtros_dataTable; 
            }
	        },
	        info: true,
	        bFilter: true,
	        columnDefs: [
		        { targets: 0, width: '10%'},
		        { targets: 1, width: '10%'},
		        { targets: 2, width: '10%'},
		        { targets: 3, width: '10%'},
				{ targets: 4, width: '10%' },
				{ targets: 5, width: '10%' },
				{ targets: 6, width: '10%' },
		        { targets: 7, width: '5%'}
	        ],
	        order: [[1,'asc']],
	        columns: [
	            {
	                title: 'Nota GDE',
	                name:  'nota',
	                data:  'nota',
	                className: 'text-left'
	            },
	            {
	                title: 'Fecha de vencimiento',
	                name:  'fecha_vencimiento',
	                data:  'fecha_vencimiento',
	                className: 'text-left',
	                render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY HH:II').format('DD/MM/YYYY'); 
						} 	
						return rta;
					}
	            },
	            {
	                title: 'Remitente',
	                name:  'remitente',
	                data:  'remitente',
	                className: 'text-left'
	            },
	            {
	                title: 'Tipo',
	                name: 'tipo',
	                data: 'tipo',
	                className: 'text-left'
	            },
	            {
	                title: 'Area',
	                name: 'area',
	                data: 'area',
	                className: 'text-left'
	            },
	            {
	                title: 'Objeto',
	                name: 'objeto',
	                data: 'objeto',
	                className: 'text-left'
	            },
	            {
	                title: 'Alerta',
	                name: 'fecha_vencimiento',
	                data: 'fecha_vencimiento',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                	var $html = '';
	               		//fecha de hoy
	                    const date = new Date();
	                  	$diaHoy = date.getDate();
	                   	$mesHoy = date.getMonth() + 1;
	                   	$anioHoy = date.getFullYear();
	                  
	                	$fechaHoyAux = $mesHoy + '/' + $diaHoy + '/' + $anioHoy;
	                   	$fechaHoy = new Date($fechaHoyAux);
	                  
	                   	//la data
	                   	$dia = data.substring(0,2);
	                   	$mes = data.substring(3,5);
	                   	$anio = data.substring(6,10);
	                  
	                   	$fechaVencAux = $mes + '/' + $dia + '/' + $anio;
	                   	var $fechaVencimiento = new Date($fechaVencAux);

	                   	//fecha hoy mas un día
	                    const date1 = new Date();
	                   	$fechaHoyMasUnoAux =date1.setDate(date1.getDate() + 1);
	                   	$NuevaFechaHoyMasUno = new Date($fechaHoyMasUnoAux);
	                   
	                   	$diaHoyMasUno = $NuevaFechaHoyMasUno.getDate();
	                   	$mesHoyMasUno = $NuevaFechaHoyMasUno.getMonth() + 1;
	                   	$anioHoyMasUno =$NuevaFechaHoyMasUno.getFullYear()

	                   	$fechaHoyMasUnoExtra = $mesHoyMasUno + "/" + $diaHoyMasUno + "/" + $anioHoyMasUno;
	                   	$fechaHoyMasUno = new Date($fechaHoyMasUnoExtra);

	                   	//fecha hoy más dos días
	                 	const date2 = new Date();
	                   	$fechaHoyMasDosAux  =date2.setDate(date2.getDate() + 2);
	                   	$NuevaFechaHoyMasDos = new Date($fechaHoyMasDosAux);

	                   	$diaHoyMasDos = $NuevaFechaHoyMasDos.getDate();
	                   	$mesHoyMasDos = $NuevaFechaHoyMasDos.getMonth() + 1;
	                   	$anioHoyMasDos =$NuevaFechaHoyMasDos.getFullYear()

	                   	$fechaHoyMasDosExtra = $mesHoyMasDos + "/" + $diaHoyMasDos + "/" + $anioHoyMasDos;
	                   	$fechaHoyMasDos = new Date($fechaHoyMasDosExtra);
	              
						
						if($fechaVencimiento.getTime() == $fechaHoy.getTime()){
                    		$html += '<span class="label label-warning">Vence Hoy</span>';
                    	}else if($fechaVencimiento.getTime() == $fechaHoyMasDos.getTime()){
                    		$html += '<span class="label label-info">Por vencer</span>';
                    	}else if($fechaVencimiento.getTime() == $fechaHoyMasUno.getTime()){
                    		$html += '<span class="label label-info">Por vencer</span>';
                    	}else if($fechaVencimiento > $fechaHoyMasDos){
                    		$html += '<span class="label label-success">Correcta</span>';
                    	}else{
							$html += '<span class="label label-danger">Vencida</span>';
						}
				
	                    return $html;
	                }
	            },
	            {
	                title: 'Acciones',
	                data: 'acciones',
	                name: 'acciones',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                    var $html = '';
	                    $html += '<div class="btn-group btn-group-sm">';
	                    $html += ' <a href="' + $url_base + '/index.php/notas/modificar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="modificar" target="_self"><i class="fa fa-edit"></i></a>';
	                    $html += ' <a href="' + $url_base + '/index.php/notas/tomar_conocimiento/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Tomar Conomiento de nota" target="_self"><i class="fa fa-check"></i></a>';
	                    $html += ' <a href="' + $url_base + '/index.php/notas/contestar/' + row.id + '" " data-user="" data-toggle="tooltip" data-placement="top" title="Contestar nota" target="_self"><i class="fa fa-pencil"></i></a>';
	                    $html += ' <a href="' + $url_base + '/index.php/notas/derivar/' + row.id + '"  data-user="" data-toggle="tooltip" data-placement="top" title="Derivar nota" target="_self"><i class="fa fa-share"></i></a>';
	                    $html += '</div>';
	                    return $html;
	                }
	            },

	        ]

	    });
	

		/** Consulta al servidor los datos y redibuja la tabla
	     * @return {Void}
	    */
	    function update() {
	        tabla.draw();
	    }

	    /**
	     * Acciones para los filtros, actualizar vista
	    */
	    $('#id_area').on('change', update);

	     $('#remitente').keyup(function() {
	        if (this.value.length >= 10) {
	            update();
	        }else if(this.value == ''){
	        	update();
	        }
	    });

	    $('#fecha_vencimiento,.fecha_vencimiento').datetimepicker({
	        format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
	        update();
	        $('#fecha_vencimiento').keyup(function() { //cuando borran lo que esta en el datepicker se actualiza la tabla
	        	if(this.value == ''){
	        		update();
	       		}
	    	});
	    });

	    //filtros para el exportador
	    $(".accion_exportador").click(function () {
	    	var x = $(this).val();
	    	debugger;
		    var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
		    $(this).append(form);
		    form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
		        .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
		        .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
		        .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
		        .append($('<input/>', {name: 'area', type: 'hidden', value:$('#id_area').val() }))
		        .append($('<input/>', {name: 'remitente', type: 'hidden', value:$('#remitente').val() }))
		        .append($('<input/>', {name: 'fecha_vencimiento', type: 'hidden', value:$('#fecha_vencimiento').val() }));
		    form.submit();
		});

	}//fin: de todo esto lo hace en el index donde está la tabla	

	$(".fecha_vencimiento").datetimepicker({
				format: 'DD/MM/YYYY'
			});
	$("#fecha_vencimiento").datetimepicker({
			format: 'DD/MM/YYYY'
		});

	$(".fecha_recepcion").datetimepicker({
				format: 'DD/MM/YYYY'
			});
	$("#fecha_recepcion").datetimepicker({
			format: 'DD/MM/YYYY'
		});

	if ($('.alta').length) {
	//Para el form alta: cuando cambia el objeto, debo escribir en el select subtema
		$('select#id_area').on('change', function($e){
			var areaSelect;
			if($('select#id_area').val()==""){
			    areaSelect = 0;
			}else{
			     areaSelect = $('select#id_area').val();
			}
			$.ajax({
				url: $url_base+"/Notas/alta",
			    data: {
			    area: areaSelect,
			      
			},
			    method: "POST"
			})
			.done(function (data) {
			if(typeof data.data != 'undefined'){
				addOptionsMulti(data.data, '#id_objeto',data.data.nombre);
			}

			})
			.fail(function(data){
				addOptionsMulti([], '#id_objeto');
			});

		});
		function insertAfter(referenceNode, newNode) {
  			referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
		}

		$('select#tipo').on('change', function($e){
			if($('select#tipo').val()== $tipos[$interna].id){

				if($("select#id_reparticion").length==0){
					var select = document.createElement("select");
					select.id = "id_reparticion"; 
					select.className ="form-control";
					select.name = "id_reparticion";
					var labelElement = document.getElementById("reparticion_bloque");
					insertAfter(labelElement, select);
					addOptions($lista_reparticiones, 'select#id_reparticion');
					$('#id_reparticion').select2()

				}
					
				addOptions($lista_reparticiones, 'select#id_reparticion');
				$('#reparticion').remove()
			}else if($('select#tipo').val()== $tipos[$externa].id){
				$('#id_reparticion').select2('destroy');
				$('select#id_reparticion').remove();
				var input = document.createElement("input");
				input.type = "text";
				input.id = "reparticion"; 
				input.className ="form-control";
				input.name = "reparticion"
				var labelElement = document.getElementById("reparticion_bloque");
				insertAfter(labelElement, input);
				
	
			}

		});

		 function verificarNotaGde(val){ //NO-2019-39117039-APN-DNTTF#MTR
            var patron = /^([A-Z]{2})-((19|20)\d{2})-(\d{8,16})-([A-Z]{3,6})-([A-Z]{3,50})#([A-Z]{3,50})$/;
            if (val!= ''){
                if (patron.test(val)){
                    return '';
                } else {
                    return 'Formato Nota GDE incorrecto';
                }
            }
            
        }

         $("#nota").keyup(function(){
            if($(this).val()!=''){
             var mensaje = verificarNotaGde($(this).val());
                    if(mensaje == ''){          
                        $('#boton_nota').attr("disabled", false);
                        $('#aviso').css("display","none");		
	                	$("#aviso").text(''); 
                       
                    }else{
                        $('#boton_nota').attr("disabled", true); 
                        $('#aviso').css("display","block");		
	                	$("#aviso").text(mensaje); 


                       
                    }
            }else{
                $('#boton_nota').attr("disabled", false); 
                $('#aviso').css("display","none");		
	           	$("#aviso").text('');                
            }
                       
        });

     
		$('#fecha_recepcion,.fecha_recepcion').datetimepicker({ //cuando seleccionela fecha recepción y escriba el numero (cantidad de dias)
	        format: 'DD/MM/YYYY'
	    	}).on("dp.change", function (e) {
	    		//cuando completa la fecha de recepción da un aviso que debe completar los dias para contestar
	    		if($('#fecha_recepcion').val().length!=0){
                    $('#aviso').css("display","block");		
	                $("#aviso").text('Debe completar la cantidad de días para contestar.'); 
	    		}else{
	    			$('#aviso').css("display","none");		
	                $("#aviso").text(''); 
	    		}

	       	var fecha_recepcion = $('#fecha_recepcion').val();
	       	$('#cant_dias').keyup(function() { 
	       		if($('#cant_dias').val().length !=0){ //si los dias para contestar está completo se borra el aviso, luego manda el ajax para calcular
	       			$('#aviso').css("display","none");		
	                $("#aviso").text(''); 
	       		}
	        	var dias = $('#cant_dias').val();
	        	$.ajax({
				url: $url_base+"/Notas/calcular_fecha",
			    data: {
			    fecha: fecha_recepcion,
			    dias: dias
			      
				},
			    method: "POST"
			})
			.done(function (data) {
			if(typeof data.data != 'undefined'){
				var f = new Date(data.data[0]);
				f.setMinutes(f.getMinutes() + f.getTimezoneOffset())
				fecha = f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear();
				$("#fecha_vencimiento").val(fecha);
			}

			})
			.fail(function(data){
				$("#fecha_vencimiento").val('');
			});


	    	});



	    	$('#cant_dias').keyup(function() {  //cuando escriba cantidad para contestar solamente
		    	var fecha_recepcion = $('#fecha_recepcion').val();
		        var dias = $('#cant_dias').val();
		        $.ajax({
				url: $url_base+"/Notas/calcular_fecha",
				    data: {
				    fecha: fecha_recepcion,
				    dias: dias
				      
					},
				    method: "POST"
				})
				.done(function (data) {
				if(typeof data.data != 'undefined'){
					var f = new Date(data.data[0]);
					f.setMinutes(f.getMinutes() + f.getTimezoneOffset())
					fecha = f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear();
					$("#fecha_vencimiento").val(fecha);
				}

				})
				.fail(function(data){
					$("#fecha_vencimiento").val('');
				});


	    	});

	      

	      
	    });

        

	}
	if($('.forms_contestaciones').length){ //esto se ejecuta solo para la toma de conocimiento, contestar y derivar
		$(".fecha_accion").datetimepicker({
					format: 'DD/MM/YYYY'
				});
		$("#fecha_accion").datetimepicker({
				format: 'DD/MM/YYYY'
			});
	}



});