<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\PrizeResource;
use App\Models\Prize;
use Illuminate\Support\Arr;
use Laravel\Lumen\Routing\Controller;

class PrizeController extends Controller
{
    /**
     * 奖品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $prizes = Prize::query()
            ->select(['id', 'prize_name', 'prize_num', 'prize_image', 'probability', 'status'])
            ->get();

        // 对奖品列表进行中奖设置
        // foreach ($prizes as $key => $prize) {
        //     $prizes[$key]['selected'] = $prize['id'] == $prize_id ? 1 : 0;
        // }

        return response()->json(PrizeResource::collection($prizes));
    }

    /*
     * 获取中奖物品
     */
    public function getWinningPrize()
    {
        $prizes = Prize::query()
            ->select(['id', 'prize_name', 'prize_num', 'prize_image', 'probability', 'status'])
            ->get();

        // 自定义奖品数组 id => probability
         $prize_arr = Arr::pluck(optional($prizes)->toArray(), 'probability', 'id');

         // 获取抽中的奖品 id
         $prize_id = $this->getRand($prize_arr);

         return response([
             'prize_id' => $prize_id
         ]);
    }

    /**
     * 获取中奖奖品 id
     * @param $pro_arr
     * @return int|string
     */
    protected function getRand($pro_arr)
    {
        $rs = ''; // 中奖结果
        $proSum = array_sum($pro_arr); // 概率数组的总概率精度
        // 概率数组循环
        foreach ($pro_arr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $rs = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $rs;
    }
}