# Cache Tags Documentation

## Tag: timesheet

- **Used For:** Caching working times.
- **Files/Functions Using This Tag:**

  1- TimesheetResource/Pages/ListTimesheets.php

     - `function getTabs(){}` - cache key: `timesheets-years`

  2- Models/Timesheet.php

     - `function timeUsedThisYear(){}` - cache key: `timeUsedThisYear`

     - `function timeUsedLastYear(){}` - cache key: `timeUsedLastYear`

  3- Services/UserStatsService.php

     - `function getNumberOfAssistents(){}` - cache key: `number-of-assisstents`

     - `function getHoursUsedThisYear(){}` - cache key: `hours-used-this-year`

     - `function getHoursUsedThisMonthDescription(){}` - cache key: `hours-used-this-month-description`

     - `function getHoursUsedInMinutes(){}` - cache key: `hoursUsedInMinutes`

     - `function getHoursUsedThisWeek(){}` - cache key: `getHoursUsedThisWeek`

     - `function getPlannedHoursRestOfYear(){}` - cache key: `planned-hours-rest-of-year`

     - `function getRemainingHoursWithPlanned(){}` - cache key: `planned-hours-remaining`

     - `function getPlannedHoursThisWeek(){}` - cache key: `planned-hours-this-week`

  4- Services/DateTimeService.php

     - `function getDisabledDates(){}` - cache key: `disabled_dates:user_{userId}:record_{recordId}`

  5- Widgets/Ansatte.php

     - `Textcolumn->getStateUsing()` - cache key: `WorkedThisYear{$record->id}`

  6- TimesheetResource/Widgets/HoursUsedEachMonth.php

     - `function $table->columns([])` - cache key: `Totalt-{$state}`

  7- TimesheetResource/Widgets/CalendarWidget.php

     - `function fetchEvents(){}` - cache key: `schedules`
- **Invalidated When:** Updating or adding new working times. Default: 1 week.
- **Invalidated Where:**
  1. TimesheetResource/Pages/Edit.php
     
     - `function afterSave(){}`
     - `function DeleteAction::make()->after(),`
  
  2- TimesheetResource/Pages/Create.php
     
     - `function afterCreate(){}`
  
  3- TimesheetResource/Widgets/CalendarWidget.php

     - `function refreshRecords(){}`

     - `function eventUpdate(){}`

---

## Tag: testresult

- **Used For:** Caching Test results.
- **Files/Functions Using This Tag:**

  1- Widgets/RheitChart.php

     - `function getData(){}` - cache key: `rheitChart`

  2- Widgets/StyrkeChart.php

     - `function getData(){}` - cache key: `styrkeChart`

  3- Widgets/VektChart.php

     - `function getData(){}` - cache key: `vektChart`
     
     
     
- **Invalidated When:** Updating or adding new testresults. Default: 1 month.
- **Invalidated where:**
  1. TestResultsResource\Pages\Edit.php
     
     - `function afterSave(){}`
  
  2- TestResultsResource\Pages\Create.php
     
     - `function afterCreate(){}`

---

## Tag: settings

- **Used for:** Caching user settings

- **Files/functions Using This Tag:**

  1- Models/Settings.php

     - `function getUserSettings(){}` - cache key: `user-settings-{userId}`

- **Invalidated When:** Updating or adding new settings. Default: 1 month.

- **Invalidated Where:**
  
  1. Pages/Settings.php
     
     - `function submit(){}`
  
  ---

## Tag: medisinsk

- **Used for:** Caching Medical

- **Files/functions Using This Tag:**

  1- KategoriResource.php

     - `function getNavigationBadge(){}` - cache key: `CategoryNavigationBadge`

  2- UtstyrResource.php

     - `function getNavigationBadge(){}` - cache key: `UtstyrNavigationBadge`

  3- ResepterResource.php

     - `function getNavigationBadge(){}` - cache key: `ReseptNavigationBadge`

- **Invalidated When:** Updating or adding medical equipment/prescriptions. Default: 1 month.

- **Invalidated Where:**

  1- CreateKategori.php

     - `function afterCreate(){}`

  2- EditKategori.php

     - `function afterSave(){}`

  3- CreateUtstyr.php

     - `function afterCreate(){}`

  4- EditUtstyr.php

     - `function afterSave(){}`

  5- CreateResepter.php

     - `function afterCreate(){}`

  6- EditResepter.php

     - `function afterSave(){}`

---



## Tag: bruker

- **Used for:** Caching user data

- **Files/functions Using This Tag:**

  1- UserResource.php

     - `function getNavigationBadge(){}` - cache key: `UserNavigationBadge`

- **Invalidated When:** Updating or adding users. Default 1 month.

- **Invalidated Where:**
  
  1. EditUser.php
     
     - `function afterSave(){}`
  
  2- CreateUser.php
     
     - `function afterCreate(){}`


