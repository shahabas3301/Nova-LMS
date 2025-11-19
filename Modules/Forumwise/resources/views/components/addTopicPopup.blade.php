<div wire:ignore.self id="fw-addtopic-popup" 
    x-data="{ isDragging: false }"
    x-on:dragover.prevent="isDragging = true"
    x-on:drop="isDragging = false"
    class="fw-modal fw-addtopic-popup">
    <div class="fw-modaldialog">
        <div class="fw-modal_wrap">
            <div class="fw-modal_title">
                <h2>{{ __('forumwise::forum_wise.create_topic') }}</h2>
                <a href="javascript:void(0);" class="fw-removemodal" wire:click="closeTopicModal"><i class="fw-icon-multiply-02"></i></a>
            </div>
            <div class="fw-modal_body fw-modal-question">
                <form class="fw-themeform">
                    <fieldset>
                        <div class="fw-form-group @error('form.title') fw-invalid @enderror">
                            <label for="title" class="fw-important">{{ __('forumwise::forum_wise.title') }}</label>
                            <div class="fw-form-control-wrap">
                                <input type="text" id="title" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.enter_title') }}" wire:model="form.title">
                                <x:forumwise::input-error field_name="form.title"/>
                            </div>
                        </div>
                        <div class="fw-toggle fw-form-group @error('form.status') fw-invalid @enderror">
                            <div class="fw-toggle-content">
                                <label for="" class="fw-important">{{ __('forumwise::forum_wise.type') }}</label>
                                <div class="fw-toggle-public">
                                    <div class="fw-togglebtn">
                                        <input type="checkbox" id="switch" wire:model.live="form.type">
                                        <label for="switch"></label>
                                    </div>
                                    <span>{{ $form?->type ? __('forumwise::forum_wise.public') : __('forumwise::forum_wise.private') }}</span>
                                </div>
                                <x:forumwise::input-error field_name="form.type"/>
                            </div>
                            <div class="fw-toggle-content @error('form.status') fw-invalid @enderror">
                                <label for="" class="fw-important">{{ __('forumwise::forum_wise.status') }}</label>
                                <div class="fw-toggle-public">
                                    <div class="fw-togglebtn">
                                        <input type="checkbox" id="switch1" wire:model.live="form.status">
                                        <label for="switch1"></label>
                                    </div>
                                    <span>{{ $form?->status ? __('forumwise::forum_wise.active') : __('forumwise::forum_wise.inactive') }}</span>
                                </div>
                                <x:forumwise::input-error field_name="form.status"/>
                            </div>
                        </div>
                        <div class="fw-selectlist fw-selectlist-multiple fw-form-group @error('form.tags') fw-invalid @enderror">
                            <label for="" class="fw-important">{{ __('forumwise::forum_wise.tags') }}</label>
                            <div class="select" wire:ignore>
                                <div x-data="{ 
                                    tags: @entangle('form.tags'),
                                    newTag: '',
                                    addTag() {
                                        if (this.newTag.trim() !== '' && !this.tags.includes(this.newTag.trim())) {
                                            this.tags.push(this.newTag.trim());
                                            this.newTag = '';
                                        }
                                    },
                                    removeTag(tag) {
                                        this.tags = this.tags.filter(t => t !== tag);
                                    }
                                }">
                                    <div class="tag-input-container">
                                        <input 
                                            type="text" 
                                            x-model="newTag"
                                            @keydown.enter.prevent="addTag"
                                            placeholder="{{ __('forumwise::forum_wise.add_tags') }}"
                                            class="fw-form-control"
                                        >
                                    </div>
                                    <div class="tag-list" x-show="tags.length > 0">
                                        <template x-for="tag in tags" :key="tag">
                                            <span class="tag">
                                                <span x-text="tag" class="fw-tag-content"> </span>
                                                <button @click="removeTag(tag)" type="button" class="fw-remove-tags">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <x:forumwise::input-error field_name="form.tags"/>
                        </div>
                        <div class="fw-form-group fw-description @error('form.description') fw-invalid @enderror">
                            <label for="description" class="fw-important">{{ __('forumwise::forum_wise.description') }}</label>
                            <div class="fw-form-control-wrap">
                                <textarea name="" id="description" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.description_placeholder') }}" wire:model="form.description" maxlength="150" oninput="updateCharacterCount(this)"></textarea>
                            </div>
                            <span class="fw-formcharacters" id="descriptionCount">150 characters left</span>
                            <x:forumwise::input-error field_name="form.description"/>
                        </div>

                        <div class="fw-form-group">
                            <div class="fw-draganddrop" x-data="{isUploading:false}" wire:key="uploading-forum-{{ time() }}">
                                <div class="fw-uploadoption"
                                    x-bind:class="{ 'fw-dragfile' : isDragging, 'fw-uploading' : isUploading  }"
                                    x-on:drop.prevent="isUploading = true;isDragging = false"
                                    wire:drop.prevent="$upload('form.image', $event.dataTransfer.files[0])">
                                    <input class="fw-form-control" 
                                        name="file" 
                                        type="file" 
                                        id="fw_upload_files" 
                                        x-ref="file_upload"
                                        accept="{{ !empty($allowImgFileExt) ?  join(',', array_map(function($ex){return('.'.$ex);}, $allowImgFileExt)) : '*' }}"
                                        x-on:change="isUploading = true; $wire.upload('form.image', $refs.file_upload.files[0])"
                                        >
                                    <label for="fw_upload_files" class="fw-uploadfile">
                                        <svg class="fw-border-svg "><rect width="100%" height="100%"></rect></svg>
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
                                
                                        <span>{{ __('general.drop_file_here_or') }} <i> {{ __('general.click_here_file') }} </i> {{ __('general.to_upload') }} <em>{{ implode(', ', $allowImgFileExt) }} (max. {{ $allowImageSize }} MB)</em></span>
                                    </label>
                                </div>
                                @if(!empty($form->image))
                                <div class="fw-uploadedfile">
                                    @if (method_exists($form->image,'temporaryUrl'))
                                    <img src="{{ $form->image->temporaryUrl() }}">
                                    @else
                                    <img src="{{ Storage::url($form->image) }}">
                                    @endif
                                    @if (method_exists($form->image,'temporaryUrl'))
                                    <span>{{ basename(parse_url($form->image->temporaryUrl(), PHP_URL_PATH)) }}</span>
                                    @endif
                                    <a href="#" wire:click.prevent="removePhoto()" class="fw-delitem">
                                        <i class="fw-icon-trash-02"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="fw-form-group fw-form-group-btns">
                            <a href="javascript:void(0);" class="fw-white-btn fw-removemodal" wire:click="closeTopicModal">Cancel</a>
                            <a href="javascript:void(0);" class="fw-btn fw-purple-btn" wire:click="saveTopic" wire:loading.class="fw-btn_disable">Save</a>
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
</script>
