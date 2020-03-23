<?php 

//echo $image[1];

function newObject(){
    return new stdClass();
}

function extp($str, $delimiter="."){
    return explode($delimiter, $str);
}

function posi($csv_pos, $image_dim){
    $new_w = $csv_pos[0] * $image_dim[0];
    $new_h = $csv_pos[1] * $image_dim[1];
    return [(int) $new_w, (int) $new_h];
}

function sindeg($deg){
    return sin(deg2rad($deg));
}

function cosdeg($deg){
    return cos(deg2rad($deg));
}

function pointRotate($point, $deg, $imagesize){
    $x = $point[0];
    $y = $point[1];
    $xm = $imagesize[1]/2;
    $ym = $imagesize[0]/2;
    /*
    $new_x = (cosdeg($deg) * $x + $imagesize[0]/2) - (sindeg($deg) * $y + $imagesize[1] / 2);
    $new_y = (sindeg($deg) * $y + $imagesize[1] / 2) + (cosdeg($deg) * $x + $imagesize[0] / 2);
    */
    $new_x = ($x - $xm) * cosdeg($deg) - ($y - $ym) * sindeg($deg) + $xm;
    $new_y = ($x - $xm) * sindeg($deg) + ($y - $ym) * cosdeg($deg) + $ym;

    $obj = new stdClass();
    $obj->x = $new_x;
    $obj->y = $new_y;

    return $obj;
}

function pointRotate2($points, $deg, $imagesize){
    if($deg == 0 || $deg == 360)
        return $points;
    elseif($deg == 90){

    }
}


function orderRotate($deg, $points){
    if($deg == 0 || $deg == 360)
        return $points;
    elseif($deg == 90)
        return [$points[3], $points[0], $points[1], $points[2]];
    elseif($deg == 180)
        return [$points[2], $points[3], $points[0], $points[1]];
    elseif($deg == 270)
        return [$points[1], $points[2], $points[3], $points[0]];
    
    echo $deg;
}

class AutoMLDA{
    public $objects; 
    public $objects_count;
    public function __construct($csvname="optikic.csv", $automl_dir = "gs://optik-vcm/optikic/"){
        if(($handle = fopen($csvname, "r")) !== FALSE){
            $objects = [];
            while(($data = fgetcsv($handle, 100000, ",")) !== FALSE){
                
                $num = count($data);
                $obj = newObject();
        
                $obj->method = $data[0];
                
                $d = $data[1];
                $d = str_replace($automl_dir,"",$d);
                $filename = explode('-',$d);
                $filename = $filename[0];
                $ext = explode('.',$d);
                $ext = $ext[count($ext) - 1];
                $obj->filename = $filename.".".$ext;
        
                $obj->label = $data[2];
        
                $p1 = [$data[3], $data[4]];
                $p2 = [$data[5], $data[6]];
                $p3 = [$data[7], $data[8]];
                $p4 = [$data[9], $data[10]];
        
                $obj->automl_points = [$p1,$p2,$p3,$p4];
        
                $imagesize = getimagesize('in/'.$obj->filename);
                $w = $imagesize[0];
                $h = $imagesize[1];
                $obj->imagesize = [$w, $h];
        
                $p1_px = posi($p1, [$w, $h]);
                $p2_px = posi($p2, [$w, $h]);
                $p3_px = posi($p3, [$w, $h]);
                $p4_px = posi($p4, [$w, $h]);
                
                $obj->pixel_points = [$p1_px, $p2_px, $p3_px, $p4_px];
        
                array_push($objects, $obj);
            }
            fclose($handle);
        
            $this->objects = $objects;
            $this->objects_count = count($objects);
        } else{
            $this->objects = FALSE;
        }
    }

