<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>API Tester</title>
    <style>
        .warning {
            color: red;
            font-weight: bold;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }
        .response pre {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>API Tester</h1>
        <form method="POST" action="">
            <label for="api-url">Enter API URL:</label>
            <input type="text" id="api-url" name="api-url" required>
            <button type="submit">Test API</button>
        </form>
        <div class="response">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['api-url'])) {
                $apiUrl = $_POST['api-url'];
                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                echo "<h2>Response (HTTP Code: $httpCode):</h2>";
                echo "<pre>" . htmlspecialchars($response) . "</pre>";

                // Check for PII in the response
                $piiFields = ['name', 'email', 'phone', 'address'];
                $piiWarnings = [];
                $responseData = json_decode($response, true);

                if (is_array($responseData)) {
                    $firstItem = reset($responseData); // Get the first item of the array
                    if (is_array($firstItem)) {
                        foreach ($piiFields as $field) {
                            if (isset($firstItem[$field])) {
                                $piiWarnings[] = "$field: " . htmlspecialchars($firstItem[$field]);
                            }
                        }
                    }
                }

                if (!empty($piiWarnings)) {
                    echo "<div class='warning'>";
                    echo "<h2>Warning: PII Detected</h2>";
                    echo "<ul>";
                    foreach ($piiWarnings as $warning) {
                        echo "<li>$warning</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>