<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Tracker</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f6f8fa;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        h1 {
            color: #24292e;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #586069;
            font-size: 18px;
            margin-bottom: 40px;
        }
        .nav-button {
            display: inline-block;
            background: #0366d6;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px;
            transition: background-color 0.2s;
        }
        .nav-button:hover {
            background: #0256cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Git Tracker</h1>
        <div class="subtitle">Track Git repositories (only GitHub is enabled right now)</div>
        
        <a href="/commits" class="nav-button">View Commits</a>
    </div>
</body>
</html> 