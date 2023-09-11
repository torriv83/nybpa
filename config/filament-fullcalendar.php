<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 *
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

    'locale' => config('app.locale'),

    'headerToolbar' => [
        'left' => 'prev,next,today',
        'center' => 'title',
        'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek', //,timeGridFourDay',
    ],
    //    'views' => [
    //        'timeGridFourDay' => [
    //            'type' => 'timeGrid',
    //            'duration' => ['days' => 4],
    //            'buttonText' => '4 day'
    //        ]
    //    ],

    'buttonText' => [
        'prev' => '<',
        'next' => '>',
        'today' => 'Idag',
        'month' => 'Mnd',
        'week' => 'U',
        'day' => 'D',
        'prevYear' => 'Forrige år',
        'nextYear' => 'Neste år',
        'listMonth' => 'Agenda',
        'listWeek' => 'UL',
    ],

    'contentHeight' => 'auto',

    'navLinks' => true,

    'editable' => true,

    'selectable' => true,

    'dayMaxEvents' => true,

    'weekNumbers' => true,

    'weekNumberCalculation' => 'ISO',

    'weekNumberFormat' => ['week' => 'numeric'],

    'nowIndicator' => true,

    'droppable' => true,

    'displayEventEnd' => true,

    'slotDuration' => '00:15:00',

    // 'googleCalendarApiKey' => 'AIzaSyBo-Vvbll0RbhGtA8wPqW8Tdxloy0GbUxA',

    // 'slotLabelInterval' => ['hours' => 1],
    'slotLabelFormat' => [
        'hour' => 'numeric',
        'minute' => '2-digit',
        'omitZeroMinute' => false,
        'meridiem' => 'short',
    ],

//    'aspectRatio' => 2,
];
