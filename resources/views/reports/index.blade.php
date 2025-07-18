@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Reports</h3>
        </div>
    </div>
    <div class="kt-card-body p-6">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="mb-px flex space-x-8">
                <a href="#profit-loss" class="tab-link active py-2 border-b-2 border-primary text-primary font-medium text-sm data-tab="profit-loss">
                    Profit & Loss Report
                </a>
                <a href="#inventory-valuation" class="tab-link py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="inventory-valuation">
                    Inventory Valuation
                </a>
                <a href="#equipment-cost-summary" class="tab-link py-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="equipment-cost-summary">
                    Equipment Cost Summary
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="profit-loss" class="tab-content active">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-blue-800 mb-4">Profit & Loss Report</h4>
                <p class="text-blue-700 mb-4">View your net profit, total sales, and costs to understand your business performance.</p>
                <a href="{{ route('reports.profit-loss') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-chart-line"></i>
                    View Report
                </a>
            </div>
        </div>

        <div id="inventory-valuation" class="tab-content hidden">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-green-800 mb-4">Inventory Valuation</h4>
                <p class="text-green-700 mb-4">See the total value of your current inventory and understand your asset worth.</p>
                <a href="{{ route('reports.inventory-valuation') }}" class="kt-btn kt-btn-success">
                    <i class="ki-filled ki-dollar"></i>
                    View Report
                </a>
            </div>
        </div>

        <div id="equipment-cost-summary" class="tab-content hidden">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-orange-800 mb-4">Equipment Cost Summary</h4>
                <p class="text-orange-700 mb-4">Total and pending equipment costs to manage your expenses effectively.</p>
                <a href="{{ route('reports.equipment-cost-summary') }}" class="kt-btn kt-btn-warning">
                    <i class="ki-filled ki-gear"></i>
                    View Report
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            tabLinks.forEach(l => {
                l.classList.remove('active', 'border-primary', 'text-primary');
                l.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.add('active', 'border-primary', 'text-primary');
            this.classList.remove('border-transparent', 'text-gray-500');
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            
            // Show selected tab content
            const targetTab = this.getAttribute('data-tab');
            const targetContent = document.getElementById(targetTab);
            targetContent.classList.remove('hidden');
            targetContent.classList.add('active');
        });
    });
});
</script>
@endsection 