<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .container {
            display: flex;
            align-items: center; /* Align items vertically */
            justify-content: space-between; /* Distribute space between items */
            width: 100%; /* Ensure the container takes up the full width */
        }
        .image {
            flex: 1; /* Take up equal space */
            padding: 10px; /* Optional padding */
        }
        .text {
            flex: 1; /* Take up equal space */
            padding: 10px; /* Optional padding */
        }
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image">
            <img src="{{ base64_image(public_path('assets/img/von.png')) }}" alt="image">
        </div>
        <div class="text">
            <h1>{{ $title }}</h1>
        </div>
    </div>
    <p>{{ $content }}</p>
</body>
</html>
