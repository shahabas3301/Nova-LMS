<div class="fw-forum_content">
    <div class="fw-forum_about">
        <div class="fw-forum_about_title">
            <div class="fw-forum-content-main">
                <div class="fw-forum-content">
                    <h2>{{ __('forumwise::forum_wise.topics') }}</h2>
                    <p>{{ __('forumwise::forum_wise.create_new_topics') }}</p>
                </div>
            </div>
            <div class="fw-searhcoption">
                <div class="fw-searhcoption_form">
                    <input wire:model.live.debounce.500ms="search" type="text" class="form-control" placeholder="{{ __('forumwise::forum_wise.search_placeholder') }}">
                    <i class="fw-icon-search-02"></i>
                </div>
                <div class="fw-searhcoption_btn">
                    <a href="javascript:void(0)" class="{{ $filterType == 'all' ? 'fw-active' : '' }}" wire:click="filterTopics('all')" >{{ __('forumwise::forum_wise.all') }}</a>
                    <a href="javascript:void(0)" class="{{ $filterType == 'my' ? 'fw-active' : '' }}" wire:click="filterTopics('my')" >{{ __('forumwise::forum_wise.my_topics') }}</a>
                </div>
            </div>
        </div>   
        @if($topics->count() > 0)
            @foreach($topics as $topic)
            <div class="fw-forum_lists fw-forum_lists_food">
                <div class="fw-forum_items">
                    <div class="fw-forum_item">
                        <figure class="fw-forum_item_img">
                                @if($topic?->media->count() > 0)
                                    <img src="{{ url(Storage::url($topic?->media->first()->path)) }}" alt="{{$topic->title}}">
                                @else
                                    <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.placeholder_image') }}">
                                @endif
                        </figure>
                        <div class="fw-forum_item_content">
                            <div class="fw-forum_item_title">
                                <h4><a href="{{ route('topic', $topic?->slug) }}">{{ $topic?->title }}</a>
                                    @if($topic?->type == 'private')
                                        <span class="fw-forum-stats"><i class="fw-icon-lock-close"></i></span>
                                    @endif
                                </h4>
                                <p>{{ $topic?->description }}</p>
                            </div>
                            <div class="fw-forum_item_action">
                                <div class="fw-forum_auther">
                                    <div class="fw-forum_auther_info">
                                        @if($topic?->creator?->profile)
                                            <img src="{{ url(Storage::url($topic?->creator?->profile->image)) }}" alt="{{ __('forumwise::forum_wise.author_image') }}">
                                            <h4>{{ $topic?->creator?->profile->first_name }} {{ $topic?->creator?->profile->last_name }}</h4>
                                        @else
                                            <img src="{{ url(Storage::url($topic?->creator?->image)) }}" alt="{{ __('forumwise::forum_wise.author_image') }}">
                                            <h4>{{ $topic?->creator?->first_name }} {{ $topic?->creator?->last_name }}</h4>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('topic', $topic?->slug) }}" class="fw-forum-stats-main">{{ $topic?->posts_count }}<strong>{{ __('forumwise::forum_wise.posts') }}</strong></a>
                                </div>
                                <div class="fw-forum-stats-wrapper">
                                    <a href="{{ route('topic', $topic?->slug) }}" class="fw-forum-stats-info">
                                        <i class="fw-icon-arrow-corner-cw-lt"></i>
                                        <span class="fw-forum-stats">{{ $topic?->comments_count }}<strong>{{ __('forumwise::forum_wise.replies') }}</strong></span>
                                    </a>
                                    <a href="{{ route('topic', $topic?->slug) }}" class="fw-forum-stats-info">
                                        <i class="fw-icon-eye-open-01"></i>
                                        @php
                                            $viewsCount = $topic?->views_count;
                                            if ($viewsCount >= 1000) {
                                                $formattedCount = number_format($viewsCount / 1000, 1) . 'k';
                                            } else {
                                                $formattedCount = $viewsCount;
                                            }
                                        @endphp
                                        <span class="fw-forum-stats">{{ $formattedCount }}<strong>{{ __('forumwise::forum_wise.views') }}</strong></span>
                                    </a>
                                    <div class="fw-forum-stats-info">
                                        <i class="fw-icon-calender"></i>
                                        <span class="fw-forum-stats">{{ $topic?->updated_at->format('M d Y, H:i A') }}<strong>{{ __('forumwise::forum_wise.last_activity') }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>        
            </div>
            @endforeach
        @elseif($search != '')
            @include('forumwise::components.emptyRecord',['title' => __('forumwise::forum_wise.not_found'), 'description' => __('forumwise::forum_wise.no_topics_found'), 'type' => 'topics', 'roles' => $roles])
        @else
            @include('forumwise::components.emptyRecord',['title' => __('forumwise::forum_wise.no_topics_available'), 'description' => __('forumwise::forum_wise.check_back_later'), 'type' => 'topics', 'roles' => $roles])
        @endif
    </div>
    <div class="fw-forum_info">
        @include('forumwise::components.topUsers')
        @if (!$popularTopics->isEmpty())
            @include('forumwise::components.popularTopics', ['popularTopics' => $popularTopics, 'title' => __('forumwise::forum_wise.popular_topics'), 'description' => __('forumwise::forum_wise.related_discussions')])
        @endif
    </div>
    @include('forumwise::components.addTopicPopup')
</div>