<div wire:ignore.self id="fw-addforumpopup" 
    x-data="{ isDragging: false }"
    x-on:dragover.prevent="isDragging = true"
    x-on:drop="isDragging = false"
    class="fw-modal fw-addforumpopup fw-topicroll-popup">
    <div class="fw-modaldialog">
        <div class="fw-modal_wrap">
            <div class="fw-modal_title">
                <h2>{{ __('forumwise::forum_wise.forum') }}</h2>
                <a href="javascript:void(0);" class="fw-removemodal" wire:click="closeForumModal"><i class="fw-icon-multiply-02"></i></a>
            </div>
            <div class="fw-modal_body">
                <form class="fw-themeform">
                    <fieldset>
                        <div class="fw-form-group @error('form.title') fw-invalid @enderror">
                            <label for="title" class="fw-important">{{ __('forumwise::forum_wise.title') }}</label>
                            <div class="fw-form-control-wrap">
                                <input type="text" id="title" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.enter_title') }}" wire:model="form.title">
                                <x:forumwise::input-error field_name="form.title"/>
                            </div>
                        </div>
                        
                        <div class="fw-selectlist fw-form-group @error('form.topic_role') fw-invalid @enderror">
                            <label for="fw-role" class="fw-important">{{ __('forumwise::forum_wise.topic_role') }}</label>
                            <div class="select" wire:ignore>
                                <select class="fw-select2 roles" data-componentid="@this" wire:model="form.topic_role" data-parent="#fw-addforumpopup"  data-placeholder="{{ __('forumwise::forum_wise.select_role') }}" data-wiremodel="form.topic_role" id="fw-topic_role" multiple>
                                    <option value="">{{ __('forumwise::forum_wise.select_role') }}</option>
                                    @foreach (config('forumwise.db.roles') as $key => $role)
                                            @if($role != 'admin')
                                                <option value="{{ $role }}">{{ ucfirst($role )}}</option>
                                            @endif
                                    @endforeach
                                </select>
                            </div>
                            <x:forumwise::input-error field_name="form.topic_role"/>
                            <div class="languageList">
                                <ul class="tu-labels">
                                   @foreach($form?->topic_role as $role)                                   
                                        <li>
                                            <span>{{ ucfirst($role) }} <a href="javascript:void(0)" wire:click="removeTopicRole('{{ $role }}')"><i class="fw-icon-multiply-02"></i></a></span>
                                        </li>
                                     @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="fw-selectlist fw-form-group @error('form.category_id') fw-invalid @enderror">
                            <label for="fw-categories" class="fw-important">{{ __('forumwise::forum_wise.category') }}</label>
                            <div class="select" wire:ignore>
                                <select class="fw-select2" data-componentid="@this" wire:model="form.category_id" data-parent="#fw-addforumpopup"  data-placeholder="{{ __('forumwise::forum_wise.select_category') }}" data-wiremodel="form.category_id" id="fw-categories">
                                    <option value="">{{ __('forumwise::forum_wise.select_category') }}</option>
                                    @if (!empty($categories))
                                        @foreach ($categories as $category)
                                        <option  value="{{ $category?->id }}">{{ $category?->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <x:forumwise::input-error field_name="form.category_id"/>
                        </div>
                        <div class="fw-form-group fw-description @error('form.description') fw-invalid @enderror">
                            <label for="description" class="fw-important">{{ __('forumwise::forum_wise.description') }}</label>
                            <div class="fw-form-control-wrap">
                                <textarea name="" id="description" class="fw-form-control" placeholder="{{ __('forumwise::forum_wise.description_placeholder') }}" wire:model="form.description" maxlength="150" oninput="updateCharacterCount(this)"></textarea>
                            </div>
                            <span class="fw-formcharacters" id="descriptionCount">150 {{ __('forumwise::forum_wise.characters_left') }}</span>
                            <x:forumwise::input-error field_name="form.description"/>
                        </div>

                        <div class="fw-toggle fw-form-group">
                            <div class="fw-toggle-content @error('form.title') fw-invalid @enderror"">
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
                                            {{ __('forumwise::forum_wise.drop_file_here') }}
                                        </span>
                                        <em>
                                            <i class="fw-icon-export-03"></i>
                                        </em>
                                        
                                        <span>{{ __('general.drop_file_here_or') }} <i> {{ __('general.click_here_file') }} </i> {{ __('general.to_upload') }} <em>{{ implode(', ', $allowImgFileExt) }} (max. {{ $allowImageSize }} MB)</em></span>
                                    </label>
                                </div>
                                @if(!empty($form->image))
                                <div class="fw-uploadedfile">
                                    @if (method_exists($form?->image,'temporaryUrl'))
                                    <img src="{{ $form?->image->temporaryUrl() }}">
                                    @else
                                    <img src="{{ Storage::url($form->image) }}">
                                    @endif
                                    @if (method_exists($form?->image,'temporaryUrl'))
                                    <span>{{ basename(parse_url($form?->image->temporaryUrl(), PHP_URL_PATH)) }}</span>
                                    @endif
                                    <a href="#" wire:click.prevent="removePhoto()" class="fw-delitem">
                                        <i class="fw-icon-trash-02"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="fw-form-group fw-form-group-btns">
                            <a href="javascript:void(0);" class="fw-white-btn" wire:click="closeForumModal">{{ __('forumwise::forum_wise.cancel') }}</a>
                            <button type="button" wire:click="saveForum" wire:loading.class="fw-btn_disable" class="fw-btn fw-purple-btn">{{ __('forumwise::forum_wise.save') }}</button>
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