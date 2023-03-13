<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use File;
use PDF;
use Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\ValidationException;

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
                        dd($spot_group);
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

            // dd($spots);

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
            array_push($result, $cp);
            $cp = [];
        }
        return $result;
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
            // each pages should have advertiser, agency, month_year, station, ae, date, product, com_len, brod_no, contract_num, letter_of_codes. version
                $per_page =  39; // each pages should only contain 26 rows of content / sentences
                $spots_value_in_row = 10; // e.g 1)05:10:22H  2)06:11:05H  3)07:11:05H  4)08:10:18H  5)09:12:38H  6)10:11:50H  7)11:11:42H  8)12:13:00H  the previews version is 8
                $spots =  $cp["spots"];
                dd($spots);
                $spots_date_and_value = []; // contains the 8 sections spots
                foreach ($spots as $spots_key => $spots_value) { // each date of spots, e.g. 1 09:40:09H || 2 11:40:09H || 5 01:40:09H
                    $spots_value = explode("  ", $spots_value);
                    // sort($spots_value);
                    // dd($spots_value);
                    $spots_to_render_perge = [];
                    $spots_time_value = array_map(function($d){
                        return implode("  ", $d);
                    },array_chunk($spots_value, $spots_value_in_row)); // split array and make a group by 8, merge the group to make 1 spot value
                    $spots_date_and_value[$spots_key] = $spots_time_value;
                }

                $header_details = array_diff_key($cp, array_flip(["spots", "version"])); // get all header details except spots
                $spot_count = 1;
                $spot_container = [];
                foreach ($spots_date_and_value as $spots_date_and_value_key => $spots_date_and_value_value) {
                    for($i = 0; $i < sizeof($spots_date_and_value_value); $i++){
                        array_push($spot_container, [$spots_date_and_value_key => $spots_date_and_value_value[$i]]);
                        //push all spot lines to $spot_container with spot date as Key value and content is all the value for that date of spot, chucked by 8
                    }
                }
                $page = array_map(function($d) use ($header_details){
                    return array_merge($header_details, ["spots" => $d]);// add the header_details here
                }, array_chunk($spot_container, $per_page)); // chunk spots to 28 so  each cp page will have 30 spots
                //** 86 character per row
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

                // check the last index of page, add version for the page if it can accommodate
           }
           array_push($pages, $pages_per_file_cp);
        }


        foreach ($pages as $key => $value) {
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
