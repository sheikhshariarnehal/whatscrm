import React, { memo } from 'react';
import { Handle, Position } from '@xyflow/react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const ListNode = memo(({ id, data, selected }) => {
  const selectNode = useWorkflowStore((s) => s.selectNode);
  const deleteNode = useWorkflowStore((s) => s.deleteNode);
  const duplicateNode = useWorkflowStore((s) => s.duplicateNode);

  const items = data.items || [
    { id: 'item_0', title: 'Option 1', description: 'Details' },
    { id: 'item_1', title: 'Option 2', description: 'Details' },
  ];

  const bodyText = data.content?.text?.body || 'Explore our options:';

  return (
    <div
      onClick={() => selectNode({ id, type: 'send_list', data })}
      className={`group relative w-[290px] rounded-2xl bg-white dark:bg-[#18202b] border shadow-md hover:shadow-2xl transition-all duration-200 cursor-pointer ${
        selected
          ? 'border-primary ring-4 ring-primary/20 scale-[1.01]'
          : 'border-gray-200/90 dark:border-gray-800 hover:border-teal-400'
      }`}
    >
      {/* Input Handle */}
      <Handle
        type="target"
        position={Position.Left}
        id="in"
        className="!w-3.5 !h-3.5 !bg-gray-400 dark:!bg-gray-300 !border-2 !border-white dark:!border-[#18202b] shadow-sm hover:!scale-125 !transition-transform !cursor-crosshair"
      />

      {/* Node Header */}
      <div className="px-3.5 py-2.5 border-b border-gray-100 dark:border-gray-800/80 flex items-center justify-between">
        <div className="flex items-center gap-2 min-w-0">
          <div className="w-6 h-6 rounded-lg bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
            <i className="ph ph-list-bullets text-xs font-bold"></i>
          </div>
          <span className="font-bold text-xs text-gray-800 dark:text-gray-100 truncate">
            {data.title || 'Interactive Menu'}
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

      {/* Node Body */}
      <div className="p-3 text-xs flex flex-col gap-2">
        <p className="text-[11px] text-gray-600 dark:text-gray-300 italic line-clamp-1">
          "{bodyText}"
        </p>

        {/* List Items with Dedicated Ports */}
        <div className="space-y-1.5 pt-1">
          {items.map((item, idx) => (
            <div
              key={item.id || idx}
              className="relative flex items-center justify-between px-2.5 py-1.5 rounded-xl bg-teal-50/60 dark:bg-teal-950/30 border border-teal-200/70 dark:border-teal-800/50"
            >
              <div className="min-w-0 pr-2">
                <div className="font-semibold text-[11px] text-teal-800 dark:text-teal-300 truncate">
                  {item.title || `Item ${idx + 1}`}
                </div>
                {item.description && (
                  <div className="text-[9.5px] text-gray-400 dark:text-gray-500 truncate max-w-[170px]">
                    {item.description}
                  </div>
                )}
              </div>
              <span className="text-[9px] font-mono text-teal-400 shrink-0">→</span>

              {/* Individual Output Handle for this menu item */}
              <Handle
                type="source"
                position={Position.Right}
                id={`item_${idx}`}
                className="!w-3 !h-3 !bg-teal-500 !border-2 !border-white dark:!border-[#18202b] shadow-xs hover:!scale-150 !transition-transform !cursor-crosshair"
                style={{ right: -6 }}
              />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
});

ListNode.displayName = 'ListNode';
