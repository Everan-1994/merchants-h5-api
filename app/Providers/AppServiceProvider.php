<?php

namespace App\Providers;

use App\Models\Share;
use App\Models\Zan;
use App\Observers\ShareObserver;
use App\Observers\ZanObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendValidator();
        Carbon::setLocale('zh');
        Share::observe(ShareObserver::class); // 分享观察器
        Zan::observe(ZanObserver::class); // 点赞观察器
    }

    /**
     * Extend validator
     * add uniqueSoftDelete valiator type
     * 
     * @return null
     */
    protected function extendValidator()
    {
        Validator::extend('uniqueSoftDelete', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0])->whereRaw('deletedAt is null')->where($parameters[1], '=', $value);
            if (isset($parameters[2]) && isset($parameters[3])) {
                $query->where($parameters[3], '!=', $parameters[2]);
            }
            for ($i = 4; $i < count($parameters); $i = $i + 2) {
                $query->where($parameters[$i], $parameters[$i + 1]);
            }
            $count = $query->count();
            return $count > 0 ? false : true;
        }, ':attribute 已经存在。');
    }
}
