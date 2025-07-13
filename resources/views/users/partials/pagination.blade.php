@if ($users->hasPages())
    <div class="kt-card">
        <div class="kt-card-body">
            <div class="flex items-center justify-between">
                <!-- Pagination Info -->
                <div class="text-sm text-gray-500">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                </div>

                <!-- Pagination Links -->
                <div class="flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($users->onFirstPage())
                        <span class="kt-btn kt-btn-outline kt-btn-sm disabled">
                            <i class="ki-filled ki-arrow-left"></i>
                            Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="kt-btn kt-btn-outline kt-btn-sm">
                            <i class="ki-filled ki-arrow-left"></i>
                            Previous
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="flex items-center gap-1">
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span class="kt-btn kt-btn-primary kt-btn-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="kt-btn kt-btn-outline kt-btn-sm">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="kt-btn kt-btn-outline kt-btn-sm">
                            Next
                            <i class="ki-filled ki-arrow-right"></i>
                        </a>
                    @else
                        <span class="kt-btn kt-btn-outline kt-btn-sm disabled">
                            Next
                            <i class="ki-filled ki-arrow-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif 