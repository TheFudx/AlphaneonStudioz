@extends('layouts.main')
@section('title')
    {{ config('app.project_name') }} | Search
@endsection

@section('main-section')
     <style>
        /* Search bar styles */
        .search-bar {
            background: #242631;
            border-radius: 8px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            width: 100%;
            color: #aaa;
            position: relative;
        }

        .search-bar i {
            font-size: 20px;
            margin-right: 10px;
            color: #8e8e9c;
        }

        .search-bar i.fa-times {
            margin-right: 0;
            margin-left: 10px;
            cursor: pointer;
        }

        .search-bar input {
            border: none;
            background: transparent;
            color: #ccc;
            width: 100%;
            outline: none;
            font-size: 20px;
        }

        .search-bar input::placeholder {
            opacity: 0.3;
        }

        /* Dropdown styles */
        #searchDropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #1a1b27;
            border: 1px solid #FF4B2B;
            border-radius: 8px;
            max-height: 500px;
            overflow-y: auto;
            z-index: 999;
        }

        #searchDropdown::-webkit-scrollbar {
            width: 6px;
        }

        #searchDropdown::-webkit-scrollbar-track {
            background: #1a1b27;
        }

        #searchDropdown::-webkit-scrollbar-thumb {
            background: #FF4B2B;
            border-radius: 3px;
        }

        .dropdown-item-row {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            gap: 12px;
            border-bottom: 1px solid #2a2b38;
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
        }

        .dropdown-item-row:last-child {
            border-bottom: none;
        }

        .dropdown-item-row:hover {
            background: #242631;
        }

        .dropdown-item-row img {
            width: 44px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .dropdown-item-row .d-info {
            flex: 1;
            min-width: 0;
        }

        .dropdown-item-row .d-info h4 {
            font-size: 14px;
            color: #fff;
            margin: 0 0 3px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-item-row .d-info p {
            font-size: 12px;
            color: #888;
            margin: 0;
        }

        .dropdown-item-row .type-badge {
            font-size: 10px;
            background: #FF4B2B;
            color: #fff;
            padding: 2px 10px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-badge.video { background: #007bff; }
        .type-badge.show { background: #28a745; }
        .type-badge.series { background: #ffc107; color: #000; }
        .type-badge.episode { background: #17a2b8; }
        .type-badge.category { background: #6f42c1; }
        .type-badge.season { background: #fd7e14; }

        /* Trending Section */
        .trending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .trending-item {
            background: #1a1b27;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .trending-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }

        .trending-item .image-wrapper {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
            background: #2a2b38;
        }

        .trending-item .image-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trending-item .image-wrapper .type-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #FF4B2B;
            color: #fff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .trending-item .info {
            padding: 12px;
        }

        .trending-item .info h5 {
            color: #fff;
            font-size: 14px;
            margin: 0 0 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .trending-item .info .subtitle {
            color: #888;
            font-size: 12px;
        }

        .trending-item .info .views {
            color: #666;
            font-size: 11px;
            margin-top: 4px;
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 540px) {
            .search-bar input {
                font-size: 15px;
            }
            .trending-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
            .dropdown-item-row img {
                width: 36px;
                height: 48px;
            }
        }

        .no-results {
            color: #888;
            text-align: center;
            padding: 40px 20px;
        }

        .no-results i {
            font-size: 40px;
            display: block;
            margin-bottom: 15px;
            color: #333;
        }

        .loading-spinner {
            text-align: center;
            padding: 30px;
            color: #888;
        }

        .loading-spinner i {
            font-size: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .dropdown-section-title {
            padding: 8px 14px;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #2a2b38;
            background: #14151f;
        }

        .highlight {
            color: #FF4B2B;
            font-weight: bold;
        }
    </style>

    <section id="section-home-newdes" class="podcast mt-5 pt-5 mb-5">
        <div class="home-newdes-section">
            <div class="home-newdes-section-container">
                <div class="container-fluid mt-2">

                    {{-- Search Bar --}}
                    <div class="search-bar mx-auto" style="position:relative;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search Podcasts, Series, Movies, Music, Short films" autocomplete="off">
                        <i class="fa fa-times" id="clearBtn" aria-hidden="true" style="display:none;"></i>
                        <div id="searchDropdown"></div>
                    </div>

                    {{-- Trending Section --}}
                    <div id="trendingSection">
                        <h4 class="text-white mt-4 mb-3">🔥 Trending Now</h4>
                        <div id="trendingContent" class="trending-grid">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner"></i>
                                <p style="margin-top:10px;">Loading trending content...</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            let searchTimeout;
            const searchInput = $('#searchInput');
            const searchDropdown = $('#searchDropdown');
            const clearBtn = $('#clearBtn');
            const trendingContent = $('#trendingContent');

            // Load trending content on page load
            loadTrendingContent();

            // Search functionality
            searchInput.on('input', function() {
                const query = $(this).val().trim();
                
                if (query.length >= 2) {
                    clearBtn.show();
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        performSearch(query);
                    }, 300);
                } else {
                    clearBtn.hide();
                    searchDropdown.hide();
                    if (query.length === 0) {
                        loadTrendingContent();
                    }
                }
            });

            // Clear button
            clearBtn.on('click', function() {
                searchInput.val('');
                clearBtn.hide();
                searchDropdown.hide();
                loadTrendingContent();
                searchInput.focus();
            });

            // Close dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-bar').length) {
                    searchDropdown.hide();
                }
            });

            // Perform search
            function performSearch(query) {
                $.ajax({
                    url: '{{ route("search.ajax") }}',
                    method: 'GET',
                    data: { query: query },
                    beforeSend: function() {
                        searchDropdown.html(`
                            <div class="loading-spinner">
                                <i class="fas fa-spinner"></i>
                                <p style="margin-top:10px;">Searching...</p>
                            </div>
                        `);
                        searchDropdown.show();
                    },
                    success: function(data) {
                        if (data.length > 0) {
                            let html = '';
                            
                            // Group results by type
                            const grouped = {};
                            data.forEach(item => {
                                if (!grouped[item.type]) {
                                    grouped[item.type] = [];
                                }
                                grouped[item.type].push(item);
                            });

                            // Sort types for display
                            const typeOrder = ['Upcoming', 'Music', 'Movies', 'Trailer', 'Short Films', 'Series', 'Podcast', 'Comedy Show', 'Episode', 'Category', 'Season'];
                            const sortedTypes = Object.keys(grouped).sort((a, b) => {
                                return typeOrder.indexOf(a) - typeOrder.indexOf(b);
                            });

                            sortedTypes.forEach(type => {
                                html += `<div class="dropdown-section-title">${type}s</div>`;
                                grouped[type].forEach(item => {
                                    const imageHtml = item.image ? 
                                        `<img src="${item.image}" alt="${item.title}" loading="lazy">` : 
                                        `<div style="width:44px;height:60px;background:#2a2b38;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#666;font-size:20px;">
                                            <i class="fas fa-${getIconForType(item.type)}"></i>
                                        </div>`;
                                    
                                    html += `
                                        <a href="${item.url}" class="dropdown-item-row">
                                            ${imageHtml}
                                            <div class="d-info">
                                                <h4>${highlightText(item.title, query)}</h4>
                                                <p>${item.subtitle || ''}</p>
                                                <span class="type-badge ${item.type.toLowerCase()}">${item.type}</span>
                                                ${item.views ? `<span style="color:#666;font-size:11px;margin-left:8px;">👁️ ${item.views}</span>` : ''}
                                            </div>
                                        </a>
                                    `;
                                });
                            });

                            searchDropdown.html(html);
                            searchDropdown.show();
                        } else {
                            searchDropdown.html(`
                                <div class="no-results">
                                    <i class="fas fa-search"></i>
                                    No results found for "<strong>${query}</strong>"
                                    <p style="margin-top:10px;font-size:13px;color:#666;">Try different keywords or check your spelling</p>
                                </div>
                            `);
                            searchDropdown.show();
                        }
                    },
                    error: function() {
                        searchDropdown.html(`
                            <div class="no-results">
                                <i class="fas fa-exclamation-circle"></i>
                                Error searching. Please try again.
                            </div>
                        `);
                        searchDropdown.show();
                    }
                });
            }

            // Helper function to highlight search term
            function highlightText(text, query) {
                if (!text) return '';
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }

            // Get icon for content type
            function getIconForType(type) {
                const icons = {
                    'upcoming': 'clock',
                    'music': 'music',
                    'movies': 'film',
                    'trailer': 'video',
                    'short films': 'film',
                    'series': 'layer-group',
                    'podcast': 'microphone',
                    'comedy show': 'laugh',
                    'episode': 'play-circle',
                    'category': 'folder',
                    'season': 'calendar-alt'
                };
                return icons[type.toLowerCase()] || 'file';
            }

            // Load trending content
            function loadTrendingContent() {
                trendingContent.html(`
                    <div class="loading-spinner" style="grid-column:1/-1;">
                        <i class="fas fa-spinner"></i>
                        <p style="margin-top:10px;">Loading trending content...</p>
                    </div>
                `);
                
                $.ajax({
                    url: '{{ route("search.trending") }}',
                    method: 'GET',
                    success: function(data) {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(item => {
                                const imageHtml = item.image ? 
                                    `<img src="${item.image}" alt="${item.title}" loading="lazy">` : 
                                    `<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#666;font-size:30px;">
                                        <i class="fas fa-${getIconForType(item.type)}"></i>
                                    </div>`;
                                
                                html += `
                                    <div class="trending-item" onclick="window.location.href='${item.url}'">
                                        <div class="image-wrapper">
                                            ${imageHtml}
                                            <span class="type-badge">${item.type}</span>
                                        </div>
                                        <div class="info">
                                            <h5 title="${item.title}">${item.title}</h5>
                                            <div class="subtitle">${item.subtitle || ''}</div>
                                            ${item.views ? `<div class="views">👁️ ${item.views} views</div>` : ''}
                                        </div>
                                    </div>
                                `;
                            });
                            trendingContent.html(html);
                        } else {
                            trendingContent.html(`
                                <div class="no-results" style="grid-column:1/-1;">
                                    <i class="fas fa-fire"></i>
                                    No trending content available
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        trendingContent.html(`
                            <div class="no-results" style="grid-column:1/-1;">
                                <i class="fas fa-exclamation-circle"></i>
                                Could not load trending content
                            </div>
                        `);
                    }
                });
            }

            // Keyboard navigation for dropdown
            searchInput.on('keydown', function(e) {
                const items = searchDropdown.find('.dropdown-item-row');
                
                if (e.key === 'ArrowDown' && items.length) {
                    e.preventDefault();
                    const current = items.filter('.active');
                    if (current.length) {
                        current.removeClass('active');
                        const next = current.next('.dropdown-item-row');
                        if (next.length) {
                            next.addClass('active');
                            next[0].scrollIntoView({ block: 'nearest' });
                        }
                    } else {
                        items.first().addClass('active');
                    }
                } else if (e.key === 'ArrowUp' && items.length) {
                    e.preventDefault();
                    const current = items.filter('.active');
                    if (current.length) {
                        current.removeClass('active');
                        const prev = current.prev('.dropdown-item-row');
                        if (prev.length) {
                            prev.addClass('active');
                            prev[0].scrollIntoView({ block: 'nearest' });
                        }
                    } else {
                        items.last().addClass('active');
                    }
                } else if (e.key === 'Enter') {
                    const active = items.filter('.active');
                    if (active.length) {
                        window.location.href = active.attr('href');
                    } else if (items.length === 1) {
                        window.location.href = items.attr('href');
                    }
                } else if (e.key === 'Escape') {
                    searchDropdown.hide();
                    searchInput.blur();
                }
            });
        });
    </script>

    <script src="{{ URL::to('asset/js/gridView.js') }}"></script>
@endsection
