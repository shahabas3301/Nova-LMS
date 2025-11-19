<li class="fw-comment_item">
    <div class="fw-villy-about">
        <figure class="fw-villy-img">
            @if ($comment?->creator?->profile?->image)
                <img src="{{ url(Storage::url($comment?->creator?->profile?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
            @elseif($comment?->creator?->image)
                <img src="{{ url(Storage::url($comment?->creator?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
            @else
                <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
            @endif
        </figure>
        <div class="fw-villy-about-content">
            <div class="fw-david-villy-content">
                <div class="fw-david-villy">
                    @if($comment?->creator?->profile)
                        <span>{{ $comment?->creator?->profile?->first_name }} {{ $comment?->creator?->profile?->last_name }}</span>
                    @else
                        <span>{{ $comment?->creator?->first_name }} {{ $comment?->creator?->last_name }}</span>
                    @endif
                </div>
                <em>{{ $comment?->created_at->diffForHumans() }}</em>
            </div>
            <div class="fw-villy-paragraph">
                {!! $comment?->description !!}
            </div>
            <div class="fw-villy-stats">
                <div class="fw-villy-stats_info">
                    <a href="javascript:void(0)" class="fw-active-villy fw-stats_info-like"  wire:click="likeComment({{ $comment?->id }})">
                        @if ($comment?->likes->count() > 0)
                            <i class="fw-icon-heart-filled-1"></i>
                        @else
                            <i class=" fw-icon-heart-02"></i>
                        @endif
                        <span class="fw-villy-count">
                            @if ($comment?->likes_count > 0)
                                <span>{{ $comment?->likes_count }}</span>
                            @endif
                            <em>{{ __('forumwise::forum_wise.like') }}</em>
                        </span>
                    </a>
                </div>
                <div class="fw-villy-stats_info">
                    <a href="javascript:void(0)" onclick="toggleReplySection({{ $comment?->id }})" class="fw-stats_info-reply">
                        <i class="fw-icon-chat-03"></i>
                        <span class="fw-villy-count">
                            @if ($comment?->replies_count > 0)
                                <span class="fw-countreply">{{ $comment?->replies_count }}</span>
                            @endif
                            <em id="reply-count-{{ $comment?->id }}">{{ $comment?->replies_count > 1 ? __('forumwise::forum_wise.replies') : __('forumwise::forum_wise.reply') }}</em>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if ($comment?->replies->isNotEmpty())
        <ul class="fw-comment">
            @foreach ($comment?->replies as $reply)
               @include('forumwise::components.comment', ['comment' => $reply])
            @endforeach
        </ul>
    @endif
    <!-- <div class="fw-loadmore-btn">
        <button class="fw-bookmark">{{ __('forumwise::forum_wise.load_more') }}</button>
    </div> -->
</li>