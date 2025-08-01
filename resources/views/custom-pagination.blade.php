@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação de Paginação" class="pagination-nav">
        <!-- Mobile Version -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="pagination-btn pagination-btn-disabled">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn pagination-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn pagination-btn-primary">
                    Próximo
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="pagination-btn pagination-btn-disabled">
                    Próximo
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>

        <!-- Desktop Version -->
        <div class="hidden sm:flex sm:items-center sm:justify-end w-full">
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="pagination-arrow pagination-arrow-disabled" aria-disabled="true" aria-label="Anterior">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" 
                       class="pagination-arrow pagination-arrow-active" aria-label="Anterior">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="pagination-dots">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="pagination-number pagination-number-current">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="pagination-number pagination-number-link" 
                                   aria-label="Ir para página {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" 
                       class="pagination-arrow pagination-arrow-active" aria-label="Próximo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <span class="pagination-arrow pagination-arrow-disabled" aria-disabled="true" aria-label="Próximo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <style>
        /* Base pagination styles */
        .pagination-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Mobile pagination buttons */
        .pagination-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s;
            transform: translateZ(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pagination-btn-primary {
            color: #1d4ed8;
            background: linear-gradient(to right, #eff6ff, #dbeafe);
            border: 1px solid #bfdbfe;
        }
        
        .pagination-btn-primary:hover {
            background: linear-gradient(to right, #dbeafe, #bfdbfe);
            border-color: #93c5fd;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            transform: translateY(-1px);
        }

        .pagination-btn-disabled {
            color: #9ca3af;
            background: linear-gradient(to right, #f9fafb, #f3f4f6);
            border: 1px solid #e5e7eb;
            cursor: not-allowed;
        }

        /* Desktop pagination controls */
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Pagination arrows */
        .pagination-arrow {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            transform: translateZ(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pagination-arrow-active {
            color: #2563eb;
            background: linear-gradient(to bottom right, #ffffff, #eff6ff);
            border: 1px solid #bfdbfe;
        }
        
        .pagination-arrow-active:hover {
            color: #1d4ed8;
            background: linear-gradient(to bottom right, #eff6ff, #dbeafe);
            border-color: #93c5fd;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            transform: translateY(-1px) scale(1.05);
        }

        .pagination-arrow-disabled {
            color: #9ca3af;
            background: linear-gradient(to bottom right, #f9fafb, #f3f4f6);
            border: 1px solid #e5e7eb;
            cursor: not-allowed;
        }

        /* Pagination numbers */
        .pagination-number {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s;
            transform: translateZ(0);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pagination-number-current {
            color: white;
            background: linear-gradient(to bottom right, #2563eb, #1d4ed8);
            border: 1px solid #2563eb;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
        }

        .pagination-number-link {
            color: #374151;
            background: linear-gradient(to bottom right, #ffffff, #f9fafb);
            border: 1px solid #e5e7eb;
        }
        
        .pagination-number-link:hover {
            color: #1d4ed8;
            background: linear-gradient(to bottom right, #eff6ff, #dbeafe);
            border-color: #93c5fd;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            transform: translateY(-1px) scale(1.05);
        }

        /* Pagination dots */
        .pagination-dots {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #9ca3af;
            background: transparent;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .pagination-btn {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                font-size: 0.75rem;
            }
            
            .pagination-nav {
                justify-content: center;
            }
        }

        @media (min-width: 641px) {
            .pagination-nav {
                justify-content: flex-end;
                align-items: center;
            }
            
            .pagination-nav > div {
                width: 100%;
                justify-content: flex-end;
            }
        }

        /* Enhanced hover effects */
        .pagination-arrow-active,
        .pagination-number-link {
            position: relative;
            overflow: hidden;
        }

        .pagination-arrow-active::before,
        .pagination-number-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s;
        }

        .pagination-arrow-active:hover::before,
        .pagination-number-link:hover::before {
            left: 100%;
        }

        /* Current page glow effect */
        .pagination-number-current {
            animation: currentPageGlow 2s ease-in-out infinite alternate;
        }

        @keyframes currentPageGlow {
            from {
                box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
            }
            to {
                box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
            }
        }
    </style>
@endif