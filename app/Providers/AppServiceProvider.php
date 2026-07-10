<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use Illuminate\Support\Facades\View;
use App\Models\PenerimaanBarang;
use App\Observers\PenerimaanBarangObserver;
use App\Observers\DetailKaryawanObserver;

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
        View::share('countKaryawan', Karyawan::count());
        View::share('countDetailKaryawan', DetailKaryawan::count());
        PenerimaanBarang::observe(PenerimaanBarangObserver::class);
        DetailKaryawan::observe(DetailKaryawanObserver::class);
    }
}