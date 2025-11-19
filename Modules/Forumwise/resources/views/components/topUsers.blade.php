<div class="fw-forum-top-users">
<h3>Top Users</h3>
<p>Most Active & Engaged Forum Members</p>
@if ($topUsers->isEmpty())
<div class="fw-forum-top-users-list fw-forum-emptycase">
    <div class="fw-forum-emptycase-content">
        <h5>{{ __('forumwise::forum_wise.no_user_available') }}</h5>
        <span>{{ __('forumwise::forum_wise.oosps_no_user_available') }}</span>
    </div>
</div>
@else
<ul class="fw-forum-top-users-list">
        @foreach ($topUsers as $user)
        <li>
            @if ($user?->image)
                <img  src="{{ url(Storage::url($user?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
            @else
            <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
            @endif  
                <div class="fw-forum-top-users-info">
                <h4>{{ $user?->name }}</h4>
                <div class="fw-forum-top-users-stat">
                    <span class="fw-forum-stats">{{ $user->topic_count }}<strong>{{ __('forumwise::forum_wise.topics') }}</strong></span>
                    <span class="fw-forum-stats">{{ $user?->post_count }}<strong>{{ __('forumwise::forum_wise.posts') }}</strong></span>
                </div>
            </div>
            </li>
        @endforeach
    </ul>
    @endif
</div>