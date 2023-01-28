<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'og :count til',
        ],

        'messages' => [
            'copied' => 'Kopiert',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Søk',
            'placeholder' => 'Søk',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => 'Viser :first til :last av :total resultater',

        'fields' => [

            'records_per_page' => [

                'label' => 'per side',

                'options' => [
                    'all' => 'Alle',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Gå til side :page',
            ],

            'next' => [
                'label' => 'Neste',
            ],

            'previous' => [
                'label' => 'Forrige',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Reorder records',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [
        'heading' => 'Ingen resultater funnet',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Fjern filter',
            ],

            'remove_all' => [
                'label' => 'Fjern alle filtre',
                'tooltip' => 'Fjern alle filtre',
            ],

            'reset' => [
                'label' => 'Tilbakestill filtre',
            ],

        ],

        'indicator' => 'Aktive filtre',

        'multi_select' => [
            'placeholder' => 'Alle',
        ],

        'select' => [
            'placeholder' => 'Alle',
        ],

        'trashed' => [

            'label' => 'Deleted records',

            'only_trashed' => 'Only deleted records',

            'with_trashed' => 'With deleted records',

            'without_trashed' => 'Without deleted records',

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => '1 post valgt.|:count poster valgt.',

        'buttons' => [

            'select_all' => [
                'label' => 'Velg alle :count',
            ],

            'deselect_all' => [
                'label' => 'Fjern merket for alle',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sorter etter',
            ],

            'direction' => [

                'label' => 'Sort direction',

                'options' => [
                    'asc' => 'Stigende',
                    'desc' => 'Synkende',
                ],

            ],

        ],

    ],

];
