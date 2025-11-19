<?php

namespace Modules\CourseBundles\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Modules\CourseBundles\Models\Bundle;
use Modules\CourseBundles\Services\BundleService;
use Modules\CourseBundles\Http\Resources\CourseBundleCollection;
use Modules\CourseBundles\Http\Resources\CourseBundlesCollection;
use Symfony\Component\HttpFoundation\Response;

class CourseBundleController extends Controller
{
    use ApiResponser;

    public function getCourseBundles(Request $request)
    {
        $bundles = Bundle::select('id', 'title', 'price', 'discount_percentage', 'final_price', 'short_description', 'instructor_id')
            ->with('thumbnail:mediable_id,mediable_type,type,path', 'instructor.profile')
            ->withCount('courses')
            ->withSum('courses', 'content_length')
            ->where('status', Bundle::STATUS_PUBLISHED)
            ->when(!empty($request->keyword), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            })
            ->orderBy('id', 'desc')->paginate(setting('_general.per_page_record') ?? 10);
        return $this->success(data: new CourseBundlesCollection($bundles), code: Response::HTTP_OK);
    }
    
    public function courseBundlesList(Request $request)
    {
        $bundles = Bundle::select('id', 'title', 'price', 'discount_percentage', 'final_price', 'status', 'created_at')
            ->with('thumbnail:mediable_id,mediable_type,type,path')
            ->withCount('courses')
            ->withSum('courses', 'content_length')
            ->where('instructor_id', Auth::id())
            ->when(!empty($request->keyword), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            })
            ->when(isset($request->status) && $request->status != '', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('id', 'desc')->paginate(setting('_general.per_page_record') ?? 10);
        return $this->success(data: new CourseBundleCollection($bundles), code: Response::HTTP_OK);
    }

    public function publishCourseBundle(Request $request)
    {
        if (isDemoSite()) {
            return $this->error(__('general.demosite_res_txt'), code: Response::HTTP_FORBIDDEN);
        }

        $id = $request->id;
        if (empty($id) || !is_numeric($id)) {
            return $this->error(message: __('coursebundles::bundles.invalid_id'), code: Response::HTTP_BAD_REQUEST);
        }

        $bundle = (new BundleService())->getBundle($id);
        if (!$bundle) {
            return $this->error(message: __('coursebundles::bundles.no_bundles_found'), code: Response::HTTP_BAD_REQUEST);
        }

        $updated = (new BundleService())->updateBundleStatus($bundle, 'published');

        if ($updated) {
            return $this->success(message: __('coursebundles::bundles.bundle_published_successfully'), code: Response::HTTP_OK);
        } else {
            return $this->error(message: __('coursebundles::bundles.bundle_publish_error'), code: Response::HTTP_BAD_REQUEST);
        }
    }

    public function archiveCourseBundle(Request $request)
    {
        if (isDemoSite()) {
            return $this->error(__('general.demosite_res_txt'), code: Response::HTTP_FORBIDDEN);
        }

        $id = $request->id;
        if (empty($id) || !is_numeric($id)) {
            return $this->error(message: __('coursebundles::bundles.invalid_id'), code: Response::HTTP_BAD_REQUEST);
        }

        $bundle = (new BundleService())->getBundle($id);
        if (!$bundle) {
            return $this->error(message: __('coursebundles::bundles.no_bundles_found'), code: Response::HTTP_BAD_REQUEST);
        }

        $updated = (new BundleService())->updateBundleStatus($bundle, 'archived');

        if ($updated) {
            return $this->success(message: __('coursebundles::bundles.bundle_archived_successfully'), code: Response::HTTP_OK);
        } else {
            return $this->error(message: __('coursebundles::bundles.bundle_archive_error'), code: Response::HTTP_BAD_REQUEST);
        }
    }
    
    public function deleteCourseBundle(Request $request)
    {
        if (isDemoSite()) {
            return $this->error(__('general.demosite_res_txt'), code: Response::HTTP_FORBIDDEN);
        }

        $id = $request->id;
        if (empty($id) || !is_numeric($id)) {
            return $this->error(message: __('coursebundles::bundles.invalid_id'), code: Response::HTTP_BAD_REQUEST);
        }

        $deleted = (new BundleService())->deleteBundle($id);
        if ($deleted) {
            return $this->success(message: __('coursebundles::bundles.bundle_deleted_successfully'), code: Response::HTTP_OK);
        } else {
            return $this->error(message: __('coursebundles::bundles.bundle_delete_error'), code: Response::HTTP_BAD_REQUEST);
        }
    }
}
