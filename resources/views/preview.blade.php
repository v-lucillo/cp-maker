<!DOCTYPE html>
<html>
<head>
	<title>CP Preview</title>
	<link href="{{asset('assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/dist/css/iziToast.min.css')}}">
	<link rel="stylesheet" href="{{asset('assets/dist/css/iziModal.min.css')}}">
    <style type="text/css">
    	body{
    		padding: 1.5em 6%;
		    color: #35d2c5;
		    background: #202124;
    	}
    	textarea{
		    color: #35d2c5;
		    background: #202124;
    	}

    	input{
		    color: #35d2c5 !important;
		    background: #202124 !important;
    	}

    	/*div.ruler{
    		border-left: 1px solid #ff0000;
		  	height: 100%;
		  	position:fixed;
		  	top: 0px;
			bottom:0px;
    	}*/
    	div.section{
    		border-left: 10px solid #35d2c5;;
		    padding-left: 50px;
		    margin-left: -50px;
		    border-radius: 50px;
    	}
    	.grid {
		  height: 700px;
		}
		img#remove_form{
			position: absolute;
		    right: 70px;
		    margin-top: -10px;
		    cursor: pointer;
		}
		a#to_main_page{
			position: fixed;
		    left: 0;
		    margin-top: -20px;
		}
    </style>
		
</head>
<body>
	<a id = "to_main_page" href = "{{route('main')}}">
		<img src="{{asset('images/back.png')}}">
	</a>
	<div id = "cp_form_container">
	</div>
	<div style="width: 100%">
		<button style="width: 100%" name = "generate_cp" type="button" class="btn btn-primary btn-lg btn-block">Generate Certificate of Performance</button>
	</div>



<div  id="modal">
	<div style = "padding: 20px">
		<div id = "output_container"></div>
		<div style = "display: flex;justify-content: space-between;align-items: center;margin-top: 30px;">
			<div id = "gdrive_upload" style = "float: right;cursor: pointer;">
				<img src="{{asset('images/google_drive.png')}}" width="50px">
				<small>Upload to google drive</small>
			</div>
			<div id = "g_drive_upload_spinner" style = "text-align: center; display:none;">
				<div class="spinner-border" role="status">
					<span class="sr-only"></span>
				</div>
			</div>
		</div>
		<small id = "error_upload" style = "display: none"></small>
		<small><i>*Archive your cp(s) by pressing the google drive button above</i></small>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script src="{{asset('assets/dist/js/iziModal.min.js')}}"></script>
<script src="{{asset('assets/dist/js/iziToast.min.js')}}"></script>
<script src="{{asset('assets/dist/js/datagridxl.min.js')}}"></script>


<script type="text/javascript">	  

	let grids = {};
	let cps =  JSON.parse("{{json_encode($cps)}}".replace(/&quot;/g, '"').replaceAll('	', '\\t'));
	if(cps.length == 0){
		iziToast.warning({
			title: 'Unexpected process occurs',
			message: "Please report to the developer and let him know the file name",
		});
	};
	for(var i = 0; i < cps.length; i++){ // group of CP
		generate_cp_form(i, cps[i]); // i determinse the CP group, and cps[i] is every cp inside the CP group
	}
	
	function generate_cp_form(cps_group,cps){
		$.each(cps, (key,data) => {
			$('div#cp_form_container').append(format_cp_form(cps_group,key,data));
		});
	}

	$("#modal").iziModal({
		title: "Output file(s)",
		overlayClose: false,
		width: 800,
	});	

	// $('div#modal').iziModal('open');

