import React, { memo } from 'react';
import { Handle, Position } from '@xyflow/react';
import { useWorkflowStore } from '../store/useWorkflowStore';

const NODE_CONFIG = {
  send_message: {
    icon: 'ph ph-chat-teardrop-text',
    badgeClass: 'bg-blue-500/15 text-blue-600 dark:text-blue-400',
    hoverBorder: 'hover:border-blue-400',
    category: 'Messaging',
  },
  send_media: {
    icon: 'ph ph-image',
    badgeClass: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    hoverBorder: 'hover:border-emerald-400',
    category: 'Media Attachment',
  },
  send_template: {
    icon: 'ph ph-file-text',
    badgeClass: 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400',
    hoverBorder: 'hover:border-indigo-400',
    category: 'Meta HSM Template',
  },
  collect_input: {
    icon: 'ph ph-floppy-disk',
    badgeClass: 'bg-purple-500/15 text-purple-600 dark:text-purple-400',
    hoverBorder: 'hover:border-purple-400',
    category: 'Logic & Capture',
  },
  set_tag: {
    icon: 'ph ph-tag',
    badgeClass: 'bg-pink-500/15 text-pink-600 dark:text-pink-400',
    hoverBorder: 'hover:border-pink-400',
    category: 'CRM Tagging',
  },
  update_field: {
    icon: 'ph ph-pencil-simple-line',
    badgeClass: 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400',
    hoverBorder: 'hover:border-cyan-400',
    category: 'Contact Field',
  },
  delay: {
    icon: 'ph ph-clock',
    badgeClass: 'bg-orange-500/15 text-orange-600 dark:text-orange-400',
    hoverBorder: 'hover:border-orange-400',
    category: 'Flow Timer',
  },
  handoff: {
    icon: 'ph ph-user-switch',
    badgeClass: 'bg-purple-500/15 text-purple-600 dark:text-purple-400',
    hoverBorder: 'hover:border-purple-400',
    category: 'Live Support',
    isTerminal: true,
  },
  ai_assistant: {
    icon: 'ph ph-sparkle',
    badgeClass: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
    hoverBorder: 'hover:border-amber-400',
    category: 'AI Auto-Response',
  },
  http_webhook: {
    icon: 'ph ph-globe',
    badgeClass: 'bg-sky-500/15 text-sky-600 dark:text-sky-400',
    hoverBorder: 'hover:border-sky-400',
    category: 'Integration',
  },
  end: {
    icon: 'ph ph-flag',
    badgeClass: 'bg-slate-500/15 text-slate-600 dark:text-slate-400',
    hoverBorder: 'hover:border-slate-400',
    category: 'Flow Termination',
    isTerminal: true,
  },
};

