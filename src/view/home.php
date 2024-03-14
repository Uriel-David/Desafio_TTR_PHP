<?php
    use App\lib\ProcessData;
    $processData = new ProcessData();
    $string = $processData->compareData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge TTR - PHP</title>
</head>
<body>
    <div><?= $string; ?></div>
</body>
</html>