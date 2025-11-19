<div class="fw-forum-top-users">
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    @if ($popularTopics->isEmpty())
     <div class="fw-forum-top-users-list fw-forum-emptycase">
        <div class="fw-forum-emptycase-content">
            <h5>{{ __('forumwise::forum_wise.no_topics_available') }}</h5>
            <span>{{ __('forumwise::forum_wise.oops_no_topics_available') }}</span>
        </div>
    </div>
    @else
    <ul class="fw-forum-top-users-list fw-forum-popular-list" wire:ignore>
        @foreach ($popularTopics as $topic)
        <li>    
            @if($topic?->media->count() > 0)
                <img src="{{ url(Storage::url($topic->media->first()->path)) }}" alt="{{ __('forumwise::forum_wise.image') }}">
            @else
                <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}">
            @endif
            <div class="fw-forum-top-users-info">
                <a href="{{ route('topic', $topic?->slug) }}">
                    <h4>{{$topic?->title}}</h4>
                </a>
                <div class="fw-forum-top-users-stat">
                    @if ($topic?->creator?->profile)
                        <span class="fw-forum-stats-maincontent">By {{ $topic->creator->profile->first_name }} {{ $topic->creator->profile->last_name }}</span>
                    @else
                        <span class="fw-forum-stats"><strong>By {{ $topic?->creator?->first_name }} {{ $topic?->creator?->last_name }}</strong></span>
                    @endif
                    <a class="fw-forum-target" href="{{ route('topic', $topic?->slug) }}">{{ $topic->posts_count }}<strong> {{ __('forumwise::forum_wise.posts') }}</strong></a>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    @endif   
</div>