// <button type="button" id = "remove_form" class="btn btn-danger">Remove</button>\
	function format_cp_form(cps_group,key,cp){
		let $container = 
		$('<form class = "cp_form">\
			<img src="{{"images/delete.png"}}" id = "remove_form"/>\
			  <div class="row">\
			    <div class="col">\
			      <input type="text" id = "__advertiser" name = "advertiser" class="form-control" placeholder="Advertiser">\
			    </div>\
			    <div class="col">\
			      <input type="text" id = "__agency" name = "agency" class="form-control" placeholder="Agency">\
			    </div>\
			  </div>\
			  <br>\
			  <div class="row">\
			    <div class="col">\
			      <input type="text" id = "__month_year" name = "month_year" class="form-control" placeholder="Month / Year">\
			    </div>\
			    <div class="col">\
			      <input type="text" id = "__station" name = "station" class="form-control" placeholder="Station">\
			    </div>\
			  </div>\
			  <br>\
			  <div class="row">\
			    <div class="col">\
			      <input type="text" id = "__ae" name = "ae" class="form-control" placeholder="A E">\
			    </div>\
			    <div class="col">\
			      <input type="text" id = "__date" name = "date" class="form-control" placeholder="Date">\
			    </div>\
			  </div>\
			  <br>\
			  <div class="row">\
			    <div class="col">\
			      <input type="text" id = "__product" name = "product" class="form-control" placeholder="Product">\
			    </div>\
			    <div class="col">\
			      <input type="text" id = "__com_len" name = "com_len" class="form-control" placeholder="Commercial Length">\
			    </div>\
			  </div>\
			  <br>\
			  <div class="row">\
			    <div class="col">\
			      <input type="text" id = "__brod_no" name = "brod_no" class="form-control" placeholder="Broadcast Number">\
			    </div>\
			    <div class="col">\
			      <input type="text" id = "__contract_num" name = "contract_num" class="form-control" placeholder="Contrat Number">\
			    </div>\
			  </div><br>\
			  <div class="row">\
			    <div class="col">\
			    	<textarea id = "__letter_of_codes" name = "letter_of_codes" style ="width: 100%"></textarea>\
			    </div>\
			  </div>\
			  <div class="row">\
			    <div class="col">\
			      <textarea style ="width: 100%" id = "__version" name = "version"></textarea>\
			    </div>\
			  </div>\
			  <div class="grid"></div>\
			  <hr/>\
			</form>');

		let grid_id = "grid_"+cps_group+"_"+key;
		$container.find("#__advertiser").val(cp.advertiser);
		$container.find("#__agency").val(cp.agency);
		$container.find("#__month_year").val(cp.month_year);
		$container.find("#__station").val(cp.station);
		$container.find("#__ae").val(cp.ae);
		$container.find("#__date").val(cp.date);
		$container.find("#__product").val(cp.product);
		$container.find("#__com_len").val(cp.com_len);
		$container.find("#__brod_no").val(cp.brod_no);
		$container.find("#__letter_of_codes").val(cp.letter_of_codes);
		$container.find("#__version").val(cp.version);
		$container.find(".grid").attr("id", grid_id);
		$container.find("#__contract_num").val(cp.contract_num);
		$container.attr("name", cps_group+"_"+key)
		 	.attr("id", "form_"+(cps_group + 1)+"_set");
		
		
		let spots_data = DataGridXL.createEmptyData(50, 11);
		let spot = [];
		let spots = cp.spots;
		for(let key in spots){
			let spot_holder = array_chunk(10,spots[key].split(/\s+/)); //02	07:10:38C	19:40:20C 
			spot_holder.forEach((data,index) => {
				if(/^[0-9]{2}:/.test(data)){
					data.unshift(" ");
				}

				if(data.length < 11){
					let fill =  11 - data.length;
					for(var j = 0; j < fill; j++){
						data.push(null);
					}
				}
				spot.push(data);
			});
		}
		spot.reverse().map(data => {
			spots_data.push(data);
		});

		var options = {
			data: spots_data.reverse(),
			colAlign: "center",
			colWidth: 100
		};
		var grid = new DataGridXL(grid_id,options);
		grids[grid_id] = grid;
		return $container;
	}

	function array_chunk(chunkSize,array){
		let result = [];
		for (let i = 0; i < array.length; i += chunkSize) {
		    result.push(array.slice(i, i + chunkSize));
		}
		return result;
	}



	function arranged_data(){
		let forms = $("form");
		let data_set = [];
		let cp_group = [];
		let cp_file_key_group = 0;

		for (var i = 0; i < forms.length; i++) { // form
			let _cp_form = $(forms[i]);
			let form_name = _cp_form.prop("name");
			let main_key = form_name.split("_")[0];
			let cp_inputs = _cp_form.find("input");
			let cp_text_area = _cp_form.find("textarea");
			let letter_of_codes = $(cp_text_area[0]).val();
			let version = $(cp_text_area[1]).val();

			let advertiser = $(cp_inputs[0]).val();
			let agency =  $(cp_inputs[1]).val();
			let month_year = $(cp_inputs[2]).val();
			let station = $(cp_inputs[3]).val();
			let ae = $(cp_inputs[4]).val();
			let date = $(cp_inputs[5]).val();
			let product  = $(cp_inputs[6]).val();
			let com_len = $(cp_inputs[7]).val();
			let brod_no = $(cp_inputs[8]).val();
			let contract_num = $(cp_inputs[9]).val();
			if(main_key !=  cp_file_key_group){
				data_set.push(cp_group);
				cp_group = [];
			}
			
			let grid_object = "grid_"+form_name;
			var spot = grids[grid_object].getData(); // change this also
			for(var k = 0; k < spot.length; k++){
				if(spot[k][0] === null && spot[k][1] === null){ // check col1 and col2 if both are null therefore it the end
					spot = spot.slice(0, k);
					break;
				}
			}
			
			cp_group.push({
				"advertiser": advertiser,
				"agency": agency,
				"month_year": month_year,
				"station": station,
				"ae" : ae,
				"date" : date,
				"product" : product,
				"com_len" : com_len,
				"brod_no" : brod_no,
				"contract_num" : contract_num,
				"letter_of_codes" : letter_of_codes,
				"version" : version,
				"spots": spot
			});

			if(i == (forms.length - 1)){
				data_set.push(cp_group);
			}
			cp_file_key_group = main_key;
		}

		return data_set;
	}


	$("button[name='generate_cp']").on('click', function(){
		// arranged_data();
		// return;
		// console.log(arranged_data());
		$.LoadingOverlay("show");
		$.ajax({
			url: "{{route('invoke_cp')}}",
			method: "POST",
			data: {
				data: arranged_data(),
				_token: "{{ csrf_token() }}",
			},
			success: function(e){
				$.LoadingOverlay("hide");
				iziToast.success({
                    title: 'Successlly generated.',
                    message: "Redirecting to generated files...",
                });
				// TODO::
				// add MODAL
				// OPEN modal

			$('div#modal').iziModal('open');
			$.ajax({
				url: "{{route('get_generated_files')}}",
				success: function(response){
				$("div#output_container").empty();
				for(var i =0; i < response.length; i++){
					$("div#output_container").append(
					"<div class='d-flex align-items-center flex-wrap text-muted pt-3'>\
						<img src='{{asset("images/pdf.png")}}' width='40px'>\
						<a target = '_blank' href = '"+response[i]+"' class='pb-3 mb-0 small lh-sm border-bottom'>\
						"+response[i].replace("http://localhost:8080/cp_output/{{session('user_data')->username}}/","")+"\
						</a>\
						</div>"
					);
				}
				},
				error: function(){
				iziToast.error({
						title: 'Something went wrong',
						message: "Please contact the Developer",
					});
				}
			});
			},
			error: function(e){
				$.LoadingOverlay("hide");
				iziToast.error({
	                title: 'Something went wrong',
	                message: "Please contact the Developer",
	            });
			}
		});
	});


	let form_sets = "{{sizeof($cps)}}";
	for(let i =  1; i <= form_sets ; i++){
		$("form#"+"form_"+i+"_set").wrapAll("<div class='section'/>");
	}

	$(document).on('click', 'img#remove_form', function(){
		let form_object = $(this).parent();
		let form_selected = "grid_"+form_object.attr("name");
		// delete the form and the grid object
		// console.log(form_selected);
		delete grids[form_selected];
		// console.log(grids);
		form_object.remove();
		// grids object | remove accordingly
	});


	$(document).on("click", "div#gdrive_upload",function(){ // upload to g drive
		$('div#g_drive_upload_spinner').show();
		$('div#error_upload').hide();
		$.ajax({
			url: "{{route('g_drive_upload')}}",
			success: function(e){
				$('div#g_drive_upload_spinner').hide();
				iziToast.success({
                    title: 'Good!',
                    message: "Successfully archived in Google Drive",
                });
			},
			error: function(e){
				$('div#g_drive_upload_spinner').hide();
				$('div#error_upload').show();
				$('div#error_upload').append(e);
				// let them know to report to me
			}
		});
	});
</script>
</body>
</html>