<div wire:ignore.self id="fw-askQuestionpopup" 
    x-data="{ isDragging: false }"
    x-on:dragover.prevent="isDragging = true"
    x-on:drop="isDragging = false"
    class="fw-modal fw-askquestionpopup fw-askquestion-popup">
    <div class="fw-modaldialog">
        <div class="fw-modal_wrap">
            <div class="fw-modal_title">
                <h2>{{ __('forumwise::forum_wise.ask_question') }}</h2>
                <a href="javascript:void(0);" class="fw-removemodal" wire:click="closeAskQuestionModal"><i class="fw-icon-multiply-02"></i></a>
            </div>
            <div class="fw-modal_body">
                <form class="fw-themeform">
                    <fieldset>
                        <div class="fw-form-group @error('questionTitle') fw-invalid @enderror">
                            <label for="title" class="fw-important">{{ __('forumwise::forum_wise.write_subject') }}</label>
                            <div class="fw-form-control-wrap">
                                <input type="text" id="title" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.write_subject') }}" wire:model="questionTitle">
                                <x:forumwise::input-error field_name="questionTitle"/>
                            </div>
                        </div>
                        <div class="fw-selectlist fw-form-group @error('questionCategory') fw-invalid @enderror">
                            <label for="fw-categories" class="fw-important">{{ __('forumwise::forum_wise.select_category') }}</label>
                            <div class="select" wire:ignore>
                                <select class="fw-select2" data-componentid="@this" wire:model="questionCategory" data-parent="#fw-askQuestionpopup"  data-placeholder="{{ __('forumwise::forum_wise.select_category') }}" data-wiremodel="questionCategory" id="fw-categories">
                                    <option value="">{{ __('forumwise::forum_wise.select_category') }}</option>
                                </select>
                            </div>
                            <x:forumwise::input-error field_name="questionCategory"/>
                        </div>
                        <div class="fw-form-group fw-description @error('questionDescription') fw-invalid @enderror">
                            <label for="description" class="fw-important">{{ __('forumwise::forum_wise.add_your_comment') }}</label>
                            <div class="fw-form-control-wrap">
                                <textarea name="" id="description" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.add_your_comment') }}" wire:model="questionDescription" maxlength="150" oninput="updateCharacterCount(this)"></textarea>
                            </div>
                            <span class="fw-formcharacters" id="descriptionCount">767 {{ __('forumwise::forum_wise.characters_left') }}</span>
                            <x:forumwise::input-error field_name="questionDescription"/>
                        </div>
                        
                        <div class="fw-form-group">
                            <div class="fw-draganddrop" x-data="{isUploading:false}" wire:key="uploading-forum-{{ time() }}">
                                <div class="fw-uploadoption"
                                    x-bind:class="{ 'fw-dragfile' : isDragging, 'fw-uploading' : isUploading  }"
                                    x-on:drop.prevent="isUploading = true;isDragging = false"
                                    wire:drop.prevent="$upload('questionImage', $event.dataTransfer.files[0])">
                                    <input class="fw-form-control" 
                                        name="file" 
                                        type="file" 
                                        id="fw_upload_file" 
                                        x-ref="file_upload"
                                        accept="{{ !empty($allowImgFileExt) ?  join(',', array_map(function($ex){return('.'.$ex);}, $allowImgFileExt)) : '*' }}"
                                        x-on:change="isUploading = true; $wire.upload('questionImage', $refs.file_upload.files[0])"
                                        >
                                    <label for="fw_upload_file" class="fw-uploadfile">
                                        <span class="fw-dropfileshadow">
                                            <svg class="fw-border-svg "><rect width="100%" height="100%"></rect></svg>
                                            <i class="fw-icon-plus-02"></i>
                                            <span class="fw-uploadiconanimation">
                                                <i class="fw-icon-upload-03"></i>
                                            </span>
                                            Drop File Here
                                        </span>
                                        <em>
                                            <i class="fw-icon-export-03"></i>
                                        </em>
                                        
                                        <span>{{ __('forumwise::forum_wise.drop_file_here_or') }} <i> {{ __('forumwise::forum_wise.click_here_file') }} </i> {{ __('forumwise::forum_wise.to_upload') }} <em>{{ implode(', ', $allowImgFileExt) }} (max. {{ $allowImageSize }} MB)</em></span>
                                    </label>
                                </div>
                                @if(!empty($questionImage))
                                <div class="fw-uploadedfile">
                                    @if (method_exists($questionImage,'temporaryUrl'))
                                    <img src="{{ $questionImage->temporaryUrl() }}">
                                    @else
                                    <img src="{{ Storage::url($questionImage) }}">
                                    @endif
                                    @if (method_exists($questionImage,'temporaryUrl'))
                                    <span>{{ basename(parse_url($questionImage->temporaryUrl(), PHP_URL_PATH)) }}</span>
                                    @endif
                                    <a href="#" wire:click.prevent="removePhoto()" class="fw-delitem">
                                        <i class="fw-icon-trash-02"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="fw-form-group fw-form-group-btns">
                            <a href="javascript:void(0);" class="fw-white-btn" wire:click="closeAskQuestionModal">Cancel</a>
                            <button type="button" wire:click="saveForum" class="fw-btn fw-purple-btn">Save</button>
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
        document.getElementById('descriptionCount').innerText = `${remaining} characters left`;
    }
    document.addEventListener('livewire:navigated', function() {
        component = @this;
    });
    document.addEventListener('livewire:initialized', function() {
        $(document).on("change", ".roles", function(e){
            component.set('form.topic_role', $(this).select2("val"), true);
        });
    });
</script>