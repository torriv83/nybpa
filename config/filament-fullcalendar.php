<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

    'locale' => config('app.locale'),

    'headerToolbar' => [
        'left' => 'prev,next today',
        'center' => 'title',
        'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
    ],

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

    'slotLabelInterval' => ['hours' => 1],

    'aspectRatio' => 2.5,
];
