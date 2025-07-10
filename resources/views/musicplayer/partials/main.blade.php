<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alphanon Studio</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --color-dark: #1A1A1A;
            /* A very dark charcoal */
            --color-dark-light: #2C2C2C;
            /* A slightly lighter charcoal */
            --color-gray-700-custom: #374151;
            /* Custom shade for hover */
            --color-gray-400-custom: #9CA3AF;
            /* Custom shade for text */
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--color-dark);
            /* Apply the dark background */
        }

        .bg-dark {
            background-color: var(--color-dark);
        }

        .bg-dark-light {
            background-color: var(--color-dark-light);
        }

        .hover\:bg-gray-700:hover {
            /* Custom hover for nav items */
            background-color: var(--color-gray-700-custom);
        }

        .text-gray-400 {
            /* Custom text color for certain elements */
            color: var(--color-gray-400-custom);
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-dark-light);
            /* Darker gray for the track */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4A5568;
            /* Even darker gray for the thumb */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #606f80;
        }

        .aside_logo {
            height: 62px;
            width: 175px;
        }

        .bg-theame {
            background-color: #FF3A1F;
            font-weight: 600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bg-theame:hover {
            font-weight: 700;
        }
       /* Specific styles for the album play button */
        .album-play-button {
            width: 60px;
            height: 60px;
            font-size: 1.8rem;
        }
        .table-play-pause-button {
            width: 32px;
            height: 32px;
            /* font-size: 0.8rem; */
        }

        .table-image {
            height: 35px;
            width: 35px;
            display: inline;
        }
         /* Styles for the fixed footer player */
        .fixed-footer-player {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--color-dark-light); /* Darker background for player */
            border-top: 1px solid #374151; /* Subtle top border */
            z-index: 1000; /* Ensure it stays on top */
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem; /* Space between sections */
        }

        /* Padding for the main content to avoid being hidden by the footer */
        main {
            padding-bottom: 96px; /* Adjust this value based on your footer's height */
                                  /* Footer height (approx 64px) + some extra for breathing room */
        }

        /* Seek bar styling */
        #music-seek-bar {
            width: 100%;
            height: 4px;
            background-color: #4A5568;
            border-radius: 2px;
            -webkit-appearance: none; /* Remove default styling */
            appearance: none;
            cursor: pointer;
        }

        #music-seek-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            margin-top: -4px; /* Center thumb vertically */
        }

        #music-seek-bar::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
        }

        /* Volume slider styling */
        #volume-slider {
            width: 100px; /* Fixed width for volume slider */
            height: 4px;
            background-color: #4A5568;
            border-radius: 2px;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
        }

        #volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
            margin-top: -4px;
        }

        #volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            background-color: var(--color-theme);
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
    @yield('style')
</head>

<body class="bg-dark text-white flex min-h-screen">
    @include('musicplayer.partials.sidebar')
    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden">
        @include('musicplayer.partials.navbar')
        @yield('content')
        
    </main>
    
    @include('musicplayer.partials.footer')
    @yield('script')
    
</body>

</html>