    public function original($form='automl',  $output_filename, $path_multiply_before='', $path_multiply_after='', $method='TRAIN'){
        $objs = $this->objects;
        $row_list = [];
        foreach($objs AS $o){
            $ext = extp($o->filename);
            $filename = $path_multiply_before.$ext[0].$path_multiply_after.".".$ext[1];

            if($form == "automl"){
                $automl_points = $o->automl_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $automl_points[0][0], $automl_points[0][1],
                    $automl_points[1][0], $automl_points[1][1],
                    $automl_points[2][0], $automl_points[2][1],
                    $automl_points[3][0], $automl_points[3][1]
                ]);    
            } elseif($form == 'pixel'){
                $pixel_points = $o->pixel_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $pixel_points[0][0], $pixel_points[0][1],
                    $pixel_points[1][0], $pixel_points[1][1],
                    $pixel_points[2][0], $pixel_points[2][1],
                    $pixel_points[3][0], $pixel_points[3][1]
                ]);
            }
        }

        

        $out_csv = fopen($output_filename, 'w');
        foreach($row_list AS $rl){
            fputcsv($out_csv, $rl);
        }

        fclose($out_csv);

        echo "CSV created as $output_filename filename<br>";
    }

    public function flipX($form = 'automl', $output_filename, $path_multiply_before='', $path_multiply_after='', $method='TRAIN'){
        $objs = $this->objects;
        $row_list = [];
        foreach($objs AS $o){
            $ext = extp($o->filename);
            $filename = $path_multiply_before.$ext[0].$path_multiply_after.".".$ext[1];

            if($form == "automl"){
                $automl_points = $o->automl_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $automl_points[1][0], 1 - $automl_points[1][1],
                    $automl_points[0][0], 1 - $automl_points[0][1],
                    $automl_points[3][0], 1 - $automl_points[3][1],
                    $automl_points[2][0], 1 - $automl_points[2][1]
                ]);    
            } elseif($form == 'pixel'){
                $pixel_points = $o->pixel_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $pixel_points[1][0], $o->imagesize[0] - $pixel_points[1][1],
                    $pixel_points[0][0], $o->imagesize[0] - $pixel_points[0][1],
                    $pixel_points[3][0], $o->imagesize[0] - $pixel_points[3][1],
                    $pixel_points[2][0], $o->imagesize[0] - $pixel_points[2][1]
                ]);
            }
        }

        

        $out_csv = fopen($output_filename, 'w');
        foreach($row_list AS $rl){
            fputcsv($out_csv, $rl);
        }

        fclose($out_csv);

        echo "CSV created as $output_filename filename<br>";
    }

    public function flipY($form = 'automl', $output_filename, $path_multiply_before='', $path_multiply_after='', $method='TRAIN'){
        $objs = $this->objects;
        $row_list = [];
        foreach($objs AS $o){
            $ext = extp($o->filename);
            $filename = $path_multiply_before.$ext[0].$path_multiply_after.".".$ext[1];

            if($form == "automl"){
                $automl_points = $o->automl_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    1 - $automl_points[3][0], $automl_points[3][1],
                    1 - $automl_points[2][0], $automl_points[2][1],
                    1 - $automl_points[1][0], $automl_points[1][1],
                    1 - $automl_points[0][0], $automl_points[0][1]
                ]);    
            } elseif($form == 'pixel'){ 
                $pixel_points = $o->pixel_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $o->imagesize[1] - $pixel_points[3][0], $pixel_points[3][1],
                    $o->imagesize[1] - $pixel_points[2][0], $pixel_points[2][1],
                    $o->imagesize[1] - $pixel_points[1][0], $pixel_points[1][1],
                    $o->imagesize[1] - $pixel_points[0][0], $pixel_points[0][1]
                ]);
            }
        }

        

        $out_csv = fopen($output_filename, 'w');
        foreach($row_list AS $rl){
            fputcsv($out_csv, $rl);
        }

        fclose($out_csv);

        echo "CSV created as $output_filename filename<br>";
    }

    public function flip2($form = 'automl', $output_filename, $path_multiply_before='', $path_multiply_after='', $method='TRAIN'){
        $objs = $this->objects;
        $row_list = [];
        foreach($objs AS $o){
            $ext = extp($o->filename);
            $filename = $path_multiply_before.$ext[0].$path_multiply_after.".".$ext[1];

            if($form == "automl"){
                $automl_points = $o->automl_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    1 - $automl_points[2][0], 1 - $automl_points[2][1],
                    1 - $automl_points[3][0], 1 - $automl_points[3][1],
                    1 - $automl_points[0][0], 1 - $automl_points[0][1],
                    1 - $automl_points[1][0], 1 - $automl_points[1][1]
                ]);    
            } elseif($form == 'pixel'){
                $pixel_points = $o->pixel_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $o->imagesize[0] - $pixel_points[2][0], $o->imagesize[1] - $pixel_points[2][1],
                    $o->imagesize[0] - $pixel_points[3][0], $o->imagesize[1] - $pixel_points[3][1],
                    $o->imagesize[0] - $pixel_points[0][0], $o->imagesize[1] - $pixel_points[0][1],
                    $o->imagesize[0] - $pixel_points[1][0], $o->imagesize[1] - $pixel_points[1][1]
                ]);
            }
        }

        

        $out_csv = fopen($output_filename, 'w');
        foreach($row_list AS $rl){
            fputcsv($out_csv, $rl);
        }

        fclose($out_csv);

        echo "CSV created as $output_filename filename<br>";
    }

    public function rotate($form = 'automl', $output_filename, $deg, $path_multiply_before='', $path_multiply_after='', $method='TRAIN'){
        $objs = $this->objects;
        $row_list = [];
        foreach($objs AS $o){
            $ext = extp($o->filename);
            $filename = $path_multiply_before.$ext[0].$path_multiply_after.".".$ext[1];

            if($form == "automl"){
                $automl_points = $o->automl_points;
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $automl_points[0][0], 1 - $automl_points[0][1],
                    $automl_points[1][0], 1 - $automl_points[1][1],
                    $automl_points[2][0], 1 - $automl_points[2][1],
                    $automl_points[3][0], 1 - $automl_points[3][1]
                ]);    
            } elseif($form == 'pixel'){
                $pixel_points = $o->pixel_points;
                $p1 = pointRotate([$pixel_points[0][0], $pixel_points[0][1]], $deg, $o->imagesize);
                $p2 = pointRotate([$pixel_points[1][0], $pixel_points[1][1]], $deg, $o->imagesize);
                $p3 = pointRotate([$pixel_points[2][0], $pixel_points[2][1]], $deg, $o->imagesize);
                $p4 = pointRotate([$pixel_points[3][0], $pixel_points[3][1]], $deg, $o->imagesize);
                $or_rot = orderRotate($deg, array($p1,$p2,$p3,$p4));
                $p1 = $or_rot[0];
                $p2 = $or_rot[1];
                $p3 = $or_rot[2];
                $p4 = $or_rot[3];
                array_push($row_list, [
                    $method,
                    $filename,
                    $o->label,
                    $p1->x, $p1->y,
                    $p2->x, $p2->y,
                    $p3->x, $p3->y,
                    $p4->x, $p4->y
                ]);
            }
        }
        $out_csv = fopen($output_filename, 'w');
        foreach($row_list AS $rl){
            fputcsv($out_csv, $rl);
        }

        fclose($out_csv);

        echo "CSV created as $output_filename filename<br>";
    }
}

