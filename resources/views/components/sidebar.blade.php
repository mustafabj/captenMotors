   <!-- Sidebar -->
   <div class="kt-sidebar bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
       data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0" id="sidebar">
       <div class="kt-sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0"
           id="sidebar_header">
           <a class="dark:hidden" href="{{ route('index') }}">
               <img class="default-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/default-logo.svg') }}" />
               <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/mini-logo.svg') }}" />
           </a>
           <a class="hidden dark:block" href="{{ route('index') }}">
               <img class="default-logo min-h-[22px] max-w-none"
                   src="{{ asset('assets/media/app/default-logo-dark.svg') }}" />
               <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/mini-logo.svg') }}" />
           </a>
           <button
               class="kt-btn kt-btn-outline kt-btn-icon size-[30px] absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
               data-kt-toggle="body" data-kt-toggle-class="kt-sidebar-collapse" id="sidebar_toggle">
               <i
                   class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:kt-toggle-active:rotate-0">
               </i>
           </button>
       </div>
       <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
           <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-kt-scrollable="true"
               data-kt-scrollable-dependencies="#sidebar_header" data-kt-scrollable-height="auto"
               data-kt-scrollable-offset="0px" data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
               <!-- Sidebar Menu -->
               <div class="kt-menu flex flex-col grow gap-1" data-kt-menu="true"
                   data-kt-menu-accordion-expand-all="false" id="sidebar_menu">
                   <div class="kt-menu-item" data-kt-menu-item-toggle="accordion" data-kt-menu-item-trigger="click">
                       <div class="kt-menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                           tabindex="0">
                           <span class="kt-menu-icon items-start text-muted-foreground w-[20px]">
                               <i class="ki-filled ki-element-11 text-lg">
                               </i>
                           </span>
                           <span
                               class="kt-menu-title text-sm font-medium text-foreground kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                               Home
                           </span>
                           <span
                               class="kt-menu-arrow text-muted-foreground w-[20px] shrink-0 justify-end ms-1 me-[-10px]">
                               <span class="inline-flex kt-menu-item-show:hidden">
                                   <i class="ki-filled ki-plus text-[11px]">
                                   </i>
                               </span>
                               <span class="hidden kt-menu-item-show:inline-flex">
                                   <i class="ki-filled ki-minus text-[11px]">
                                   </i>
                               </span>
                           </span>
                       </div>
                       <div
                           class="kt-menu-accordion show gap-1 ps-[10px] relative before:absolute before:start-[20px] before:top-0 before:bottom-0 before:border-s before:border-border">
                           <div class="kt-menu-item {{ request()->is('/') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Dashboard
                                   </span>
                               </a>
                           </div>
                           <div class="kt-menu-item {{ request()->is('cars*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('cars.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Cars
                                   </span>
                               </a>
                           </div>
                           @if (auth()->user() && auth()->user()->hasRole('admin'))
                           <div class="kt-menu-item {{ request()->is('sold-cars*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('sold-cars.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Sold Cars
                                   </span>
                               </a>
                           </div>
                           @endif
                           <div class="kt-menu-item {{ request()->is('other-costs*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('other-costs.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Other Costs
                                   </span>
                               </a>
                           </div>
                           @if (auth()->user() && auth()->user()->hasRole('admin'))
                           <div class="kt-menu-item {{ request()->is('bulk-deals*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('bulk-deals.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Bulk Deals
                                   </span>
                               </a>
                           </div>
                           <div class="kt-menu-item {{ request()->is('users*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('users.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Users
                                   </span>
                               </a>
                           </div>
                           @endif
                           {{-- <div class="kt-menu-item {{ request()->is('notifications*') ? 'active' : '' }}">
                               <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                   href="{{ route('notifications.index') }}" tabindex="0">
                                   <span
                                       class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary">
                                   </span>
                                   <span
                                       class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                       Notifications
                                   </span>
                               </a>
                           </div> --}}
                           @if (auth()->user() && auth()->user()->hasRole('admin'))
                               <div class="kt-menu-item {{ request()->is('equipment-cost-notifications*') ? 'active' : '' }}">
                                   <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                       href="{{ route('equipment-cost-notifications.index') }}" tabindex="0">
                                       <span
                                           class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary"></span>
                                       <span
                                           class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                           Pending Approvals
                                           @php
                                               $pendingCount = \App\Models\CarEquipmentCost::where('status', 'pending')->count();
                                           @endphp
                                           @if($pendingCount > 0)
                                           <span class="kt-badge kt-badge-warning kt-badge-sm ml-2">{{ $pendingCount }}</span>
                                           @endif
                                       </span>
                                   </a>
                               </div>
                               <div class="kt-menu-item {{ request()->is('store-capital*') ? 'active' : '' }}">
                                   <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                       href="{{ route('store-capital.index') }}" tabindex="0">
                                       <span
                                           class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary"></span>
                                       <span
                                           class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                           Store Capital
                                       </span>
                                   </a>
                               </div>
                           @endif
                       </div>
                   </div>
                   @if (auth()->user() && auth()->user()->hasRole('admin'))
                   <div class="kt-menu-item pt-2.25 pb-px">
                       <span
                           class="kt-menu-heading uppercase text-xs font-medium text-muted-foreground ps-[10px] pe-[10px]">
                           Reports
                       </span>
                   </div>
                   <div class="kt-menu-item" data-kt-menu-item-toggle="accordion" data-kt-menu-item-trigger="click">
                       <div class="kt-menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                           tabindex="0">
                           <span class="kt-menu-icon items-start text-muted-foreground w-[20px]">
                               <i class="ki-filled ki-tablet-text-up text-lg">
                               </i>
                           </span>
                           <span
                               class="kt-menu-title text-sm font-medium text-foreground kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                               Reports
                           </span>
                           <span
                               class="kt-menu-arrow text-muted-foreground w-[20px] shrink-0 justify-end ms-1 me-[-10px]">
                               <span class="inline-flex kt-menu-item-show:hidden">
                                   <i class="ki-filled ki-plus text-[11px]">
                                   </i>
                               </span>
                               <span class="hidden kt-menu-item-show:inline-flex">
                                   <i class="ki-filled ki-minus text-[11px]">
                                   </i>
                               </span>
                           </span>
                       </div>
                       <div
                           class="kt-menu-accordion show gap-1 ps-[10px] relative before:absolute before:start-[20px] before:top-0 before:bottom-0 before:border-s before:border-border">
                           @if (auth()->user() && auth()->user()->hasRole('admin'))
                               <div class="kt-menu-item {{ request()->is('reports/profit-loss*') ? 'active' : '' }}">
                                   <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                       href="{{ route('reports.profit-loss') }}" tabindex="0">
                                       <span
                                           class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary"></span>
                                       <span
                                           class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                           Profit & Loss
                                       </span>
                                   </a>
                               </div>
                               <div
                                   class="kt-menu-item {{ request()->is('reports/inventory-valuation*') ? 'active' : '' }}">
                                   <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                       href="{{ route('reports.inventory-valuation') }}" tabindex="0">
                                       <span
                                           class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary"></span>
                                       <span
                                           class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                           Inventory Valuation
                                       </span>
                                   </a>
                               </div>
                               <div
                                   class="kt-menu-item {{ request()->is('reports/equipment-cost-summary*') ? 'active' : '' }}">
                                   <a class="kt-menu-link border border-transparent items-center grow kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                       href="{{ route('reports.equipment-cost-summary') }}" tabindex="0">
                                       <span
                                           class="kt-menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary"></span>
                                       <span
                                           class="kt-menu-title text-2sm font-normal text-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary">
                                           Equipment Costs
                                       </span>
                                   </a>
                               </div>
                           @endif
                       </div>
                   </div>
                   @endif
               </div>
               <!-- End of Sidebar Menu -->
           </div>
       </div>
   </div>
   <!-- End of Sidebar -->
