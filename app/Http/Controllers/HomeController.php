<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }

    public function home(Request $request)
    {
        $times = $request->input('times', 1); // 默认取今日数据
        switch ($times) {
            case '1': // 今日
                $start = $this->now->copy()->toDateString() . ' 00:00:00';
                $end = $this->now->copy()->toDateString() . ' 23:59:59';
                $data = $this->getTodayData($start, $end);
                break;
            case '2': // 本周
                $start = $this->now->copy()->startOfWeek();
                $end = $this->now->copy()->endOfWeek();
                $data = $this->getWeekData($start, $end);
                break;
            case '3': // 本月
                $start = $this->now->copy()->startOfMonth();
                $end = $this->now->copy()->endOfMonth();
                $data = $this->getMonthData($start, $end);
                break;
            case '4': // 全年
                $start = $this->now->copy()->startOfYear();
                $end = $this->now->copy()->endOfYear();
                $data = $this->getYearData($start, $end);
                break;
            default:
                $start = date('Y-m-d H:i:s', $request->input('startTime'));
                $end = date('Y-m-d' . ' 23:59:59', $request->input('endTime'));

                if ($request->input('startTime') && $request->input('endTime')) {
                    $data = $this->getRandomData($start, $end);
                } else {
                    $data = [
                        'date' => [],
                        'num'  => [],
                    ];
                }

                break;
        }

        return $this->success([
            'data'           => $data,
            'user_count'     => $this->getUserCount(),
            'today_view'     => $this->getTodayView($start, $end),
            'today_register' => $this->getTodayRegister($start, $end),
        ]);
    }

    /*
     * 用户
     */
    protected function getUserCount()
    {
        return User::query()->count();
    }

    /**
     * 活跃量
     * @param $start
     * @param $end
     * @return int
     */
    protected function getTodayView($start, $end)
    {
        return UserLog::query()
            ->whereBetween('created_at', [
                $start,
                $end,
            ])
            ->count();
    }

    /**
     * 新用户
     * @param $start
     * @param $end
     * @return int
     */
    protected function getTodayRegister($start, $end)
    {
        return User::query()
            ->whereBetween('createdAt', [
                $start,
                $end,
            ])
            ->count();
    }

    /**
     * 今日活跃数据
     * @param $start
     * @param $end
     * @return array
     */
    protected function getTodayData($start, $end)
    {
        $list = UserLog::query()
            ->select(\DB::raw("DATE_FORMAT(created_at,'%H:00') as time, COUNT(*) as num"))
            ->whereBetween('created_at', [
                $start,
                $end,
            ])
            ->groupBy('time')
            ->get()
            ->toArray();

        $date = [];
        $num = [];
        for ($i = 0; $i <= $this->now->copy()->hour; $i++) {
            if ($i > 9) {
                $date[$i] = $i . ':00';
            } else {
                $date[$i] = '0' . $i . ':00';
            }
            $num[$i] = 0;

            foreach ($list as $item) {
                if ($date[$i] == $item['time']) {
                    $num[$i] = $item['num'];
                }
            }
        }

        return [
            'date' => $date,
            'num'  => $num,
        ];
    }

    /**
     * 本周活跃量
     * @param $start
     * @param $end
     * @return array
     */
    protected function getWeekData($start, $end)
    {
        $monday = Carbon::parse($start);

        $list = UserLog::query()
            ->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as day, COUNT(*) as num"))
            ->whereBetween('created_at', [
                $start,
                $end,
            ])
            ->groupBy('day')
            ->get()
            ->toArray();

        $date = [];
        $num = [];
        for ($i = 0; $i <= $this->now->copy()->diffInDays($monday->copy()); $i++) {
            $date[$i] = $this->getWeekCName($monday->copy()->addDays($i)->dayOfWeek);
            $num[$i] = 0;

            foreach ($list as $item) {
                if ($monday->copy()->addDays($i)->toDateString() == $item['day']) {
                    $num[$i] = $item['num'];
                }
            }
        }

        return [
            'date' => $date,
            'num'  => $num,
        ];
    }

    /**
     * 本月活跃量
     * @param $start
     * @param $end
     * @return array
     */
    protected function getMonthData($start, $end)
    {
        $first_day = Carbon::parse($start);

        $list = UserLog::query()
            ->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as day, COUNT(*) as num"))
            ->whereBetween('created_at', [
                $start,
                $end,
            ])
            ->groupBy('day')
            ->get()
            ->toArray();

        $date = [];
        $num = [];
        for ($i = 0; $i <= $this->now->copy()->diffInDays($first_day->copy()); $i++) {
            $date[$i] = $first_day->copy()->addDays($i)->format('m-d');
            $num[$i] = 0;

            foreach ($list as $item) {
                if ($first_day->copy()->addDays($i)->toDateString() == $item['day']) {
                    $num[$i] = $item['num'];
                }
            }
        }

        return [
            'date' => $date,
            'num'  => $num,
        ];
    }

    /**
     * 全年活跃量
     * @param $start
     * @param $end
     * @return array
     */
    protected function getYearData($start, $end)
    {
        $first_month = Carbon::parse($start);

        $list = UserLog::query()
            ->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as num"))
            ->whereBetween('created_at', [
                $start,
                $end,
            ])
            ->groupBy('month')
            ->get()
            ->toArray();

        $date = [];
        $num = [];
        for ($i = 0; $i < $this->now->copy()->month; $i++) {
            $date[$i] = $first_month->copy()->addMonths($i)->format('Y-m');
            $num[$i] = 0;

            foreach ($list as $item) {
                if ($date[$i] == $item['month']) {
                    $num[$i] = $item['num'];
                }
            }
        }

        return [
            'date' => $date,
            'num'  => $num,
        ];
    }

    /*
     * 随机日期获取活跃量
     */
    protected function getRandomData($start, $end)
    {
        // 日
        if ($start == $end) {
            return $this->getTodayData($start, $end);
        }

        // 周
        if (Carbon::parse($end)->diffInDays(Carbon::parse($start)) < 7) {
            return $this->getWeekData($start, $end);
        }

        // 月
        if (Carbon::parse($end)->diffInDays(Carbon::parse($start)) < 31) {
            return $this->getMonthData($start, $end);
        }

        // 年
        if (Carbon::parse($end)->diffInDays(Carbon::parse($start)) < 366) {
            return $this->getYearData($start, $end);
        }

        return [
            'date' => [],
            'num'  => [],
        ];
    }

    /**
     * 获取对应周几
     * @param $int
     * @return string
     */
    protected function getWeekCName($int)
    {
        switch ($int) {
            case 1:
                $str = '周一';
                break;
            case 2:
                $str = '周二';
                break;
            case 3:
                $str = '周三';
                break;
            case 4:
                $str = '周四';
                break;
            case 5:
                $str = '周五';
                break;
            case 6:
                $str = '周六';
                break;
            default:
                $str = '周日';
                break;
        }

        return $str;
    }
}