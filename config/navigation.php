<?php

return [
    'top' => [
        [
            'label' => 'マイページ',
            'href' => '/mypage',
            'active' => ['route' => 'mypage'],
            'icon' => 'user',
        ],
        [
            'label' => 'ワークページ',
            'href' => '/',
            'active' => ['route' => 'home'],
            'icon' => 'home',
        ],
    ],

    'sections' => [
        [
            'id' => 'selfCare',
            'title' => '日々のセルフケア',
            'items' => [
                [
                    'id' => 'conditionCheck',
                    'label' => 'コンディションチェック',
                    'active' => ['pattern' => 'condition-checks*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/condition-checks/create',
                            'active' => ['pattern' => 'condition-checks/create'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/condition-checks',
                            'active' => ['pattern' => 'condition-checks', 'except' => 'condition-checks/*'],
                        ],
                    ],
                ],
                [
                    'id' => 'selfCompassion',
                    'label' => 'セルフコンパッション日記',
                    'active' => ['pattern' => 'self-compassion-journals*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/self-compassion-journals',
                            'active' => ['pattern' => 'self-compassion-journals', 'except' => 'self-compassion-journals/list'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/self-compassion-journals/list',
                            'active' => ['pattern' => 'self-compassion-journals/list'],
                        ],
                    ],
                ],
                [
                    'label' => 'マインドフルネス瞑想',
                    'href' => '/mindfulness',
                    'active' => ['pattern' => 'mindfulness*'],
                ],
                [
                    'label' => 'コーピングリスト',
                    'href' => '/copings',
                    'active' => ['pattern' => 'copings*'],
                ],
                [
                    'label' => 'サポートネットワーク',
                    'href' => '/support-networks',
                    'active' => ['pattern' => 'support-networks*'],
                ],
            ],
        ],
        [
            'id' => 'cbt',
            'title' => '認知行動療法（CBT）',
            'items' => [
                [
                    'id' => 'stressor',
                    'label' => 'ストレッサーとストレス反応',
                    'active' => ['pattern' => 'stressor-and-responses*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/stressor-and-responses',
                            'active' => ['pattern' => 'stressor-and-responses', 'except' => 'stressor-and-responses/*'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/stressor-and-responses/list',
                            'active' => ['pattern' => 'stressor-and-responses/list'],
                        ],
                    ],
                ],
                [
                    'id' => 'column',
                    'label' => '認知再構成法(コラム法)',
                    'active' => ['pattern' => 'columns*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/columns',
                            'active' => ['pattern' => 'columns'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/columns/list',
                            'active' => ['pattern' => 'columns/list'],
                        ],
                        [
                            'label' => '適応的思考',
                            'href' => '/columns/adaptive-thoughts',
                            'active' => ['pattern' => 'columns/adaptive-thoughts'],
                        ],
                    ],
                ],
                [
                    'id' => 'problem',
                    'label' => '問題解決法',
                    'active' => ['pattern' => 'problem-solvings*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/problem-solvings',
                            'active' => ['pattern' => 'problem-solvings', 'except' => 'problem-solvings/*'],
                        ],
                        [
                            'label' => '問題解決法シート一覧',
                            'href' => '/problem-solvings/list',
                            'active' => ['pattern' => 'problem-solvings/list'],
                        ],
                        [
                            'label' => '振り返り',
                            'href' => '/problem-solvings/plans/new',
                            'active' => ['pattern' => 'problem-solvings/plans/new'],
                        ],
                        [
                            'label' => '振り返り一覧',
                            'href' => '/problem-solvings/plans',
                            'active' => ['pattern' => 'problem-solvings/plans', 'except' => 'problem-solvings/plans/*'],
                        ],
                    ],
                ],
                [
                    'id' => 'exposure',
                    'label' => 'エクスポージャー療法',
                    'active' => ['pattern' => 'exposures*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/exposures',
                            'active' => ['pattern' => 'exposures', 'except' => 'exposures/*'],
                        ],
                        [
                            'label' => 'シート一覧',
                            'href' => '/exposures/list',
                            'active' => ['pattern' => 'exposures/list'],
                        ],
                        [
                            'label' => '実施記録作成',
                            'href' => '/exposures/sessions/new',
                            'active' => ['pattern' => 'exposures/sessions/new'],
                        ],
                        [
                            'label' => '実施記録一覧',
                            'href' => '/exposures/sessions',
                            'active' => ['pattern' => 'exposures/sessions', 'except' => 'exposures/sessions/*'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'schema',
            'title' => 'スキーマ療法',
            'items' => [
                [
                    'id' => 'chronology',
                    'label' => 'スキーマ年表',
                    'active' => ['pattern' => 'schema-therapy/chronology*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/schema-therapy/chronology/create',
                            'active' => ['pattern' => 'schema-therapy/chronology/create'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/schema-therapy/chronology',
                            'active' => ['pattern' => 'schema-therapy/chronology', 'except' => 'schema-therapy/chronology/*'],
                        ],
                    ],
                ],
                [
                    'label' => '早期不適応的スキーマ',
                    'href' => '/early-maladaptive-schemas',
                    'active' => ['pattern' => 'early-maladaptive-schemas*'],
                ],
                [
                    'id' => 'modeDialogue',
                    'label' => 'スキーマモードの対話ワーク',
                    'active' => ['pattern' => 'schema-therapy/mode-work/dialogue*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/schema-therapy/mode-work/dialogue/create',
                            'active' => ['pattern' => 'schema-therapy/mode-work/dialogue/create'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/schema-therapy/mode-work/dialogue',
                            'active' => ['pattern' => 'schema-therapy/mode-work/dialogue', 'except' => 'schema-therapy/mode-work/dialogue/*'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'records',
            'title' => '記録・その他',
            'items' => [
                [
                    'id' => 'stressPersonEncyclopedia',
                    'label' => 'ストレス人物図鑑',
                    'active' => ['pattern' => 'stress-person-encyclopedias*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/stress-person-encyclopedias',
                            'active' => ['pattern' => 'stress-person-encyclopedias', 'except' => 'stress-person-encyclopedias/list'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/stress-person-encyclopedias/list',
                            'active' => ['pattern' => 'stress-person-encyclopedias/list'],
                        ],
                    ],
                ],
                [
                    'id' => 'notepad',
                    'label' => 'メモ帳',
                    'active' => ['pattern' => 'simple-notepad*'],
                    'children' => [
                        [
                            'label' => '新規作成',
                            'href' => '/simple-notepads',
                            'active' => ['pattern' => 'simple-notepads', 'except' => 'simple-notepads/*'],
                        ],
                        [
                            'label' => '一覧',
                            'href' => '/simple-notepads/list',
                            'active' => ['pattern' => 'simple-notepads/list'],
                        ],
                        [
                            'label' => 'タグ管理',
                            'href' => '/simple-notepad-tags',
                            'active' => ['pattern' => 'simple-notepad-tags*'],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
