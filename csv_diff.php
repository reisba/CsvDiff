<?php
if (count($argv) !== 4){
    $filename = __FILE__;
    echo "Run: {$filename} {existing_source_csv} {new_source_csv} {shared_unique_csv_cell_title}";
    echo "\n";
    echo "Example: {$filename} existing_data.csv current_production_data.csv ID";
    echo "\n";
    exit();
}
ini_set('auto_detect_line_endings',TRUE);
$existingTranslations = $argv[1];
$currentTranslationData = $argv[2];
$sharedUniqueId = $argv[3];

$handle = fopen($argv[1], 'r');

$uniqueIds = [];
$loop = 1;
$uniqueIdRowIndex = null;
while ($row = fgetcsv($handle, 0, ';')) {
    if ($loop === 1){
        $uniqueIdRowIndex = getIndexOf($sharedUniqueId, $row);
        if (is_null($uniqueIdRowIndex)){
            dumpRow($row);
            throw new \LogicException("Cannot find unique header {$sharedUniqueId} in {$existingTranslations}");
        }
    }
    if ($loop > 1){
        $uniqueIds[] = $row[$uniqueIdRowIndex];
    }
    $loop++;
}
fclose($handle);
$handle = fopen($currentTranslationData, 'r');
$loop = 1;
$uniqueIdRowIndex = null;
$diffedRows = array();
$cSkipped = 0;
while ($row = fgetcsv($handle, 0, ';')) {
    if ($loop === 1){
        $uniqueIdRowIndex = getIndexOf($sharedUniqueId, $row);
        if (is_null($uniqueIdRowIndex)){
            dumpRow($row);
            throw new \LogicException("Cannot find unique header {$sharedUniqueId} in {$currentTranslationData}");
        }
        // Title cells
        $diffedRows[] = $row;
    }
    if ($loop > 1){
        $id = $row[$uniqueIdRowIndex];
        if (in_array($id, $uniqueIds)){
            echo "Skipped (same unique id found): {$id} \n";
            $cSkipped++;
        } else {
            $diffedRows[] = $row;
        }
    }
    $loop++;
}
fclose($handle);
ini_set('auto_detect_line_endings',FALSE);
$filename = $currentTranslationData . ".diffed.csv";
$handle = fopen($filename, 'w');
foreach ($diffedRows as $item){
    fputcsv($handle, $item, ';');
}
fclose($handle);
echo "Total rows skipped: {$cSkipped}"; echo "\n";
echo "New rows in {$currentTranslationData}: " . count($diffedRows); echo "\n";
echo "Diff written in: {$filename}"; echo "\n";

function getIndexOf($value, $arrayData) {
    foreach ($arrayData as $index => $itemValue){
        if ($itemValue === $value){
            return $index;
        }
    }

    return null;
}

function dumpRow($row){
    echo "Content of the row:"; echo "\n";
    foreach ($row as $index => $value){
        echo "[{$index}] {$value}"; echo "\n";
    }
}
