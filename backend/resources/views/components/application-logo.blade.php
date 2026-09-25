<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    <img src="/img/logo/logo-light-full.png" alt="{{ config('app.name', 'WhatsCRM') }}" class="h-9 max-h-9 dark:hidden object-contain">
    <img src="/img/logo/logo-dark-full.png" alt="{{ config('app.name', 'WhatsCRM') }}" class="h-9 max-h-9 hidden dark:block object-contain">
</div>
