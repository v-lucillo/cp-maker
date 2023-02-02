<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <style type="text/css">
        body{
            font-family:courier, courier new, serif;
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
        input.spots{
            width: 642px;
            font-size: 12px;
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
                        <td><b>Broadcast Order #</b>: <input style="width: 140px" type="text" name="{{rand(100000,999999)}}" value="{{$content['brod_no']}}"></td>
                    </tr>
                </table>
            </div>

            <div>
                <p style="text-align: center;"><b>SCHEDULE OF BROADCAST</b></p>
                <p><b><input class = "letter_of_codes" type="text" name="{{rand(100000,999999)}}" value='{{$content["letter_of_codes"]}}'></b></p>
                    <?php 
                        $spots = $content["spots"];
                        $per_page_spots = 0; // should only be 26 line of spots
                        $max_spots_len = max(array_map('strlen', $spots));
                    ?>
                    @foreach($spots as $key => $spot)
                        <?php 
                            // this is a sigle line of spot
                            // check the string length, it must be exactly 130 for each line and 26 line of sport per page
                            $iteration  = (int)ceil($max_spots_len/130);
                            $offset = 0;
                            $length =  130;
                        ?>
                        @for($i = 0; $i < $iteration; $i++)
                            <?php  
                                $line_of_spot =  substr($spot,$offset,$length);
                                $offset += $length;
                                $name = (string)rand(10000,99999);
                                $per_page_spots += 1;
                            ?>
                             @if($i == 0)
                                <span>
                                    <b style="font-size: 10px;">{{str_pad($key, 2, '0', STR_PAD_LEFT)}}</b>
                                </span>
                                <input class = "spots" type="text" name="field_{{$name}}" value="{{$line_of_spot}}">
                             @else
                                <div style="margin-left: 21px">
                                    <input class = "spots" type="text" name="field_{{$name}}" value="{{$line_of_spot}}">
                                </div>
                             @endif

                             @if($per_page_spots == 26)
                                <?php $per_page_spots = 0; ?>
                                <pagebreak></pagebreak>

                                <div class="border">
                                     <table class="details">
                                        <tr>
                                            <td width="60%"><b>Advertiser</b>: <input style="width: 310px" type="text" name="{{rand(100000,999999)}}" value="{{$content['advertiser']}}"></td>
                                            <td><b>Date</b>: <input style="width: 220px" type="text" name="{{rand(100000,999999)}}" value="{{$content['date']}}"></td>
                                        </tr>
                                        <tr>
                                            <td width="60%"><b>Agency</b>:<input style="width: 340px" type="text" name="{{rand(100000,999999)}}" value="{{$content['agency']}}"></td>
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
                                            <td><b>Broadcast Order #</b>: <input style="width: 140px" type="text" name="{{rand(100000,999999)}}" value="{{$content['brod_no']}}"></td>
                                        </tr>
                                    </table>
                                </div>

                                <p style="text-align: center;"><b>SCHEDULE OF BROADCAST</b></p>
                                <p><b><input class = "letter_of_codes" type="text" name="{{rand(100000,999999)}}" value='{{$content["letter_of_codes"]}}'></b></p>
                             @endif
                        @endfor
                    @endforeach
                <?php
                    $rows = (int)ceil(strlen($content['version'])/130) + 2;
                ?>
                @if(($rows + $per_page_spots) >= 26)
                    <pagebreak></pagebreak>
                    <div class="border">
                         <table class="details">
                            <tr>
                                <td width="60%"><b>Advertiser</b>: <input style="width: 310px" type="text" name="{{rand(100000,999999)}}" value="{{$content['advertiser']}}"></td>
                                <td><b>Date</b>: <input style="width: 220px" type="text" name="{{rand(100000,999999)}}" value="{{$content['date']}}"></td>
                            </tr>
                            <tr>
                                <td width="60%"><b>Agency</b>:<input style="width: 340px" type="text" name="{{rand(100000,999999)}}" value="{{$content['agency']}}"></td>
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
                                <td><b>Broadcast Order #</b>: <input style="width: 140px" type="text" name="{{rand(100000,999999)}}" value="{{$content['brod_no']}}"></td>
                            </tr>
                        </table>
                    </div>

                    <p style="text-align: center;"><b>SCHEDULE OF BROADCAST</b></p>
                    <p><b><input class = "letter_of_codes" type="text" name="{{rand(100000,999999)}}" value='{{$content["letter_of_codes"]}}'></b></p>

                    <p><b><textarea rows = "{{$rows}}" name = "{{rand(100000,999999)}}">{{$content["version"]}}</textarea></b></p>
                @else
                    <p><b><textarea rows = "{{$rows}}" name = "{{rand(100000,999999)}}">{{$content["version"]}}</textarea></b></p>
                @endif
            </div>
        </div>
        @if($pdf_key + 1 < sizeof($pdf))
            <pagebreak></pagebreak>
        @endif
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
                        <img style="float: left; margin: 0 0 -38px 0" src="{{$jenelle_sig}}" width="150px">
                        <p><u>Jenelle R. Gomez</u></p>
                        <p>CRM Analyst</p>
                    </td>
                    <td>
                        <img style="float: left; margin: 0 0 -25px -60px" src="{{$vic_sig}}" width="60px">
                        <img style="float: left; margin: 0 -70px -25px 10px" src="{{$jen_sig}}" width="60px">
                        <p><u>Marivic Ferrer / Jennifer Lubi</u></p>
                        <p>Team Lead / CRM Head</p>
                    </td>
                </tr>
            </table>
        </htmlpagefooter>
    </body>
</html>
