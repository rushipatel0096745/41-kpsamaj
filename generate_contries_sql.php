<?php
$file   = '/Users/corewixteam08/Rushikesh/KP-FTP copy main/countries.csv';
$handle = fopen($file, 'r');
$header = fgetcsv($handle); // skip header row

$inserts = [];

while (($row = fgetcsv($handle)) !== false) {
    [$iso_code, $country_name, $phone, $currency, $latitude, $longitude] = $row;

    $iso_code     = addslashes(trim($iso_code));
    $country_name = addslashes(trim($country_name));
    $phone        = addslashes(trim($phone));
    $currency     = addslashes(trim($currency));
    $latitude     = is_numeric(trim($latitude))  ? trim($latitude)  : 'NULL';
    $longitude    = is_numeric(trim($longitude)) ? trim($longitude) : 'NULL';

    $inserts[] = "('$iso_code', '$country_name', '$country_name', '$phone', '$currency', 0, $latitude, $longitude)";
}

fclose($handle);

$sql = "INSERT INTO `list_countries` (`iso_code`, `country_name`, `name`, `phone`, `currency`, `delflag`, `latitude`, `longitude`) VALUES \n"
     . implode(",\n", $inserts) . ";";

file_put_contents('countries_insert.sql', $sql);
echo "Done. countries_insert.sql generated.\n";
?>