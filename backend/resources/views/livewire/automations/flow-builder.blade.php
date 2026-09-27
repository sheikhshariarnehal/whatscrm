<div id="workflow-builder-root"
     data-flow-id="{{ $flowId }}"
     data-flow-name="{{ $flowName }}"
     data-flow-description="{{ $flowDescription ?? '' }}"
     data-trigger-keywords="{{ $flowTriggerKeywords ?? '' }}"
     data-is-active="{{ $isActive ? 'true' : 'false' }}"
     data-initial-graph="{{ json_encode($graphData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
     data-api-get-url="{{ route('automations.api.flows.show', $flowId) }}"
     data-api-save-url="{{ route('automations.api.flows.update', $flowId) }}"
     data-api-toggle-url="{{ route('automations.api.flows.toggle-active', $flowId) }}"
     data-back-url="{{ route('automations') }}"
     data-csrf-token="{{ csrf_token() }}"
     class="fixed inset-0 z-40 bg-[#f4f5f8] dark:bg-[#0f141c] flex flex-col overflow-hidden select-none font-sans text-gray-800 dark:text-gray-100">
    
    <!-- Pre-hydration Skeleton Spinner -->
    <div class="flex-1 flex flex-col items-center justify-center gap-3">
        <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Loading Visual WhatsApp Workflow Builder...</p>
    </div>
</div>

@push('scripts')
@vite(['resources/js/workflow-builder/main.jsx'])
@endpush
