<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use File;
use PDF;
use Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Exceptions\Handler;

class GenerateController extends Controller
{  
    public function main(){
        ($this->format_data_feed());
        return view("main");
    }

    public function preview(){
        $cps = $this->format_data_feed();
        return view('preview', compact('cps'));
    }

    public function get_generated_files(){
        $userdata = session("user_data");
        $username = $userdata->username;
        $dir = public_path("cp_output\\$username");
        $output_file = array_map(function($data) use ($username){
            return asset("cp_output/$username")."/$data";
        },array_slice(scandir($dir), 2));
        return response()->json($output_file);
    }

    public function upload_cp(Request $request){
        $userdata = session("user_data");
        $username = $userdata->username;
        $this->deleteAllfileDIR("cp/$username");
        $files = $request->file("file");
        foreach ($files as $key => $file) {
            $file_name =  $file->getClientOriginalName();
            $file->move(public_path("cp/$username"), $file_name);
        }
    }

    private function deleteAllfileDIR($dir){
        $cp_files = new Filesystem;
        $cp_files->cleanDirectory(public_path("$dir"));
    }

    private function format_data_feed(){
        $userdata = session("user_data");
        $username = $userdata->username;
        $result = [];
        $parser = new \Smalot\PdfParser\Parser(); 
        $dir = public_path("cp/$username");
        $file = array_slice(scandir(public_path("cp/$username")), 2);
        $cp = [];
        foreach ($file as $cps_file) {
            $cp_file =  $dir."\\".$cps_file; // this is a sigle file CP
            $pdf = $parser->parseFile($cp_file); 
            $pdf_content = ($pdf->getText()); 
            $pdf_content_to_array = explode("\n", $pdf_content);
            
            $letter_of_codes =  array_filter($pdf_content_to_array, function($d){
                return strstr($d, "Letter Codes");
            });
            $advertiser =  array_filter($pdf_content_to_array, function($d){
                return strstr($d, "Advertiser");
            });
            $agency =  array_filter($pdf_content_to_array, function($d){
                return strstr($d, "Agency :");
            });
            $month_year =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Month / Y ear") ||strstr($d,"Month / Year");
            });
            $station =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Station");
            });
            $ae =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"A . E.") || strstr($d,"A. E.");
            });
            $date =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Date :");
            });
            $product =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Product :");
            });
            $com_len =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Commercial Length");
            });
            $brod_no =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Broadcast Order No. ");
            });
            $version =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"VERSION");
            });

            $contract_num =  array_filter($pdf_content_to_array, function($d){
                return strstr($d,"Contract");
            });
 
            foreach ($version as $key => $d) {
                $key = (int)$key + 1;
                for ($i= $key; $i < sizeof($pdf_content_to_array); $i++) { 
                    $check = $pdf_content_to_array[$i];
                    if(strstr($check, "SCHEDULE") ||
                        strstr($check, "Manila") ||
                        strstr($check, "Agency") ||
                        strstr($check, "A.") || strstr($check, "A .") || 
                        $check == "" ){
                        break 1;
                    }else{
                        $test = (string) ((int)$key -1);
                        $version[$test] .= (preg_replace('/\s\s+/', ' ', $pdf_content_to_array[$i]));
                    }
                }
            }

            // $test = "24\t";
            // if(preg_match("/^[0-9]{2}/", $test) && substr($test,2,1) == "\t"){
            //         dd($test);
            //     }
            // dd($pdf_content_to_array);
            $spots =  array_filter($pdf_content_to_array, function($d) use ($version){
                if(preg_match("/^[0-9]{2}:/", $d)){
                    $d = array_filter(explode("\t", $d));
                    sort($d);
                    $sd = implode("  ", $d);
                    return $d;
                }


                if(preg_match("/^[0-9]{2}/", $d) && substr($d,2,1) == "\t"){
                    // if($d == "24\t"){
                    //     dd($d);
                    //     dd(array_filter(explode("\t", substr($d."\t", 3))));
                    //     dd(substr($d."\t", 3));
                    //     dd(explode("\t", substr($d."\t", 3)));
                    // }
                    $holder = $d;
                    $d = array_filter(explode("\t", substr($d."\t", 3)));
                    if($d == null || sizeof($d) == 0){
                        // dd(substr($holder, 0,2));
                        return $holder;
                    }
                    sort($d);
                    $d = implode("  ", $d);
                    return $d;
                }
                if(sizeof($version) > 1){  // condition added for Jelyns case 18292-PEERLESS-CORPORATE-IM10072335-PEERLESS-CORPORATE-PO-2023-01-RHTV (1)
                    // dd($version);
                    return strstr($d,"VERSION");
                }
            });

            // dd($spots);

            $spot_group = [];
            $spot_set = [];
            foreach ($spots as $key => $value) {
                if(preg_match("/^[0-9]{2}:/", $value)){
                    try {
                        $spot_group[array_key_last($spot_group)] = $spot_group[array_key_last($spot_group)].$value."\t";
                    } catch (\Exception $e) {
                        // dd($spot_group);  dito na stack si Arriane
                    }
                    if((int)$key == (int)array_key_last($spots)){
                       array_push($spot_set,$spot_group);
                    }
                    continue;
                }

                if(strstr($value, "VERSION")){
                    array_push($spot_set,$spot_group);
                    $spot_group = [];
                }

                if(preg_match("/^[0-9]{2}/", $value) && substr($value,2,1) == "\t"){
                    $current_key = (int)array_key_last($spot_group);
                    $spot_key = (int)substr($value, 0, 2);
                    if($spot_key > $current_key){ // it means the current spots belong to the current spots group
                        $spot_group[$spot_key] = substr($value."\t", 3); 
                        if((int)$key == (int)array_key_last($spots)){
                           array_push($spot_set,$spot_group);
                        }
                    }else{ // this means a new set of spots must be created
                        array_push($spot_set,$spot_group);
                        $spot_group = [];
                        $spot_group[$spot_key] = substr($value."\t", 3);
                        if((int)$key == (int)array_key_last($spots)){
                           array_push($spot_set,$spot_group);
                        }
                    }
                    
                }
            }

            // dd($spot_set);

            $version = array_values($version);
            $letter_of_codes = array_values($letter_of_codes);

            $advertiser = array_values(array_map(function($d){
                return str_replace("Advertiser :", "", $d);
            },$advertiser));

            $agency = array_values(array_map(function($d){
                return str_replace("Agency :", "", $d);
            },$agency));

            $month_year = array_values(array_map(function($d){
                $m_y = str_replace("Month / Y ear :", "", $d);
                $m_y = str_replace("Month / Year :", "", $d);
                return $m_y;
            },$month_year));

            $station = array_values(array_map(function($d){
                return str_replace("Station :", "", $d);
            },$station));

            $ae = array_values(array_map(function($d){
                $ae_data = explode(":", $d)[1];
                return $ae_data;
            },$ae));
            $date = array_values(array_map(function($d){
                return str_replace("Date :", "", $d);
            },$date));

            $product = array_values(array_map(function($d){
                return str_replace("Product :", "", $d);
            },$product));
            $com_len = array_values(array_map(function($d){
                return str_replace("Commercial Length :", "", $d);
            },$com_len));
            $brod_no = array_values(array_map(function($d){
                return str_replace("Broadcast Order No. :", "", $d);
            },$brod_no));
            $contract_num = array_values(array_map(function($d){
                return str_replace("Contract No. :", "", $d);
            },$contract_num));
            $spot_set = array_values(array_filter($spot_set));
            // dd($spot_set);
            // dd($agency);

            try {
                for($i = 0; $i < sizeof($spot_set); $i++){
                    array_push($cp, [
                        "version" => $version[$i],
                        "letter_of_codes" => $letter_of_codes[$i],
                        "advertiser" => $advertiser[$i],
                        "agency" => $agency[$i],
                        "month_year" => $month_year[$i],
                        "station" => $station[$i],
                        "ae" => $ae[$i],
                        "date" => $date[$i],
                        "product" => $product[$i],
                        "com_len" => $com_len[$i],
                        "brod_no" => $brod_no[$i],
                        "contract_num" => $contract_num[$i],
                        "spots" => $spot_set[$i],
                    ]);
                }  
            } catch (Throwable $e) {
                // do error logging here, move the file containing error to its respective directory 
            }finally{
                // dd($spots);
                $spot_set =  $this->re_index_spots($spots);
                // dd($spot_set);
                for($i = 0; $i < sizeof($spot_set); $i++){
                    array_push($cp, [
                        "version" => $version[$i],
                        "letter_of_codes" => $letter_of_codes[$i],
                        "advertiser" => $advertiser[$i],
                        "agency" => $agency[$i],
                        "month_year" => $month_year[$i],
                        "station" => $station[$i],
                        "ae" => $ae[$i],
                        "date" => $date[$i],
                        "product" => $product[$i],
                        "com_len" => $com_len[$i],
                        "brod_no" => $brod_no[$i],
                        "contract_num" => $contract_num[$i],
                        "spots" => $spot_set[$i],
                    ]);
                } 
            }

            array_push($result, $cp);
            $cp = [];
        }
        // dd($result);
        return $result;
    }

     private function re_index_spots($spots){
        $spot_holder = [];
        $current_cursor =  0;
        $spots = array_values($spots); // reindex array
        foreach ($spots as $key => $value) { // future enhancement, please optimize this algo
            if(strstr($value, "VERSION")){
                //35 -1 =  34 32  
                $end_cursor =  $key;
                array_push($spot_holder, array_slice($spots,$current_cursor,$end_cursor - $current_cursor));
                $current_cursor =  $key + 1; // assuming the version has spots value to its front
            }
        }
        return array_values(array_filter($spot_holder)) ;
    }

    public function invoke_cp(Request $request){
        $userdata = session("user_data");
        $username = $userdata->username;
        $data = $request->data;
        $this->deleteAllfileDIR("cp_output/$username");
        $cps = $data;
        $dir = public_path("cp/$username");
        $file = array_slice(scandir(public_path("cp/$username")), 2); // for the file name puroses only
        $pages = [];
        foreach ($cps as $cps_group_key => $cp_set) {  // FILES uploaded, it has many set of CP
           $pages_per_file_cp = [];
            foreach ($cp_set as $cp_set_key => $cp) {  // SINGLE file cp , it has multiple monthly cp
                $per_page =  39;
                $header_details = array_diff_key($cp, array_flip(["spots", "version"])); // get all header details except spots
                $spot_holder = $cp["spots"];
                $spots = array_filter(array_map(function($data){
                    return (implode("  ", array_filter($data)));
                },$spot_holder));

                $page = array_map(function($data) use ($header_details){
                    return array_merge($header_details, ["spots" => $data]);
                },array_chunk($spots, $per_page));

                $last_page_spot_rows = sizeof($page[array_key_last($page)]["spots"]); // get how may row of spot is in the last page
                $cp_version = preg_replace('/\s\s+/', ' ', $cp["version"]);
                $rows_for_cp_version = (int)ceil(strlen($cp_version) / 60); // get how may rows does a version need
                // dd($rows_for_cp_version);
                if(($rows_for_cp_version + $last_page_spot_rows) >= $per_page){
                    // create last page here for the version
                    array_push($page, array_diff_key($page[array_key_last($page)], array_flip(["spots"])));
                    $page[array_key_last($page)]["spots"] = array();
                    $page[array_key_last($page)]["version"] = $cp_version;
                }else{
                    $page[array_key_last($page)]["version"] = $cp_version; // include the CP version in the last page of spot if $rows_for_cp_version + $last_page_spot_rows is not >= $per_page
                    // dd($page[array_key_last($page)]);
                }
                $pages_per_file_cp = array_merge($pages_per_file_cp, $page);
                // dd($pages_per_file_cp);
            }
            array_push($pages, $pages_per_file_cp);
        }


        foreach ($pages as $key => $value) {
            // dd($value);
            $userdata = session("user_data");
            $username = $userdata->username;
            $data = [
                "mbclogo" => asset('images/mbclogo.jpg'),
                "kbplogo" => asset('images/kbplogo.jpg'),
                "userdata" => $userdata,
                "jen_sig" => asset('images/jen_sig.png'),
                "pdf" => $value
            ];
            $file_name = (string)rand(0,2000);
            $pdf = new \Mpdf\Mpdf(['format' => 'Letter']);
            $pdf->falseBoldWeight = 8;
            $pdf->useActiveForms = true;
            $html = view('welcome', ($data))->render();
            $pdf->WriteHTML($html);
            $pdf->Output(public_path("cp_output\\$username\\") . "CPM_".$file[$key], \Mpdf\Output\Destination::FILE);
            // $pdf->Output('filename.pdf', 'I');
        }

    }

}
