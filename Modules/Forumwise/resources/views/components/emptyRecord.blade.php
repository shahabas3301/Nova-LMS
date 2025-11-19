<div class="fw-forum_norecord">
    <div class="fw-forum_norecord_content">
        <svg width="44" height="59" viewBox="0 0 44 59" fill="none">
            <path d="M37.3999 15.2007C39.8299 15.2007 41.7998 17.1706 41.7998 19.6007V48.2C41.7998 53.0601 37.86 56.9999 33 56.9999H11.0001C6.14004 56.9999 2.2002 53.06 2.2002 48.2V10.8009C2.2002 5.94082 6.14003 2.00098 11.0001 2.00098H24.2C26.63 2.00098 28.6 3.9709 28.6 6.40091V10.8008C28.6 13.2308 30.5699 15.2007 32.9999 15.2007H37.3999Z" stroke="#EAEAEA" stroke-width="3.29995" stroke-miterlimit="10"/>
            <path d="M21.5109 2H25.0237C27.3137 2 29.5136 2.89274 31.156 4.48862L39.1272 12.2338C40.8325 13.8908 41.7947 16.1674 41.7947 18.5451V26.2655" stroke="#EAEAEA" stroke-width="3.29995" stroke-miterlimit="10"/>
            <path d="M20.3476 43.798C26.7265 43.798 31.8975 38.627 31.8975 32.2483C31.8975 25.8695 26.7265 20.6985 20.3476 20.6985C13.9688 20.6985 8.79773 25.8695 8.79773 32.2483C8.79773 38.627 13.9688 43.798 20.3476 43.798Z" stroke="#EAEAEA" stroke-width="2.47496" stroke-miterlimit="10"/>
            <path d="M18.5667 30.864C18.0823 31.6926 17.1825 32.2523 16.1584 32.2523C15.125 32.2523 14.2284 31.6926 13.7471 30.864" stroke="#EAEAEA" stroke-width="2.09005" stroke-miterlimit="10" stroke-linecap="round"/>
            <path d="M26.9501 30.8643C26.4669 31.6977 25.5645 32.2614 24.5367 32.2614C23.4996 32.2614 22.6003 31.6977 22.1202 30.8643" stroke="#EAEAEA" stroke-width="2.09005" stroke-miterlimit="10" stroke-linecap="round"/>
            <path d="M20.3917 36.9304H20.4046" stroke="#EAEAEA" stroke-width="2.78674" stroke-linecap="round"/>
            <path d="M35.1916 47.0973L31.8916 43.7974" stroke="#EAEAEA" stroke-width="2.47496" stroke-linecap="round"/>
        </svg>
        <h5>{{ $title }}</h5>
        <span>{{ $description }}</span>
        @if($title != 'Not found!')
            @if($type == 'forums')
                @if($user?->role == config('forumwise.db.roles.administrator'))
                    <a href="javascript:void(0);" wire:click="openForumModal()" class="fw-createbtn">{{ __('forumwise::forum_wise.create_forum') }} <i class="fw-icon-plus-01"></i></a>
                @endif
            @else
                @if($user?->role == config('forumwise.db.roles.administrator') || in_array(auth()?->user()?->role, $roles))
                    <a href="javascript:void(0);" wire:click="openAddTopicPopup()" class="fw-createbtn">{{ __('forumwise::forum_wise.create_topic') }} <i class="fw-icon-plus-01"></i></a>
                @endif
            @endif
        @endif
    </div>
</div>