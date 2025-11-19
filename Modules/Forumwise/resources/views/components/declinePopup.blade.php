<div wire:ignore.self id="fw-delete-popup" class="fw-modal fw-del-popup">
    <div class="fw-modaldialog">
        <div class="fw-modal_wrap">
            <div class="fw-modal_delete">
                <a href="#" class="fw-delete-icon" wire:click="closeDeclinePopup"><i class="fw-icon-trash-03"></i></a>
                <span>{{ __('forumwise::forum_wise.confirm') }}</span>
                <em>{{ __('forumwise::forum_wise.are_you_sure_you_wanna_do_that') }}</em>
                <div class="fw-popup-btn">
                    <button type="button" wire:click="closeDeclinePopup" class="fw-button-no fw-removemodal">{{ __('forumwise::forum_wise.no') }}</button>
                    <button type="button" wire:click="invitationAction('rejected')" class="fw-btn-danger">{{ __('forumwise::forum_wise.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>