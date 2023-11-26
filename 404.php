<?php
http_response_code(404);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .error-container {
            text-align: center;
            margin-top: 100px;
        }

        h1 {
            font-size: 10rem;
            color: #dc3545;
        }

        p {
            font-size: 1.5rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <h1 class="display-4">404</h1>
        <p class="lead">Not Found</p>
        <p>The requested page could not be found.</p>
    </div>
</body>
</html>
