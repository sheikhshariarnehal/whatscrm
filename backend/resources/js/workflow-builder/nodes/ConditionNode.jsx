import React, { memo } from 'react';
import { Handle, Position } from '@xyflow/react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const ConditionNode = memo(({ id, data, selected }) => {
  const selectNode = useWorkflowStore((s) => s.selectNode);
  const deleteNode = useWorkflowStore((s) => s.deleteNode);
  const duplicateNode = useWorkflowStore((s) => s.duplicateNode);

  const condition = (data.conditions && data.conditions[0]) || {
    type: 'text_contains',
    value: 'yes',
  };

  const getOperatorLabel = (type) => {
    switch (type) {
      case 'text_exact':
        return 'equals';
      case 'starts_with':
        return 'starts with';
      case 'regex':
        return 'matches regex';
      default:
        return 'contains';
    }
  };

  return (
    <div
      onClick={() => selectNode({ id, type: 'condition', data })}
      className={`group relative w-[280px] rounded-2xl bg-white dark:bg-[#18202b] border shadow-md hover:shadow-2xl transition-all duration-200 cursor-pointer ${
        selected
          ? 'border-primary ring-4 ring-primary/20 scale-[1.01]'
          : 'border-gray-200/90 dark:border-gray-800 hover:border-amber-400'
      }`}
    >
      {/* Input Handle */}
      <Handle
        type="target"
        position={Position.Left}
        id="in"
        className="!w-3.5 !h-3.5 !bg-gray-400 dark:!bg-gray-300 !border-2 !border-white dark:!border-[#18202b] shadow-sm hover:!scale-125 !transition-transform !cursor-crosshair"
      />

      {/* Header */}
      <div className="px-3.5 py-2.5 border-b border-gray-100 dark:border-gray-800/80 flex items-center justify-between">
        <div className="flex items-center gap-2 min-w-0">
          <div className="w-6 h-6 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <i className="ph ph-git-fork text-xs font-bold"></i>
          </div>
          <span className="font-bold text-xs text-gray-800 dark:text-gray-100 truncate">
            {data.title || 'If / Else Branch'}
          </span>
        </div>
        <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              duplicateNode(id);
            }}
            className="p-1 text-gray-400 hover:text-primary transition-colors"
            title="Duplicate step"
          >
            <i className="ph ph-copy text-xs"></i>
          </button>
          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              deleteNode(id);
            }}
            className="p-1 text-gray-400 hover:text-rose-500 transition-colors"
            title="Delete step"
          >
            <i className="ph ph-trash text-xs"></i>
          </button>
        </div>
      </div>

      {/* Body with 2 distinct port branches */}
      <div className="p-3 text-xs space-y-2">
        {/* Match Branch */}
        <div className="relative flex items-center justify-between p-2 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/70 dark:border-emerald-800/60">
          <div className="min-w-0">
            <span className="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-400 tracking-wider">
              ✓ True
            </span>
            <div className="text-[11px] font-mono text-emerald-800 dark:text-emerald-300 truncate max-w-[190px]">
              {getOperatorLabel(condition.type)} "{condition.value || 'keyword'}"
            </div>
          </div>

          <Handle
            type="source"
            position={Position.Right}
            id="match"
            className="!w-3.5 !h-3.5 !bg-emerald-500 !border-2 !border-white dark:!border-[#18202b] shadow-xs hover:!scale-150 !transition-transform !cursor-crosshair"
            style={{ right: -6 }}
          />
        </div>

        {/* Default / Else Branch */}
        <div className="relative flex items-center justify-between p-2 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/60">
          <div className="min-w-0">
            <span className="text-[10px] uppercase font-bold text-amber-700 dark:text-amber-400 tracking-wider">
              ✕ False / Else
            </span>
            <div className="text-[11px] text-gray-500 dark:text-gray-400 truncate">
              Any other input
            </div>
          </div>

          <Handle
            type="source"
            position={Position.Right}
            id="default"
            className="!w-3.5 !h-3.5 !bg-amber-500 !border-2 !border-white dark:!border-[#18202b] shadow-xs hover:!scale-150 !transition-transform !cursor-crosshair"
            style={{ right: -6 }}
          />
        </div>
      </div>
    </div>
  );
});

ConditionNode.displayName = 'ConditionNode';
