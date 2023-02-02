
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>CP(s) Automation</title>
    <link href="{{asset('assets/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="{{asset('assets/dist/css/iziToast.min.css')}}">
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
      <h3 style="margin-left: 180px;color: white; padding: 4px">CP(s)A || Certificate of Performance Automation</h3>
    </nav>
    <main class="container p-5">
      <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h6 class="border-bottom pb-2 mb-0">Input File(s)</h6>
        <form class="dropzone" id="files" method="POST" enctype="multipart/form-data">
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
      <footer style="float: right;padding-top: 20px" class="blockquote-footer">Copy right 2023 <cite title="Source Title">Jenelle R. Gomez<img style="float: left; margin: 0px -258px 0 109px;" src="{{asset('images/jenelle_sig.png')}}" width="150px"></cite></footer>
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
                  "+response[i].replace("http://localhost:8080/cp_output/","")+"\
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


    </script>
  </body>
</html>
