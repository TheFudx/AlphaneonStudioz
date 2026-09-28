@extends('layouts.main')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('title')
    {{ config('app.project_name') }} | Khulps Play
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ URL::to('/asset/css/khlups.css') }}">
@endsection

@section('scripts')
<script>
    window.KHLUPS_CONFIG = {
        routes: {
            like: "{{ route('khlups.like') }}",
            comment: "{{ route('khlups.comment') }}",
            commentsGet: "{{ url('/khlups') }}",
            commentDelete: "{{ url('/khlups/comment') }}",
            commentUpdate: "{{ url('/khlups/comment') }}",
            commentReact: "{{ url('/khlups/comment') }}",
            share: "{{ route('khlups.share') }}",
            login: "{{ route('login') }}"
        },
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}",
        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        currentUserId: {{ auth()->id() ?? 'null' }}
    };

    document.addEventListener('DOMContentLoaded', () => {
        const shortsContainer = document.querySelector('.shorts-container');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const body = document.body;

        let currentPlayingVideo = null;
        let globalMutedState = true;
        let isLoading = false;

        /* ---------------- Toast Notification Helper ---------------- */
        const toastEl = document.getElementById('khlupToast');
        let toastTimeout = null;
        function showToast(message, duration = 2500) {
            if (!toastEl) return;
            toastEl.textContent = message;
            toastEl.classList.add('active');
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                toastEl.classList.remove('active');
            }, duration);
        }

        /* ---------------- Custom Confirmation Helper ---------------- */
        const confirmOverlay = document.getElementById('khlupConfirmOverlay');
        const confirmCancelBtn = document.getElementById('btnConfirmCancel');
        const confirmDeleteBtn = document.getElementById('btnConfirmDelete');
        let pendingConfirmCallback = null;

        function showConfirmDialog(title, desc, onConfirm) {
            if (!confirmOverlay) {
                if (confirm(desc)) onConfirm();
                return;
            }
            confirmOverlay.querySelector('.custom-confirm-title').textContent = title || 'Delete Comment?';
            confirmOverlay.querySelector('.custom-confirm-desc').textContent = desc || 'Are you sure you want to delete this comment?';
            pendingConfirmCallback = onConfirm;
            confirmOverlay.classList.add('active');
        }

        function closeConfirmDialog() {
            if (confirmOverlay) confirmOverlay.classList.remove('active');
            pendingConfirmCallback = null;
        }

        if (confirmCancelBtn) {
            confirmCancelBtn.addEventListener('click', closeConfirmDialog);
        }
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => {
                if (pendingConfirmCallback) pendingConfirmCallback();
                closeConfirmDialog();
            });
        }
        if (confirmOverlay) {
            confirmOverlay.addEventListener('click', (e) => {
                if (e.target === confirmOverlay) closeConfirmDialog();
            });
        }

        /* ---------------- Auth Verification Helper ---------------- */
        function requireAuth(actionName = 'perform this action') {
            if (!window.KHLUPS_CONFIG.isLoggedIn) {
                showToast(`Please log in to ${actionName}. Redirecting...`);
                setTimeout(() => {
                    window.location.href = window.KHLUPS_CONFIG.routes.login;
                }, 900);
                return false;
            }
            return true;
        }

        /* ---------------- Lazy Video Loader & Video Controls ---------------- */
        function lazyVideoLoader(video) {
            if (!video || video.dataset.loaded) return;
            const sources = video.querySelectorAll("source[data-src]");
            if (sources.length === 0) return;

            sources.forEach(src => {
                if (src.dataset.src) src.src = src.dataset.src;
            });

            video.load();
            video.dataset.loaded = "true";
        }

        const updatePlayPauseIcon = (videoElement, playBtn, pauseBtn) => {
            if (!playBtn || !pauseBtn) return;
            if (videoElement.paused) {
                playBtn.style.display = 'block';
                pauseBtn.style.display = 'none';
            } else {
                playBtn.style.display = 'none';
                pauseBtn.style.display = 'block';
            }
        };

        const updateMuteIcon = (muteBtn, isMuted) => {
            if (!muteBtn) return;
            const volumeOnIcon = muteBtn.querySelector('.volume-on');
            const volumeOffIcon = muteBtn.querySelector('.volume-off');

            if (isMuted) {
                if (volumeOnIcon) volumeOnIcon.style.display = 'none';
                if (volumeOffIcon) volumeOffIcon.style.display = 'block';
                muteBtn.dataset.muted = 'true';
            } else {
                if (volumeOnIcon) volumeOnIcon.style.display = 'block';
                if (volumeOffIcon) volumeOffIcon.style.display = 'none';
                muteBtn.dataset.muted = 'false';
            }
        };

        const pauseVideo = (videoElement) => {
            if (videoElement) {
                videoElement.pause();
                const card = videoElement.closest('.video-card');
                if (card) {
                    const playIcon = card.querySelector('.play-icon');
                    const pauseIcon = card.querySelector('.pause-icon');
                    updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
                }
            }
        };

        const playVideo = (videoElement) => {
            if (currentPlayingVideo && currentPlayingVideo !== videoElement) {
                pauseVideo(currentPlayingVideo);
            }

            const card = videoElement.closest('.video-card');
            if (!card) return;

            const playIcon = card.querySelector('.play-icon');
            const pauseIcon = card.querySelector('.pause-icon');

            videoElement.muted = globalMutedState;

            videoElement.play().then(() => {
                videoElement.classList.add('loaded');
                updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
                currentPlayingVideo = videoElement;
            }).catch(e => {
                videoElement.classList.remove('loaded');
                updatePlayPauseIcon(videoElement, playIcon, pauseIcon);
            });
        };

        /* ---------------- Intersection Observer Setup ---------------- */
        const observerOptions = {
            root: shortsContainer,
            rootMargin: '0px',
            threshold: 0.15
        };

        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const card = entry.target;
                const video = card.querySelector('video');
                if (!video) return;

                if (entry.isIntersecting) {
                    lazyVideoLoader(video);
                    playVideo(video);
                } else {
                    pauseVideo(video);
                }
            });
        }, observerOptions);

        function attachVideoEvents(targetCards = document.querySelectorAll('.video-card')) {
            targetCards.forEach((card) => {
                const video = card.querySelector('video');
                if (!video) return;

                const muteButton = card.querySelector('.mute-button');
                videoObserver.observe(card);
                updateMuteIcon(muteButton, globalMutedState);

                video.addEventListener('playing', () => {
                    video.classList.add('loaded');
                    const playIcon = card.querySelector('.play-icon');
                    const pauseIcon = card.querySelector('.pause-icon');
                    updatePlayPauseIcon(video, playIcon, pauseIcon);
                });

                video.addEventListener('pause', () => {
                    const playIcon = card.querySelector('.play-icon');
                    const pauseIcon = card.querySelector('.pause-icon');
                    updatePlayPauseIcon(video, playIcon, pauseIcon);
                });

                video.addEventListener('ended', () => {
                    video.currentTime = 0;
                    video.play().catch(() => {});
                });
            });
        }

        attachVideoEvents();

        // Autoplay initial visible video
        const firstCard = document.querySelector('.video-card');
        if (firstCard) {
            const firstVideo = firstCard.querySelector('video');
            if (firstVideo) {
                lazyVideoLoader(firstVideo);
                playVideo(firstVideo);
            }
        }

        /* ---------------- Comment HTML Generator Helper ---------------- */
        function generateCommentHtml(item, isReply = false) {
            const reactionsCount = item.reactions_count || 0;
            const userReaction = item.user_reaction || null;
            const hasReaction = Boolean(userReaction);
            const displayEmoji = userReaction || '❤️';

            const uname = (item.uname && String(item.uname).trim() !== '') ? String(item.uname).trim() : 'Guest';
            let initial = (item.initial && String(item.initial).trim() !== '') 
                ? String(item.initial).trim().toUpperCase() 
                : (uname.charAt(0).toUpperCase() || 'G');
            if (!initial) {
                initial = 'G';
            }

            const isRedAvatar = (initial === 'G' || initial === 'A');
            const avatarBg = isRedAvatar ? '#f44336' : '#555';

            let avatarHtml = '';
            if (item.profile_picture) {
                avatarHtml = `<img src="${item.profile_picture}" class="comment-user-img" alt="${initial}" onerror="this.style.display='none'; if(this.nextElementSibling){ this.nextElementSibling.style.display='inline-flex'; }"><span class="comment-user-img" style="display:none;align-items:center;justify-content:center;background:${avatarBg};color:#ffffff;font-weight:700;font-size:${isReply ? '10px' : '12px'};text-transform:uppercase;">${initial}</span>`;
            } else {
                avatarHtml = `<span class="comment-user-img" style="display:inline-flex;align-items:center;justify-content:center;background:${avatarBg};color:#ffffff;font-weight:700;font-size:${isReply ? '10px' : '12px'};text-transform:uppercase;">${initial}</span>`;
            }

            // More actions (Owner edit and delete)
            let moreMenuHtml = '';
            if (item.can_delete) {
                moreMenuHtml = `
                    <div class="comment-menu-wrapper">
                        <button type="button" class="comment-more-btn" title="More options">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <div class="comment-dropdown-menu">
                            <button type="button" class="dropdown-edit-btn" data-comment-id="${item.id}">
                                <i class="fa-regular fa-pen-to-square"></i> Edit
                            </button>
                            <button type="button" class="dropdown-delete-btn" data-comment-id="${item.id}">
                                <i class="fa-regular fa-trash-can"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            }

            // Threaded replies
            let repliesHtml = '';
            if (!isReply) {
                const replies = item.replies || [];
                const repliesCount = item.replies_count || replies.length;
                let repliesItemsHtml = '';
                replies.forEach(reply => {
                    repliesItemsHtml += generateCommentHtml(reply, true);
                });

                repliesHtml = `
                    <div class="replies-container" data-parent-id="${item.id}">
                        ${repliesCount > 0 ? `
                            <button type="button" class="toggle-replies-btn" data-expanded="false">
                                View ${repliesCount} ${repliesCount === 1 ? 'reply' : 'replies'}
                            </button>
                        ` : ''}
                        <div class="replies-list" style="display:none;">
                            ${repliesItemsHtml}
                        </div>
                    </div>
                `;
            }

            return `
                <div class="insta-comment-item ${isReply ? 'reply' : ''}" data-id="${item.id}" data-parent-id="${item.parent_id || ''}">
                    ${avatarHtml}
                    <div class="comment-content">
                        <div class="comment-header-row">
                            <span class="username">${uname}</span>
                            <span class="comment-time">${item.time_ago || 'just now'}</span>
                        </div>
                        <div class="comment-text">${item.comment}</div>
                        <div class="comment-actions">
                            <button type="button" class="comment-action-btn reply-action-btn" data-comment-id="${item.parent_id || item.id}" data-username="${uname}">
                                Reply
                            </button>

                            <div class="comment-react-wrapper">
                                <button type="button" class="comment-react-pill ${hasReaction ? 'has-reaction' : ''}" data-comment-id="${item.id}" data-user-reaction="${userReaction || ''}">
                                    <span class="reaction-emoji-display">${displayEmoji}</span>
                                    <span class="reaction-count-display">${reactionsCount > 0 ? reactionsCount : ''}</span>
                                </button>
                                <div class="emoji-selector-popover" data-comment-id="${item.id}">
                                    <button type="button" class="emoji-btn" data-emoji="❤️">❤️</button>
                                    <button type="button" class="emoji-btn" data-emoji="🔥">🔥</button>
                                    <button type="button" class="emoji-btn" data-emoji="😂">😂</button>
                                    <button type="button" class="emoji-btn" data-emoji="👏">👏</button>
                                    <button type="button" class="emoji-btn" data-emoji="😮">😮</button>
                                    <button type="button" class="emoji-btn" data-emoji="😢">😢</button>
                                </div>
                            </div>

                            ${moreMenuHtml}
                        </div>
                        ${repliesHtml}
                    </div>
                </div>
            `;
        }

        /* ---------------- Load Comments via API ---------------- */
        async function fetchAndRenderComments(card) {
            const modal = card.querySelector('.comment-modal');
            const commentBody = modal.querySelector('.comment-body');
            const videoId = card.dataset.videoId;

            commentBody.innerHTML = `
                <div class="comments-loading-state">
                    <div class="comments-spinner"></div>
                    <span>Loading comments...</span>
                </div>
            `;

            try {
                const res = await fetch(`${window.KHLUPS_CONFIG.routes.commentsGet}/${videoId}/comments`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    // Update counts
                    const countBadge = modal.querySelector('.comments-count-badge');
                    if (countBadge) countBadge.textContent = `(${data.total_count})`;
                    const actionCount = card.querySelector('.comment-count');
                    if (actionCount) actionCount.textContent = data.total_count;

                    if (!data.comments || data.comments.length === 0) {
                        commentBody.innerHTML = `
                            <div class="comments-empty-state">
                                <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
                                <h5>No comments yet</h5>
                                <p>Be the first to share your thoughts!</p>
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.comments.forEach(item => {
                            html += generateCommentHtml(item, false);
                        });
                        commentBody.innerHTML = html;
                    }
                    modal.dataset.loaded = "true";
                } else {
                    commentBody.innerHTML = `<div class="comments-empty-state"><p>Could not load comments. Please try again.</p></div>`;
                }
            } catch (err) {
                console.error("Error fetching comments:", err);
                commentBody.innerHTML = `<div class="comments-empty-state"><p>Network error loading comments.</p></div>`;
            }
        }

        /* ---------------- Global Document Event Delegation ---------------- */
        document.addEventListener('click', async (e) => {
            // Close any open emoji popovers if clicking outside
            if (!e.target.closest('.comment-react-wrapper')) {
                document.querySelectorAll('.emoji-selector-popover.active').forEach(p => p.classList.remove('active'));
            }

            // Close any open comment dropdown menus if clicking outside
            if (!e.target.closest('.comment-menu-wrapper')) {
                document.querySelectorAll('.comment-dropdown-menu.active').forEach(m => m.classList.remove('active'));
            }

            // 1. Play / Pause Button
            const playPauseBtn = e.target.closest('.play-pause-button');
            if (playPauseBtn) {
                e.stopPropagation();
                const card = playPauseBtn.closest('.video-card');
                const video = card.querySelector('video');
                if (video) {
                    if (video.paused) playVideo(video);
                    else pauseVideo(video);
                }
                return;
            }

            // 2. Tap Video Container to Play/Pause
            const playerContainer = e.target.closest('.video-player');
            if (playerContainer && !e.target.closest('.video-controls') && !e.target.closest('.shorts-actions') && !e.target.closest('.video-info') && !e.target.closest('.comment-modal') && !e.target.closest('.share-modal')) {
                const card = playerContainer.closest('.video-card');
                const video = card.querySelector('video');
                if (video) {
                    if (video.paused) playVideo(video);
                    else pauseVideo(video);
                }
                return;
            }

            // 3. Mute / Unmute Button
            const muteBtn = e.target.closest('.mute-button');
            if (muteBtn) {
                e.stopPropagation();
                globalMutedState = !globalMutedState;
                document.querySelectorAll('.video-card').forEach(c => {
                    const vid = c.querySelector('video');
                    const b = c.querySelector('.mute-button');
                    if (vid) vid.muted = globalMutedState;
                    if (b) updateMuteIcon(b, globalMutedState);
                });
                if (!globalMutedState && currentPlayingVideo && currentPlayingVideo.paused) {
                    playVideo(currentPlayingVideo);
                }
                return;
            }

            // 4. Fullscreen Button
            const fullscreenBtn = e.target.closest('.fullscreen-button');
            if (fullscreenBtn) {
                e.stopPropagation();
                const isFullscreen = shortsContainer.classList.toggle('fullscreen-swiper-mode');
                body.classList.toggle('fullscreen-active');
                const fullscreenIcon = fullscreenBtn.querySelector('.fullscreen-icon');
                const exitFullscreenIcon = fullscreenBtn.querySelector('.exit-fullscreen-icon');
                if (fullscreenIcon) fullscreenIcon.style.display = isFullscreen ? 'none' : 'block';
                if (exitFullscreenIcon) exitFullscreenIcon.style.display = isFullscreen ? 'block' : 'none';
                return;
            }

            // 5. Like / Unlike Button
            const likeBtn = e.target.closest('.like-btn');
            if (likeBtn) {
                e.stopPropagation();
                if (!requireAuth('like this video')) return;

                const videoId = likeBtn.dataset.id;
                const wasLiked = likeBtn.classList.contains('activated');
                const countEl = likeBtn.querySelector('.like-count');
                let count = parseInt(countEl.textContent) || 0;

                // Optimistic UI update
                likeBtn.classList.toggle('activated');
                countEl.textContent = wasLiked ? Math.max(0, count - 1) : count + 1;

                try {
                    const res = await fetch(window.KHLUPS_CONFIG.routes.like, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: videoId, action: wasLiked ? 'unlike' : 'like' })
                    });
                    const data = await res.json();

                    if (data.status === 'unauthenticated') {
                        likeBtn.classList.toggle('activated');
                        countEl.textContent = count;
                        requireAuth('like this video');
                    } else if (data.status === 'success') {
                        countEl.textContent = data.likes_count;
                        if (data.liked) likeBtn.classList.add('activated');
                        else likeBtn.classList.remove('activated');
                    } else {
                        likeBtn.classList.toggle('activated');
                        countEl.textContent = count;
                        showToast(data.message || 'Error updating like.');
                    }
                } catch (err) {
                    console.error("Like error:", err);
                    likeBtn.classList.toggle('activated');
                    countEl.textContent = count;
                    showToast("Network error. Please try again.");
                }
                return;
            }

            // 6. Inline Description Expand / Collapse Toggle
            const descToggle = e.target.closest('.desc-toggle-btn');
            if (descToggle) {
                e.stopPropagation();
                const container = descToggle.closest('.video-desc-container');
                const textSpan = container.querySelector('.video-desc-text');
                const fullText = container.dataset.full;
                const shortText = container.dataset.short;

                if (descToggle.textContent.includes('more')) {
                    textSpan.textContent = fullText;
                    descToggle.textContent = 'less';
                    container.classList.add('expanded');
                } else {
                    textSpan.textContent = shortText;
                    descToggle.textContent = '...more';
                    container.classList.remove('expanded');
                }
                return;
            }

            // 7. Open Comments Drawer
            const commentBtn = e.target.closest('.comment-btn');
            if (commentBtn) {
                e.stopPropagation();
                const card = commentBtn.closest('.video-card');
                const modal = card.querySelector('.comment-modal');
                const overlay = card.querySelector('.comment-overlay');

                overlay.classList.add('active');
                modal.classList.add('active');
                card.classList.add('show-comments');

                // Load comments if not yet loaded
                if (modal.dataset.loaded !== "true") {
                    fetchAndRenderComments(card);
                }
                return;
            }

            // 8. Close Comments Drawer
            if (e.target.classList.contains('comment-overlay') || e.target.closest('.close-comment')) {
                const card = e.target.closest('.video-card');
                const modal = card.querySelector('.comment-modal');
                const overlay = card.querySelector('.comment-overlay');

                modal.classList.remove('active');
                overlay.classList.remove('active');
                               // Reset replying context
                const replyingBanner = card.querySelector('.replying-banner');
                if (replyingBanner) replyingBanner.style.display = 'none';
                const parentIdInput = card.querySelector('.comment-composer input[name="parent_id"]');
                if (parentIdInput) parentIdInput.value = '';

                // Reset editing context
                const editingBanner = card.querySelector('.editing-banner');
                if (editingBanner) editingBanner.style.display = 'none';
                const editIdInput = card.querySelector('.comment-composer input[name="edit_comment_id"]');
                if (editIdInput) editIdInput.value = '';
                const sendBtn = card.querySelector('.comment-composer .send-comment-btn');
                if (sendBtn) {
                    sendBtn.textContent = 'Post';
                    sendBtn.classList.remove('active');
                }
                const inputField = card.querySelector('.comment-composer .comment-input-field');
                if (inputField) {
                    inputField.value = '';
                    inputField.placeholder = 'Add a comment...';
                }
                return;
            }

            // 9. Reply Action Button
            const replyActionBtn = e.target.closest('.reply-action-btn');
            if (replyActionBtn) {
                e.stopPropagation();
                if (!requireAuth('reply to comments')) return;

                const card = replyActionBtn.closest('.video-card');
                const commentId = replyActionBtn.dataset.commentId;
                const username = replyActionBtn.dataset.username;
                const composer = card.querySelector('.comment-composer');
                const parentInput = composer.querySelector('input[name="parent_id"]');
                const editInput = composer.querySelector('input[name="edit_comment_id"]');
                const replyingBanner = composer.querySelector('.replying-banner');
                const editingBanner = composer.querySelector('.editing-banner');
                const targetUserEl = composer.querySelector('.reply-target-user');
                const inputField = composer.querySelector('.comment-input-field');
                const sendBtn = composer.querySelector('.send-comment-btn');

                // Cancel editing mode if active
                if (editInput) editInput.value = '';
                if (editingBanner) editingBanner.style.display = 'none';
                if (sendBtn) sendBtn.textContent = 'Post';

                const safeName = (username && username.trim() !== '') ? username.trim() : 'Guest';
                parentInput.value = commentId;
                targetUserEl.textContent = `@${safeName}`;
                replyingBanner.style.display = 'flex';
                inputField.placeholder = `Reply to @${safeName}...`;
                inputField.focus();
                return;
            }

            // 10. Cancel Reply Button
            const cancelReplyBtn = e.target.closest('.cancel-reply-btn');
            if (cancelReplyBtn) {
                e.stopPropagation();
                const composer = cancelReplyBtn.closest('.comment-composer');
                const parentInput = composer.querySelector('input[name="parent_id"]');
                const replyingBanner = composer.querySelector('.replying-banner');
                const inputField = composer.querySelector('.comment-input-field');

                parentInput.value = '';
                replyingBanner.style.display = 'none';
                inputField.placeholder = 'Add a comment...';
                return;
            }

            // 11. Toggle Replies Thread Button (View / Hide Replies)
            const toggleRepliesBtn = e.target.closest('.toggle-replies-btn');
            if (toggleRepliesBtn) {
                e.stopPropagation();
                const container = toggleRepliesBtn.closest('.replies-container');
                const repliesList = container.querySelector('.replies-list');
                const isExpanded = toggleRepliesBtn.dataset.expanded === 'true';
                const count = container.querySelectorAll('.insta-comment-item.reply').length;

                if (isExpanded) {
                    repliesList.style.display = 'none';
                    toggleRepliesBtn.textContent = `View ${count} ${count === 1 ? 'reply' : 'replies'}`;
                    toggleRepliesBtn.dataset.expanded = 'false';
                } else {
                    repliesList.style.display = 'block';
                    toggleRepliesBtn.textContent = `Hide replies`;
                    toggleRepliesBtn.dataset.expanded = 'true';
                }
                return;
            }

            // 12. Toggle Emoji Reaction Selector Popover
            const reactPill = e.target.closest('.comment-react-pill');
            if (reactPill) {
                e.stopPropagation();
                const wrapper = reactPill.closest('.comment-react-wrapper');
                const popover = wrapper.querySelector('.emoji-selector-popover');
                const isActive = popover.classList.contains('active');

                // Close any other open popovers
                document.querySelectorAll('.emoji-selector-popover.active').forEach(p => p.classList.remove('active'));

                if (!isActive) {
                    popover.classList.add('active');
                }
                return;
            }

            // 13. Select Emoji Reaction
            const emojiBtn = e.target.closest('.emoji-btn');
            if (emojiBtn) {
                e.stopPropagation();
                if (!requireAuth('react to comments')) return;

                const popover = emojiBtn.closest('.emoji-selector-popover');
                const commentId = popover.dataset.commentId;
                const emoji = emojiBtn.dataset.emoji;
                const wrapper = popover.closest('.comment-react-wrapper');
                const pill = wrapper.querySelector('.comment-react-pill');
                const emojiDisplay = pill.querySelector('.reaction-emoji-display');
                const countDisplay = pill.querySelector('.reaction-count-display');

                popover.classList.remove('active');

                try {
                    const res = await fetch(`${window.KHLUPS_CONFIG.routes.commentReact}/${commentId}/react`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ reaction: emoji })
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        pill.dataset.userReaction = data.user_reaction || '';
                        if (data.user_reaction) {
                            pill.classList.add('has-reaction');
                            emojiDisplay.textContent = data.user_reaction;
                        } else {
                            pill.classList.remove('has-reaction');
                            emojiDisplay.textContent = '❤️';
                        }
                        countDisplay.textContent = data.reactions_count > 0 ? data.reactions_count : '';
                    } else if (data.status === 'unauthenticated') {
                        requireAuth('react to comments');
                    } else {
                        showToast(data.message || 'Could not update reaction.');
                    }
                } catch (err) {
                    console.error("Reaction error:", err);
                    showToast("Network error updating reaction.");
                }
                return;
            }

            // 14. 3-Dot More Menu for Comment Owner
            const commentMoreBtn = e.target.closest('.comment-more-btn');
            if (commentMoreBtn) {
                e.stopPropagation();
                const wrapper = commentMoreBtn.closest('.comment-menu-wrapper');
                const menu = wrapper.querySelector('.comment-dropdown-menu');
                const isActive = menu.classList.contains('active');

                document.querySelectorAll('.comment-dropdown-menu.active').forEach(m => m.classList.remove('active'));
                if (!isActive) menu.classList.add('active');
                return;
            }

            // 14b. Edit Comment Button (Populate Composer)
            const editBtn = e.target.closest('.dropdown-edit-btn');
            if (editBtn) {
                e.stopPropagation();
                const commentId = editBtn.dataset.commentId;
                const commentItem = editBtn.closest('.insta-comment-item');
                const card = editBtn.closest('.video-card');
                const composer = card.querySelector('.comment-composer');
                const inputField = composer.querySelector('.comment-input-field');
                const parentInput = composer.querySelector('input[name="parent_id"]');
                const editInput = composer.querySelector('input[name="edit_comment_id"]');
                const replyingBanner = composer.querySelector('.replying-banner');
                const editingBanner = composer.querySelector('.editing-banner');
                const sendBtn = composer.querySelector('.send-comment-btn');

                document.querySelectorAll('.comment-dropdown-menu.active').forEach(m => m.classList.remove('active'));

                // Cancel replying mode
                parentInput.value = '';
                if (replyingBanner) replyingBanner.style.display = 'none';

                // Extract current comment text
                const textEl = commentItem.querySelector('.comment-text');
                const currentText = textEl ? textEl.textContent.trim() : '';

                // Activate editing mode
                if (editInput) editInput.value = commentId;
                if (editingBanner) editingBanner.style.display = 'flex';
                inputField.value = currentText;
                inputField.placeholder = 'Edit your comment...';
                sendBtn.textContent = 'Save';
                sendBtn.classList.add('active');
                inputField.focus();
                inputField.setSelectionRange(inputField.value.length, inputField.value.length);
                return;
            }

            // 14c. Cancel Edit Button
            const cancelEditBtn = e.target.closest('.cancel-edit-btn');
            if (cancelEditBtn) {
                e.stopPropagation();
                const composer = cancelEditBtn.closest('.comment-composer');
                const editInput = composer.querySelector('input[name="edit_comment_id"]');
                const editingBanner = composer.querySelector('.editing-banner');
                const inputField = composer.querySelector('.comment-input-field');
                const sendBtn = composer.querySelector('.send-comment-btn');

                if (editInput) editInput.value = '';
                if (editingBanner) editingBanner.style.display = 'none';
                inputField.value = '';
                inputField.placeholder = 'Add a comment...';
                sendBtn.textContent = 'Post';
                sendBtn.classList.remove('active');
                return;
            }

            // 15. Delete Comment (Confirm & API call)
            const deleteBtn = e.target.closest('.dropdown-delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
                const commentId = deleteBtn.dataset.commentId;
                const commentItem = deleteBtn.closest('.insta-comment-item');
                const card = deleteBtn.closest('.video-card');

                document.querySelectorAll('.comment-dropdown-menu.active').forEach(m => m.classList.remove('active'));

                showConfirmDialog(
                    'Delete Comment?',
                    'Are you sure you want to delete this comment? This action cannot be undone.',
                    async () => {
                        try {
                            const res = await fetch(`${window.KHLUPS_CONFIG.routes.commentDelete}/${commentId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();

                            if (data.status === 'success') {
                                // Smoothly remove item from DOM
                                commentItem.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                                commentItem.style.opacity = '0';
                                commentItem.style.transform = 'translateX(20px)';
                                setTimeout(() => {
                                    commentItem.remove();

                                    // Update comment count indicators
                                    const modal = card.querySelector('.comment-modal');
                                    const countBadge = modal.querySelector('.comments-count-badge');
                                    if (countBadge) countBadge.textContent = `(${data.new_comments_count})`;
                                    const actionCount = card.querySelector('.comment-count');
                                    if (actionCount) actionCount.textContent = data.new_comments_count;

                                    // Check if comment body is empty
                                    const commentBody = modal.querySelector('.comment-body');
                                    if (!commentBody.querySelector('.insta-comment-item')) {
                                        commentBody.innerHTML = `
                                            <div class="comments-empty-state">
                                                <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
                                                <h5>No comments yet</h5>
                                                <p>Be the first to share your thoughts!</p>
                                            </div>
                                        `;
                                    }
                                }, 250);
                                showToast('Comment deleted.');
                            } else {
                                showToast(data.message || 'Could not delete comment.');
                            }
                        } catch (err) {
                            console.error("Delete comment error:", err);
                            showToast("Network error deleting comment.");
                        }
                    }
                );
                return;
            }

            // 16. Send / Post / Save Comment Button
            const sendBtn = e.target.closest('.send-comment-btn');
            if (sendBtn) {
                e.stopPropagation();
                if (!requireAuth('post comments')) return;

                const composer = sendBtn.closest('.comment-composer');
                const card = composer.closest('.video-card');
                const inputField = composer.querySelector('.comment-input-field');
                const parentInput = composer.querySelector('input[name="parent_id"]');
                const editInput = composer.querySelector('input[name="edit_comment_id"]');
                const editingBanner = composer.querySelector('.editing-banner');
                const videoId = card.dataset.videoId;
                const commentText = inputField.value.trim();
                const parentId = parentInput.value || null;
                const editCommentId = editInput ? editInput.value : null;

                if (!commentText) return;

                // ── Edit / Update Existing Comment ─────────────────────────────
                if (editCommentId) {
                    sendBtn.disabled = true;
                    sendBtn.textContent = 'Saving...';

                    try {
                        const res = await fetch(`${window.KHLUPS_CONFIG.routes.commentUpdate}/${editCommentId}/update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                comment: commentText
                            })
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            const commentItem = card.querySelector(`.insta-comment-item[data-id="${editCommentId}"]`);
                            if (commentItem) {
                                const textEl = commentItem.querySelector('.comment-text');
                                if (textEl) {
                                    textEl.textContent = data.comment;
                                }
                            }

                            // Reset composer back to post mode
                            inputField.value = '';
                            if (editInput) editInput.value = '';
                            if (editingBanner) editingBanner.style.display = 'none';
                            inputField.placeholder = 'Add a comment...';
                            sendBtn.textContent = 'Post';
                            sendBtn.classList.remove('active');

                            showToast('Comment updated!');
                        } else if (data.status === 'unauthenticated') {
                            requireAuth('edit comments');
                        } else {
                            showToast(data.message || 'Could not update comment.');
                        }
                    } catch (err) {
                        console.error("Update comment error:", err);
                        showToast("Network error updating comment.");
                    } finally {
                        sendBtn.disabled = false;
                        if (editInput && editInput.value) {
                            sendBtn.textContent = 'Save';
                        } else {
                            sendBtn.textContent = 'Post';
                        }
                    }
                    return;
                }

                // ── Create New Comment / Reply ─────────────────────────────────
                sendBtn.disabled = true;
                sendBtn.textContent = 'Posting...';

                try {
                    const res = await fetch(window.KHLUPS_CONFIG.routes.comment, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id: videoId,
                            parent_id: parentId,
                            comment: commentText
                        })
                    });
                    const data = await res.json();

                    if (data.status === 'success') {
                        const commentBody = card.querySelector('.comment-body');
                        const emptyState = commentBody.querySelector('.comments-empty-state');
                        if (emptyState) emptyState.remove();

                        const newHtml = generateCommentHtml(data.comment, Boolean(parentId));

                        if (parentId) {
                            // Insert into parent replies list
                            const parentItem = commentBody.querySelector(`.insta-comment-item[data-id="${parentId}"]`);
                            if (parentItem) {
                                let repliesContainer = parentItem.querySelector('.replies-container');
                                if (!repliesContainer) {
                                    const repliesDiv = document.createElement('div');
                                    repliesDiv.className = 'replies-container';
                                    repliesDiv.dataset.parentId = parentId;
                                    repliesDiv.innerHTML = `
                                        <button type="button" class="toggle-replies-btn" data-expanded="true">Hide replies</button>
                                        <div class="replies-list" style="display:block;">${newHtml}</div>
                                    `;
                                    parentItem.querySelector('.comment-content').appendChild(repliesDiv);
                                } else {
                                    const repliesList = repliesContainer.querySelector('.replies-list');
                                    repliesList.style.display = 'block';
                                    repliesList.insertAdjacentHTML('beforeend', newHtml);
                                    const toggleBtn = repliesContainer.querySelector('.toggle-replies-btn');
                                    if (toggleBtn) {
                                        const count = repliesList.querySelectorAll('.insta-comment-item.reply').length;
                                        toggleBtn.dataset.expanded = 'true';
                                        toggleBtn.textContent = 'Hide replies';
                                    }
                                }
                            }
                        } else {
                            // Prepend top-level comment
                            commentBody.insertAdjacentHTML('afterbegin', newHtml);
                            commentBody.scrollTop = 0;
                        }

                        // Reset composer state
                        inputField.value = '';
                        sendBtn.classList.remove('active');
                        parentInput.value = '';
                        const replyingBanner = composer.querySelector('.replying-banner');
                        if (replyingBanner) replyingBanner.style.display = 'none';
                        inputField.placeholder = 'Add a comment...';

                        // Update counts
                        const modal = card.querySelector('.comment-modal');
                        const countBadge = modal.querySelector('.comments-count-badge');
                        if (countBadge) countBadge.textContent = `(${data.total_comments})`;
                        const actionCount = card.querySelector('.comment-count');
                        if (actionCount) actionCount.textContent = data.total_comments;

                        showToast(parentId ? 'Reply posted!' : 'Comment posted!');
                    } else if (data.status === 'unauthenticated') {
                        requireAuth('post comments');
                    } else {
                        showToast(data.message || 'Failed to post comment.');
                    }
                } catch (err) {
                    console.error("Post comment error:", err);
                    showToast("Network error posting comment.");
                } finally {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Post';
                }
                return;
            }

            // 17. Share Button
            const shareBtn = e.target.closest('.share-btn');
            if (shareBtn) {
                e.stopPropagation();
                const card = shareBtn.closest('.video-card');
                const videoId = shareBtn.dataset.id;
                const shareUrl = shareBtn.dataset.url;
                const shareTitle = shareBtn.dataset.title || 'Check out this video on {{ config('app.project_name') }}!';

                // Helper to increment share count on backend
                const recordShare = async () => {
                    try {
                        const res = await fetch(window.KHLUPS_CONFIG.routes.share, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ id: videoId })
                        });
                        const data = await res.json();
                        if (data.status === 'success') {
                            const countEl = card.querySelector('.share-count');
                            if (countEl) countEl.textContent = data.shares_count;
                        }
                    } catch (err) {
                        console.error("Share count error:", err);
                    }
                };

                // Use Web Share API if supported
                if (navigator.share) {
                    try {
                        await navigator.share({
                            title: shareTitle,
                            text: shareTitle,
                            url: shareUrl
                        });
                        recordShare();
                        showToast("Shared successfully!");
                        return;
                    } catch (shareErr) {
                        // If user cancelled, do nothing
                        if (shareErr.name === 'AbortError') return;
                        // Otherwise open fallback modal
                    }
                }

                // Fallback Share Modal
                const shareModal = card.querySelector('.share-modal');
                const shareOverlay = card.querySelector('.share-overlay');
                shareOverlay.classList.add('active');
                shareModal.classList.add('active');
                card.classList.add('show-share');
                return;
            }

            // 18. Close Fallback Share Drawer
            if (e.target.classList.contains('share-overlay') || e.target.closest('.close-share')) {
                const card = e.target.closest('.video-card');
                const shareModal = card.querySelector('.share-modal');
                const shareOverlay = card.querySelector('.share-overlay');
                shareModal.classList.remove('active');
                shareOverlay.classList.remove('active');
                card.classList.remove('show-share');
                return;
            }

            // 19. Copy Link Button in Share Modal
            const copyBtn = e.target.closest('.copy-link-btn');
            if (copyBtn) {
                e.stopPropagation();
                const card = copyBtn.closest('.video-card');
                const videoId = card.dataset.videoId;
                const url = copyBtn.dataset.url;

                const onCopySuccess = () => {
                    copyBtn.textContent = 'Copied!';
                    copyBtn.classList.add('copied');
                    showToast("Link copied to clipboard!");

                    // Record share count
                    fetch(window.KHLUPS_CONFIG.routes.share, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: videoId })
                    }).then(r => r.json()).then(data => {
                        if (data.status === 'success') {
                            const countEl = card.querySelector('.share-count');
                            if (countEl) countEl.textContent = data.shares_count;
                        }
                    }).catch(() => {});

                    setTimeout(() => {
                        copyBtn.textContent = 'Copy Link';
                        copyBtn.classList.remove('copied');
                    }, 2000);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(onCopySuccess).catch(() => {
                        const input = card.querySelector('#share-url-input');
                        input.select();
                        document.execCommand('copy');
                        onCopySuccess();
                    });
                } else {
                    const input = card.querySelector('#share-url-input');
                    input.select();
                    document.execCommand('copy');
                    onCopySuccess();
                }
                return;
            }

            // 20. Social Icon Click inside Share Modal
            const socialIcon = e.target.closest('.social-share-icons .share-icon');
            if (socialIcon) {
                const card = socialIcon.closest('.video-card');
                const videoId = card.dataset.videoId;
                fetch(window.KHLUPS_CONFIG.routes.share, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.KHLUPS_CONFIG.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id: videoId })
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') {
                        const countEl = card.querySelector('.share-count');
                        if (countEl) countEl.textContent = data.shares_count;
                    }
                }).catch(() => {});
            }
        });

        /* ---------------- Comment Input Event Listeners ---------------- */
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('comment-input-field')) {
                const composer = e.target.closest('.comment-composer');
                const sendBtn = composer.querySelector('.send-comment-btn');
                if (e.target.value.trim().length > 0) {
                    sendBtn.classList.add('active');
                } else {
                    sendBtn.classList.remove('active');
                }
            }
        });

        /* ---------------- Emoji Picker Logic ---------------- */
        document.addEventListener('click', (e) => {
            // Toggle emoji picker on trigger button click
            const triggerBtn = e.target.closest('.emoji-trigger-btn');
            if (triggerBtn) {
                e.stopPropagation();
                const wrapper = triggerBtn.closest('.composer-input-wrapper');
                const panel = wrapper.querySelector('.emoji-picker-panel');
                const isOpen = panel.style.display !== 'none';

                // Close all other open pickers first
                document.querySelectorAll('.emoji-picker-panel').forEach(p => {
                    p.style.display = 'none';
                });
                document.querySelectorAll('.emoji-trigger-btn').forEach(b => {
                    b.classList.remove('active');
                });

                if (!isOpen) {
                    panel.style.display = 'block';
                    triggerBtn.classList.add('active');
                }
                return;
            }

            // Insert emoji into input
            const emojiBtn = e.target.closest('.emoji-insert-btn');
            if (emojiBtn) {
                e.stopPropagation();
                const emoji = emojiBtn.dataset.emoji;
                const wrapper = emojiBtn.closest('.composer-input-wrapper');
                const inputField = wrapper.querySelector('.comment-input-field');
                const composer = inputField.closest('.comment-composer');
                const sendBtn = composer.querySelector('.send-comment-btn');

                // Insert emoji at cursor position
                const start = inputField.selectionStart;
                const end = inputField.selectionEnd;
                const current = inputField.value;
                inputField.value = current.slice(0, start) + emoji + current.slice(end);
                inputField.setSelectionRange(start + emoji.length, start + emoji.length);
                inputField.focus();

                // Activate send button if there's text
                if (inputField.value.trim().length > 0) {
                    sendBtn.classList.add('active');
                }
                return;
            }

            // Close picker when clicking outside
            if (!e.target.closest('.composer-input-wrapper')) {
                document.querySelectorAll('.emoji-picker-panel').forEach(p => {
                    p.style.display = 'none';
                });
                document.querySelectorAll('.emoji-trigger-btn').forEach(b => {
                    b.classList.remove('active');
                });
            }
        });


        document.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('comment-input-field') && e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const composer = e.target.closest('.comment-composer');
                const sendBtn = composer.querySelector('.send-comment-btn');
                if (sendBtn.classList.contains('active')) {
                    sendBtn.click();
                }
            }
        });

        /* ---------------- Infinite Scroll ---------------- */
        async function loadMore() {
            const nextPageInput = document.getElementById('nextPage');
            const nextPage = nextPageInput?.value;
            if (!nextPage || isLoading) return;

            isLoading = true;
            if (loadingIndicator) loadingIndicator.style.display = 'block';

            try {
                const res = await fetch(nextPage, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();

                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newCards = Array.from(temp.querySelectorAll('.video-card'));
                newCards.forEach(card => shortsContainer.appendChild(card));

                const newNextPageInput = temp.querySelector('#nextPage');
                if (newNextPageInput) {
                    nextPageInput.value = newNextPageInput.value;
                } else {
                    nextPageInput.value = '';
                }

                attachVideoEvents(newCards);
            } catch (err) {
                console.error("Load more failed:", err);
                nextPageInput.value = '';
            } finally {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                isLoading = false;
            }
        }

        shortsContainer.addEventListener('scroll', () => {
            if (shortsContainer.scrollHeight - (shortsContainer.scrollTop + shortsContainer.clientHeight) < 600) {
                loadMore();
            }
        });

        window.addEventListener('scroll', () => {
            if (document.body.offsetHeight - (window.innerHeight + window.scrollY) < 500) {
                loadMore();
            }
        });
    });
