<?php

namespace App\Http\Controllers\Api;

use App\Models\Prize;
use Laravel\Lumen\Routing\Controller;

class PrizeController extends Controller
{
    public function index()
    {
        $prizes = Prize::query()->select(['prize_name', 'prize_num', 'prize_image', 'probability'])->get();

        return response()->json($prizes);
    }
}