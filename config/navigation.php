<?php

return [

    'menu' => [

        [
            'name' => 'لوحة التحكم',
            'icon' => 'heroicon-o-home',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'roles' => ['manager', 'admin'],
        ],

        [
            'name' => 'الأصناف والمنتجات',
            'icon' => 'heroicon-o-table-cells',
            'route' => 'variants.index',
            'active' => 'variants.*',
            'roles' => ['manager'],
        ],

        [
            'name' => 'الرصيد',
            'icon' => 'heroicon-o-banknotes',
            'route' => 'balances.index',
            'active' => 'balances.*',
            'roles' => ['manager'],
        ],

        [
            'name' => 'الأرباح',
            'icon' => 'heroicon-o-chart-bar',
            'route' => 'profits.index',
            'active' => 'profits.*',
            'roles' => ['manager'],
        ],
	
        [
            'name' => 'الموردين',
            'icon' => 'heroicon-o-building-storefront',
            'route' => 'suppliers.index',
            'active' => 'suppliers.*',
            'roles' => ['manager'],
        ],
	
        [
            'name' => 'انشاء فاتورة بيع',
            'icon' => 'heroicon-o-receipt-percent',
            'route' => 'sales.invoice',
            'active' => 'sales.invoice*',
            'roles' => ['manager', 'admin'],
        ],

        [
            'name' => 'المبيعات',
            'icon' => 'heroicon-o-credit-card',
            'route' => 'sales.index',
            'active' => 'sales.*',
            'roles' => ['manager'],
        ],

        [
            'name' => 'الخرج/الأجل',
            'icon' => 'heroicon-o-clock',
            'route' => 'credits.index',
            'active' => 'credits.*',
            'roles' => ['manager', 'admin'],
        ],
[
    'name' => 'المرتجعات',
    'icon' => 'heroicon-o-arrow-uturn-left',
    'route' => 'returns.index',
    'active' => 'returns.*',
    'roles' => ['manager', 'admin'],
],

        [
            'name' => 'المخزون',
            'icon' => 'heroicon-o-archive-box',
            'roles' => ['manager'], // المجموعة كلها للـ manager بس
            'children' => [
                [
                    'name' => 'جرد المخزون',
                    'route' => 'variants.index',
                    'icon' => 'heroicon-o-clipboard-document-list',
                    'roles' => ['manager'],
                ],
                [
                    'name' => 'تنبيهات المخزون المنخفض',
                    'route' => 'variants.index',
                    'query' => ['low_stock' => 1],
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'roles' => ['manager'],
                ],
            ],
        ],

       /*
        [
            'name' => 'المشتريات',
            'icon' => 'heroicon-o-shopping-bag',
            'active' => 'purchases.*',
            'roles' => ['manager'],
            'children' => [
                [
                    'name' => 'فواتير الشراء',
                    'route' => 'purchases.index',
                    'icon' => 'heroicon-o-document-text',
                    'roles' => ['manager'],
                ],
                [
                    'name' => 'طلبات التوريد',
                    'route' => 'purchases.create',
                    'icon' => 'heroicon-o-plus-circle',
                    'roles' => ['manager'],
                ],
                [
                    'name' => 'مرتجعات الموردين',
                    'route' => 'salesreturn.index',
                    'icon' => 'heroicon-o-arrow-uturn-left',
                    'roles' => ['manager'],
                ],
            ],
        ],
	*/
        [
            'name' => 'الإعدادات',
            'icon' => 'heroicon-o-cog-6-tooth',
            'route' => 'admins.index',
            'active' => 'admins.*',
            'roles' => ['manager'],
        ],

    ],

    'icon_style' => 'outline',
];
