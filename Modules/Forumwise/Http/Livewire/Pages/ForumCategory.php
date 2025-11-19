<?php

namespace Modules\Forumwise\Http\Livewire\Pages;

use Modules\Forumwise\Models\Category;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Forumwise\Services\CategoryServices;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ForumCategory extends Component
{
    use WithPagination;

    public $editMode = false;
    public $name, $edit_id, $label_color;
    public $search              = '';
    public $sortby              = 'desc';
    public $per_page            = '';
    public $per_page_opt        = [];
    public $delete_id           = null;
    public $selectedCategories  = [];
    public $selectAll           = false;


    protected $paginationTheme  = 'bootstrap';

    protected $listeners = ['deleteConfirmRecord' => 'deleteCategory'];

    protected $categoryServices;
    public function boot(CategoryServices $categoryServices)
    {
        $this->categoryServices = $categoryServices;
    }

    public function mount()
    {
        $this->per_page_opt     = perPageOpt();
        $per_page_record        = setting('_general.per_page_record');
        $this->per_page         = !empty($per_page_record) ? $per_page_record : 10;
        $this->dispatch('initColorPicker', color: '#000000');
    }

    public function render()
    {
        $categories = $this->categoryServices->getCategories($this->search, $this->sortby, $this->per_page);
        return view('forumwise::livewire.pages.forum-category', compact('categories'));
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        $categories = $this->categoryServices->getCategories($this->search, $this->sortby, $this->per_page);
        if ($value) {
            $this->selectedCategories = $categories->pluck('id')->toArray();
        } else {
            $this->selectedCategories = [];
        }
    }

    public function updatedselectedCategories()
    {
        $this->selectAll = false;
    }

    private function resetInputfields()
    {
        $this->name                     = null;
        $this->delete_id                = null;
        $this->selectedCategories       = [];
        $this->selectAll                = false;
        $this->label_color              = '#000000';
        $this->edit_id                  = null;
        $this->dispatch('initColorPicker', color: '#000000');
    }


    #[On('delete-category')]
    public function delete($params = [])
    {
        $response = isDemoSite();
        if ($response) {
            $this->dispatch(
                'showAlertMessage',
                type: 'error',
                title: __('forumwise::forum_wise.demosite_res_title'),
                message: __('forumwise::forum_wise.demosite_res_txt')
            );
            return;
        }
        if (!empty($params['id'])) {
            Category::findOrFail($params['id'])->delete();
            $message = __('forumwise::forum_wise.category_deleted_successfully');
        } elseif (!empty($this->selectedCategories)) {
            Category::whereIn('id', $this->selectedCategories)->delete();
            $message = __('forumwise::forum_wise.categories_deleted_successfully');
        }

        $this->selectedCategories = [];
        $this->resetInputfields();

        $this->dispatch('showAlertMessage', type: 'success', message: $message);
    }

    public function deleteAllRecord()
    {
        if (!empty($this->selectedCategories)) {
            Category::whereIn('id', $this->selectedCategories)->delete();
            $this->selectedCategories = [];
        }
        $this->dispatch('delete-category-confirm');
    }

    public function removeImage()
    {

        $this->image     = null;
        $this->old_image = array();
    }

    public function edit($id)
    {
        $record = Category::findOrFail($id);
        $this->edit_id      = $id;
        $this->name         = $record->name;
        $this->label_color  = $record->label_color;
        $this->editMode     = true;
        $this->dispatch('initColorPicker', color: $record->label_color);
    }

    public function update()
    {
        $response = isDemoSite();
        if ($response) {
            $this->dispatch(
                'showAlertMessage',
                type: 'error',
                title: __('forumwise::forum_wise.demosite_res_title'),
                message: __('forumwise::forum_wise.demosite_res_txt')
            );
            return;
        }
        $table = (new Category)->getTable();


        $validated_data = $this->validate([
            'name' => [
                'required',
                Rule::unique($table)
                    ->ignore($this->edit_id ?? null) 
                    ->whereNull('deleted_at')  
            ],
        ]);

        $validated_data['name']         = sanitizeTextField($this->name);
        $validated_data['label_color']  = $this->label_color;

        Category::updateOrCreate(['id' => $this->edit_id], $validated_data);

        if (!empty($this->edit_id)) {
            $message = __('forumwise::forum_wise.category_updated_');
        } else {
            $message = __('forumwise::forum_wise.category_created_');
        }
        $this->dispatch('showAlertMessage', type: 'success', message: $message);
        $this->dispatch('updatecategoryList');
        $this->resetInputfields();
        $this->editMode = false;
    }
}
