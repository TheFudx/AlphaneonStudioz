 <!-- Top Navbar -->
 <header class="bg-dark-light p-4 flex items-center justify-between shadow-md z-10">
    <!-- Search Bar -->
    <div class="relative flex-1 max-w-lg mx-auto md:mx-0">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" placeholder="Search"
            class="w-full bg-dark rounded-full py-2 pl-10 pr-4  transition-all duration-200">
    </div>

     <!-- User Info & Notifications -->
    <div class="flex items-center space-x-4 ml-auto">
        <a href="#" class="text-gray-400 hover:text-white transition duration-200 relative">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
        </a>

        <a href="#" class="flex items-center space-x-2">
            @if (app('logged-in-user')->profile_picture)
                <img src="{{ asset('storage/profile_pictures/' . app('logged-in-user')->profile_picture) }}"
                    alt="User Avatar" class="h-9 w-9 rounded-full border-2 border-theme">
            @else
                <img src="https://placehold.co/36x36/6B46C1/FFFFFF?text={{ app('logged-in-user')->email ? strtoupper(substr(app('logged-in-user')->email, 0, 1)) : strtoupper(substr(app('logged-in-user')->name, 0, 1)) }}"
                    alt="User Avatar" class="h-9 w-9 rounded-full border-2 border-theme">
            @endif
            <span class="font-semibold text-sm hidden sm:block">{{ app('logged-in-user')->name }}</span>
        </a>
        <a href="#" class="text-gray-400 hover:text-white transition duration-200 hidden md:block">
            <i class="fas fa-chevron-down text-sm"></i>
        </a>
    </div>
 </header>
