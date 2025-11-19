<main class="tb-main tb-badge-list-form am-dispute-system" wire:init="loadData">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="tb-dhb-mainheading">
                <h4> {{ __('starup::starup.all_badges') .' ('. $badges->total() .')'}}</h4>
                <div class="tb-sortby">
                    <form class="tb-themeform tb-displistform">
                        <fieldset>
                            <div class="tb-themeform__wrap">    
                                <div class="tb-actionselect">
                                    <a href="javascript:void(0)" wire:click="openAddBadgeModal" id="add_badge_click" class="tb-btn add-new">{{__('starup::starup.add_new_badge')}}
                                        <i class="icon-plus"></i></a>
                                </div>
                                <div class="tb-actionselect" wire:ignore>
                                    <div class="tb-select">
                                        <select data-componentid="@this" class="am-select2 form-control" data-searchable="true"
                                            data-searchable="false" data-live='true' id="category" data-wiremodel="category">
                                            <option value="" {{ $category=='' ? 'selected' : '' }}>{{ __('starup::starup.all_badges') }}
                                            </option>
                                            @foreach($categories as $id => $name)
                                                <option value="{{ $id }}" {{ $id==$category ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="tb-actionselect" wire:ignore>
                                    <div class="tb-select">
                                        <select data-componentid="@this" class="am-select2 form-control"
                                            data-searchable="false" data-live='true' id="sort_by" data-wiremodel="sortby">
                                            <option value="asc" {{ $sortby=='asc' ? 'selected' : '' }}>{{ __('starup::starup.asc')
                                                }}</option>
                                            <option value="desc" {{ $sortby=='desc' ? 'selected' : '' }}>{{
                                                __('starup::starup.desc') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group tb-inputicon tb-inputheight">
                                    <i class="icon-search"></i>
                                    <input type="text" class="form-control" wire:model.live.debounce.500ms="search"
                                        autocomplete="off" placeholder="{{ __('starup::starup.search') }}">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="am-disputelist_wrap">
                <div class="am-disputelist am-custom-scrollbar-y">
                    <table class="tb-table @if(setting('_general.table_responsive') == 'yes') tb-table-responsive @endif">
                        <thead>
                            <tr>
                                <th>{{ __('#' )}}</th>
                                <th>{{ __('starup::starup.name' )}}</th>
                                <th>{{ __('starup::starup.description' )}}</th>
                                <th>{{ __('starup::starup.category' )}}</th>
                                <th>{{ __('starup::starup.created_date' )}}</th>
                                <th>{{__('starup::starup.actions')}}</th>
                            </tr>
                        </thead>
                        @if(!$isLoading)
                            <tbody class="d-none" wire:loading.class.remove="d-none" wire:target="search,category,sortby">
                                @include('starup::skeletons.badge', ['perPage' => $perPage])
                            </tbody>
                            <tbody wire:loading.class="d-none" wire:target="search,category,sortby">
                                @if(!$badges->isEmpty() )
                                    @foreach($badges as $single)
                                        <tr>
                                            <td data-label="{{ __('#' )}}"><span>{{ $single?->id }}</span></td>
                                            <td data-label="{{ __('starup::starup.name' )}}">
                                                <div class="tb-varification_userinfo">
                                                    <strong class="tb-adminhead__img">
                                                        @if (!empty($single?->image) && Storage::disk(getStorageDisk())->exists($single?->image))
                                                            <img src="{{ resizedImage($single?->image,34,34) }}" alt="{{$single?->image}}" />
                                                        @else
                                                            <img src="{{ setting('_general.default_avatar_for_user') ? url(Storage::url(setting('_general.default_avatar_for_user')[0]['path'])) : resizedImage('placeholder.png', 34, 34) }}" alt="{{ $single?->image }}" />
                                                        @endif
                                                    </strong>
                                                    <span>{{ $single?->name }}</span>
                                                </div>
                                            </td>
                                            <td data-label="{{ __('starup::starup.description' )}}"><span>{{ $single?->description }}</span></td>
                                            <td data-label="{{ __('starup::starup.category' )}}"><span>{{ $single?->category?->name }}</span></td>
                                            <td data-label="{{ __('starup::starup.created_date' )}}"><span>{{ $single?->created_at->format('F d, Y')}}</span></td>
                                            <td  data-label="{{__('starup::starup.actions')}}">
                                                <div class="am-custom-tooltip">
                                                    <i @click="$wire.openAddBadgeModal({{ $single?->id }})" class="icon-edit"></i>
                                                    <i @click="$wire.dispatch('showConfirm', { id : {{ $single?->id }}, content: '{{ __('starup::starup.delete_badge') }}', action : 'delete-badge' })" class="icon-trash-2"></i>
                                                </div> 
                                            </td>
                                        </tr>   
                                    @endforeach
                                @endif
                            </tbody>
                        @else
                            <tbody>
                                 @include('starup::skeletons.badge', ['perPage' => $perPage])
                            </tbody>
                        @endif
                    </table>
                    @if($badges->isEmpty() && !$isLoading)
                        <div class="am-disputelist-empty" wire:loading.class="d-none" wire:target="search,category,sortby">
                            <x-no-record :image="asset('images/empty.png')"  :title="__('starup::starup.no_record_title')"/>
                        </div>
                    @endif
                </div>
                @if(!$badges->isEmpty() && !$isLoading)
                    {{ $badges->links('pagination.custom') }}
                @endif
            </div>
        </div>
        @include('starup::components.badge-popup')
    </div>
</main>