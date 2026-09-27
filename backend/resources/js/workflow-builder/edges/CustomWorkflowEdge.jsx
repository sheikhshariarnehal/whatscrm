import React, { memo } from 'react';
import { BaseEdge, getBezierPath, EdgeLabelRenderer } from '@xyflow/react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const CustomWorkflowEdge = memo(({
  id,
  sourceX,
  sourceY,
  targetX,
  targetY,
  sourcePosition,
  targetPosition,
  sourceHandleId,
  selected,
}) => {
  const [edgePath, labelX, labelY] = getBezierPath({
    sourceX,
    sourceY,
    sourcePosition,
    targetX,
    targetY,
    targetPosition,
  });

  const deleteEdge = useWorkflowStore((s) => s.deleteEdge);
  const selectEdge = useWorkflowStore((s) => s.selectEdge);

  // Dynamic wire color based on handle type and selection
  let strokeColor = '#94a3b8';
  if (selected) {
    strokeColor = '#ef4444';
  } else if (sourceHandleId === 'match') {
    strokeColor = '#10b981';
  } else if (sourceHandleId === 'default') {
    strokeColor = '#f59e0b';
  } else if (sourceHandleId && sourceHandleId.startsWith('btn_')) {
    strokeColor = '#a855f7';
  } else if (sourceHandleId && sourceHandleId.startsWith('item_')) {
    strokeColor = '#14b8a6';
  }

  return (
    <>
      <BaseEdge
        id={id}
        path={edgePath}
        style={{
          stroke: strokeColor,
          strokeWidth: selected ? 2.5 : 2,
          transition: 'stroke 0.2s, stroke-width 0.2s',
        }}
      />

      {/* Edge interactive label with Delete Pill on center */}
      <EdgeLabelRenderer>
        <div
          style={{
            position: 'absolute',
            transform: `translate(-50%, -50%) translate(${labelX}px,${labelY}px)`,
            pointerEvents: 'all',
          }}
          className="nodrag nopan"
        >
          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              deleteEdge(id);
            }}
            title="Delete connection"
            className={`w-4 h-4 rounded-full border flex items-center justify-center text-[10px] font-bold shadow-xs hover:scale-125 transition-transform ${
              selected
                ? 'bg-rose-500 border-rose-600 text-white'
                : 'bg-white dark:bg-[#18202b] border-gray-300 dark:border-gray-700 text-gray-500 hover:text-rose-500 hover:border-rose-400'
            }`}
          >
            ×
          </button>
        </div>
      </EdgeLabelRenderer>
    </>
  );
});

CustomWorkflowEdge.displayName = 'CustomWorkflowEdge';
