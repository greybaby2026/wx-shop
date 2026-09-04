<?php
namespace app\adminapi\logic\activity;

use app\common\service\ConfigService;

class DeductActivityLogic
{
    private static function presets(): array
    {
        return [
            'default' => [
                'name' => '默认主题',
                'theme_config' => [
                    'primary_color' => '#1a3a5c',
                    'accent_color' => '#c9a96e',
                    'banner_image' => '',
                    'slogan' => '充值享抵扣',
                    'badge_text' => '抵扣',
                    'badge_icon' => '',
                    'deco_element' => 'none',
                    'button_style' => 'rounded',
                    'animation' => 'none',
                ]
            ],
            'father' => [
                'name' => '父亲节',
                'theme_config' => [
                    'primary_color' => '#1a3a5c',
                    'accent_color' => '#c9a96e',
                    'banner_image' => '',
                    'slogan' => '感恩父爱，充值有礼',
                    'badge_text' => 'father',
                    'badge_icon' => 'heart',
                    'deco_element' => 'cloud',
                    'button_style' => 'rounded',
                    'animation' => 'fade',
                ]
            ],
            '618' => [
                'name' => '618大促',
                'theme_config' => [
                    'primary_color' => '#e63b3b',
                    'accent_color' => '#ffd700',
                    'banner_image' => '',
                    'slogan' => '年中大促，充值狂欢',
                    'badge_text' => '618',
                    'badge_icon' => 'tag',
                    'deco_element' => 'burst',
                    'button_style' => 'pill',
                    'animation' => 'pulse',
                ]
            ],
            'mid_autumn' => [
                'name' => '中秋节',
                'theme_config' => [
                    'primary_color' => '#1a237e',
                    'accent_color' => '#ffd54f',
                    'banner_image' => '',
                    'slogan' => '中秋团圆，充值有礼',
                    'badge_text' => 'midautumn',
                    'badge_icon' => 'moon',
                    'deco_element' => 'cloud',
                    'button_style' => 'rounded',
                    'animation' => 'fade',
                ]
            ],
            'teacher' => [
                'name' => '教师节',
                'theme_config' => [
                    'primary_color' => '#e65100',
                    'accent_color' => '#fff3e0',
                    'banner_image' => '',
                    'slogan' => '感恩师恩，充值特惠',
                    'badge_text' => 'teacher',
                    'badge_icon' => 'flower',
                    'deco_element' => 'petal',
                    'button_style' => 'rounded',
                    'animation' => 'gentle',
                ]
            ],
            'national' => [
                'name' => '国庆',
                'theme_config' => [
                    'primary_color' => '#d32f2f',
                    'accent_color' => '#ffd700',
                    'banner_image' => '',
                    'slogan' => '国庆盛典，充值钜惠',
                    'badge_text' => 'national',
                    'badge_icon' => 'star',
                    'deco_element' => 'firework',
                    'button_style' => 'pill',
                    'animation' => 'firework',
                ]
            ],
            'double11' => [
                'name' => '双十一',
                'theme_config' => [
                    'primary_color' => '#ff4400',
                    'accent_color' => '#ffffff',
                    'banner_image' => '',
                    'slogan' => '双十一狂欢，充值折上折',
                    'badge_text' => '双11',
                    'badge_icon' => 'lightning',
                    'deco_element' => 'burst',
                    'button_style' => 'pill',
                    'animation' => 'flash',
                ]
            ],
            'double12' => [
                'name' => '双十二',
                'theme_config' => [
                    'primary_color' => '#1565c0',
                    'accent_color' => '#ff9100',
                    'banner_image' => '',
                    'slogan' => '双十二年终盛典',
                    'badge_text' => '双12',
                    'badge_icon' => 'gift',
                    'deco_element' => 'confetti',
                    'button_style' => 'pill',
                    'animation' => 'float',
                ]
            ],
            'new_year' => [
                'name' => '元旦',
                'theme_config' => [
                    'primary_color' => '#c62828',
                    'accent_color' => '#ffd700',
                    'banner_image' => '',
                    'slogan' => '新年新气象，充值送好礼',
                    'badge_text' => 'newyear',
                    'badge_icon' => 'firework',
                    'deco_element' => 'firework',
                    'button_style' => 'pill',
                    'animation' => 'firework',
                ]
            ],
            'spring' => [
                'name' => '春节',
                'theme_config' => [
                    'primary_color' => '#b71c1c',
                    'accent_color' => '#ffd700',
                    'banner_image' => '',
                    'slogan' => '春节贺岁，充值红包',
                    'badge_text' => '贺岁',
                    'badge_icon' => 'hongbao',
                    'deco_element' => 'lantern',
                    'button_style' => 'pill',
                    'animation' => 'float',
                ]
            ],
            'qixi' => [
                'name' => '七夕',
                'theme_config' => [
                    'primary_color' => '#ad1457',
                    'accent_color' => '#f8bbd0',
                    'banner_image' => '',
                    'slogan' => '七夕告白，充值传情',
                    'badge_text' => '七夕',
                    'badge_icon' => 'heart',
                    'deco_element' => 'petal',
                    'button_style' => 'rounded',
                    'animation' => 'petal',
                ]
            ],
            'valentine' => [
                'name' => '情人节',
                'theme_config' => [
                    'primary_color' => '#e91e63',
                    'accent_color' => '#ffffff',
                    'banner_image' => '',
                    'slogan' => '情人节，为爱充值',
                    'badge_text' => '情人',
                    'badge_icon' => 'heart',
                    'deco_element' => 'petal',
                    'button_style' => 'rounded',
                    'animation' => 'petal',
                ]
            ],
            'school' => [
                'name' => '开学季',
                'theme_config' => [
                    'primary_color' => '#0277bd',
                    'accent_color' => '#b3e5fc',
                    'banner_image' => '',
                    'slogan' => '开学季，充值有礼',
                    'badge_text' => '开学',
                    'badge_icon' => 'book',
                    'deco_element' => 'star',
                    'button_style' => 'rounded',
                    'animation' => 'bounce',
                ]
            ],
        ];
    }

