import React from 'react';
import { useWorkflowStore } from '../store/useWorkflowStore';

const PALETTE_GROUPS = [
  {
    title: 'Messaging',
    items: [
      {
        type: 'send_message',
        label: 'Text Message',
        desc: 'Send WhatsApp text',
        icon: 'ph ph-chat-teardrop-text',
        color: 'text-blue-500 bg-blue-500/10 group-hover:bg-blue-500 group-hover:text-white',
      },
      {
        type: 'send_buttons',
        label: 'Buttons (CTA)',
        desc: 'Interactive reply buttons',
        icon: 'ph ph-squares-four',
        color: 'text-purple-500 bg-purple-500/10 group-hover:bg-purple-500 group-hover:text-white',
      },
      {
        type: 'send_list',
        label: 'Menu List',
        desc: 'Interactive options menu',
        icon: 'ph ph-list-bullets',
        color: 'text-teal-500 bg-teal-500/10 group-hover:bg-teal-500 group-hover:text-white',
      },
      {
        type: 'send_media',
        label: 'Send Media',
        desc: 'Image, Doc, or Video',
        icon: 'ph ph-image',
        color: 'text-emerald-500 bg-emerald-500/10 group-hover:bg-emerald-500 group-hover:text-white',
      },
      {
        type: 'send_template',
        label: 'Meta Template',
        desc: 'Pre-approved HSM template',
        icon: 'ph ph-file-text',
        color: 'text-indigo-500 bg-indigo-500/10 group-hover:bg-indigo-500 group-hover:text-white',
      },
    ],
  },
  {
    title: 'Logic & Capture',
    items: [
      {
        type: 'collect_input',
        label: 'Collect Input',
        desc: 'Ask & store variable',
        icon: 'ph ph-floppy-disk',
        color: 'text-purple-500 bg-purple-500/10 group-hover:bg-purple-500 group-hover:text-white',
      },
      {
        type: 'condition',
        label: 'If / Else Branch',
        desc: 'Conditional matching',
        icon: 'ph ph-git-fork',
        color: 'text-amber-500 bg-amber-500/10 group-hover:bg-amber-500 group-hover:text-white',
      },
      {
        type: 'set_tag',
        label: 'Tag Contact',
        desc: 'Assign CRM label',
        icon: 'ph ph-tag',
        color: 'text-pink-500 bg-pink-500/10 group-hover:bg-pink-500 group-hover:text-white',
      },
      {
        type: 'update_field',
        label: 'Update Field',
        desc: 'Modify CRM contact data',
        icon: 'ph ph-pencil-simple-line',
        color: 'text-cyan-500 bg-cyan-500/10 group-hover:bg-cyan-500 group-hover:text-white',
      },
    ],
  },
  {
    title: 'Flow Control',
    items: [
      {
        type: 'delay',
        label: 'Wait Timer',
        desc: 'Delay next step',
        icon: 'ph ph-clock',
        color: 'text-orange-500 bg-orange-500/10 group-hover:bg-orange-500 group-hover:text-white',
      },
      {
        type: 'handoff',
        label: 'Agent Handoff',
        desc: 'Transfer to Live Inbox',
        icon: 'ph ph-user-switch',
        color: 'text-purple-500 bg-purple-500/10 group-hover:bg-purple-500 group-hover:text-white',
      },
      {
        type: 'end',
        label: 'End Session',
        desc: 'Complete conversation',
        icon: 'ph ph-flag',
        color: 'text-slate-500 bg-slate-500/10 group-hover:bg-slate-500 group-hover:text-white',
      },
    ],
  },
  {
    title: 'Advanced AI & API',
    items: [
      {
        type: 'ai_assistant',
        label: 'AI Assistant',
        desc: 'LLM generative reply',
        icon: 'ph ph-sparkle',
        color: 'text-amber-500 bg-amber-500/10 group-hover:bg-amber-500 group-hover:text-white',
      },
      {
        type: 'http_webhook',
        label: 'REST Webhook',
        desc: 'External HTTP API call',
        icon: 'ph ph-globe',
        color: 'text-sky-500 bg-sky-500/10 group-hover:bg-sky-500 group-hover:text-white',
      },
    ],
  },
];

export const Palette = () => {
  const addNode = useWorkflowStore((s) => s.addNode);

  return (
    <aside className="w-64 bg-white dark:bg-[#151b24] border-r border-gray-200/90 dark:border-gray-800/90 flex flex-col z-20 shrink-0 select-none">
      {/* Palette Header */}
      <div className="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 className="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
          <i className="ph ph-squares-four text-primary text-sm font-bold"></i>
          <span>Add Step</span>
        </h3>
        <span className="text-[10px] text-gray-400">Click to add</span>
      </div>

      {/* Palette Item Groups */}
      <div className="flex-1 overflow-y-auto py-3 px-3 space-y-4">
        {PALETTE_GROUPS.map((group) => (
          <div key={group.title}>
            <span className="text-[9.5px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest px-1 block mb-1.5">
              {group.title}
            </span>
            <div className="space-y-1">
              {group.items.map((item) => (
                <div
                  key={item.type}
                  onClick={() => addNode(item.type, item.label)}
                  className="group flex items-center gap-2.5 p-2 rounded-xl border border-gray-100 dark:border-gray-800/80 bg-white dark:bg-[#18202b] hover:border-primary/40 hover:shadow-sm cursor-pointer transition-all duration-150"
                >
                  <div
                    className={`w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors ${item.color}`}
                  >
                    <i className={`${item.icon} text-sm font-bold`}></i>
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="text-xs font-bold text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors leading-tight">
                      {item.label}
                    </div>
                    <div className="text-[10px] text-gray-400 truncate mt-0.5">
                      {item.desc}
                    </div>
                  </div>
                  <i className="ph ph-plus text-xs text-gray-300 dark:text-gray-600 group-hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity"></i>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>
    </aside>
  );
};
