# Cache Tags Documentation

## Tag: timesheet

- **Used For:** Caching working times.
- **Files/Functions Using This Tag:**
  1. Widgets/BrukteTimerChart.php
     
     - `function getData(){}`
  
  2- TimesheetResource/Pages/ListTimesheets.php
     
     - `function getTabs(){}`
  
  3- Models/Timesheet.php
     
     - `function timeUsedThisYear(){}`
     
     - `function timeUsedLastYear(){}`
  
  4- Services/UserStatsService.php
     
     - `function getHoursUsedThisYear(){}`
     
     - `function getHoursUsedThisMonthDescription(){}`
     
     - `function getHoursUsedInMinutes(){}`
  
  5- Widgets/Ansatte.php
     
     - `Textcolumn->getStateUsing(function (Model $record) {}`
  
  6- `TimesheetResource/Widgets/HoursUsedEachMonth.php`
     
     - `function $table->columns([])`
- **Invalidated When:** Updating or adding new working times. Default: 1 week.
- **Invalidated Where:**
  1. TimesheetResource/Pages/Edit.php
     
     - `function afterSave(){}`
  
  2- TimesheetResource/Pages/Create.php
     
     - `function afterCreate(){}`
  
  3- TimesheetResource/Widgets/CalendarWidget.php
     
     - `function eventUpdate(){}`
     
     - `function onEventResize(){}`
     
     - `function onEventDrop(){}`
     
     - `function editEvent(){}`
     
     - `function createEvent(){}`
     
     - `function delteEvent(){}`

---

## Tag: testresult

- **Used For:** Caching Test results.
- **Files/Functions Using This Tag:**
  1. Widgets\RheitChart.php
     - `function getData(){}`
  2. Widgets\StyrkeChart.php
     - `function getData(){}`
  3. Widgets\vektChart.php
     - `function getData(){}`
     
     
     
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
     
     - `function getUserSettings(){}`

- **Invalidated When:** Updating or adding new settings. Default: 1 month.

- **Invalidated Where:**
  
  1. Pages/Settings.php
     
     - `function submit(){}`
  
  ---

## Tag: medisinsk

- **Used for:** Caching Medical

- **Files/functions Using This Tag:** 
  
  1. KategoriResource.php
     
     - `function getNavigationBadge(){}`
  
  2- UtstyrResource.php
     
     - `function getNavigationBadge(){}`

- **Invalidated When:**

- **Invalidated Where:**

---



## Tag: bruker

- **Used for:** Caching user data

- **Files/functions Using This Tag:**
  
  1. UserResource.php
     
     - function getNavigationBadge(){}

- **Invalidated When:** Updating or adding users. Default 1 month.

- **Invalidated Where:**


