<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // 1. حقن خط Cairo وتطبيق ألوان الهوية البصرية السورية (اللايت والدارك)
      // 1. حقن خط Cairo وتطبيق ألوان الهوية البصرية السورية (اللايت والدارك)
      FilamentView::registerRenderHook(
        PanelsRenderHook::HEAD_END,
        fn (): string => Blade::render('
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

            <style>
                :root {
                    --sy-green: #053326;
                    --sy-green-hover: #084735;
                    --sy-green-dark: #032219;
                    --sy-gold: #b9a473;
                    --sy-gold-light: #d4c297;
                    --sy-gold-dark: #968152;
                }

                /* --- الخطوط والأحجام --- */
                body, html, .fi-body {
                    font-family: "Cairo", sans-serif !important;
                    font-size: 20px !important;
                }

                .fi-fo-field-wrp-label,
                .fi-fo-field-wrp-label span {
                    font-size: 20px !important;
                    font-weight: 800 !important;
                }

                input, select, textarea, .fi-input-wrp {
                    font-size: 20px !important;
                    font-weight: 700 !important;
                    padding-top: 6px !important;
                    padding-bottom: 6px !important;
                }

                .fi-step-header, .fi-tabs-item-label, .fi-section-header-heading {
                    font-size: 20px !important;
                    font-weight: 800 !important;
                }

                .fi-ta-header-cell-label {
                    font-size: 20px !important;
                    font-weight: 800 !important;
                }

                .fi-ta-cell {
                    font-size: 20px !important;
                    font-weight: 600 !important;
                }

                .fi-sidebar-item-label {
                    font-size: 18px !important;
                    font-weight: 800 !important;
                }

                .fi-btn-label {
                    font-size: 17px !important;
                    font-weight: 800 !important;
                }

                /* =========================================
                   السلايدر (القائمة الجانبية): أخضر ملكي + كلمات ذهبية
                   ========================================= */
                .fi-sidebar,
                .fi-sidebar-header {
                    background-color: var(--sy-green) !important;
                    border-color: rgba(185, 164, 115, 0.35) !important;
                }

                /* الكلمات والأيقونات داخل السلايدر باللون الذهبي في اللايت مود */
                html:not(.dark) .fi-sidebar-item-label,
                html:not(.dark) .fi-sidebar-item-icon,
                html:not(.dark) .fi-sidebar-group-label,
                html:not(.dark) .fi-sidebar-group-collapse-button {
                    color: var(--sy-gold-light) !important;
                }

                /* في الدارك مود تبقى فاتحة وذهبية عند التحديد */
                html.dark .fi-sidebar-item-label,
                html.dark .fi-sidebar-item-icon,
                html.dark .fi-sidebar-group-label,
                html.dark .fi-sidebar-group-collapse-button {
                    color: #e5e7eb !important;
                }

                /* إلغاء الخلفية البيضاء للعنصر النشط وجعلها متناسقة مع الذهبي والأخضر */
                .fi-sidebar-item-button:hover,
                .fi-sidebar-item > a:hover {
                    background-color: var(--sy-green-hover) !important;
                }

                .fi-sidebar-item-active > a,
                .fi-sidebar-item-active .fi-sidebar-item-button,
                html:not(.dark) .fi-sidebar-item-active > a,
                html:not(.dark) .fi-sidebar-item-active .fi-sidebar-item-button {
                    background-color: rgba(185, 164, 115, 0.2) !important;
                    border-inline-start: 3px solid var(--sy-gold) !important;
                    box-shadow: none !important;
                }

                .fi-sidebar-item-active .fi-sidebar-item-label,
                .fi-sidebar-item-active .fi-sidebar-item-icon {
                    color: #E5E5E5 !important;
                }

                .fi-sidebar .brand-title {
                    color: #E5E5E5 !important;
                }
                .fi-sidebar .brand-subtitle {
                    color: var(--sy-gold-light) !important;
                }

                /* =========================================
                   اللايت مود: خلفية #E5E5E5 + جدول أخضر بحواف ذهبية
                   ========================================= */
                   /* --- تنسيق الجدول (Table) في اللايت مود: أخضر + حواف وكتابة ذهبية --- */
                   html:not(.dark) .fi-ta-ctn {
                       background-color: var(--sy-green) !important;
                       border: 2px solid var(--sy-gold) !important;
                       box-shadow: 0 4px 12px rgba(5, 51, 38, 0.15) !important;
                       --tw-ring-color: var(--sy-gold) !important;
                   }
                   
                   html:not(.dark) .fi-ta-header-ctn,
                   html:not(.dark) .fi-ta-header-toolbar,
                   html:not(.dark) .fi-ta-content,
                   html:not(.dark) .fi-ta-table,
                   html:not(.dark) .fi-ta-table tbody,
                   html:not(.dark) .fi-ta-empty-state,
                   html:not(.dark) .fi-pagination {
                       background-color: var(--sy-green) !important;
                       border-color: rgba(185, 164, 115, 0.4) !important;
                   }
                   
                   html:not(.dark) .fi-ta-table thead,
                   html:not(.dark) .fi-ta-table thead tr {
                       background-color: var(--sy-green-dark) !important;
                       border-bottom: 1.5px solid var(--sy-gold) !important;
                   }
                   
                   /* إجبار كل النصوص والأيقونات داخل الترويسة والصفوف والترقيم على اللون الذهبي */
                   html:not(.dark) .fi-ta-table thead th,
                   html:not(.dark) .fi-ta-table thead th *,
                   html:not(.dark) .fi-ta-header-cell-label,
                   html:not(.dark) .fi-ta-table tbody td,
                   html:not(.dark) .fi-ta-table tbody td *:not(.fi-badge):not(.fi-badge *),
                   html:not(.dark) .fi-ta-cell,
                   html:not(.dark) .fi-ta-text-item-label,
                   html:not(.dark) .fi-ta-empty-state-heading,
                   html:not(.dark) .fi-ta-empty-state-description,
                   html:not(.dark) .fi-pagination,
                   html:not(.dark) .fi-pagination * {
                       color: var(--sy-gold-light) !important;
                   }
                   
                   html:not(.dark) .fi-ta-row {
                       border-color: rgba(185, 164, 115, 0.25) !important;
                   }
                   
                   html:not(.dark) .fi-ta-row:hover {
                       background-color: var(--sy-green-hover) !important;
                   }
                   
                   html:not(.dark) .fi-ta-empty-state-icon-ctn {
                       background-color: rgba(185, 164, 115, 0.2) !important;
                   }
                   
                   html:not(.dark) .fi-ta-empty-state-icon {
                       color: var(--sy-gold-light) !important;
                   }
                   
                   /* حقل البحث داخل الجدول (يبقى نصه داكن ليكون واضحاً على الخلفية الفاتحة) */
                   html:not(.dark) .fi-ta-header-toolbar .fi-input-wrp {
                       background-color: #E5E5E5 !important;
                       border: 1.5px solid var(--sy-gold) !important;
                   }
                   html:not(.dark) .fi-ta-header-toolbar .fi-input-wrp input {
                       color: #053326 !important;
                   }
                /* =========================================
                   الدارك مود: خلفية #1F1F1F + أخضر ملكي + حواف ذهبية
                   ========================================= */
                html.dark body,
                html.dark .fi-body,
                html.dark .fi-main {
                    background-color: #1F1F1F !important;
                    color: #f3f4f6 !important;
                }

                html.dark .fi-topbar {
                    background-color: #0b120f !important;
                    border-bottom: 1px solid rgba(185, 164, 115, 0.35) !important;
                }

                html.dark .fi-section,
                html.dark .fi-ta-ctn,
                html.dark .fi-simple-main {
                    background-color: #0d1612 !important;
                    border: 1.5px solid rgba(185, 164, 115, 0.5) !important;
                }

                html.dark .fi-fo-field-wrp-label,
                html.dark .fi-fo-field-wrp-label span,
                html.dark .fi-section-header-heading {
                    color: var(--sy-gold-light) !important;
                }

                html.dark .fi-ta-header-cell-label {
                    color: var(--sy-gold) !important;
                }
                /* =========================================
   إطار بطاقة تسجيل الدخول: حواف ذهبية + ظل واضح (اللايت مود)
   ========================================= */
html:not(.dark) .fi-simple-main {
    background-color: #ffffff !important;
    border: 2px solid var(--sy-gold) !important;
    --tw-ring-color: var(--sy-gold) !important;
    border-radius: 18px !important;
    box-shadow:
        0 18px 38px -10px rgba(5, 51, 38, 0.22),
        0 6px 16px -4px rgba(185, 164, 115, 0.35) !important;
}

/* =========================================
   إطار بطاقة تسجيل الدخول: حواف ذهبية + ظل واضح (الدارك مود)
   ========================================= */
html.dark .fi-simple-main {
    background-color: #0d1612 !important;
    border: 2px solid var(--sy-gold) !important;
    --tw-ring-color: var(--sy-gold) !important;
    border-radius: 18px !important;
    box-shadow:
        0 20px 45px -10px rgba(0, 0, 0, 0.8),
        0 0 24px -4px rgba(185, 164, 115, 0.28) !important;
}
            </style>
        ')
    );

        // 2. زر تبديل اللغة (عربي / إنكليزي) في الشريط العلوي بألوان الهوية
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => Blade::render('
                @php
                    $isArabic = app()->getLocale() === "ar";
                    $targetLocale = $isArabic ? "en" : "ar";
                    $label = $isArabic ? "English" : "العربية";
                @endphp
                <a href="{{ route("lang.switch", $targetLocale) }}"
                   style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; background-color: #053326; color: #d4c297; border: 1px solid #b9a473; font-size: 14px; font-weight: 700; text-decoration: none; margin-inline-end: 8px;">
                    <span>🌐</span>
                    <span>{{ $label }}</span>
                </a>
            ')
        );

        // 3. سكريبت القارئ الضوئي للـ QR في صفحة سجل الزيارات
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('
                @if(request()->routeIs("filament.admin.resources.visit-records.index"))
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            let barcodeBuffer = "";
                            let lastKeyTime = Date.now();

                            document.addEventListener("keydown", function (e) {
                                if (["INPUT", "TEXTAREA", "SELECT"].includes(document.activeElement.tagName)) {
                                    return;
                                }

                                const currentTime = Date.now();
                                if (currentTime - lastKeyTime > 100) {
                                    barcodeBuffer = "";
                                }
                                lastKeyTime = currentTime;

                                if (e.key === "Enter") {
                                    if (barcodeBuffer.length > 2) {
                                        const livewireEl = document.querySelector("[wire\\:id]");
                                        if (livewireEl) {
                                            Livewire.find(livewireEl.getAttribute("wire:id")).call("processCheckout", barcodeBuffer);
                                        }
                                        barcodeBuffer = "";
                                    }
                                } else if (e.key.length === 1) {
                                    barcodeBuffer += e.key;
                                }
                            });
                        });
                    </script>
                @endif
            ')
        );

        return $panel
            ->font('Cairo')
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(fn () => app()->getLocale() === 'ar'
                ? 'نظام الزوار - الهيئة العامة للضرائب والرسوم'
                : 'Visitor System - General Commission for Taxes and Fees'
            )
            ->brandLogo(function () {
                $isArabic = app()->getLocale() === 'ar';
                $title = $isArabic
                    ? 'الجمهورية العربية السورية - وزارة المالية'
                    : 'Syrian Arab Republic - Ministry of Finance';
                $subtitle = $isArabic
                    ? 'الهيئة العامة للضرائب والرسوم (نظام الزوار)'
                    : 'General Commission for Taxes & Fees (Visitors)';

                $logoUrl = asset('image/logo.jpg');

                return new HtmlString("
                    <style>
                        /* التنسيق الافتراضي (لصفحة تسجيل الدخول): الصورة فوق بالمنتصف وحجمها 150 */
                        .brand-box {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            gap: 10px;
                            width: 100%;
                        }
                        .brand-box img {
                            height: 150px;
                            width: auto;
                            object-fit: contain;
                            border-radius: 12px;
                            flex-shrink: 0;
                        }
                        .brand-text {
                            display: flex;
                            flex-direction: column;
                        }
                        .brand-title {
                            color: #053326;
                            font-size: 16px;
                            font-weight: 800;
                            line-height: 1.35;
                        }
                        .brand-subtitle {
                            color: #968152;
                            font-size: 13px;
                            font-weight: 700;
                            line-height: 1.3;
                        }

                        /* تنسيق القائمة الجانبية والشريط العلوي: الصورة بجانب الكلام حسب اللغة وحجم متوسط */
                        .fi-sidebar .brand-box,
                        .fi-topbar .brand-box {
                            flex-direction: row;
                            justify-content: flex-start;
                            align-items: center;
                            text-align: start;
                            gap: 10px;
                        }
                        .fi-sidebar .brand-box img,
                        .fi-topbar .brand-box img {
                            height: 44px;
                            width: auto;
                            border-radius: 6px;
                        }
                        .fi-sidebar .brand-title,
                        .fi-topbar .brand-title {
                            font-size: 12px;
                            line-height: 1.25;
                        }
                        .fi-sidebar .brand-subtitle,
                        .fi-topbar .brand-subtitle {
                            font-size: 10px;
                            line-height: 1.2;
                        }

                        /* الوضع الليلي (لصفحة تسجيل الدخول) */
                        html.dark .brand-title {
                            color: #ffffff;
                        }
                        html.dark .brand-subtitle {
                            color: #d4c297;
                        }
                    </style>
                    <div class='brand-box'>
                        <img src='{$logoUrl}' alt='Logo'>
                        <div class='brand-text'>
                            <span class='brand-title'>{$title}</span>
                            <span class='brand-subtitle'>{$subtitle}</span>
                        </div>
                    </div>
                ");
            })
            ->brandLogoHeight('auto')
            ->favicon(asset('image/logo.jpg'))
            ->colors([
                'primary' => [
                    50  => '#edf7f4',
                    100 => '#d2ebe3',
                    200 => '#a8d7c8',
                    300 => '#74bca7',
                    400 => '#419b82',
                    500 => '#053326', // الأخضر الملكي الرسمي
                    600 => '#042c21',
                    700 => '#03241b',
                    800 => '#021b14',
                    900 => '#01130e',
                    950 => '#010b08',
                ],
                'warning' => [
                    50  => '#faf8f2',
                    100 => '#f2ecdc',
                    200 => '#e5d8b8',
                    300 => '#d4c297',
                    400 => '#c5b081',
                    500 => '#b9a473', // الذهبي الرسمي للشعار
                    600 => '#968152',
                    700 => '#78653f',
                    800 => '#5c4d30',
                    900 => '#423722',
                    950 => '#292214',
                ],
                'gray' => Color::Neutral,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}