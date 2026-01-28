<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Listener สำหรับ Socialite (Login ผ่าน LINE)
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Line\\LineExtendSocialite@handle',
        ],

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // [เพิ่มใหม่] เมื่อ User Login สำเร็จ -> ให้เรียก Listener กู้คืนตะกร้าสินค้า
        \App\Events\UserLoggedIn::class => [
            \App\Listeners\RestoreUserCart::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
