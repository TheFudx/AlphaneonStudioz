@php
    $id = $id ?? null;
    $type = $type ?? 'video';
@endphp

@if (in_array(['id' => $id, 'type' => $type], $watchlist))
    <form action="{{ route('watchlist.remove') }}" method="POST">
        @csrf
        <input type="hidden" name="video_id" value="{{ $id }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <button type="submit" class="btn-watchlist ms-2" 
            {{-- data-toggle="tooltip" title="Remove from Watch List" --}}
        >
            <svg width="24" height="13" viewBox="0 0 24 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="2.00845" height="9.87727" rx="1.00423"
                    transform="matrix(0.851668 -0.524082 0.556629 0.830761 0 4.08691)" fill="white" />
                <rect width="2.19463" height="21.0917" rx="1.09731"
                    transform="matrix(0.553502 0.832848 -0.853569 0.52098 22.7852 0)" fill="white" />
            </svg>
            <div class="toast-message-box-rm">Remove from Watch List</div>
        </button>
    </form>
@else
    <form action="{{ route('watchlist') }}" method="POST">
        @csrf
        <input type="hidden" name="video_id" value="{{ $id }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <button type="submit" class="btn-watchlist ms-2" 
        {{-- data-toggle="tooltip" title="Add to Watch List" --}}
        >
            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8.45654" y="0.34668" width="1.61563" height="18.8704" rx="0.807817" fill="white" />
                <rect x="17.7461" y="8.88379" width="1.79718" height="16.9642" rx="0.89859"
                    transform="rotate(90 17.7461 8.88379)" fill="white" />
            </svg>
            <div class="toast-message-box">Add to Watch List</div>
        </button>
    </form>
@endif
