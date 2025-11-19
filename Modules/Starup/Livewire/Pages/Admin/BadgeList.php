<?php
namespace Modules\Starup\Livewire\Pages\Admin;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Traits\PrepareForValidation;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Modules\Starup\Http\Requests\BadgeRequest;
use Modules\Starup\Services\BadgesServices;
use Livewire\Component;

class BadgeList extends Component
{
    use PrepareForValidation;
    public $search       = '';
    public $sortby       = 'desc';
    public $category     = '';
    public $selectedCategory = '';
    public $badgeRating  = '';
    public $badgeSession = '';
    public $badgeReviews = '';
    public $isLoading   = true;
    public $badgeId      = null;
    public $MAX_PROFILE_CHAR    = 500;
    public $perPage      = 10;
    public $profileComplete     = '1';
    public $trusted    = '0';
    public $sessionsCount       = '';
    public $bookingCount        = '';
    public $categories;
    public $allowImgFileExt;
    public $allowImageSize;   
    protected $badgesServices;
    public $badgeName, $badgeDescription, $badgeCategory, $badgeImage;
    use WithPagination;
    use WithFileUploads;
    private ?BadgeRequest $badgeRequest = null;


    public function boot()
    {
        $this->badgesServices   = new BadgesServices();
        $this->categories       = $this->badgesServices->getCategories();
        $this->badgeRequest     = new BadgeRequest();
        
    }

    public function loadData()
    {
        $this->isLoading   = false;
    }

