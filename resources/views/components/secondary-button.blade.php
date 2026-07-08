<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-default rounded-md font-semibold text-xs text-dark uppercase tracking-widest shadow-xs hover:bg-light focus:outline-hidden focus:ring-2 focus:ring-brand focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
