<div class="fw-forum_about fw-forumswrap" x-data="{ expandedCategories: [] }">
    <div class="fw-forum_about_title">
        <div class="fw-forum-content-main">
            <div class="fw-forum-content">
                <h2>{{ __('forumwise::forum_wise.forums') }}</h2>
                <p>{{ __('forumwise::forum_wise.browse_different_forums_create_new_topics_or_get_involved_in_different_topics') }}</p>
            </div>
            @if($user?->role == config('forumwise.db.roles.administrator'))
                <a href="javascript:void(0);" wire:click="openForumModal" class="fw-danger"><i class="fw-icon-layer-01"></i> {{ __('forumwise::forum_wise.create_forum') }} </a>
            @endif
        </div>
    </div>

    @if($search != '' && empty($categoriesList) || !collect($categoriesList)->contains(function($category) { return count($category->forums) > 0; }))
        @include('forumwise::components.emptyRecord', ['title' => __('forumwise::forum_wise.not_found'), 'description' => __('forumwise::forum_wise.no_forums_matching_search'), 'type' => 'forums'])
    @elseif(empty($categoriesList) || !collect($categoriesList)->contains(function($category) { return count($category->forums) > 0; }))
        @include('forumwise::components.emptyRecord', ['title' => __('forumwise::forum_wise.no_forums_available'), 'description' => __('forumwise::forum_wise.check_back_later'), 'type' => 'forums'])
    @else
        @foreach($categoriesList as $category)
        @if(count($category?->forums) > 0)
            <div class="fw-forum_lists fw-forum_lists_marketing">
                <div class="fw-forum_list_title">
                    <span class="fw-forum-title-mark" style="background-color: {{ $category?->label_color }};"> </span>
                    <h3>{{ $category?->name }}</h3>
                    @if(count($category?->forums) > 3)
                        <a href="javascript:void(0);" x-on:click="expandedCategories.includes({{ $category->id }}) ? expandedCategories = expandedCategories.filter(id => id !== {{ $category->id }}) : expandedCategories.push({{ $category->id }})">
                            <span x-show="!expandedCategories.includes({{ $category->id }})">{{ __('forumwise::forum_wise.view_all') }} (<em>{{ count($category?->forums) }}</em>)</span>
                            <span x-show="expandedCategories.includes({{ $category->id }})">{{ __('forumwise::forum_wise.show_less') }}</span>
                        </a>
                    @endif
                </div>
                @foreach($category?->forums as $index => $forum)
                <div class="fw-forum_items" x-show="expandedCategories.includes({{ $category->id }}) || {{ $index }} < 3">
                    <div class="fw-forum_item">
                        <figure class="fw-forum_item_img">
                            @if($forum?->media->count() > 0)
                                <img x-on:click="window.location.href = '{{ route('forum-topics', $forum->slug) }}'" src="{{ url(Storage::url($forum?->media->first()->path)) }}" alt="{{$forum->title}}">
                            @else
                                <img x-on:click="window.location.href = '{{ route('forum-topics', $forum->slug) }}'" src="{{ asset('modules/forumwise/images/placeholder.png') }}" alt="img">
                            @endif
                        </figure>
                        <div class="fw-forum_item_content">
                            <div class="fw-forum_item_title">
                                <h4 x-on:click="window.location.href = '{{ route('forum-topics', $forum->slug) }}'">{{ $forum?->title}}</h4>
                                <p>{{ $forum?->description }}</p>
                            </div>
                            <div class="fw-forum_item_action">
                                <ul class="fw-forum_item_info">
                                    <li>
                                        <i class="fw-icon-layer-01"></i>
                                        <a href="{{ route('forum-topics', $forum->slug) }}">
                                            <strong>{{$forum?->topics_count}} </strong>{{ __('forumwise::forum_wise.topics') }}
                                        </a>
                                    </li>
                                    <li>
                                        <i class="fw-icon-file-06"></i>
                                        <span>
                                            <strong>{{$forum?->posts_count}} </strong>{{ __('forumwise::forum_wise.posts') }}
                                        </span>
                                    </li>
                                </ul>
                                <div class="fw-forum-thread">
                                    @if($user?->role == config('forumwise.db.roles.administrator'))
                                        <a href="javascript:void(0);" class="fw-btn-light fw-btn-thread" wire:click="openForumModal({{$forum}})">{{ __('forumwise::forum_wise.edit') }} <i class="fw-icon-pencil-01"></i></a>
                                    @endif
                                    <a href="{{ route('forum-topics', $forum->slug) }}" class="fw-btn-light fw-btn-thread">{{ __('forumwise::forum_wise.view_thread') }} <i class="fw-icon-export-2-03"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        @endforeach
    @endif

    @include('forumwise::components.addForumPopup')
</div>
<script>
    document.addEventListener('fw-modal-closed', function () {
        Livewire.dispatch('resetFormAndErrors');
    });
</script>
