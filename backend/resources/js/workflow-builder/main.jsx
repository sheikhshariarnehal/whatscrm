import React from 'react';
import { createRoot } from 'react-dom/client';
import { WorkflowBuilderApp } from './WorkflowBuilderApp';

function mountWorkflowBuilder() {
  const container = document.getElementById('workflow-builder-root');
  if (!container) return;

  let initialGraph = { nodes: [], edges: [] };
  if (container.dataset.initialGraph) {
    try {
      initialGraph = JSON.parse(container.dataset.initialGraph);
    } catch (e) {
      console.warn('Failed to parse initialGraph JSON', e);
    }
  }

  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = container.dataset.csrfToken || csrfMeta?.getAttribute('content') || '';

  const props = {
    flowId: parseInt(container.dataset.flowId, 10) || 1,
    flowName: container.dataset.flowName || '',
    flowDescription: container.dataset.flowDescription || '',
    triggerKeywords: container.dataset.triggerKeywords || '',
    isActive: container.dataset.isActive === 'true',
    initialGraph: initialGraph,
    apiGetUrl: container.dataset.apiGetUrl || '',
    apiSaveUrl: container.dataset.apiSaveUrl || '',
    apiToggleUrl: container.dataset.apiToggleUrl || '',
    backUrl: container.dataset.backUrl || '/automations',
    csrfToken: csrfToken,
  };

  const root = createRoot(container);
  root.render(
    <React.StrictMode>
      <WorkflowBuilderApp {...props} />
    </React.StrictMode>
  );
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountWorkflowBuilder);
} else {
  mountWorkflowBuilder();
}
