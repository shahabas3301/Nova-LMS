<main class="tb-main tb-blogwrap">
    <div class="row">
        @include('forumwise::components.update')
        <div class="col-lg-8 col-md-12 tb-md-60">
            <div class="tb-dhb-mainheading">
                <h4>{{ __('forumwise::forum_wise.category_text') }}</h4>
                <div class="tb-sortby">
                    <form class="tb-themeform tb-displistform">
                        <fieldset>
                            <div class="tb-themeform__wrap">
                                <div class="tb-actionselect">
                                    <a href="javascript:;" class="tb-btn btnred {{ $selectedCategories ? '' : 'd-none' }}" @click="$wire.dispatch('showConfirm', { action : 'delete-category' })">{{ __('forumwise::forum_wise.delete_selected') }}</a>
                                </div>
                                <div class="tb-actionselect">
                                    <div class="tb-select" wire:ignore>
                                        <select class="am-select2" id="sortBy" data-searchable="false">
                                            <option value="desc">{{ __('forumwise::forum_wise.desc') }}</option>
                                            <option value="asc">{{ __('forumwise::forum_wise.asc') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="tb-actionselect">
                                    <div class="tb-select" wire:ignore>
                                        <select class="form-control am-select2" id="perPage" data-searchable="false">
                                            @foreach($per_page_opt as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group tb-inputicon tb-inputheight">
                                    <i class="icon-search"></i>
                                    <input type="text" class="form-control" wire:model.live.debounce.500ms="search" autocomplete="off" placeholder="{{ __('forumwise::forum_wise.search') }}">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="tb-disputetable tb-db-categoriestable">
                @if(!$categories->isEmpty())
                    <div class="tb-table-wrap">
                        <table class="table tb-table tb-dbholder">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="tb-checkbox">
                                            <input id="checkAll" wire:model.live="selectAll" type="checkbox">
                                            <label for="checkAll">{{ __('forumwise::forum_wise.id') }}</label>
                                        </div>
                                    </th>
                                    <th>{{ __('forumwise::forum_wise.category_name') }}</th>
                                    <th>{{ __('forumwise::forum_wise.label_color') }}</th>
                                    <th>{{ __('forumwise::forum_wise.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $single)
                                    <tr>
                                        <td data-label="{{ __('forumwise::forum_wise.id') }}">
                                            <div class="tb-namewrapper">
                                                <div class="tb-checkbox">
                                                    <input id="category_id{{ $single->id }}" wire:model.lazy="selectedCategories" value="{{ $single->id }}" type="checkbox">
                                                    <label for="category_id{{ $single->id }}">
                                                        <span>
                                                            {{ $single->id }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="{{ __('forumwise::forum_wise.category_name') }}">
                                            {{ $single->name }}
                                        </td>
                                        <td data-label="{{ __('forumwise::forum_wise.label_color') }}">
                                            <span class="fw-forum-title-mark" style="background-color: {{ $single->label_color }};"> </span>
                                        </td>
                                        <td data-label="{{ __('forumwise::forum_wise.actions') }}">
                                            <ul class="tb-action-icon">
                                                <li><a href="javascript:void(0);" wire:click="edit({{ $single->id }})"><i class="icon-edit-3"></i></a></li>
                                                <li>    
                                                    <a href="javascript:void(0);" 
                                                    @click="$wire.dispatch('showConfirm', { id: {{ $single->id }}, action: 'delete-category' })" 
                                                    class="tb-delete">
                                                    <i class="icon-trash-2"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $categories->links('pagination.custom') }}
                @else
                <x-no-record :image="asset('images/empty.png')" :title="__('forumwise::forum_wise.no_record_title')"/>
                @endif
            </div>
        </div>
    </div>
</main>
@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', function() {

            let title           = '{{ __("forumwise::forum_wise.confirm") }}';
            let listenerName    = 'delete-category-confirm';
            let content         = '{{ __("forumwise::forum_wise.confirm_content") }}';
            let action          = 'deleteConfirmRecord'; 
            jQuery('#sortBy').on('change', function() {
                let sortByValue = jQuery(this).val();
                @this.set('sortby', sortByValue);
            });

            jQuery('#perPage').on('change', function() {
                let perPageValue = jQuery(this).val();
                @this.set('per_page', perPageValue);
            });

            jQuery('.am-select2').each((index, item) => {
                let _this = jQuery(item);
                searchable = _this.data('searchable');
                let params = {
                    dropdownCssClass: _this.data('class'),
                    placeholder: _this.data('placeholder'),
                    allowClear: true
                }
                if(!searchable){
                    params['minimumResultsForSearch'] = Infinity;
                }
                _this.select2(params);

            });                
        });
        
    </script>
@endpush
