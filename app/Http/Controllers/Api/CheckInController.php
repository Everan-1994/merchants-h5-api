<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CheckInResource;
use App\Models\CheckIn;
use App\Models\Others;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;

class CheckInController extends Controller
{
    protected $user_id;

    public function __construct()
    {
        $this->user_id = Auth::guard('user')->id();
    }

    /**
     * 签到
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {

        // 检查当日是否签到
        $is_check = $this->checkDateOnly();

        if (!$is_check) {
            return response()->json([
                'errorCode' => 1,
                'message'   => '当日已签到',
            ]);
        }

        // 当日 日期 y-m-d
        $today = Carbon::now()->toDateString();

        CheckIn::query()->create([
            'user_id'        => $this->user_id,
            'status'         => CheckIn::NO_LOTTERY,
            'check_in_time'  => $today,
            'check_in_times' => $this->getPrevCheckIn(),
        ]);

        return response()->json([
            'errorCode' => 0,
            'messages'  => '签到成功',
        ]);
    }

    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::now()->toDateString());

        // 本月第一天
        $date_first = Carbon::parse($date)->firstOfMonth()->format('Y-m-d');
        // 本月最后一天
        $date_last = Carbon::parse($date)->lastOfMonth()->format('Y-m-d');

        $check_in_list = CheckIn::query()
            ->select([
                'id',
                'status',
                'check_in_time',
                'check_in_times',
                'created_at',
            ])
            ->where('user_id', $this->user_id)
            ->whereBetween('check_in_time', [$date_first, $date_last])
            ->get();

        return response()->json(CheckInResource::collection($check_in_list));
    }

    /**
     * 签到规则
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkInRule()
    {
        $check_in_rule = Others::query()->select(['content'])->find(1);

        return response()->json($check_in_rule);
    }

    protected function getPrevCheckIn()
    {
        // 前一天 日期 y-m-d
        $prev_date = Carbon::now()->subDay()->toDateString();

        $check_in_times = CheckIn::query()
            ->where('user_id', $this->user_id)
            ->where('check_in_time', $prev_date)
            ->value('check_in_times');

        if ($check_in_times == 0 || $check_in_times == 7 || empty($check_in_times)) {
            return 1;
        } else {
            return $check_in_times + 1;
        }
    }

    /**
     * 校对是否当日已签到
     * @return bool
     */
    protected function checkDateOnly()
    {
        return !CheckIn::query()
            ->where('user_id', $this->user_id)
            ->where('check_in_time', Carbon::now()->toDateString())
            ->exists();
    }

}