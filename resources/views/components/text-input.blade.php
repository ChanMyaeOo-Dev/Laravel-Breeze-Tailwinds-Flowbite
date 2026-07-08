@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-default focus:border-brand focus:ring-brand rounded-md shadow-xs']) }}>
