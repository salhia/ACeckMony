<?php
// config/discounts.php
return [
    'methods' => [
        'percentage' => [
            'name' => 'خصم نسبة مئوية',
            'description' => 'خصم حسب نسبة من المبلغ الإجمالي'
        ],
        'fixed' => [
            'name' => 'خصم مبلغ ثابت',
            'description' => 'خصم بمبلغ محدد بغض النظر عن المبلغ الإجمالي'
        ],
        'stair' => [
            'name' => 'خصم متدرج',
            'description' => 'خصم يزيد مع زيادة المبلغ'
        ],
        'seasonal' => [
            'name' => 'خصم موسمي',
            'description' => 'خصم لفترة محددة'
        ]
    ],
];
