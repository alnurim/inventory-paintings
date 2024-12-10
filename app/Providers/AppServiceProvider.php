<?php

namespace App\Providers;

use App\Filament\Resources\BarangKeluarResource;
use App\Filament\Resources\BarangMasukResource;
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\JenisResource;
use App\Filament\Resources\KaryawanResource;
use App\Filament\Resources\LokasiResource;
use App\Filament\Resources\PemakaianLapanganResource;
use App\Filament\Resources\PeminjamanBarangResource;
use App\Filament\Resources\PengambilResource;
use App\Filament\Resources\ProdukResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\TipeLokasiResource;
use App\Filament\Resources\UserResource;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\PemakaianLapangan;
use App\Models\PeminjamanBarang;
use App\Observers\BarangKeluarObserver;
use App\Observers\BarangMasukObserver;
use App\Observers\PemakaianLapanganObserver;
use App\Observers\PeminjamanBarangObserver;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(fn(): string => Filament::getLoginUrl());
        AuthenticateSession::redirectUsing(
            fn(): string => Filament::getLoginUrl()
        );
        AuthenticationException::redirectUsing(
            fn(): string => Filament::getLoginUrl()
        );

        // Navigation Top User Card
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_START,
            fn(): View => view('filament.user-card')
        );

        // Footer
        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('filament.footer'),
            // Render The Footer for Pages or Resource
            scopes: [
                Dashboard::class,
                UserResource::class,
                RoleResource::class,
                BarangResource::class,
                BarangMasukResource::class,
                BarangKeluarResource::class,
                JenisResource::class,
                KaryawanResource::class,
                LokasiResource::class,
                PemakaianLapanganResource::class,
                PeminjamanBarangResource::class,
                PengambilResource::class,
                ProdukResource::class,
                TipeLokasiResource::class,
            ]
        );

        // Vite Hot Reloading
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => Blade::render("@vite('resources/js/app.js')")
        );

        BarangMasuk::observe(BarangMasukObserver::class);
        PeminjamanBarang::observe(PeminjamanBarangObserver::class);
        BarangKeluar::observe(BarangKeluarObserver::class);
        PemakaianLapangan::observe(PemakaianLapanganObserver::class);
    }
}
