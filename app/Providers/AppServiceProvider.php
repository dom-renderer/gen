<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        if (!defined('DATE_FORMAT')) {
            define("DATE_FORMAT","d-m-Y");
        }

        if (!defined('DATE_FORMAT_DATEPICKER_DISPLAY')) {
            define("DATE_FORMAT_DATEPICKER_DISPLAY","dd-mm-yyyy");
        }

        if (!defined('DATE_FORMAT_DATEPICKER_FORMAT')) {
            define("DATE_FORMAT_DATEPICKER_FORMAT","dd-mm-yy");
        }

        \Illuminate\Support\Facades\Blade::directive('requiredField', function () {
            return '<span class="text-danger"> * </span>';
        });

        \Illuminate\Support\Facades\Blade::directive('tooltip', function ($expression) {
            return "<?php echo view('cases.tooltip', [
                'elementId' => {$expression},
                'content' => optional(App\Models\Tooltip::firstWhere('element_id', {$expression}))->content
            ])->render(); ?>";
        });

    }
}
