<?php

namespace Modules\Starup\Services;
use Modules\Starup\Models\Badge;
use Modules\Starup\Models\BadgeRule;
use Illuminate\Support\Facades\DB;
use Modules\Starup\Models\BadgeCategory;
use Illuminate\Support\Facades\Log;

class BadgesServices
{
    public function getAllBadges($search, $sortby, $category, $perPage = 10)
    {
        $badges = Badge::select('id', 'name', 'description', 'image', 'category_id', 'created_at')->with(['category' => function($query) {
            $query->select('id', 'name');
        }]);
        if($search){
            $badges->where('name', 'like', '%' . $search . '%');
        }
        if($category){
            $badges->where('category_id', $category);
        }
        if($sortby){
            $badges->orderBy('id', $sortby);
        }
        return $badges->paginate($perPage);
    }

    public function getCategories()
    {
        return BadgeCategory::get()->pluck('name', 'id');
    }
    
    public function addBadge($id = null, $data, $rules)
    {
        try {
            DB::beginTransaction();
                $badge = Badge::updateOrCreate(
                    ['id' => $id],
                    $data
                );
                $this->addBadgeRules($badge->id, $rules);
            DB::commit();
            return $badge;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }

    private function addBadgeRules($badgeId, $rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                BadgeRule::updateOrCreate(
                    [
                        'badge_id' => $badgeId,
                        'criterion_type' => $rule['criterion_type'],
                    ],
                    [
                        'criterion_value' => $rule['criterion_value'],
                    ]
                );
            }
        }
    }

    public function deleteBadge($id)
    {
        
        return Badge::find($id)->delete();
    }

    public function getBadgeById($id)
    {
        return Badge::where('id', $id)->with(['badgeRules' => function($query) {
            $query->select('id', 'badge_id', 'criterion_type', 'criterion_value');
        }])->first();
    }

}
