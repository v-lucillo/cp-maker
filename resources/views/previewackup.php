<!DOCTYPE html>
<html>
<head>
	<title>CP Preview</title>
	<link href="{{asset('assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/dist/css/iziToast.min.css')}}">
    <style type="text/css">
    	body{
    		padding: 1.5em 15%;
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
    		border-left: 10px solid blue;
		    padding-left: 50px;
		    margin-left: -50px;
		    border-radius: 50px;
    	}
    </style>
</head>
<body>

	<!-- ths iteration is for each CP file -->
	@foreach($cps as $main_key => $cp_set)
	<?php $form_id = "form_".($main_key + 1)."_set"; ?>
	<!-- this iteration is for each CP in each cp file-->
		@foreach($cp_set as $sub_key => $cp)
		<form name = "{{$main_key}}_{{$sub_key}}" id = "{{$form_id}}">
		  <div class="row">
		    <div class="col">
		      <input type="text" name = "advertiser" value = "{{$cp['advertiser']}}" class="form-control" placeholder="Advertiser">
		    </div>
		    <div class="col">
		      <input type="text" name = "agency" value = "{{$cp['agency']}}" class="form-control" placeholder="Agency">
		    </div>
		  </div>
		  <br>
		  <div class="row">
		    <div class="col">
		      <input type="text" name = "month_year" value = "{{$cp['month_year']}}" class="form-control" placeholder="Month / Year">
		    </div>
		    <div class="col">
		      <input type="text" name = "station" value = "{{$cp['station']}}" class="form-control" placeholder="Station">
		    </div>
		  </div>
		  <br>
		  <div class="row">
		    <div class="col">
		      <input type="text" name = "ae" value = "{{$cp['ae']}}" class="form-control" placeholder="A E">
		    </div>
		    <div class="col">
		      <input type="text" name = "date" value = "{{$cp['date']}}" class="form-control" placeholder="Date">
		    </div>
		  </div>
		  <br>
		  <div class="row">
		    <div class="col">
		      <input type="text" name = "product" value = "{{$cp['product']}}" class="form-control" placeholder="Product">
		    </div>
		    <div class="col">
		      <input type="text" name = "com_len" value = "{{$cp['com_len']}}" class="form-control" placeholder="Commercial Length">
		    </div>
		  </div>
		  <br>
		  <div class="row">
		    <div class="col">
		      <input type="text" name = "brod_no" value = "{{$cp['brod_no']}}" class="form-control" placeholder="Broadcast Number">
		    </div>
		    <div class="col">
		      <input type="text" name = "contract_num" value = "{{$cp['contract_num']}}" class="form-control" placeholder="Contrat Number">
		    </div>
		  </div>
		  <br>
		  <?php
		  	$letter_of_codes = $cp['letter_of_codes'];
		  	$letter_of_codes_rows = (int)ceil(strlen($letter_of_codes)/202);
		  ?>
		  <div class="row">
		    <div class="col">
		    	<textarea style ="width: 100%" name = "letter_of_codes" rows="{{$letter_of_codes_rows}}">{{$cp['letter_of_codes']}}</textarea>
		    </div>
		  </div>
		  <br>

		 <?php
		 	// organized and sanitized spots data
			$spot = "";
		 	$spots = array_map(function ($d){
		 		return preg_replace('!\s+!', '  ', $d);
		 	},$cp['spots']);
		 	$max_spots_len = max(array_map('strlen', $spots));
		 	$rows  = ((int)round($max_spots_len/162) * sizeof($spots)) + 2 + sizeof($spots);
		 	foreach ($spots as $key => $value) {
		 		$value = array_filter(explode("  ", $value));
		 		// sort($value);
		 		$value = implode("  ", $value);
		 		$spot .= "$key\n";
		 		$spot .= $value."\n";
		 	}
		  ?>
		  <div class="row">
		    <div class="col">
		    	<textarea name = "spot" style ="width: 100%" rows="{{$rows}}">{{$spot}}</textarea>
		    </div>
		  </div>

		  <?php
		  	$version = $cp['version'];
		  	$version_rows = (int)ceil(strlen($version)/202) + 2;
		  ?>
		  <div class="row">
		    <div class="col">
		      <textarea style ="width: 100%" name = "version" rows="{{$version_rows}}">{{$cp['version']}}</textarea>
		    </div>
		  </div>
		  <br>
		</form>
		@endforeach
		<br>
	@endforeach
	<div style="width: 100%">
		<button style="width: 100%" name = "generate_cp" type="button" class="btn btn-primary btn-lg btn-block">Generate Certificate of Performance</button>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.6.1/js/iziModal.min.js"></script>
	<script src="{{asset('assets/dist/js/iziToast.min.js')}}"></script>

<script type="text/javascript">

	
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
			let spot = $(cp_text_area[1]).val().split("\n");
			let version = $(cp_text_area[2]).val();

			let spot_key =  spot.filter((a,b,c) =>{
				return b%2 == 0;
			});

			let spot_value =  spot.filter((a,b,c) =>{
				return b%2 == 1;
			});

			let spots = {};

			for(var j = 0; j < spot_value.length; j++){
				var key = spot_key[j];
				spots[key] = spot_value[j];
			}
			// console.log(spots);
			// break;
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
				"spots" : spots,
			});

			if(i == (forms.length - 1)){
				data_set.push(cp_group);
			}
			cp_file_key_group = main_key;
		}

		return data_set;
	}

	$.LoadingOverlaySetup({
          background      : "rgba(0, 0, 0, 0.5)",
          image           : "{{asset('images/timer.png')}}",
          imageAnimation  : "1.5s fadein",
          imageColor      : "#ffcc00",
          // text        : "Processing, please wait"
      });

	$("button[name='generate_cp']").on('click', function(){
		console.log(arranged_data());
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
                window.open("{{route('main')}}");
                // window.location.replace("{{route('main')}}");
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

	var count = 80;
	for(let i = 1 ; i <= 15; i++){
		$("body").append(
			"<div  class = 'ruler' style='\
				right: "+((count * i) +  120)+"px;'>\
			</div>"
		);
	}

	let form_sets = "{{sizeof($cps)}}";
	for(let i =  1; i <= form_sets ; i++){
		$("form#"+"form_"+i+"_set").wrapAll("<div class='section'/>");
	}
</script>

</body>
</html>