$automl = new AutoMLDA();

/*
$automl->original('automl','csv/csv_original.csv','gs://optik-vcm/augmented/','_original');
$automl->flipX('automl','csv/csv_flipX.csv','gs://optik-vcm/augmented/','_flipX');
$automl->flipY('automl','csv/csv_flipY.csv','gs://optik-vcm/augmented/','_flipY');
$automl->flip2('automl','csv/csv_flip2.csv','gs://optik-vcm/augmented/','_flip2');
$automl->original('automl','csv/csv_gaussian.csv','gs://optik-vcm/augmented/','_gaussian', 'TEST');
$automl->original('automl','csv/csv_gaussian2.csv','gs://optik-vcm/augmented/','_gaussian2', 'VALIDATION');
$automl->flipX('automl','csv/csv_gaussian_flipX.csv','gs://optik-vcm/augmented/','_gaussian_flipX');
$automl->flipY('automl','csv/csv_gaussian_flipY.csv','gs://optik-vcm/augmented/','_gaussian_flipY');
$automl->flip2('automl','csv/csv_gaussian_flip2.csv','gs://optik-vcm/augmented/','_gaussian_flip2');
*/

$automl->original('pixel','csv/csv_original.csv','out/','_original');
$automl->flipX('pixel','csv/csv_flipX.csv','out/','_flipX');
$automl->flipY('pixel','csv/csv_flipY.csv','out/','_flipY');
$automl->flip2('pixel','csv/csv_flip2.csv','out/','_flip2');
$automl->original('pixel','csv/csv_gaussian.csv','out/','_gaussian', 'TEST');
$automl->original('pixel','csv/csv_gaussian2.csv','out/','_gaussian2', 'VALIDATION');
$automl->flipX('pixel','csv/csv_gaussian_flipX.csv','out/','_gaussian_flipX');
$automl->flipY('pixel','csv/csv_gaussian_flipY.csv','out/','_gaussian_flipY');
$automl->flip2('pixel','csv/csv_gaussian_flip2.csv','out/','_gaussian_flip2');





?>