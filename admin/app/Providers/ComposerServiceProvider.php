<?php

namespace App\Providers;

use App\View\Composers\MailComposer;
use App\View\Composers\MenuComposer;
use Illuminate\Support\Facades\View;
use App\View\Composers\FaviconComposer;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\CurrencyComposer;
use App\View\Composers\LanguageComposer;
use App\View\Composers\DemoStatusComposer;
use App\View\Composers\ModuleStatusComposer;
use App\View\Composers\NotificationComposer;
use App\View\Composers\CompanyProfileComposer;


class ComposerServiceProvider extends ServiceProvider
{
    public function register()
    {}
    public function boot()
    {
        View::composer(
            ['layouts.inc.header', 'admin.settings.inc.links', 'admin.settings.advancedSettings.inc.links', 'layouts.inc.footer', 'employee.inc.employee-footer'],
            ModuleStatusComposer::class
        );
        View::composer('layouts.inc.navigation', MenuComposer::class);
        View::composer(
            ['layouts.inc.header', 'admin.profile.profile_view', 'employee.inc.employee-header'],
            LanguageComposer::class
        );
        View::composer(
            ['admin.profile.profile_view', 'layouts.inc.header'],
            CurrencyComposer::class
        );
        View::composer(
            ['admin.reports.*', 'admin.report.*', 'layouts.inc.header', 'layouts.inc.head', 'layouts.inc.footer', 'layouts.inc.navigation', 'auth.login'],
            CompanyProfileComposer::class
        );
        View::composer(
            ['layouts.inc.*', 'auth.login'],
            FaviconComposer::class
        );
        View::composer(['layouts.inc.footer', 'layouts.inc.demo-script'], DemoStatusComposer::class);
        View::composer('layouts.inc.header', NotificationComposer::class);
        View::composer('mailBox.sidebar', MailComposer::class);
    }
}
