<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\TryUseReportResource;
use App\Models\ExperienceReport;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    /**
     * @param $activityId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($activityId, Request $request)
    {
        $try_use_report = ExperienceReport::query()
            ->where([
                ['type', 1],
                ['type_id', $activityId]
            ])
            ->with('user')
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d H:i:s', $request->input('endTime')),
                ]);
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->WhereHas('user', function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('name').'%');
                });
            })
            ->orderBy($request->input('order') ?: 'created_at', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => TryUseReportResource::collection($try_use_report),
            'total' => $try_use_report->total(),
        ]);
    }


    /**
     * 删除.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        $num = count($params['ids']);
        $numDestroied = ExperienceReport::query()
            ->whereIn('id', collect($params['ids']))
            ->delete();

        if ($num == $numDestroied) {
            return $this->success([], '删除成功');
        }

        return $this->fail('删除失败');
    }

}