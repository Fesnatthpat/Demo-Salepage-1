<?php

namespace App\Http\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class SettingsComposer
{
    protected $settings;

    public function __construct()
    {
        // Cache the settings for the duration of the request
        $this->settings = Setting::pluck('value', 'key')->all();
    }

    public function compose(View $view)
    {
        $view->with('settings', $this->settings);
    }
}
