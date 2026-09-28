@php
    $id = $id ?? null;
    $type = $type ?? 'video';
    $type_id = $type_id ?? 0;
    $category_id = $category_id ?? 0;
    $user = app('logged-in-user');

    $isInWatchlist = false;

    if ($user && $id) {
        $query = \App\Models\UserWatchLaterList::where('user_id', $user->id);
        if ($type == 'series') {
            $query->where('series_id', $id);
        } elseif ($type == 'episode') {
            $query->where('episode_id', $id);
        } else {
            $query->where('video_id', $id);
        }
        // ONLY FOR NORMAL VIDEOS
        if ($type == 'video') {
            $query->where('type_id', $type_id);

            if (!empty($category_id)) {
                $query->where('category_id', $category_id);
            }
        }
        $isInWatchlist = $query->exists();
    }
@endphp

{{-- Rest of your button code remains the same --}}

@if ($isInWatchlist)
    {{-- Already in watchlist → show remove button --}}
    <form action="{{ route('watchlist.toggle') }}" class="mb-0" method="POST">
        @csrf
        @if ($type == 'series')
            <input type="hidden" name="series_id" value="{{ $id }}">
             <input type="hidden" name="video_id" value="{{ $id }}">
        @elseif($type == 'episode')
            <input type="hidden" name="series_id" value="{{ $id }}">
            <input type="hidden" name="episode_id" value="{{ $id }}">
            <input type="hidden" name="video_id" value="{{ $id }}">
        @else
            <input type="hidden" name="video_id" value="{{ $id }}">
        @endif
        <input type="hidden" name="type_id" value="{{ $type_id }}">
        <input type="hidden" name="category_id" value="{{ $category_id }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <button type="submit" class="btn-watchlist ms-2">
            <svg width="24" height="13" viewBox="0 0 24 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="2.00845" height="9.87727" rx="1.00423"
                    transform="matrix(0.851668 -0.524082 0.556629 0.830761 0 4.08691)" fill="white" />
                <rect width="2.19463" height="21.0917" rx="1.09731"
                    transform="matrix(0.553502 0.832848 -0.853569 0.52098 22.7852 0)" fill="white" />
            </svg>
            <div class="toast-message-box-rm">Remove from Watch List</div>
        </button>
    </form>
@elseif (!$user)
    {{-- Not logged in → redirect to login --}}
    <a href="{{ route('login') }}" class="btn-watchlist ms-2">
        <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704" rx="0.807817" fill="white" />
            <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642" rx="0.89859"
                transform="rotate(90 17.7461 8.88379)" fill="white" />
        </svg>
        <div class="toast-message-box">Add to Watch List</div>
    </a>
@else
    {{-- Logged in → normal add form --}}
    <form action="{{ route('watchlist.toggle') }}" method="POST" class="mb-0">
        @csrf
        @if ($type == 'series')
            <input type="hidden" name="series_id" value="{{ $id }}">
            <input type="hidden" name="video_id" value="{{ $id }}">
        @elseif($type == 'episode')
            <input type="hidden" name="series_id" value="{{ $id }}">
            <input type="hidden" name="episode_id" value="{{ $id }}">
            <input type="hidden" name="video_id" value="{{ $id }}">
        @else
            <input type="hidden" name="video_id" value="{{ $id }}">
        @endif
        <input type="hidden" name="type_id" value="{{ $type_id }}">
        <input type="hidden" name="category_id" value="{{ $category_id }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <button type="submit" class="btn-watchlist ms-2">
            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704" rx="0.807817" fill="white" />
                <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642" rx="0.89859"
                    transform="rotate(90 17.7461 8.88379)" fill="white" />
            </svg>
            <div class="toast-message-box">Add to Watch List</div>
        </button>
    </form>
@endif
