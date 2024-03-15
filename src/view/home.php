<?php
    use App\exception\MissingCsvException;
    use App\lib\ProcessData;

    if(isset($_FILES['newCsv']) && isset($_FILES['olderCsv'])) {
        $newCsv     = $_FILES['newCsv']['tmp_name'];
        $olderCsv   = $_FILES['olderCsv']['tmp_name'];

        try {
            $processData    = new ProcessData($newCsv, $olderCsv);
            $results        = $processData->compareCSV();
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

    <?php if (isset($results)): ?>
        <h3>Unchanged Rows</h3>
        <?php if (count($results['unchanged_rows']) > 0): ?>
            <table>
                <tr>
                    <?php foreach ($results['header'] as $header): ?>
                        <th><?php echo $header; ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($results['unchanged_rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?php echo $value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No unchanged rows found.</p>
        <?php endif; ?>

        <h3>Updated Rows</h3>
        <?php if (count($results['updated_rows']) > 0): ?>
            <table>
                <tr>
                    <?php foreach ($results['header'] as $header): ?>
                        <th><?php echo $header; ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($results['updated_rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?php echo $value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No updated rows found.</p>
        <?php endif; ?>

        <h3>New Rows</h3>
        <?php if (count($results['new_rows']) > 0): ?>
            <table>
                <tr>
                    <?php foreach ($results['header'] as $header): ?>
                        <th><?php echo $header; ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($results['new_rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?php echo $value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No new rows found.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>