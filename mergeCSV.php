<?php 

$directory = "csv/*"; // CSV Files Directory Path

$masterCSVFile = fopen('all.csv', "w+");

foreach(glob($directory) as $file) {

    $data = [];

    if (strpos($file, '.csv') !== false) {

        if (($handle = fopen($file, 'r')) !== false) {
            while (($dataValue = fgetcsv($handle, 1000)) !== false) {
                $data[] = $dataValue;
            }
        }

        fclose($handle); 
        
        unset($data[0]);

        if(count($data) > 0) {

            foreach ($data as $value) {
                try {
                   fputcsv($masterCSVFile, $value, ", ", "'");
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            
            }

        } else {
            echo "[$file] file contains no record to process.";
        }

    } else {
        echo "[$file] is not a CSV file.";
    }

}

fclose($masterCSVFile);
?>