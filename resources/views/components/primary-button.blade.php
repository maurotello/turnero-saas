<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-3 bg-brand-teal border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-blue-deep hover:-translate-y-0.5 focus:bg-brand-blue-deep active:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 transition-all ease-in-out duration-300 shadow-lg shadow-teal-200/50']) }}>
    {{ $slot }}
</button>
