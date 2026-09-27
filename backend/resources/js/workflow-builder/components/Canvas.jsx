import React, { useEffect, useCallback } from 'react';
import {
  ReactFlow,
  Background,
  Controls,
  MiniMap,
  BackgroundVariant,
} from '@xyflow/react';
import '@xyflow/react/dist/style.css';

import { useWorkflowStore } from '../store/useWorkflowStore';
import { nodeTypes } from '../nodes';
import { CustomWorkflowEdge } from '../edges/CustomWorkflowEdge';

const edgeTypes = {
  customWorkflowEdge: CustomWorkflowEdge,
};

export const Canvas = () => {
  const {
    nodes,
    edges,
    onNodesChange,
    onEdgesChange,
    onConnect,
    selectNode,
    selectEdge,
    selectedNode,
    selectedEdge,
    deleteNode,
    deleteEdge,
    saveFlow,
    undo,
    redo,
  } = useWorkflowStore();

  // Keyboard shortcuts
  useEffect(() => {
    const handleKeyDown = (e) => {
      // Don't intercept when user is typing in inputs or textareas
      if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;

      if (e.key === 'Delete' || e.key === 'Backspace') {
        if (selectedNode && selectedNode.type !== 'start') {
          deleteNode(selectedNode.id);
        } else if (selectedEdge) {
          deleteEdge(selectedEdge.id);
        }
      } else if (e.key === 'Escape') {
        selectNode(null);
        selectEdge(null);
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
        saveFlow();
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
        e.preventDefault();
        undo();
      } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
        e.preventDefault();
        redo();
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [selectedNode, selectedEdge, deleteNode, deleteEdge, saveFlow, undo, redo, selectNode, selectEdge]);

  const onNodeClick = useCallback(
    (_, node) => {
      selectNode(node);
    },
    [selectNode]
  );

  const onEdgeClick = useCallback(
    (_, edge) => {
      selectEdge(edge);
    },
    [selectEdge]
  );

  const onPaneClick = useCallback(() => {
    selectNode(null);
    selectEdge(null);
  }, [selectNode, selectEdge]);

  return (
    <main className="flex-1 h-full w-full relative overflow-hidden bg-[#f8fafc] dark:bg-[#0b111a] select-none">
      <ReactFlow
        nodes={nodes}
        edges={edges}
        nodeTypes={nodeTypes}
        edgeTypes={edgeTypes}
        onNodesChange={onNodesChange}
        onEdgesChange={onEdgesChange}
        onConnect={onConnect}
        onNodeClick={onNodeClick}
        onEdgeClick={onEdgeClick}
        onPaneClick={onPaneClick}
        fitView
        fitViewOptions={{ padding: 0.25 }}
        minZoom={0.2}
        maxZoom={2.2}
        deleteKeyCode={null} // custom handled
        proOptions={{ hideAttribution: true }}
      >
        <Background
          variant={BackgroundVariant.Dots}
          gap={20}
          size={1.5}
          className="dark:opacity-30"
          color="#94a3b8"
        />

        <Controls
          showInteractive={false}
          className="!bg-white dark:!bg-[#151b24] !border !border-gray-200 dark:!border-gray-800 !rounded-2xl !shadow-md overflow-hidden !m-4"
        />

        <MiniMap
          nodeStrokeWidth={3}
          nodeColor={(n) => {
            if (n.type === 'start') return '#10b981';
            if (n.type === 'send_buttons') return '#a855f7';
            if (n.type === 'send_list') return '#14b8a6';
            if (n.type === 'condition') return '#f59e0b';
            return '#2a85ff';
          }}
          className="!bg-white/90 dark:!bg-[#151b24]/90 !border !border-gray-200 dark:!border-gray-800 !rounded-2xl !shadow-md !m-4"
          maskColor="rgba(0, 0, 0, 0.15)"
        />
      </ReactFlow>
    </main>
  );
};
