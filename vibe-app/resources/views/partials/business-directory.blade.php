@if (! request()->routeIs('home', 'customers', 'suppliers'))
<div class="fixed bottom-20 right-4 z-40 flex items-center gap-1 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-slate-200 lg:bottom-auto lg:left-4 lg:right-auto lg:top-1/2 lg:-translate-y-1/2 lg:flex-col lg:items-stretch">
    <span class="hidden px-3 pb-1 text-xs font-bold text-slate-500 lg:block">Business records</span>
    <a href="{{ route('customers') }}" class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-xs font-bold text-slate-600 hover:bg-blue-50 hover:text-primary"><span class="material-symbols-outlined text-base">groups</span>Customers</a>
    <a href="{{ route('suppliers') }}" class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-xs font-bold text-slate-600 hover:bg-blue-50 hover:text-primary"><span class="material-symbols-outlined text-base">local_shipping</span>Suppliers</a>
</div>
@endif
