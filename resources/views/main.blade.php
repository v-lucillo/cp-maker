
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="generator" content="Hugo 0.108.0">
    <title>CP(s) Automation</title>
    <link href="{{asset('assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="{{asset('assets/dist/css/iziToast.min.css')}}">
    <style type="text/css">
      body{
        background-image: url("{{asset('images/background.png')}}");
        background-repeat: no-repeat;
        background-size: auto;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }

      .dropzone {
        min-height: 150px;
        border: 1px solid rgb(30 189 72 / 30%);
        background: #fff;
        padding: 20px 20px;
        border-radius: 25px;
        margin-top: 14px;
    }
    </style>
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-light">
      <h5 style="margin-left: 10px;padding: 2px">CP(s)A || Certificate of Performance Automation</h5>
      <div style="line-height: 0;">
        <p style="margin-right: 10px;">Victorino I C. Lucillo</p>
        <small><i>Current User</i> <span style=" color: green;font-size: 20px;">●</span></small>
        <span><a href = "{{route('logout')}}"><img src="{{asset('images/logout.png')}}" style="cursor: pointer;margin-top: -5px;margin-right: 18px;width: 20px;float: right;"></span></a>
      </div>
    </nav>
    <main class="container p-5">

      <div class="form-check form-switch">
        <div style = "display: flex;justify-content: center;align-items: center">
          <input {{$data["preview_attr"]}} name = "preview_format" style = "height: 25px;width: 60px;" class="form-check-input" type="checkbox"/>
          <label id = "preview_format_label" style = "margin-left: 15px;font-size: 20px;font-weight: bold;" class="form-check-label" for="flexSwitchCheckChecked">{{$data["preview_format_label"]}}</label>
        </div>
      </div>

      <div class="my-3 p-3 bg-body rounded shadow-sm">
        <!-- <h6 class="border-bottom pb-2 mb-0">Input File(s)</h6> -->
        <form class="dropzone" id="files" method="POST" enctype="multipart/form-data">
          <div class="dz-message" data-dz-message>
            <img src="{{asset('images/drop.gif')}}" width="200px">
            <div style="display: inline-grid">
              <h2>Drag and drop your files here</h2>
              <span>CP(s) must be generated from Winsales system</span><br><br>
          </div>
          </div>
          @csrf
        </form>
      </div>

      <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h6 class="border-bottom pb-2 mb-0">Output File(s)</h6>
        <div id = "output_container">
          
        </div>
        <small class="d-block text-end mt-3">
        </small>
      </div>
      <footer style="float: right;padding-top: 20px" class="blockquote-footer">Copy right 2023 <cite title="Source Title">Manila Broadcasting Company</cite></footer>
    </main>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izimodal/1.6.1/js/iziModal.min.js"></script>
    <script src="{{asset('assets/dist/js/iziToast.min.js')}}"></script>
    <script>
      $.LoadingOverlaySetup({
          background      : "rgba(0, 0, 0, 0.5)",
          image           : "{{asset('images/timer.png')}}",
          imageAnimation  : "1.5s fadein",
          imageColor      : "#ffcc00",
          // text        : "Processing, please wait"
      });
      
      var files = new Dropzone( "form#files", {
          url: "{{route('upload_cp')}}",
          uploadMultiple: true,
          addRemoveLinks: true,
          autoProcessQueue:true,
          acceptedFiles: ".pdf",
          parallelUploads:100,
          uploadprogress: function(file, progress, bytesSent){
            $.LoadingOverlay("show");
          },
          success: function(file, response){
            $.LoadingOverlay("hide");
            this.removeFile(file);
            window.location.replace("{{route('preview')}}");
          },
          error: function(){
            iziToast.error({
                title: 'Something went wrong',
                message: "Please contact the Developer",
            });
            $.LoadingOverlay("hide");
          }
      });


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
      
    
      
      $("input[name='preview_format']").on('click', function(){
        let is_checked = $(this).is(":checked");
        let flag;
        if(is_checked){
          flag =  1;
          $("label#preview_format_label").text("WORK BOOK FORMAT");
        }else{
          flag = 0;
          $("label#preview_format_label").text("TEXT AREA FORMAT");
        }

        $.ajax({
          url: "{{route('set_preview_format')}}",
          data: {
            flag: flag
          },
          success: function(e){
            console.log(e);
          },
          error: function(e){
            console.log(e);
          }
        });
        
      });
    </script>
  </body>
</html>