<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'sort_order'];

    protected static array $defaultSettings = [
        // General
        ['key' => 'site_name', 'value' => 'RBAC System', 'group' => 'general', 'type' => 'text', 'label' => 'Site Name', 'sort_order' => 1],
        ['key' => 'site_tagline', 'value' => 'Professional Role Based Access Control', 'group' => 'general', 'type' => 'text', 'label' => 'Tagline', 'sort_order' => 2],
        ['key' => 'site_description', 'value' => 'A powerful and flexible RBAC system.', 'group' => 'general', 'type' => 'textarea', 'label' => 'Description', 'sort_order' => 3],
        ['key' => 'site_logo', 'value' => null, 'group' => 'general', 'type' => 'image', 'label' => 'Logo', 'sort_order' => 4],
        ['key' => 'site_favicon', 'value' => null, 'group' => 'general', 'type' => 'image', 'label' => 'Favicon', 'sort_order' => 5],
        ['key' => 'admin_email', 'value' => 'admin@example.com', 'group' => 'general', 'type' => 'text', 'label' => 'Admin Email', 'sort_order' => 6],
        // Contact
        ['key' => 'contact_email', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'Contact Email', 'sort_order' => 1],
        ['key' => 'contact_phone', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'Contact Phone', 'sort_order' => 2],
        ['key' => 'contact_phone2', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'Alternate Phone', 'sort_order' => 3],
        ['key' => 'contact_address', 'value' => '', 'group' => 'contact', 'type' => 'textarea', 'label' => 'Address', 'sort_order' => 4],
        ['key' => 'contact_city', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'City', 'sort_order' => 5],
        ['key' => 'contact_state', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'State', 'sort_order' => 6],
        ['key' => 'contact_country', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'Country', 'sort_order' => 7],
        ['key' => 'contact_zip', 'value' => '', 'group' => 'contact', 'type' => 'text', 'label' => 'ZIP Code', 'sort_order' => 8],
        // Social
        ['key' => 'social_facebook', 'value' => '', 'group' => 'social', 'type' => 'text', 'label' => 'Facebook URL', 'sort_order' => 1],
        ['key' => 'social_twitter', 'value' => '', 'group' => 'social', 'type' => 'text', 'label' => 'Twitter/X URL', 'sort_order' => 2],
        ['key' => 'social_linkedin', 'value' => '', 'group' => 'social', 'type' => 'text', 'label' => 'LinkedIn URL', 'sort_order' => 3],
        ['key' => 'social_instagram', 'value' => '', 'group' => 'social', 'type' => 'text', 'label' => 'Instagram URL', 'sort_order' => 4],
        ['key' => 'social_youtube', 'value' => '', 'group' => 'social', 'type' => 'text', 'label' => 'YouTube URL', 'sort_order' => 5],
        // SEO
        ['key' => 'meta_keywords', 'value' => '', 'group' => 'seo', 'type' => 'text', 'label' => 'Meta Keywords', 'sort_order' => 1],
        ['key' => 'meta_description', 'value' => '', 'group' => 'seo', 'type' => 'textarea', 'label' => 'Meta Description', 'sort_order' => 2],
        ['key' => 'google_analytics', 'value' => '', 'group' => 'seo', 'type' => 'text', 'label' => 'Google Analytics ID', 'sort_order' => 3],
        // System
        ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'system', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'sort_order' => 1],
        ['key' => 'registration_enabled', 'value' => '1', 'group' => 'system', 'type' => 'boolean', 'label' => 'Allow Registration', 'sort_order' => 2],
        ['key' => 'items_per_page', 'value' => '15', 'group' => 'system', 'type' => 'text', 'label' => 'Items Per Page', 'sort_order' => 3],
        ['key' => 'timezone', 'value' => 'Asia/Kolkata', 'group' => 'system', 'type' => 'text', 'label' => 'Timezone', 'sort_order' => 4],
        ['key' => 'date_format', 'value' => 'd M Y', 'group' => 'system', 'type' => 'text', 'label' => 'Date Format', 'sort_order' => 5],
    ];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever('settings.' . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.' . $key);
    }

    public static function getAllSettings(): array
    {
        return static::orderBy('group')->orderBy('sort_order')->get()->keyBy('key')->toArray();
    }

    public static function getGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group', $group)->orderBy('sort_order')->get();
    }

    public static function getDefaultSettings(): array
    {
        return static::$defaultSettings;
    }

    public static function clearCache(): void
    {
        static::all()->each(function ($setting) {
            Cache::forget('settings.' . $setting->key);
        });
    }
}
