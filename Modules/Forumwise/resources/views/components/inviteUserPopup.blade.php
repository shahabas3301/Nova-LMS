<div wire:ignore.self id="fw-invite-popup" class="fw-modal fw-addforumpopup">
    <div class="fw-modaldialog">
        <div class="fw-modal_wrap">
            <div class="fw-modal_title">
                <h2>{{ __('forumwise::forum_wise.send_invite') }}</h2>
                <a href="javascript:void(0);" wire:click="closeInviteUserPopup" class="fw-removemodal"><i class="fw-icon-multiply-02"></i></a>
            </div>
            <div class="fw-modal_body fw-modal-question">
                <form class="fw-themeform">
                    <fieldset>
                        <div class="fw-form-group @error('email') fw-invalid @enderror">
                            <label for="email" class="fw-important">{{ __('forumwise::forum_wise.email') }}</label>
                            <div class="fw-form-control-wrap">
                                <input type="text" id="email" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.enter_email') }}" wire:model="email">
                                <x:forumwise::input-error field_name="email"/>
                            </div>
                        </div>
                        <div class="fw-form-group fw-description @error('message') fw-invalid @enderror">
                            <label for="message" class="fw-important">{{ __('forumwise::forum_wise.message') }}</label>
                            <div class="fw-form-control-wrap">
                                <textarea name="" id="message" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.message') }}" wire:model="message" maxlength="150" oninput="updateCharacterCount(this)"></textarea>
                            </div>
                            <span class="fw-formcharacters" id="messageCount">150 {{ __('forumwise::forum_wise.characters_left') }}</span>
                            <x:forumwise::input-error field_name="message"/>
                        </div>
                        <div class="fw-form-group fw-form-group-btns">
                            <a href="javascript:void(0)" wire:click="sendInvite" wire:loading.class="fw-btn_disable" class="fw-btn fw-purple-btn">{{ __('forumwise::forum_wise.send') }}</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function updateCharacterCount(textarea) {
        const maxLength = textarea.getAttribute('maxlength');
        const currentLength = textarea.value.length;
        const remaining = maxLength - currentLength;
        document.getElementById('messageCount').innerText = `${remaining} characters left`;
    }
    document.addEventListener('livewire:navigated', function() {
        component = @this;
    });
    document.addEventListener('livewire:initialized', function() {
        $(document).on("change", ".roles", function(e){
            component.set('email', $(this).val(), true);
        });
    });
</script>