    public function mount()
    {
        $image_file_ext          = setting('_general.allowed_image_extensions') ?? 'jpg,png';
        $this->perPage = setting('_general.per_page_record') ?? 10;
        $image_file_size         = (int) (setting('_general.max_image_size') ?? '5');
        $this->allowImageSize    = !empty( $image_file_size ) ? $image_file_size : '5';
        $this->allowImgFileExt   = !empty( $image_file_ext ) ?  explode(',', $image_file_ext) : [];
        $this->dispatch('initSelect2', target: '.am-select2');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'category','sortby'])) {
            $this->resetPage();
        }
    }

    #[Layout('layouts.admin-app')]
    public function render()
    {
        $badges = $this->badgesServices->getAllBadges($this->search, $this->sortby, $this->category, $this->perPage);
        return view('starup::livewire.pages.admin.badge-list', compact('badges'));
    }

    function resetBadgeData(){
        $this->badgeName        = '';
        $this->badgeDescription = '';
        $this->badgeImage       = '';
        $this->selectedCategory = '';
        $this->badgeRating      = '';
        $this->badgeSession     = '';
        $this->profileComplete  = '1';
        $this->trusted = '0';
        $this->sessionsCount    = '';
        $this->badgeReviews     = '';
        $this->bookingCount     = '';
    }


    public function updatedBadgeImage($value)
    {
        $mimeType = $value->getMimeType();
        $type = explode('/', $mimeType);
        if($type[0] != 'image') {
            $this->dispatch('showAlertMessage', type: `error`, message: __('validation.invalid_file_type', ['file_types' => fileValidationText($this->allowImgFileExt)]));
            $this->badgeImage = null;
            return;
        }
    }

    public function openAddBadgeModal($id = null)
    {
        $this->resetBadgeData();
        $this->badgeId = $id;
        if($id){
            $badge = $this->badgesServices->getBadgeById($id);
            $this->badgeName            = $badge->name;
            $this->badgeDescription     = $badge->description;
            $this->badgeImage           = $badge->image;
            $this->selectedCategory     = $badge->category_id;
            if($this->selectedCategory == '1'){
                $this->badgeRating      = $badge->badgeRules->where('criterion_type', 'avg_rating')->first()?->criterion_value;
                $this->badgeReviews     = $badge->badgeRules->where('criterion_type', 'total_reviews')->first()?->criterion_value;
            }elseif($this->selectedCategory == '2'){
                $this->badgeSession     = $badge->badgeRules->where('criterion_type', 'book_sessions_count')->first()?->criterion_value;
            }elseif($this->selectedCategory == '3'){
                $this->profileComplete  = $badge->badgeRules->where('criterion_type', 'profile_complete')->first()?->criterion_value ? '1' : '0';
                $this->trusted = $badge->badgeRules->where('criterion_type', 'trusted_educator')->first()?->criterion_value ? '1' : '0';
            }elseif($this->selectedCategory == '4'){
                $this->sessionsCount    = $badge->badgeRules->where('criterion_type', 'completed_sessions_count')->first()?->criterion_value;
            }elseif($this->selectedCategory == '5'){
                $this->bookingCount     = $badge->badgeRules->where('criterion_type', 'rehired_booking_count')->first()?->criterion_value;
            }
        }

        $this->dispatch('toggleModel', id:'add-badge-popup', action:'show');
    }

    public function closeBadgeModal()
    {
        $this->resetErrorBag();
        $this->dispatch('toggleModel', id:'add-badge-popup', action:'hide');
    }

    public function rules(): array
    {
        return $this->badgeRequest->rules();
         
    }
    public function messages(): array
    {
        return $this->badgeRequest->messages();
    }

    public function addBadge()
    {


        $response = isDemoSite();
        if ($response) {
            $this->dispatch('toggleModel', id:'add-badge-popup', action:'hide');
            $this->dispatch('showAlertMessage', type: 'error', title: __('general.demosite_res_title'), message: __('general.demosite_res_txt'));
            return;
        }

        $this->beforeValidation(['badgeImage', 'selectedCategory']);
        $rules = $this->rules();
        if(empty($this->badgeId)){
            $imageFileExt                   = setting('_general.allowed_image_extensions') ?? 'jpg,png';
            $imageFileSize                  = (int) (setting('_general.max_image_size') ?? '5');
            $imageValidation                = 'required|mimes:'.$imageFileExt.'|max:'.$imageFileSize*1024;
            $rules['badgeImage'] = $imageValidation;
        }

        if($this->selectedCategory == ''){
          $this->validate($rules,$this->messages());
        }

        $image = $this->badgeImage instanceof \Illuminate\Http\UploadedFile ? 
            $this->badgeImage->storeAs('badge', Str::random(40) . '_' . $this->badgeImage->getClientOriginalName(), getStorageDisk()) : 
            $this->badgeImage;

        $data = [
            'name'          => $this->badgeName,
            'description'   => $this->badgeDescription,
            'category_id'   => $this->selectedCategory,
            'image'         => $image,
        ];

        $badgeRules = $this->getBadgeRules();

        $this->badgesServices->addBadge($this->badgeId, $data, $badgeRules);

        $this->dispatch('toggleModel', id:'add-badge-popup', action:'hide');
        $this->dispatch('showAlertMessage', type: 'success',  message: $this->badgeId ? __('starup::starup.badge_updated_success') : __('starup::starup.badge_added_success'));
    }

    private function getBadgeRules()
    {
        $badgeRules = [];
        switch ($this->selectedCategory) {
            case '1':
                $badgeRules = $this->getRatingBadgeRules();
                break;
            case '2':
                $badgeRules = $this->getSessionBadgeRules();
                break;
            case '3':
                $badgeRules = $this->getProfileBadgeRules();
                break;
            case '4':
                $badgeRules = $this->getSessionCountBadgeRules();
                break;
            case '5':
                $badgeRules = $this->getBookingCountBadgeRules();
                break;
        }
        return $badgeRules;
    }

    private function getRatingBadgeRules()
    {
        $rules = $this->rules();
        $rules['badgeRating'] = ['required','numeric','min:1.0','max:5.0','regex:/^\d+(\.\d{1})?$/'];
        $rules['badgeReviews'] = 'required|integer|min:1';

        $this->validate($rules,$this->messages());
        return [
            ['criterion_value' => $this->badgeRating, 'criterion_type' => "avg_rating"],
            ['criterion_value' => $this->badgeReviews, 'criterion_type' => "total_reviews"],
        ];
    }

    private function getSessionBadgeRules()
    {
        $rules = $this->rules();
        $rules['badgeSession'] = 'required|numeric|min:1';

        $this->validate($rules);
        return [
            ['criterion_value' => $this->badgeSession, 'criterion_type' => "book_sessions_count"],
        ];
    }

    private function getProfileBadgeRules()
    {
        $this->validate();
        return [
            ['criterion_value' => $this->profileComplete, 'criterion_type' => "profile_complete"],
            ['criterion_value' => $this->trusted, 'criterion_type' => "trusted_educator"],
        ];
    }

    private function getSessionCountBadgeRules()
    {
        $rules = $this->rules();
        $rules['sessionsCount'] = 'required|numeric|min:1';
        
        $this->validate($rules);
        return [
            ['criterion_value' => $this->sessionsCount, 'criterion_type' => "completed_sessions_count"],
        ];
    }

    private function getBookingCountBadgeRules()
    {
        $rules = $this->rules();
        $rules['bookingCount'] = 'required|numeric|min:1';

        $this->validate($rules);
        return [
            ['criterion_value' => $this->bookingCount, 'criterion_type' => "rehired_booking_count"],
        ];
    }

    public function removePhoto()
    {
        $this->badgeImage = null;
    }
    
    #[On('delete-badge')]
    public function deleteBadge($params = [])
    {
        
        $response = isDemoSite();
        if ($response) {
            $this->dispatch('showAlertMessage', type: 'error', title: __('general.demosite_res_title'), message: __('general.demosite_res_txt'));
            return;
        }
        $badge = $this->badgesServices->deleteBadge($params['id']);
        if($badge){
            $this->dispatch('showAlertMessage', type: 'success', message: __('starup::starup.badge_deleted_success'));
        }else{
            $this->dispatch('showAlertMessage', type: 'error', message: __('starup::starup.badge_deleted_error'));
        }
    }

}
