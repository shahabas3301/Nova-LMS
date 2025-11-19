<div wire:ignore.self class="modal fade tb-addonpopup" id="add-badge-popup" data-bs-backdrop="static"
    x-data="{ isDragging: false }"
    x-on:dragover.prevent="isDragging = true"
    x-on:drop="isDragging = false"
    >
    <div class="modal-dialog modal-dialog-centered modal-lg tb-modaldialog tb-badge-popup" role="document">
        <div class="modal-content">
            <div class="tb-popuptitle">
                <h5 id="tb_badge_info_label">{{ $badgeId ? __('starup::starup.edit_badge') : __('starup::starup.add_new_badge') }}</h5>
                <a href="javascript:void(0);" class="close" wire:click="closeBadgeModal"><i class="icon-x"></i></a>
            </div>
            <div class="modal-body">
                <form class="tb-themeform" wire:submit.prevent="addBadge" id="add_badge_form">
                    <fieldset>
                            <div class="form-group {{ $badgeId ? 'tb-field-disabled' : '' }}">
                                <label class="tb-label tb-important" for="selectedCategory">{{ __('starup::starup.category') }}</label>
                                <div @class(['form-control_wrap am-select-w-full ', 'tk-invalid' => $errors->has('selectedCategory')])>
                                    <span class="am-select" wire:ignore>
                                        <select data-componentid="@this" class="am-custom-select" placeholder="{{ __('starup::starup.select_a_category') }}" data-searchable="true" wire:key="{{ time() }}" data-parent="#add-badge-popup" id="selectedCategory" data-wiremodel="selectedCategory">
                                            <option value="">{{ __('starup::starup.select_a_category') }}</option>
                                            @foreach($categories as $id => $name)
                                            <option value="{{ $id }}" {{ $id == $selectedCategory ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                        </select>
                                    </span>
                                </div>
                                @error('selectedCategory')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                @enderror
                            </div>  
                            <div class="form-group">
                                <label class="tb-label tb-important">{{__('starup::starup.name')}}</label>
                                <input type="text"
                                    class="form-control @error('badgeName') tk-invalid @enderror"
                                    wire:model="badgeName" placeholder="{{__('starup::starup.name_placeholder')}}">
                                @error('badgeName')
                                <div class="tk-errormsg">
                                    <span>{{$message}}</span>
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="tb-label">{{__('starup::starup.description')}}</label>
                                <div class="am-custom-editor" wire:ignore>
                                    <textarea id="badgeDescription" class="form-control" 
                                        wire:model="badgeDescription"
                                        placeholder="{{__('starup::starup.description_placeholder')}}"></textarea>
                                </div>
                                <div class='tb-group-badges'>
                                    @error('badgeDescription')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror
                                    <span class="total-characters">
                                            <div class='tb-input-counter'>
                                                <span>{{ __('general.char_left') }}:</span>
                                                <b>
                                                    {!! $MAX_PROFILE_CHAR - Str::length($badgeDescription) !!}
                                                </b> <em>/ {{ $MAX_PROFILE_CHAR }}</em>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            @if($selectedCategory == '1')
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="tb-label tb-important">{{__('starup::starup.rating')}}</label>
                                    <input type="text"
                                        class="form-control @error('badgeRating') tk-invalid @enderror"
                                        wire:model="badgeRating" placeholder="{{__('starup::starup.rating_placeholder')}}">
                                        <small style="display: block; margin-top: 5px; color: #6c757d;">
                                            {{__('starup::starup.minimum_avg_dec')}}
                                        </small>
                                    @error('badgeRating')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="tb-label tb-important">{{__('starup::starup.total_reviews')}}</label>
                                    <input type="text"
                                        class="form-control @error('badgeReviews') tk-invalid @enderror"
                                        wire:model="badgeReviews" placeholder="{{__('starup::starup.total_reviews_placeholder')}}">
                                        <small style="display: block; margin-top: 5px; color: #6c757d;">
                                            {{__('starup::starup.minimum_reviews_dec')}}
                                        </small>
                                    @error('badgeReviews')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            @elseif($selectedCategory == '2')
                            <div class="form-group">
                                <label class="tb-label">{{__('starup::starup.total_session')}}</label>
                                <input type="text"
                                    class="form-control @error('badgeSession') tk-invalid @enderror"
                                    wire:model="badgeSession" placeholder="{{__('starup::starup.total_session_placeholder')}}">
                                    <small style="display: block; margin-top: 5px; color: #6c757d;">
                                        {{__('starup::starup.minimum_session_dec')}}
                                    </small>
                                    @error('badgeSession')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror   
                            </div>
                            @elseif($selectedCategory == '3')
                            <div class="form-group">
                                <label class="tb-label">{{__('starup::starup.badge_criteria')}}</label>
                                <div class="tb-radiobox-wrap">
                                    <div class="tb-radiobox">
                                        <input wire:model="profileComplete" type="radio" id="profileComplete" name="badgeStatus" value="1">
                                        <label for="profileComplete">
                                            {{ __('starup::starup.profile_complete') }}
                                        </label>
                                        <small style="display: block; margin-top: 5px; color: #6c757d;">
                                            {{__('starup::starup.profile_complete_dec')}}
                                        </small>
                                    </div>
                                    <div class="tb-radiobox">
                                        <input wire:model="trusted" type="radio" id="trusted" name="badgeStatus" value="1">
                                        <label for="trusted">
                                            {{ __('starup::starup.trusted') }}
                                        </label>
                                        <small style="display: block; margin-top: 5px; color: #6c757d;">
                                            {{__('starup::starup.trusted_dec')}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @elseif($selectedCategory == '4')
                            <div class="form-group">
                                <label class="tb-label">{{__('starup::starup.completed_sessions_count')}}</label>
                                <input type="text"
                                    class="form-control @error('sessionsCount') tk-invalid @enderror"
                                    wire:model="sessionsCount" placeholder="{{__('starup::starup.completed_sessions_count_placeholder')}}">
                                    <small style="display: block; margin-top: 5px; color: #6c757d;">
                                        {{__('starup::starup.minimum_completed_session_dec')}}
                                    </small>
                                    @error('sessionsCount')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror   
                            </div>
                            @elseif($selectedCategory == '5')
                            <div class="form-group">
                                <label class="tb-label">{{__('starup::starup.rehired_booking_count')}}</label>
                                <input type="text"
                                    class="form-control @error('bookingCount') tk-invalid @enderror"
                                    wire:model="bookingCount" placeholder="{{__('starup::starup.rehired_booking_count_placeholder')}}">
                                    <small style="display: block; margin-top: 5px; color: #6c757d;">
                                        {{__('starup::starup.minimum_rehired_session_dec')}}
                                    </small>
                                    @error('bookingCount')
                                    <div class="tk-errormsg">
                                        <span>{{$message}}</span>
                                    </div>
                                    @enderror   
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="tb-label tb-important">{{__('starup::starup.badge_icon')}}</label>
                                <div class="tb-draganddrop" x-data="{isUploading:false}" wire:key="uploading-badge-{{ time() }}">
                                    <div class="tb-uploadoption @error('badgeImage') tk-invalid @enderror"
                                        x-bind:class="{ 'tb-dragfile' : isDragging, 'tb-uploading' : isUploading  }"
                                        x-on:drop.prevent="isUploading = true;isDragging = false"
                                        wire:drop.prevent="$upload('badgeImage', $event.dataTransfer.files[0])">
                                        <input class="tb-form-control" 
                                            name="file" 
                                            type="file" 
                                            id="upload_files" 
                                            x-ref="file_upload"
                                            accept="{{ !empty($allowImgFileExt) ?  join(',', array_map(function($ex){return('.'.$ex);}, $allowImgFileExt)) : '*' }}"
                                            x-on:change="isUploading = true; $wire.upload('badgeImage', $refs.file_upload.files[0])"
                                            >
                                        <label for="upload_files" class="tb-uploadfile">
                                            <svg class="tb-border-svg "><rect width="100%" height="100%"></rect></svg>
                                            <em>
                                                <i class="icon-upload"></i>
                                            </em>
                                            <span>{{ __('starup::starup.drop_file_here_or') }} <i> {{ __('starup::starup.click_here_file') }} </i> {{ __('starup::starup.to_upload') }} <em>{{ implode(', ', $allowImgFileExt) }} (max. {{ $allowImageSize }} MB). {{ __('starup::starup.recommended_size') }}</em></span>
                                        </label>
                                    </div>
                                    @if(!empty($badgeImage))
                                    <div class="tb-uploadedfile">
                                        @if (method_exists($badgeImage,'temporaryUrl'))
                                        <figure>
                                            <img src="{{ $badgeImage->temporaryUrl() }}">
                                        </figure>
                                        @else
                                        <figure>
                                            <img src="{{ Storage::url($badgeImage) }}">
                                        </figure>
                                        @endif
                                        @if (method_exists($badgeImage,'temporaryUrl'))
                                        <span>{{ basename(parse_url($badgeImage->temporaryUrl(), PHP_URL_PATH)) }}</span>
                                        @endif
                                        <a href="#" wire:click.prevent="removePhoto()" class="tb-delitem">
                                            <i class="icon-trash-2"></i>
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                @error('badgeImage')
                                <div class="tk-errormsg">
                                    <span>{{$message}}</span>
                                </div>
                                @enderror
                            </div>
                            <div class="form-group tb-formbtn">
                                <button type='button' href="javascript:void(0);" class="tb-btn tb-cancel" data-bs-dismiss="modal">{{__('starup::starup.cancel')}}</button>
                                <button class="tb-btn" type="submit" wire:target="addBadge"
                                    wire:loading.class="am-btn_disable">{{__('starup::starup.save_badge')}}</button>
                            </div>
                            </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@push('styles')
@vite([
'public/summernote/summernote-lite.min.css',
])
@endpush
@push('scripts')
<script defer src="{{ asset('summernote/summernote-lite.min.js')}}"></script>
@endpush
<script type="text/javascript">
    document.addEventListener('livewire:initialized', function() { 
        var component = @this;
        $(document).on('show.bs.modal','#add-badge-popup', function () {            
            var initialContent = component.get('badgeDescription');
            $('#badgeDescription').summernote('destroy');
            $('#badgeDescription').summernote(summernoteConfigs('#badgeDescription', '.total-characters'));
            $('#badgeDescription').summernote('code', initialContent);

            $(document).on('summernote.change', '#badgeDescription', function(we, contents, $editable) {             
                component.set("badgeDescription",contents, false);
                updateCharacterCounter();
            });
            updateCharacterCounter();

            $('#selectedCategory').select2({
                dropdownParent: $('#add-badge-popup')
            });
            $('#selectedCategory').val(component.selectedCategory).trigger('change');
        });
        function updateCharacterCounter() {

            let contentLength = $('#badgeDescription').summernote('code').replace(/(<([^>]+)>)/gi, "").length;
            let maxChars = {!! $MAX_PROFILE_CHAR !!};
            let charsLeft = maxChars - contentLength;

            $('.total-characters b').text(charsLeft);
        }
        jQuery(document).on('change', '.am-custom-select', function(e){
            component.set('selectedCategory', jQuery('.am-custom-select').select2("val"), true);
        });
    });
</script>

