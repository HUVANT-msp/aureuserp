<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CustomerPanelProvider;
use Huvant\Bridge\BridgeServiceProvider;
use Huvant\Calendar\CalendarServiceProvider;
use Huvant\Documents\DocumentsServiceProvider;
use Huvant\Home\HomeServiceProvider;
use Huvant\Insights\InsightsServiceProvider;
use Huvant\Meetings\MeetingsServiceProvider;
use Huvant\Orders\OrdersServiceProvider;
use Huvant\Tasks\TasksServiceProvider;
use Huvant\Teams\TeamsServiceProvider;
use Huvant\Worklog\WorklogServiceProvider;
use Webkul\Account\AccountServiceProvider;
use Webkul\Accounting\AccountingServiceProvider;
use Webkul\Analytic\AnalyticServiceProvider;
use Webkul\Barcode\BarcodeServiceProvider;
use Webkul\Blog\BlogServiceProvider;
use Webkul\Chatter\ChatterServiceProvider;
use Webkul\Contact\ContactServiceProvider;
use Webkul\Employee\EmployeeServiceProvider;
use Webkul\Field\FieldServiceProvider;
use Webkul\FullCalendar\FullCalendarServiceProvider;
use Webkul\Inventory\InventoryServiceProvider;
use Webkul\Invoice\InvoiceServiceProvider;
use Webkul\Maintenance\MaintenanceServiceProvider;
use Webkul\Manufacturing\ManufacturingServiceProvider;
use Webkul\Partner\PartnerServiceProvider;
use Webkul\Payment\PaymentServiceProvider;
use Webkul\PluginManager\PluginManagerServiceProvider;
use Webkul\Product\ProductServiceProvider;
use Webkul\Project\ProjectServiceProvider;
use Webkul\Purchase\PurchaseServiceProvider;
use Webkul\Recruitment\RecruitmentServiceProvider;
use Webkul\Sale\SaleServiceProvider;
use Webkul\Security\SecurityServiceProvider;
use Webkul\Support\SupportServiceProvider;
use Webkul\TableViews\TableViewsServiceProvider;
use Webkul\TimeOff\TimeOffServiceProvider;
use Webkul\Timesheet\TimesheetServiceProvider;
use Webkul\Website\WebsiteServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    CustomerPanelProvider::class,
    AccountingServiceProvider::class,
    AccountServiceProvider::class,
    AnalyticServiceProvider::class,
    BarcodeServiceProvider::class,
    BlogServiceProvider::class,
    ChatterServiceProvider::class,
    ContactServiceProvider::class,
    EmployeeServiceProvider::class,
    FieldServiceProvider::class,
    InventoryServiceProvider::class,
    InvoiceServiceProvider::class,
    MaintenanceServiceProvider::class,
    ManufacturingServiceProvider::class,
    PartnerServiceProvider::class,
    PaymentServiceProvider::class,
    ProductServiceProvider::class,
    ProjectServiceProvider::class,
    PurchaseServiceProvider::class,
    RecruitmentServiceProvider::class,
    SaleServiceProvider::class,
    SecurityServiceProvider::class,
    SupportServiceProvider::class,
    TableViewsServiceProvider::class,
    TimeOffServiceProvider::class,
    FullCalendarServiceProvider::class,
    TimesheetServiceProvider::class,
    WebsiteServiceProvider::class,
    BridgeServiceProvider::class,
    MeetingsServiceProvider::class,
    TeamsServiceProvider::class,
    WorklogServiceProvider::class,
    OrdersServiceProvider::class,
    DocumentsServiceProvider::class,
    TasksServiceProvider::class,
    InsightsServiceProvider::class,
    CalendarServiceProvider::class,
    HomeServiceProvider::class,
    PluginManagerServiceProvider::class,
];
