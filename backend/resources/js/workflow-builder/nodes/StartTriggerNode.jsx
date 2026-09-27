import React, { memo } from 'react';
import { Handle, Position } from '@xyflow/react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const StartTriggerNode = memo(({ id, data, selected }) => {
  const selectNode = useWorkflowStore((s) => s.selectNode);

  return (
    <div
      onClick={() => selectNode({ id, type: 'start', data })}
      className={`group relative flex items-center justify-between min-w-[210px] h-[50px] px-4 rounded-full bg-white dark:bg-[#18202b] border-2 shadow-md hover:shadow-xl transition-all duration-200 cursor-pointer ${
        selected
          ? 'border-primary ring-4 ring-primary/20 scale-[1.02]'
          : 'border-emerald-500/80 dark:border-emerald-500/60 hover:border-emerald-600'
      }`}
    >
      <div className="flex items-center gap-2.5 min-w-0 pr-2">
        <div className="w-7 h-7 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
          <i className="ph ph-lightning text-sm font-bold"></i>
        </div>
        <div className="min-w-0 flex flex-col">
          <span className="text-[11px] font-bold uppercase tracking-wider text-gray-900 dark:text-white truncate">
            {data.title || 'Inbound Start'}
          </span>
          <span className="text-[9.5px] font-mono text-gray-400 dark:text-gray-400 truncate max-w-[130px]">
            {data.keywords || 'Any inbound message'}
          </span>
        </div>
      </div>

      <div className="p-1 text-gray-400 group-hover:text-primary transition-colors">
        <i className="ph ph-gear text-xs"></i>
      </div>

      {/* Output Port Handle */}
      <Handle
        type="source"
        position={Position.Right}
        id="default"
        className="!w-3.5 !h-3.5 !bg-emerald-500 !border-2 !border-white dark:!border-[#18202b] shadow-sm hover:!scale-125 !transition-transform !cursor-crosshair"
      />
    </div>
  );
});

StartTriggerNode.displayName = 'StartTriggerNode';
