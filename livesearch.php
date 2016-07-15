<?php
$pattern = filter_input(INPUT_POST, "pattern"); //email input jelenlegi tartalma
$container_id = filter_input(INPUT_POST, "elementId"); //a livesearch div-et tartalmazó div ID-ja
$row = 1;
/*if (($handle = fopen("mailcimtar.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}
  
 */

if (($handle = fopen("mailcimtar.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //$num_fields = count($data); //Adatmezők száma
//        echo "<p> $num fields in line $row: <br /></p>\n";
        if($row >= 2 && strlen($pattern) >= 2) { //Az első sorba a fejlécek vannak
            $email_address = $data[4];
            if (strpos($email_address, $pattern) !== false) {
                echo ('<p data-containerId=' .$container_id. '" class="liveSearchItem">' . $email_address . "</p><br/>");
            }
        }
        $row++;
    }
    fclose($handle);
}

//output the response
//$response = "ures";
//echo $response;

//echo ('{"valasz" : "hurrá"}');
?> 