export const StandardActionNode = memo(({ id, type, data, selected }) => {
  const selectNode = useWorkflowStore((s) => s.selectNode);
  const deleteNode = useWorkflowStore((s) => s.deleteNode);
  const duplicateNode = useWorkflowStore((s) => s.duplicateNode);

  const config = NODE_CONFIG[type] || NODE_CONFIG.send_message;

  const renderPreview = () => {
    switch (type) {
      case 'send_message':
        return (
          <p className="text-[11px] text-gray-600 dark:text-gray-300 italic line-clamp-2 leading-relaxed">
            "{data.content?.text?.body || 'Hello!'}"
          </p>
        );
      case 'send_media':
        return (
          <div className="flex items-center gap-2 text-xs">
            <span className="capitalize font-semibold text-emerald-600 dark:text-emerald-400 text-[11px]">
              [{data.mediaType || 'Image'}]
            </span>
            <span className="text-[11px] text-gray-500 truncate max-w-[170px]">
              {data.caption || data.url || 'Media attachment'}
            </span>
          </div>
        );
      case 'send_template':
        return (
          <div className="text-[11px] space-y-0.5">
            <div className="font-mono font-bold text-indigo-600 dark:text-indigo-400 truncate">
              {data.templateName || 'template_name'}
            </div>
            <div className="text-[10px] text-gray-400">
              Language: {data.language || 'en_US'} ({data.parameters?.length || 0} params)
            </div>
          </div>
        );
      case 'collect_input':
        return (
          <div className="text-[11px] space-y-1">
            <p className="text-gray-600 dark:text-gray-300 italic line-clamp-1">
              "{data.prompt_text || 'Waiting for reply...'}"
            </p>
            <div className="font-mono text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/40 px-2 py-0.5 rounded-md inline-block">
              → {'{{'}vars.{data.var_key || 'reply'}{'}}'}
            </div>
          </div>
        );
      case 'set_tag':
        return (
          <div className="flex items-center gap-1.5">
            <span className="px-2 py-0.5 rounded-md bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 text-[11px] font-bold">
              🏷️ {data.label || 'Customer'}
            </span>
          </div>
        );
      case 'update_field':
        return (
          <div className="font-mono text-[10.5px] text-cyan-700 dark:text-cyan-300 bg-cyan-50 dark:bg-cyan-950/40 px-2 py-1 rounded-md truncate">
            {data.field || 'status'} = "{data.value || ''}"
          </div>
        );
      case 'delay':
        return (
          <div className="text-[11px] font-bold text-orange-600 dark:text-orange-400 flex items-center gap-1.5">
            <i className="ph ph-clock text-xs"></i>
            <span>Wait {data.seconds || 5} {data.unit || 'seconds'}</span>
          </div>
        );
      case 'handoff':
        return (
          <div className="text-[11px] space-y-0.5">
            <div className="font-semibold text-purple-600 dark:text-purple-400">
              Transfer to Human Inbox
            </div>
            <div className="text-[10px] text-gray-400">
              Pause bot: {data.duration || 24} hours
            </div>
          </div>
        );
      case 'ai_assistant':
        return (
          <p className="text-[11px] text-amber-700 dark:text-amber-300 italic line-clamp-2">
            "{data.systemPrompt || 'AI auto-responder prompt'}"
          </p>
        );
      case 'http_webhook':
        return (
          <div className="font-mono text-[10px] truncate space-x-1">
            <span className="font-bold text-sky-600 dark:text-sky-400">{data.method || 'POST'}</span>
            <span className="text-gray-500 dark:text-gray-400">{data.url || 'https://api.example.com'}</span>
          </div>
        );
      case 'end':
        return (
          <div className="text-[11px] text-slate-500 font-semibold flex items-center gap-1.5">
            <i className="ph ph-check-circle text-xs"></i>
            <span>Session complete & reset</span>
          </div>
        );
      default:
        return <p className="text-[11px] text-gray-400">Configured step</p>;
    }
  };

  return (
    <div
      onClick={() => selectNode({ id, type, data })}
      className={`group relative w-[280px] rounded-2xl bg-white dark:bg-[#18202b] border shadow-md hover:shadow-2xl transition-all duration-200 cursor-pointer ${
        selected
          ? 'border-primary ring-4 ring-primary/20 scale-[1.01]'
          : `border-gray-200/90 dark:border-gray-800 ${config.hoverBorder}`
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
          <div className={`w-6 h-6 rounded-lg ${config.badgeClass} flex items-center justify-center shrink-0`}>
            <i className={`${config.icon} text-xs font-bold`}></i>
          </div>
          <span className="font-bold text-xs text-gray-800 dark:text-gray-100 truncate">
            {data.title || 'Action Step'}
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

      {/* Body Content Preview */}
      <div className="p-3 text-xs min-h-[50px] flex flex-col justify-center">
        {renderPreview()}
      </div>

      {/* Footer Meta */}
      <div className="px-3 py-1.5 border-t border-gray-50 dark:border-gray-800/50 flex items-center justify-between text-[9.5px] text-gray-400">
        <span>{config.category}</span>
        <span className="font-mono">#{id.replace('node_', '').slice(-4)}</span>
      </div>

      {/* Output Handle (if not terminal) */}
      {!config.isTerminal && (
        <Handle
          type="source"
          position={Position.Right}
          id="default"
          className="!w-3.5 !h-3.5 !bg-primary !border-2 !border-white dark:!border-[#18202b] shadow-xs hover:!scale-150 !transition-transform !cursor-crosshair"
        />
      )}
    </div>
  );
});

StandardActionNode.displayName = 'StandardActionNode';
