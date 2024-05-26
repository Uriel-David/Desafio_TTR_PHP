<?php
    use App\exception\MissingCsvException;
    use App\lib\FileCsvReader;
    use App\lib\ProcessData;

    if(isset($_FILES['newCsv']) && isset($_FILES['olderCsv'])) {
        $newCsv     = $_FILES['newCsv']['tmp_name'];
        $olderCsv   = $_FILES['olderCsv']['tmp_name'];

        try {
            $adapter        = new FileCsvReader();
            $processData    = new ProcessData($adapter);
            $result         = $processData->compareCSV($newCsv, $olderCsv);
        } catch (MissingCsvException $e) {
            echo $e->errorMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge TTR - PHP</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h4>Upload your files:</h4>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="newCsv">Select new CSV:</label>
        <input type="file" name="newCsv" id="newCsv" accept=".csv" style="margin-bottom: 10px;" required>
        <br/>

        <label for="olderCsv">Select older CSV:</label>
        <input type="file" name="olderCsv" id="olderCsv" accept=".csv" style="margin-bottom: 10px;" required>
        <br/>

        <br/>
        <input type="submit" value="compare" style="margin-bottom: 10px;">
    </form>

    <?php if (isset($result)): ?>
        <h2>Result</h2>

        <h3>Identical Rows</h3>
            <?php foreach ($result['identical'] as $row) : ?>
                <?= implode(",", $row['data']) . " (" . $row['old_position'] . "-" . $row['new_position'] . ")" ?><br />
            <?php endforeach; ?>

            <h3>Updated Rows</h3>
            <?php foreach ($result['updated'] as $row) : ?>
                <?= implode(",", $row['data']) . " (" . $row['old_position'] . "-" . $row['new_position'] . ")" ?><br />
            <?php endforeach; ?>

            <h3>New Rows</h3>
            <?php foreach ($result['new'] as $row) : ?>
                <?= implode(",", $row['data']) . " (" . $row['old_position'] . "-" . $row['new_position'] . ")" ?><br />
            <?php endforeach; ?>

            <h3>Removed Rows</h3>
            <?php foreach ($result['removed'] as $row) : ?>
                <?= implode(",", $row['data']) . " (" . $row['old_position'] . "-" . $row['new_position'] . ")" ?><br />
            <?php endforeach; ?>
    <?php else: ?>
        <p>No new rows found.</p>
    <?php endif; ?>
</body>
</html>