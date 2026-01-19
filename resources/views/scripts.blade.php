{{-- Filters JavaScript - Compiled from TypeScript --}}
<script data-accelade-filters-script>
@php
    $distPath = __DIR__ . '/../../dist/filters.iife.js';
    if (file_exists($distPath)) {
        echo file_get_contents($distPath);
    } else {
        // Fallback to inline script if dist not available
        echo '// Accelade Filters: Please run "npm run build" in packages/filters directory';
    }
@endphp
</script>
