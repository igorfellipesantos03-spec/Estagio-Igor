{{-- Toast / Flash Messages Component --}}
@if (session('success') || session('error') || $errors->any())
<div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">

    {{-- Success Toast --}}
    @if (session('success'))
    <div class="toast-item pointer-events-auto flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-500 opacity-100 translate-x-0"
         role="alert">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold">Sucesso!</p>
            <p class="text-sm mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="this.closest('.toast-item').remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Error Toast --}}
    @if (session('error'))
    <div class="toast-item pointer-events-auto flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-500 opacity-100 translate-x-0"
         role="alert">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold">Erro!</p>
            <p class="text-sm mt-0.5">{{ session('error') }}</p>
        </div>
        <button onclick="this.closest('.toast-item').remove()" class="text-red-400 hover:text-red-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Validation Errors Toast --}}
    @if ($errors->any())
    <div class="toast-item pointer-events-auto flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-xl shadow-lg backdrop-blur-sm transition-all duration-500 opacity-100 translate-x-0"
         role="alert">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold">Atenção!</p>
            <ul class="text-sm mt-0.5 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="this.closest('.toast-item').remove()" class="text-amber-400 hover:text-amber-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('.toast-item');
        toasts.forEach(function (toast) {
            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(function () {
                    toast.remove();
                    // Remove container if empty
                    const container = document.getElementById('toast-container');
                    if (container && container.children.length === 0) {
                        container.remove();
                    }
                }, 500);
            }, 5000);
        });
    });
</script>
@endif