</script>
@endsection

@section('main-section')
    <div class="main-page-wrapper">
        <div id="shortsContainer" class="shorts-container">
            @if (isset($selectedVideo) && $selectedVideo)
                @include('partials.khlup-card', ['video' => $selectedVideo])
            @endif
            @include('partials.khlup-items', ['videos' => $videos])
        </div>

        <div id="loadingIndicator" style="text-align:center; padding:20px; display:none; color:white;">
            <div class="comments-spinner" style="margin:0 auto 8px auto;"></div>
            Loading more...
        </div>

        {{-- Next page URL for infinite scroll --}}
        <input type="hidden" id="nextPage" value="{{ $videos->nextPageUrl() ?? '' }}">
    </div>

    {{-- Custom Confirmation Dialog Overlay --}}
    <div id="khlupConfirmOverlay" class="custom-confirm-overlay">
        <div class="custom-confirm-card">
            <h4 class="custom-confirm-title">Delete Comment?</h4>
            <p class="custom-confirm-desc">Are you sure you want to delete this comment? This action cannot be undone.</p>
            <div class="custom-confirm-actions">
                <button type="button" class="btn-confirm-cancel" id="btnConfirmCancel">Cancel</button>
                <button type="button" class="btn-confirm-delete" id="btnConfirmDelete">Delete</button>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="khlupToast" class="khlup-toast"></div>
@endsection
