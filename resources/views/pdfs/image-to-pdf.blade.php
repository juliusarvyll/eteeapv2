<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Image</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .image-container {
            width: 100%;
            text-align: center;
        }
        img {
            max-width: 100%;
            max-height: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="file://{{ $imagePath }}" alt="Document Image">
    </div>
</body>
</html>
