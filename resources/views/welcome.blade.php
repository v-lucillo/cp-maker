<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <style type="text/css">
        body{
            font-family: courier, courier new, serif;
        }
        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 140px;
        }
        table {
          width: 100%;
        }
        table.details{
            font-size: 10px;
        }
        table.authorization{
            margin-top : 20px;
            text-align: center;
        }
        p{
            font-size: 10px;
        }
        p.header{
            font-size: 14px;
        }
        p.sub_header{
            font-size: 12px;
        }
        div.border{
            border:1px solid black;
        }
        div.disclaimer{
            padding: 10px;
        }
        div.content{
            padding: 5px 10px 10px 10px;
            height: 710px;
        }

        input{
            font-size:10px;height: 11px;border-color: white; background-color: #FFFFFF;width: 170px;
        }
        textarea{
            font-size:10px;border-color: white; background-color: #FFFFFF;width: 100%;
        }
        textarea.spots{
            width: 642px;
            font-size: 9.7px;
            font-family: courier, courier new, serif;
            height: 10px;
        }

        textarea.version{
            width: 100%;
            font-size: 12px;
            font-family: courier, courier new, serif;
        }

        input.letter_of_codes{
            width: 100%;
            font-size: 11px;
        }
        
    </style>
    <body>
        
       <htmlpageheader name="page-header">
            <table>
              <tr>
                <th><img src="{{$mbclogo}}" width="80"></th>
                <th style="text-align: center;">
                    <p class = "header">Manila Broadcasting Company</p>
                    <p class="sub_header">A Member of the FJE Group of Companies</p>
                    <p>Star City Complex, Sotto St., Roxas Boulevard, Pasay City</p>
                    <p>Telefax Nos.: 832-6205; 832-6168</p><br>
                    <p class="header"><b>CERTIFICATE OF PERFORMANCE</b></p>
                </th>
                <th><img src="{{$kbplogo}}" width="70"></th>
              </tr>
            </table>
        </htmlpageheader>
            
        @foreach($pdf as $pdf_key => $content)
            <div class="border content">
            <div class="border" style="margin-top: 4px;">
                <table class="details">
                    <tr>
                        <td width="60%"><b>Advertiser</b>: <input style="width: 310px" type="text" name="{{rand(100000,999999)}}" value="{{$content['advertiser']}}"></td>
                        <td><b>Date</b>: <input style="width: 220px" type="text" name="{{rand(100000,999999)}}" value="{{$content['date']}}"></td>
                    </tr>
                    <tr>
                        <td width="60%"><b>Agency</b>:<input style="width: 340px; height: 10px" type="text" name="{{rand(100000,999999)}}" value="{{$content['agency']}}"></td>
                        <td><b>Product</b>: <input style="width: 200px" type="text" name="{{rand(100000,999999)}}" value="{{$content['product']}}"></td>
                    </tr>
                    <tr>
                        <td width="60%"><b>Month / Year</b>: <input style="width: 300px" type="text" name="{{rand(100000,999999)}}" value="{{$content['month_year']}}"></td>
                        <td><b>Commercial Length</b>: <input style="width: 140px" type="text" name="{{rand(100000,999999)}}" value="{{$content['com_len']}}"></td>
                    </tr>
                    <tr>
                        <td width="60%"><b>Station</b>: <input style="width: 330px" type="text" name="{{rand(100000,999999)}}" value="{{$content['station']}}"></td>
                        <td><b>Contract #.</b> : <input type="text" name="{{rand(100000,999999)}}" value="{{$content['contract_num']}}"></td>
                    </tr>
                    <tr>
                        <td width="60%"><b>A. E.</b>: <input style="width: 343px" type="text" name="{{rand(100000,999999)}}" value="{{$content['ae']}}"></td>
                        <td><b>B.O #</b>: <input style="width: 214px" type="text" name="{{rand(100000,999999)}}" value="{{$content['brod_no']}}"></td>
                    </tr>
                </table>
            </div>

            <div>
                <p style="text-align: center;"><b>SCHEDULE OF BROADCAST</b></p>
                <p><b><input class = "letter_of_codes" type="text" name="{{rand(100000,999999)}}" value='{{$content["letter_of_codes"]}}'></b></p>

                <?php  
                    $spots = $content["spots"];
                    $key_date = 0;
                ?>
                
                @foreach($spots as $spot)
                    @if($spot == "" || $spot == null)
                        @continue
                    @endif
                    @if(!preg_match("/^[0-9]{1,2}:/", $spot))
                    <?php  
                        $data = explode("  ", $spot);
                        $id = $data[0];
                        unset($data[0]);
                        $spot = implode("  ", $data);
                    ?>
                        <div style="margin-top: -5px">
                            <span>
                            <b style="font-size: 9px;">{{str_pad($id, 2, '0', STR_PAD_LEFT)}}</b>
                            </span>
                            <textarea class = "spots" rows = '1' name="field_{{rand(100000,999999)}}">  {{$spot}}
                            </textarea>
                        </div>
                    @else
                        <div style="margin-left: 20px; margin-top: 0px ">
                            <textarea class = "spots" rows = '1' name="field_{{rand(100000,999999)}}">{{$spot}}</textarea>
                        </div>
                    @endif
                @endforeach

                @if(isset($content["version"]))
                    <?php 
                        $version = $content["version"];
                        $rows =  (int)ceil(strlen($version )/86);
                    ?>
                    <div style="padding-top: 10px; width: 100%">
                        <textarea class = "version" rows = "{{$rows}}" name = "{{rand(100000,999999)}}">{{$version}}</textarea>
                    </div>
                @endif

            </div>
        </div>
        @endforeach



        <htmlpagefooter name="page-footer">
            <div class="border disclaimer">
                <p>
                    <i>This is s system-generated report and bears the e-signatures of the station manager and station traffic with their authorization.
                    We warrant that in accordinance with the CERTIFIED OFFICIAL STATION LOGS, the schedules above indicated were broadcast.
                    </i>
                </p>
            </div>
            <table class="authorization">
                <tr>
                    <td width="50%">
                        <img style="float: left; margin: 0 0 -38px 0" src="{{$userdata->signature}}" width="150px">
                        <p><u>{{$userdata->fullname}}</u></p>
                        <p>CRM Analyst</p>
                    </td>
                    <td>
                        <img style="float: left; margin: 0 0 -25px -60px" src="{{$userdata->signatory_signature}}" width="60px">
                        <img style="float: left; margin: 0 -70px -25px 10px" src="{{$jen_sig}}" width="60px">
                        <p><u>{{$userdata->signatory_fullname}} / Jennifer Lubi</u></p>
                        <p>Team Lead / CRM Head</p>
                    </td>
                </tr>
            </table>
        </htmlpagefooter>
    </body>
</html>