    public static function getThemePresets(): array
    {
        return array_values(self::presets());
    }

    public static function getConfig(): array
    {
        $config = [
            'switch'        => ConfigService::get('activity_deduct', 'switch', 0),
            'ratio'         => (int) ConfigService::get('activity_deduct', 'ratio', 40),
            'recharge_to_activity' => (int) ConfigService::get('activity_deduct', 'recharge_to_activity', 0),
            'activity_name' => ConfigService::get('activity_deduct', 'activity_name', ''),
            'current_theme' => ConfigService::get('activity_deduct', 'current_theme', ''),
            'start_time'    => ConfigService::get('activity_deduct', 'start_time', ''),
            'end_time'      => ConfigService::get('activity_deduct', 'end_time', ''),
            'theme_config'  => ConfigService::get('activity_deduct', 'theme_config', []),
        ];
        $config['theme_presets'] = self::getThemePresets();
        return $config;
    }

    public static function setConfig(array $params): void
    {
        ConfigService::set('activity_deduct', 'switch',        $params['switch'] ?? 0);
        ConfigService::set('activity_deduct', 'ratio',         $params['ratio'] ?? 40);
        ConfigService::set('activity_deduct', 'recharge_to_activity', $params['recharge_to_activity'] ?? 0);
        ConfigService::set('activity_deduct', 'activity_name', $params['activity_name'] ?? '');
        ConfigService::set('activity_deduct', 'current_theme', $params['current_theme'] ?? '');
        ConfigService::set('activity_deduct', 'start_time',    $params['start_time'] ?? '');
        ConfigService::set('activity_deduct', 'end_time',      $params['end_time'] ?? '');
        ConfigService::set('activity_deduct', 'theme_config',  $params['theme_config'] ?? []);
    }
}
