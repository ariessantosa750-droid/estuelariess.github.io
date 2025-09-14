<?php
$versi = phpversion();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Versi PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 350px;
            width: 90%;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }
        .version {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            background: #eaf2ff;
            padding: 10px 15px;
            border-radius: 8px;
            display: inline-block;
        }
        footer {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Versi PHP yang digunakan:</h1>
        <div class="version"><?php echo $versi; ?></div>
        <footer>&copy; <?php echo date("Y"); ?> - Info Server</footer>
    </div>
</body>
</html>
