<div class="fw-forum_info">
    <ul class="fw-forum_info_card">
        <li>
            <span>
                <strong>{{$totalForums}}</strong>
                {{ __('forumwise::forum_wise.forums') }}
            </span>
            <figure>
                <img src="{{ asset('modules\forumwise\images\card\icons/img-01.png') }}" alt="img">
            </figure>
        </li>
        <li>
            <span>
                <strong>{{$totalTopics}}</strong>
                {{ __('forumwise::forum_wise.topics') }}
            </span>
            <figure>
                <img src="{{ asset('modules\forumwise\images\card\icons/img-02.png') }}" alt="img">
            </figure>
        </li>
        <li>
            <span>
                <strong>{{$totalPosts}}</strong>
                {{ __('forumwise::forum_wise.posts') }}
            </span>
            <figure>
                <img src="{{ asset('modules\forumwise\images\card\icons/img-03.png') }}" alt="img">
            </figure>
        </li>
        <li>
            <span>
                <strong>{{$totalMembers}}</strong>
                {{ __('forumwise::forum_wise.members') }}
            </span>
            <figure>
                <img src="{{ asset('modules\forumwise\images\card\icons/img-04.png') }}" alt="img">
            </figure>
        </li>
    </ul>
</div>