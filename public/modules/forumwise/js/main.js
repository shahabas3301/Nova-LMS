(function($){
    "use strict";
	
	function fwShowAlert( data ){
		let { message, type, autoclose = 2000 } = data;
	
		$('.am-themetoast').addClass('show');
		$('.am-themetoast').removeClass('hide');
		$('.am-themetoast h6').text(message);
		let successIcon = `
		<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
			<path d="M3 9.75L6.75 13.5L15 4.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
		</svg>
		`;
		let errorIcon = `
		<svg width="32" height="32" viewBox="0 0 32 32" fill="none">
			<path d="M0 16C0 7.16344 7.16344 0 16 0V0C24.8366 0 32 7.16344 32 16V16C32 24.8366 24.8366 32 16 32V32C7.16344 32 0 24.8366 0 16V16Z" fill="#F04438"/>
			<path d="M11.5 20.5L20.5 11.5M11.5 11.5L20.5 20.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>`;
		$('.am-themetoast .am-toast-icon').html(type == 'success' ? successIcon : errorIcon);
		setTimeout(()=>{
			$('.am-themetoast').removeClass('show');
			$('.am-themetoast').addClass('hide');
		},autoclose)
	}

    
	if (typeof Livewire !== 'undefined') {
			
	    Livewire.on('fw-showAlertMessage', fwShowAlert);		
        Livewire.on('fw-toggleModel', event => {
			if (event.action === 'show') {
				$(`#${event.id}`).addClass('fw-modalopen');
				setTimeout(function(){
					$(`#${event.id}`).addClass('fw-fadin');
					$('.fw-overlaymodal').addClass('fw-fadin');
				}, 1);
				$('body').css('overflow', 'hidden');
				$('body').append("<div class='fw-overlaymodal'></div>");
			} else if (event.action === 'hide') {
				$(`#${event.id}`).removeClass('fw-fadin');
				$('.fw-overlaymodal').removeClass('fw-fadin');
				setTimeout(function(){
					$(`#${event.id}`).removeClass('fw-modalopen');
					$('.fw-overlaymodal').remove();
				}, 300);
				$('body').css({'overflow' : ''});
			}
		});
        
		Livewire.on('fw-initSelect2', event => {
			const { target, timeOut = 500 } = event;
			setTimeout(() => {
				$(event.target).each((index, item) => {
					let _this = $(item);
					let searchable = _this.data('searchable');
					let componentId = _this.data('componentid');
					let modelInput = _this.data('wiremodel');
					let live = _this.data('live') ?? false;
					let component = eval(componentId)
					let params = {
						placeholder: _this.data('placeholder'),
						allowClear: true,
					}
	
					let autoclose = _this.data('autoclose');
					if(event.data?.length){
						params['data'] = event.data;
					}
					if (autoclose == false) {
						params['closeOnSelect'] = false;
					}
	
					if (_this.data('hide_search_opt')) {
						params['minimumResultsForSearch'] = -1;
					}
	
					if (_this.data('parent')) {
						params['dropdownParent'] = $( _this.data('parent') );
					}
	
					if(!searchable){
						params['minimumResultsForSearch'] = Infinity;
					}
					params['dropdownCssClass'] = _this.data('class')
	
					if (_this.data('format') == 'custom') {
						params['templateResult']    = formatSelect2Option
						params['templateSelection'] = formatSelect2Selection
						params['escapeMarkup'] = function(markup) { return markup; }
					}
					if(event.reset){
						if(_this.data('select2')){
							_this.val('').trigger('change');
							_this.select2("destroy");
						}
					}
					_this.select2(params);
					if(event.reset){
						_this.val(event.value ?? '').trigger('change');
					}
					if(_this.data('disable_onchange') != 'true'){
						_this.on('change', function (e) {
							if(modelInput){
								let value = _this.select2("val");
								component.set(modelInput, value, live)
							}
						});
					}
				});
			}, timeOut);
		});
	}
	function formatSelect2Option(option) {
		if (!option.id) {
			return option.text;
		}
	
		var $option = $(
			'<span>' + option.text + ' <span class="price">' + $(option.element).data('price') + '</span></span>'
		);
	
		return $option;
	}
	
	function formatSelect2Selection(option) {
		if (!option.id) {
			return option.text;
		}
	
		var $selection = $(
			'<span>' + option.text + ' <span class="price">' + $(option.element).data('price') + '</span></span>'
		);
	
		return $selection;
	}
})($);



