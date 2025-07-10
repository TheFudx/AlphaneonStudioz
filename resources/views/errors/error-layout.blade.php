<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #191919;
            color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }

        .error-container {
            max-width: 600px;
            margin: auto;
        }

        .error-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 2rem;
        }

        .error-code {
            font-size: 40px;
            font-weight: bold;
            color: #ff3a1f;
        }

        .error-message {
            font-size: 24px;
            margin-bottom: 1.5rem;
        }

        .error-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #ff3a1f;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .error-button:hover {
            background-color: #e63317;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <img src="{{ URL::to('asset/images/404-image-2.png') }}" alt="Coming soon" class="img-fluid"
            style="max-width: 300px; margin-top: 10px;">

            @yield('content')

        <a href="{{ url('/') }}" class="error-button">Back to Home</a>
    </div>
</body>

</html>
