<!-- Pagination -->
@if ($cars->count() > 0)
    <div class="flex flex-col md:flex-row items-center justify-between mb-4">
        <div class="flex items-center justify-between mb-2 md:mb-0">
            <div class="text-sm text-gray-600">
                Showing {{ $cars->firstItem() ?? 0 }} to {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} cars
            </div>
        </div>
        <ol class="kt-pagination flex justify-center flex-wrap">
            <li class="kt-pagination-item">
                <a href="{{ $cars->url(1) }}" class="kt-btn kt-btn-icon kt-btn-ghost pagination-link" data-page="1" aria-label="First Page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-chevron-first rtl:rotate-180"
                        aria-hidden="true">
                        <path d="m17 18-6-6 6-6"></path>
                        <path d="M7 6v12"></path>
                    </svg>
                </a>
            </li>
            <li class="kt-pagination-item">
                <a href="{{ $cars->previousPageUrl() }}" class="kt-btn kt-btn-icon kt-btn-ghost pagination-link" data-page="{{ $cars->currentPage() - 1 }}"
                    aria-label="Previous Page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-chevron-left rtl:rotate-180" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </a>
            </li>
            @foreach ($cars->getUrlRange(1, $cars->lastPage()) as $page => $url)
                <li class="kt-pagination-item">
                    <a href="{{ $url }}" data-page="{{ $page }}"
                        class="kt-btn kt-btn-icon kt-btn-ghost pagination-link {{ $page == $cars->currentPage() ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                </li>
            @endforeach
            <li class="kt-pagination-item">
                <a href="{{ $cars->nextPageUrl() }}" class="kt-btn kt-btn-icon kt-btn-ghost pagination-link" data-page="{{ $cars->currentPage() + 1 }}" aria-label="Next Page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-chevron-right rtl:rotate-180"
                        aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </a>
            </li>
            <li class="kt-pagination-item">
                <a href="{{ $cars->url($cars->lastPage()) }}" class="kt-btn kt-btn-icon kt-btn-ghost pagination-link" data-page="{{ $cars->lastPage() }}"
                    aria-label="Last Page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-chevron-last rtl:rotate-180" aria-hidden="true">
                        <path d="m7 18 6-6-6-6"></path>
                        <path d="M17 6v12"></path>
                    </svg>
                </a>
            </li>
        </ol>
    </div>
@endif 