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
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #2D3748; /* Darker gray for the track */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #4A5568; /* Even darker gray for the thumb */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #606f80;
        }
    </style>
</head>
<body class="bg-gray-900 text-white flex min-h-screen">

    <!-- Blade: @include('partials.sidebar') -->
    <!-- Left Sidebar -->
    <aside class="w-64 bg-gray-800 p-6 flex flex-col md:flex-shrink-0 md:block hidden min-h-screen overflow-y-auto">
        <!-- Logo -->
        <div class="mb-8">
            <a href="#" class="flex items-center space-x-2 text-white">
                <img src="https://placehold.co/40x40/3182CE/FFFFFF?text=Logo" alt="Alphanon Studio Logo" class="h-10 w-10 rounded-full">
                <div class="flex flex-col">
                    <span class="text-xl font-bold">alphaneon</span>
                    <span class="text-sm text-gray-400 -mt-1">Studio2</span>
                </div>
            </a>
        </div>

        <!-- Main Navigation -->
        <nav class="space-y-4 mb-8">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Discover</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 text-purple-400 font-semibold">
                            <i class="fas fa-compact-disc"></i>
                            <span>Popular Albums</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-list"></i>
                            <span>Genres</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-music"></i>
                            <span>Popular songs</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-calendar-alt"></i>
                            <span>New releases</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Your Music</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-headphones"></i>
                            <span>Songs</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-compact-disc"></i>
                            <span>Albums</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-users"></i>
                            <span>Artists</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Playlists Section -->
        <div class="mt-auto">
            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Playlists</h3>
            <ul class="space-y-2">
                <!-- Example Playlists -->
                <li><a href="#" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 transition duration-200"><span>My Playlist 1</span><i class="fas fa-ellipsis-h text-gray-400 text-sm"></i></a></li>
                <li><a href="#" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 transition duration-200"><span>Workout Jams</span><i class="fas fa-ellipsis-h text-gray-400 text-sm"></i></a></li>
                <li><a href="#" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 transition duration-200"><span>Chill Vibes</span><i class="fas fa-ellipsis-h text-gray-400 text-sm"></i></a></li>
                <li>
                    <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700 transition duration-200 text-gray-400">
                        <i class="fas fa-plus"></i>
                        <span>New Playlist</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Blade: @include('partials.navbar') -->
        <!-- Top Navbar -->
        <header class="bg-gray-800 p-4 flex items-center justify-between shadow-md z-10">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-lg mx-auto md:mx-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search" class="w-full bg-gray-700 text-white rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200">
            </div>

            <!-- User Info & Notifications -->
            <div class="flex items-center space-x-4 ml-auto">
                <a href="#" class="text-gray-400 hover:text-white transition duration-200 relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                </a>
                <a href="#" class="flex items-center space-x-2">
                    <img src="https://placehold.co/36x36/6B46C1/FFFFFF?text=U" alt="User Avatar" class="h-9 w-9 rounded-full border-2 border-purple-500">
                    <span class="font-semibold text-sm hidden sm:block">alphastudioz</span>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition duration-200 hidden md:block">
                    <i class="fas fa-chevron-down text-sm"></i>
                </a>
            </div>
        </header>

        <!-- Blade: @yield('content') -->
        <!-- Main Content (Dashboard/Albums Page) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-8">
            <!-- Breadcrumbs / Navigation -->
            <nav class="text-sm text-gray-400">
                <a href="#" class="hover:text-white">Popular Albums</a> &gt;
                <span class="text-purple-400">All Albums</span>
            </nav>

            <!-- Popular Albums Section -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Popular Albums</h2>
                    <a href="#" class="text-purple-400 hover:underline text-sm">View All &gt;</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <!-- Album Card 1 -->
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative group">
                        <img src="https://placehold.co/300x300/1E3A8A/FFFFFF?text=Justice" alt="Justice Album Cover" class="w-full h-48 object-cover rounded-t-lg">
                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition duration-300 transform scale-100 hover:scale-110">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">Justice</h3>
                            <p class="text-gray-400 text-sm">Justin Bieber</p>
                        </div>
                    </div>

                    <!-- Album Card 2 -->
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative group">
                        <img src="https://placehold.co/300x300/374151/FFFFFF?text=Memories" alt="Memories Album Cover" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition duration-300 transform scale-100 hover:scale-110">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">Memories...Do Not Open</h3>
                            <p class="text-gray-400 text-sm">The Chainsmokers</p>
                        </div>
                    </div>

                    <!-- Album Card 3 -->
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative group">
                        <img src="https://placehold.co/300x300/4F46E5/FFFFFF?text=Student%20of%20the%20Year" alt="Student of the Year Album Cover" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition duration-300 transform scale-100 hover:scale-110">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">Student of the Year (Original Motion Picture Soundtrack)</h3>
                            <p class="text-gray-400 text-sm">Vishal-Shekhar</p>
                        </div>
                    </div>

                    <!-- Album Card 4 -->
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative group">
                        <img src="https://placehold.co/300x300/DC2626/FFFFFF?text=O%20Maahi" alt="O Maahi Album Cover" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition duration-300 transform scale-100 hover:scale-110">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">O Maahi (From "Dunki")</h3>
                            <p class="text-gray-400 text-sm">Pritam, Irshad Kamil, Arijit Singh</p>
                        </div>
                    </div>

                    <!-- Album Card 5 (Dummy) -->
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative group">
                        <img src="https://placehold.co/300x300/059669/FFFFFF?text=The+Next+Chapter" alt="The Next Chapter Album Cover" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition duration-300 transform scale-100 hover:scale-110">
                                <i class="fas fa-play text-xl"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold truncate">The Next Chapter</h3>
                            <p class="text-gray-400 text-sm">Imagine Dragons</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Popular Tracks Section -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Popular Tracks</h2>
                    <a href="#" class="text-purple-400 hover:underline text-sm">View All &gt;</a>
                </div>
                <div class="bg-gray-800 rounded-lg shadow-lg p-4">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr class="text-gray-400 uppercase text-xs">
                                <th class="py-2 px-4">#</th>
                                <th class="py-2 px-4">Title</th>
                                <th class="py-2 px-4">Artist</th>
                                <th class="py-2 px-4 hidden md:table-cell">Album</th>
                                <th class="py-2 px-4 text-right hidden lg:table-cell">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Track Row 1 -->
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition duration-200">
                                <td class="py-3 px-4 flex items-center space-x-3">
                                    <span class="text-gray-400">1</span>
                                    <button class="text-purple-400 hover:text-purple-500 text-lg"><i class="fas fa-play-circle"></i></button>
                                </td>
                                <td class="py-3 px-4 font-semibold">Birds Of A Feather</td>
                                <td class="py-3 px-4 text-gray-400">Billie Eilish</td>
                                <td class="py-3 px-4 text-gray-400 hidden md:table-cell">HIT ME HARD AND SOFT</td>
                                <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">3:30</td>
                            </tr>
                            <!-- Track Row 2 -->
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition duration-200">
                                <td class="py-3 px-4 flex items-center space-x-3">
                                    <span class="text-gray-400">2</span>
                                    <button class="text-purple-400 hover:text-purple-500 text-lg"><i class="fas fa-play-circle"></i></button>
                                </td>
                                <td class="py-3 px-4 font-semibold">Espresso</td>
                                <td class="py-3 px-4 text-gray-400">Sabrina Carpenter</td>
                                <td class="py-3 px-4 text-gray-400 hidden md:table-cell">Espresso</td>
                                <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">2:55</td>
                            </tr>
                            <!-- Track Row 3 -->
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition duration-200">
                                <td class="py-3 px-4 flex items-center space-x-3">
                                    <span class="text-gray-400">3</span>
                                    <button class="text-purple-400 hover:text-purple-500 text-lg"><i class="fas fa-play-circle"></i></button>
                                </td>
                                <td class="py-3 px-4 font-semibold">Gata Only</td>
                                <td class="py-3 px-4 text-gray-400">FloyyMenor, Cris Mj</td>
                                <td class="py-3 px-4 text-gray-400 hidden md:table-cell">Gata Only</td>
                                <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">3:42</td>
                            </tr>
                            <!-- Track Row 4 -->
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition duration-200">
                                <td class="py-3 px-4 flex items-center space-x-3">
                                    <span class="text-gray-400">4</span>
                                    <button class="text-purple-400 hover:text-purple-500 text-lg"><i class="fas fa-play-circle"></i></button>
                                </td>
                                <td class="py-3 px-4 font-semibold">Fortnight (feat. Post Malone)</td>
                                <td class="py-3 px-4 text-gray-400">Taylor Swift</td>
                                <td class="py-3 px-4 text-gray-400 hidden md:table-cell">The Tortured Poets Department</td>
                                <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">3:48</td>
                            </tr>
                            <!-- Track Row 5 -->
                            <tr class="hover:bg-gray-700 transition duration-200">
                                <td class="py-3 px-4 flex items-center space-x-3">
                                    <span class="text-gray-400">5</span>
                                    <button class="text-purple-400 hover:text-purple-500 text-lg"><i class="fas fa-play-circle"></i></button>
                                </td>
                                <td class="py-3 px-4 font-semibold">I Can Do It With a Broken Heart</td>
                                <td class="py-3 px-4 text-gray-400">Taylor Swift</td>
                                <td class="py-3 px-4 text-gray-400 hidden md:table-cell">The Tortured Poets Department</td>
                                <td class="py-3 px-4 text-right text-gray-400 hidden lg:table-cell">3:38</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
