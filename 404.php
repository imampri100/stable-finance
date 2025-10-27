<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }

        .error-code {
            font-size: 96px;
            font-weight: 800;
            color: #4A90E2;
            margin-bottom: 10px;
        }

        .error-message {
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .description {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .home-button {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #4A90E2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .home-button:hover {
            background-color: #3A7BD5;
        }

        .illustration {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 72px;
            }
            .error-message {
                font-size: 18px;
            }
            .home-button {
                font-size: 14px;
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <svg class="illustration" viewBox="0 0 24 24" fill="none" stroke="#4A90E2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <line x1="15" y1="9" x2="9" y2="15" />
        <line x1="9" y1="9" x2="15" y2="15" />
    </svg>
    <div class="error-code">404</div>
    <div class="error-message">Page Not Found</div>
    <div class="description">The page you’re looking for doesn’t exist or has been moved.</div>
    <a href="index.php" class="home-button">← Back to Home</a>
</div>
</body>
</html>
