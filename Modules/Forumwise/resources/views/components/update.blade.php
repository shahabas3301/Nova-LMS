<div class="col-lg-4 col-md-12 tb-md-40">
    <div class="tb-dbholder tb-packege-setting">
        <div class="tb-dbbox tb-dbboxtitle">
            <h5>
                 {{ $editMode ? __('forumwise::forum_wise.update_category') : __('forumwise::forum_wise.add_category') }}
            </h5>
        </div>
        <div class="tb-dbbox">
            <form class="tk-themeform tk-form-blogcategories">
                <fieldset>
                    <div class="tk-themeform__wrap">
                        <div class="form-group">
                            <label class="tb-label">{{ __('forumwise::forum_wise.category_name') }}</label>
                            <input type="text" class="form-control @error('name') tk-invalid @enderror"   wire:model="name" required placeholder="{{ __('forumwise::forum_wise.category_placeholder_name') }}">
                            @error('name')
                                <div class="tk-errormsg">
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="kd-colorpicker_wrap form-group">
                            <label class="tb-label">{{ __('forumwise::forum_wise.label_color') }}</label>
                            <div class="kd-colorpicker" wire:ignore>
                                <div class="myColorPicker">
                                    <input type="text" class="form-control" wire:model="label_color">
                                    <span class="kd-inputbtn">
                                        <span class="input-group-addon kd-colordemo myColorPicker-preview">&nbsp;</span>
                                    </span>
                                </div>
                            </div>
                            @error('label_color')
                                <div class="tk-errormsg">
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group tb-dbtnarea">
                            <a href="javascript:void(0);" wire:loading.class="tb-btn_disable" wire:loading.attr="disabled" wire:target="update" wire:click.prevent="update" class="tb-btn">
                                {{ $editMode ? __('forumwise::forum_wise.update_category') : __('forumwise::forum_wise.add_category') }}
                            </a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/forumwise/css/jquery.colorpicker.bygiro.css') }}">
@endpush

@push('scripts')
    <script defer src="{{ asset('modules/forumwise/js/jquery.colorpicker.bygiro.js') }}"></script>
    <script>
        window.addEventListener('initColorPicker', (event) => {
            let { color } = event.detail;
            setTimeout(() => {
                let $colorPicker = $('.myColorPicker').colorPickerByGiro({
                    preview: '.myColorPicker-preview',
                    text: {
                        close: 'Confirm',
                        none: 'None',
                    },
                    options: {
                        defaultColor: color
                    },
                    onColorSelected: function (selectedColor) {
                },
                });

                $('.myColorPicker-preview').css('background-color', color);
                jQuery('.kd-colorpicker input').val(color).trigger('change');

                setTimeout(() => {
                    $('.myColorPicker').find('.colorPicker--preview').css('background-color', color);
                    $('.myColorPicker').find('.colorPicker input').val(color);
                }, 50);
            }, 100);
        });
        jQuery(document).on('change', '.kd-colorpicker input', function() {
            let getcolor =  jQuery(this).val();
            let setcolor = jQuery('.colorPicker--preview')
            jQuery(setcolor).css("background-color", getcolor);
            @this.set('label_color', getcolor);
        });
    </script>
@endpush
