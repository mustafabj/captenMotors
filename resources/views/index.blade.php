@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
            <!-- Welcome Section -->
            <div class="grid lg:grid-cols-4 gap-5 lg:gap-7.5">
                <div class="lg:col-span-4">
                    <div class="kt-card">
                        <div class="kt-card-content p-7.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        Welcome back, {{ auth()->user()->name }}!
                                    </h1>
                                    <p class="text-gray-600 mt-1">
                                        Here's what's happening with your car dealership today.
                                    </p>
         </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                                    <div class="text-2xl font-bold text-primary">{{ now()->format('g:i A') }}</div>
        </div>
         </div>
        </div>
         </div>
        </div>
         </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-7.5">
                <!-- Total Cars -->
                <div class="kt-card">
                    <div class="kt-card-content p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ki-filled ki-car text-blue-600 text-xl"></i>
        </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ \App\Models\Car::count() }}
       </div>
      </div>
           </div>
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-500">Total Cars</div>
                            <a href="{{ route('cars.index') }}"
                                class="text-blue-600 hover:text-blue-700 text-xs font-medium block">
                                View all cars →
                            </a>
         </div>
        </div>
        </div>

                <!-- Ready for Sale -->
                <div class="kt-card">
                    <div class="kt-card-content p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="ki-filled ki-check-circle text-green-600 text-xl"></i>
       </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ \App\Models\Car::where('status', 'ready')->count() }}
      </div>
     </div>
            </div>
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-500">Ready for Sale</div>
                            <a href="{{ route('cars.search', ['status' => 'ready']) }}"
                                class="text-green-600 hover:text-green-700 text-xs font-medium block">
                                View ready cars →
             </a>
            </div>
             </div>
              </div>

                @if(auth()->user()->hasRole('admin'))
                <!-- Cars Sold -->
                <div class="kt-card">
                    <div class="kt-card-content p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="ki-filled ki-medal-star text-purple-600 text-xl"></i>
              </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ \App\Models\SoldCar::count() }}
              </div>
             </div>
            </div>
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-500">Cars Sold</div>
                            <a href="{{ route('sold-cars.index') }}"
                                class="text-purple-600 hover:text-purple-700 text-xs font-medium block">
                                View sold cars →
             </a>
            </div>
            </div>
            </div>
                @else
                <!-- My Equipment Costs -->
                <div class="kt-card">
                    <div class="kt-card-content p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ki-filled ki-wrench text-blue-600 text-xl"></i>
           </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ \App\Models\CarEquipmentCost::where('user_id', auth()->id())->count() }}
          </div>
         </div>
        </div>
                        <div class="space-y-2">
                            <div class="text-sm font-medium text-gray-500">My Equipment Costs</div>
                            <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium block">
                                View my requests →
                            </a>
          </div>
         </div>
          </div>
                @endif

                @if (auth()->user()->hasRole('admin'))
                    <!-- Total Capital (Admin Only) -->
                    <div class="kt-card">
                        <div class="kt-card-content p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="ki-filled ki-dollar text-yellow-600 text-xl"></i>
          </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-yellow-600 relative flex items-center">
                                        <span id="capitalAmount" class="hidden">
                                            ${{ number_format(\App\Models\StoreCapital::currentTotal(), 0) }}
           </span>
                                        <span id="capitalHidden">
                                            ****
           </span>
                                        <button type="button" onclick="toggleCapitalVisibility()"
                                            class="ml-2 text-gray-400 hover:text-yellow-600 transition-colors cursor-pointer mx-2">
                                            <i id="capitalEyeIcon" class="ki-filled ki-eye text-lg"></i>
                                        </button>
          </div>
          </div>
          </div>
                            <div class="space-y-2">
                                <div class="text-sm font-medium text-gray-500">Store Capital</div>
                                <a href="{{ route('store-capital.index') }}"
                                    class="text-yellow-600 hover:text-yellow-700 text-xs font-medium block">
                                    Manage capital →
                                </a>
         </div>
         </div>
           </div>
                @else
                    <!-- Pending Notifications (User) -->
                    <div class="kt-card">
                        <div class="kt-card-content p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="ki-filled ki-notification-status text-orange-600 text-xl"></i>
           </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-orange-600">
                                        {{ auth()->user()->equipmentCostNotifications()->unread()->count() }}
          </div>
           </div>
           </div>
                            <div class="space-y-2">
                                <div class="text-sm font-medium text-gray-500">Pending Notifications</div>
                                <a href="{{ route('notifications.index') }}"
                                    class="text-orange-600 hover:text-orange-700 text-xs font-medium block">
                                    View notifications →
                                </a>
          </div>
           </div>
           </div>
                @endif
          </div>

            <!-- Charts and Details Section -->
            <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5">
                <!-- Car Status Distribution -->
      <div class="lg:col-span-2">
       <div class="kt-card h-full">
        <div class="kt-card-header">
                            <h3 class="kt-card-title">Car Status Distribution</h3>
         </div>
                        <div class="kt-card-content p-7.5">
                            @php
                                $statusCounts = \App\Models\Car::select('status', \DB::raw('count(*) as count'))
                                    ->groupBy('status')
                                    ->pluck('count', 'status')
                                    ->toArray();

                                $statusConfig = [
                                    'not_received' => ['color' => 'bg-gray-500', 'name' => 'Not Received'],
                                    'paint' => ['color' => 'bg-blue-500', 'name' => 'Paint'],
                                    'upholstery' => ['color' => 'bg-purple-500', 'name' => 'Upholstery'],
                                    'mechanic' => ['color' => 'bg-orange-500', 'name' => 'Mechanic'],
                                    'electrical' => ['color' => 'bg-yellow-500', 'name' => 'Electrical'],
                                    'agency' => ['color' => 'bg-indigo-500', 'name' => 'Agency'],
                                    'polish' => ['color' => 'bg-teal-500', 'name' => 'Polish'],
                                    'ready' => ['color' => 'bg-green-500', 'name' => 'Ready'],
                                    'sold' => ['color' => 'bg-red-500', 'name' => 'Sold'],
                                ];
                            @endphp

                            <div class="space-y-4">
                                @foreach ($statusConfig as $status => $config)
                                    @php $count = $statusCounts[$status] ?? 0; @endphp
                                    @if ($count > 0)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-4 h-4 rounded {{ $config['color'] }}"></div>
                                                <span
                                                    class="text-sm font-medium text-gray-700">{{ $config['name'] }}</span>
        </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="{{ $config['color'] }} h-2 rounded-full"
                                                        style="width: {{ $count > 0 ? ($count / array_sum($statusCounts)) * 100 : 0 }}%">
         </div>
        </div>
       </div>
      </div>
                                    @endif
                                @endforeach
     </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
      <div class="lg:col-span-1">
       <div class="kt-card h-full">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Recent Activity</h3>
          </div>
                        <div class="kt-card-content p-7.5">
                            <div class="space-y-4">
                                @php
                                    $recentCars = \App\Models\Car::with('statusHistories')->latest()->take(5)->get();
                                @endphp

                                @forelse($recentCars as $car)
                                    <div class="flex items-start space-x-3">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $car->model }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Added {{ $car->created_at->diffForHumans() }}
                                            </p>
           </div>
           </div>
                                @empty
                                    <div class="text-center py-6">
                                        <i class="ki-filled ki-information-2 text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No recent activity</p>
          </div>
                                @endforelse
           </div>
            </div>
            </div>
            </div>
            </div>

            @if (auth()->user()->hasRole('admin'))
                <!-- Admin Only Sections -->
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- Financial Overview -->
                    <div class="kt-card">
         <div class="kt-card-header">
                            <h3 class="kt-card-title">Financial Overview</h3>
          </div>
                        <div class="kt-card-content p-7.5">
                            @php
                                $totalPurchasePrice = \App\Models\Car::sum('purchase_price');
                                $totalSaleRevenue = \App\Models\SoldCar::sum('sale_price');
                                $totalEquipmentCosts = \App\Models\CarEquipmentCost::where('status', 'approved')->sum(
                                    'amount',
                                );
                                $totalOtherCosts = \App\Models\OtherCost::sum('amount');
                                $grossProfit =
                                    $totalSaleRevenue - $totalPurchasePrice - $totalEquipmentCosts - $totalOtherCosts;
                            @endphp

                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Total Investment</span>
                                    <span
                                        class="font-semibold text-red-600">-${{ number_format($totalPurchasePrice, 0) }}</span>
         </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Sales Revenue</span>
                                    <span
                                        class="font-semibold text-green-600">+${{ number_format($totalSaleRevenue, 0) }}</span>
                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Equipment Costs</span>
                                    <span
                                        class="font-semibold text-red-600">-${{ number_format($totalEquipmentCosts, 0) }}</span>
                 </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Other Costs</span>
                                    <span
                                        class="font-semibold text-red-600">-${{ number_format($totalOtherCosts, 0) }}</span>
                 </div>
                                <div class="flex justify-between items-center py-3 pt-4 border-t-2 border-gray-200">
                                    <span class="text-gray-900 font-bold">Net Profit</span>
                                    <span
                                        class="font-bold text-xl {{ $grossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $grossProfit >= 0 ? '+' : '' }}${{ number_format($grossProfit, 0) }}
                  </span>
                 </div>
                </div>
                </div>
                 </div>

                    <!-- Pending Approvals -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Pending Approvals</h3>
                 </div>
                        <div class="kt-card-content p-7.5">
                            @php
                                $pendingCosts = \App\Models\CarEquipmentCost::where('status', 'pending')
                                    ->with(['car', 'user'])
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp

                            <div class="space-y-4">
                                @forelse($pendingCosts as $cost)
                                    <div
                                        class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $cost->description }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $cost->car->model }} • by {{ $cost->user->name }}
                                            </p>
                 </div>
                                        <div class="text-right ml-3">
                                            <p class="text-sm font-bold text-gray-900">
                                                ${{ number_format($cost->amount, 0) }}</p>
                                            <a href="{{ route('equipment-cost-notifications.index') }}"
                                                class="text-xs text-blue-600 hover:text-blue-700">Review</a>
                 </div>
                 </div>
                                @empty
                                    <div class="text-center py-6">
                                        <i class="ki-filled ki-check-circle text-green-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No pending approvals</p>
                </div>
                                @endforelse
                 </div>
                 </div>
                </div>
                </div>
            @endif

            @if (!auth()->user()->hasRole('admin'))
                <!-- User Only Section -->
                <div class="grid lg:grid-cols-2 gap-5 lg:gap-7.5">
                    <!-- My Recent Equipment Costs -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">My Recent Equipment Costs</h3>
                 </div>
                        <div class="kt-card-content p-7.5">
                            @php
                                $myCosts = \App\Models\CarEquipmentCost::where('user_id', auth()->id())
                                    ->with('car')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp

                            <div class="space-y-4">
                                @forelse($myCosts as $cost)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $cost->description }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $cost->car->model }} • {{ $cost->cost_date->format('M j, Y') }}
                                            </p>
                 </div>
                                        <div class="text-right ml-3">
                                            <p class="text-sm font-bold text-gray-900">
                                                ${{ number_format($cost->amount, 0) }}</p>
                                            <span class="kt-badge {{ $cost->getStatusBadgeClass() }} kt-badge-sm">
                                                {{ $cost->getStatusText() }}
                  </span>
                 </div>
                </div>
                                @empty
                                    <div class="text-center py-6">
                                        <i class="ki-filled ki-information-2 text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No equipment costs yet</p>
                </div>
                                @endforelse
                 </div>
                 </div>
                 </div>

                    <!-- My Notifications -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">My Notifications</h3>
                 </div>
                        <div class="kt-card-content p-7.5">
                            @php
                                $myNotifications = auth()
                                    ->user()
                                    ->equipmentCostNotifications()
                                    ->with(['car', 'carEquipmentCost'])
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp

                            <div class="space-y-4">
                                @forelse($myNotifications as $notification)
                                    <div
                                        class="flex items-start space-x-3 {{ $notification->isUnread() ? 'bg-blue-50 p-3 rounded-lg border border-blue-200' : '' }}">
                                        <div
                                            class="w-2 h-2 {{ $notification->isUnread() ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full mt-2 flex-shrink-0">
                 </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                </div>
                 </div>
                                @empty
                                    <div class="text-center py-6">
                                        <i class="ki-filled ki-notification text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No notifications</p>
                 </div>
                                @endforelse
                </div>

                            @if ($myNotifications->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="{{ route('notifications.index') }}"
                                        class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View all notifications →
                                    </a>
                </div>
                            @endif
                 </div>
                 </div>
                 </div>
            @endif

            <!-- Quick Actions -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Quick Actions</h3>
                 </div>
                <div class="kt-card-content p-7.5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('cars.create') }}"
                            class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg  transition-colors">
                            <i class="ki-filled ki-plus text-blue-600 text-xl" style="margin-right: 10px;"></i>
                            <span class="text-sm font-medium text-blue-700">Add New Car</span>
                        </a>

                        <a href="{{ route('cars.search') }}"
                            class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors">
                            <i class="ki-filled ki-magnifier text-green-600 text-xl" style="margin-right: 10px;"></i>
                            <span class="text-sm font-medium text-green-700">Search Cars</span>
                        </a>

                        @if (auth()->user()->hasRole('admin'))
                            <a href="{{ route('bulk-deals.create') }}"
                                class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg  transition-colors">
                                <i class="ki-filled ki-package text-purple-600 text-xl" style="margin-right: 10px;"></i>
                                <span class="text-sm font-medium text-purple-700">New Bulk Deal</span>
                            </a>

                            <a href="{{ route('store-capital.index') }}"
                                class="flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg  transition-colors">
                                <i class="ki-filled ki-dollar text-yellow-600 text-xl" style="margin-right: 10px;"></i>
                                <span class="text-sm font-medium text-yellow-700">Manage Capital</span>
                            </a>
                        @else
                            <a href="{{ route('notifications.index') }}"
                                class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg  transition-colors">
                                <i class="ki-filled ki-notification text-orange-600 text-xl" style="margin-right: 10px;"></i>
                                <span class="text-sm font-medium text-orange-700">View Notifications</span>
                            </a>

                            <a href="{{ route('other-costs.index') }}"
                                class="flex items-center p-4 bg-teal-50 hover:bg-teal-100 rounded-lg border border-teal-200 transition-colors">
                                <i class="ki-filled ki-receipt text-teal-600 text-xl" style="margin-right: 10px;"></i>
                                <span class="text-sm font-medium text-teal-700">Other Costs</span>
                            </a>
                        @endif
                </div>
                 </div>
                 </div>
    </div>
   </div>
   @endsection

@push('scripts')
    <script>
        function toggleCapitalVisibility() {
            const capitalAmount = document.getElementById('capitalAmount');
            const capitalHidden = document.getElementById('capitalHidden');
            const eyeIcon = document.getElementById('capitalEyeIcon');

            if (capitalAmount.classList.contains('hidden')) {
                // Show the amount, hide the stars
                capitalAmount.classList.remove('hidden');
                capitalHidden.classList.add('hidden');
                eyeIcon.classList.remove('ki-eye');
                eyeIcon.classList.add('ki-eye-slash');
            } else {
                // Hide the amount, show the stars
                capitalAmount.classList.add('hidden');
                capitalHidden.classList.remove('hidden');
                eyeIcon.classList.remove('ki-eye-slash');
                eyeIcon.classList.add('ki-eye');
            }
        }
    </script>
@endpush
