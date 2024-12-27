<div>
<?php 
$jsonVal = json_decode($result[$config['field']], true);
if($jsonVal)
    foreach ($jsonVal as $key => $value)
    {
        echo "<b>" . ucwords(str_replace("_"," ", $key)) . '</b>: ';
        if(is_array($value)){
            echo "<br>";
            foreach ($value as $key2 => $value2)
            {
                echo "<b>" . ucwords(str_replace("_"," ", $key2)) . '</b>: ';
                if(is_array($value2)){
                    json_encode($value2, JSON_PRETTY_PRINT);
                } else {
                    echo (filter_var($value2, FILTER_VALIDATE_URL) ? '<a href="'.$value2.'" target="_blank">Link</a>' : $value2) . '<br>'; 
                }
            }
        } else {
            echo (filter_var($value, FILTER_VALIDATE_URL) ? '<a href="'.$value.'" target="_blank">Link</a>' : $value) . '<br>'; 
        }
    }
?>
</div>
