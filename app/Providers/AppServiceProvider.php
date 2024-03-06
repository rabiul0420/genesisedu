<?php

namespace App\Providers;

use App\Courses;
use App\Institutes;
use App\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;


class AppServiceProvider extends ServiceProvider
{
    public static $BCPS_INSTITUTE_ID = 4;
    public static $BSMMU_INSTITUTE_ID = 6;
    public static $COMBINED_INSTITUTE_ID = 16;
    public static $MPH_DIPLOMA_COURSE_ID = 13;
    public static $FCPSP1_COURSE_ID = 19;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function menus() {
        return Cache::rememberForever('AdminPanelMenus', function () {
            return Menu::query()
                ->with('submenu.thirdmenu')
                ->mainMenu()
                ->orderBy('priority', 'asc')
                ->get();
        });
    }

    public function boot()
    {
        view()->composer('admin.layouts.app', function ($view) {
            $view->with('menus',$this->menus());
        });

        view()->composer('tailwind.layouts.admin', function ($view) {
            $view->with('menus',$this->menus());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
