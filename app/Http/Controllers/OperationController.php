<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperationLogResource;
use App\Models\OperationLog;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $operationLog = OperationLog::query()
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                return $query->whereBetween('createdAt', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d ' . '23:59:59', $request->input('endTime')),
                ]);
            })
            ->when($request->has('username'), function ($query) use ($request) {
                return $query->where('username', 'like', $request->input('username') . '%');
            })
            ->when($request->has('uri'), function ($query) use ($request) {
                return $query->where('uri', 'like', '%' . $request->input('uri') . '%');
            })
            ->orderBy($request->input('order') ?: 'createdAt', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize') ?: 10, ['*'], 'page', $request->input('page') ?: 1);

        return $this->success([
            'data' => OperationLogResource::collection($operationLog),
            'meta' => [
                'total' => $operationLog->total(),
            ],
        ]);
    }
}