<div class="fw-contribute">
    @if ($contributors->count() > 0)
        <h3>{{ __('forumwise::forum_wise.contributors') }}</h3>
        <div class="fw-contribute-img">
            @php
                $count = $contributors->count()-14;
            @endphp
            @foreach ($contributors->take(14) as $key => $contributor)
            @if ($key == 13)
                @if($count != 0)
                <span id="fw-contributor-img" class="fw-add-more">
                    @if ($contributor?->users?->profile?->image)
                        <img  src="{{ url(Storage::url($contributor?->users?->profile?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                    @elseif($contributor?->users?->image)
                        <img src="{{ url(Storage::url($contributor?->users?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                    @else
                        <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
                    @endif
                    <em fw-data-target="#fw-invite-popup">+{{$count}}</em>
                </span>
                @else
                <span>
                    @if ($contributor?->users?->profile?->image)
                        <img  src="{{ url(Storage::url($contributor?->users?->profile?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                    @elseif($contributor?->users?->image)
                        <img src="{{ url(Storage::url($contributor?->users?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                    @else
                        <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
                    @endif
                </span>
                @endif
            @else
            <span>
                @if ($contributor?->users?->profile?->image)
                    <img  src="{{ url(Storage::url($contributor?->users?->profile?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                @elseif($contributor?->users?->image)
                    <img src="{{ url(Storage::url($contributor?->users?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
                @else
                    <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
                @endif
            </span>
            @endif
            @endforeach
        </div>
    @endif
    <div class="fw-contribute-author">
        <div class="fw-author-lyons">
            @if ($topic?->creator?->profile?->image)
                <img src="{{ url(Storage::url($topic?->creator?->profile?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
            @elseif($topic?->creator?->image)
                <img src="{{ url(Storage::url($topic?->creator?->image)) }}" alt="{{ __('forumwise::forum_wise.image') }}" />
            @else
                <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('forumwise::forum_wise.image') }}" />  
            @endif
            <div class="fw-author-content">
                @if (config('forumwise.userinfo_relation'))
                    <em>{{ $topic?->creator?->profile?->first_name }} {{ $topic?->creator?->profile?->last_name }}</em>
                @else
                    <em>{{ $topic?->creator?->first_name }} {{ $topic?->creator?->last_name }}</em>
                @endif
            </div>
        </div>
        <em class="fw-author-button">{{ __('forumwise::forum_wise.author') }}</em>
    </div>
    <div id="fw-contributor-popup" class="fw-modal fw-addforumpopup fw-addsidebar" wire:ignore.self>
        <div class="fw-modaldialog">
            <div class="fw-modal_wrap">
                <div class="fw-modal_title">
                    <h2>Contributors</h2>
                    <a href="javascript:void(0);" class="fw-removemodal" wire:click="closeContributorPopup"><i class="fw-icon-multiply-02"></i></a>
                </div>
                <div class="fw-modal_body fw-modal-sidebar">
                    <div class="fw-james-main">
                        @foreach ($contributors->slice(14) as $contributor)
                        <div class="fw-james-content">
                            @if ($contributor?->users?->profile?->image)
                                <img src="{{ url(Storage::url($contributor?->users?->profile?->image)) }}" alt="{{ __('Image') }}" />
                            @elseif($contributor?->users?->image)
                                <img src="{{ url(Storage::url($contributor?->users?->image)) }}" alt="{{ __('Image') }}" />
                            @else
                                <img src="{{ asset('modules\forumwise\images\placeholder.png') }}" alt="{{ __('Image') }}" />  
                            @endif
                            @if (config('forumwise.userinfo_relation'))
                                <span>{{ $topic?->creator?->profile?->first_name }} {{ $topic?->creator?->profile?->last_name }}</span>
                            @else
                                <span>{{ $topic?->creator?->first_name }} {{ $topic?->creator?->last_name }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
