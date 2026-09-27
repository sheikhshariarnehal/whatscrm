import React from 'react';
import { useWorkflowStore } from '../store/useWorkflowStore';

const PRESETS = [
  {
    key: 'lead_qualification',
    title: 'Lead Qualification & Routing',
    desc: 'Greeting → Quick Reply Buttons → Capture Business Email → CRM Tag → Live Agent Handoff.',
    icon: 'ph ph-user-focus',
    color: 'bg-blue-500/10 text-blue-600',
    tags: ['Sales', 'Lead Gen', 'Popular'],
  },
  {
    key: 'support_triage',
    title: 'Support & FAQ Department Triage',
    desc: 'Menu List → Department Branching → Instant FAQ Answers → Escalation to Tech Support.',
    icon: 'ph ph-lifebuoy',
    color: 'bg-teal-500/10 text-teal-600',
    tags: ['Support', 'Helpdesk'],
  },
  {
    key: 'order_lookup',
    title: 'Dynamic Order Status Bot',
    desc: 'Ask Customer Order # → Query REST Webhook API → Send Real-time Shipping Status → End.',
    icon: 'ph ph-package',
    color: 'bg-amber-500/10 text-amber-600',
    tags: ['E-Commerce', 'Webhook API'],
  },
  {
    key: 'welcome_menu',
    title: 'Interactive Welcome Menu',
    desc: 'Warm introductory message with quick CTA buttons and targeted conversational pathways.',
    icon: 'ph ph-hand-waving',
    color: 'bg-purple-500/10 text-purple-600',
    tags: ['Onboarding', 'Welcome'],
  },
];

export const PresetsModal = () => {
  const { showPresetsModal, setShowPresetsModal, applyPreset } = useWorkflowStore();

  if (!showPresetsModal) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs select-none">
      <div className="bg-white dark:bg-[#151b24] rounded-3xl border border-gray-200/90 dark:border-gray-800 shadow-2xl max-w-2xl w-full p-6 space-y-5">
        {/* Header */}
        <div className="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
          <div className="flex items-center gap-2.5">
            <div className="w-9 h-9 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
              <i className="ph ph-magic-wand text-lg font-bold"></i>
            </div>
            <div>
              <h3 className="font-bold text-base text-gray-900 dark:text-white">
                Flow Starter Presets
              </h3>
              <p className="text-xs text-gray-400">
                Load battle-tested WhatsApp CRM flow architectures in 1 click
              </p>
            </div>
          </div>

          <button
            type="button"
            onClick={() => setShowPresetsModal(false)}
            className="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          >
            <i className="ph ph-x text-sm font-bold"></i>
          </button>
        </div>

        {/* 2x2 Grid of Presets */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          {PRESETS.map((preset) => (
            <div
              key={preset.key}
              onClick={() => applyPreset(preset.key)}
              className="p-4 rounded-2xl border border-gray-200 dark:border-gray-800 hover:border-primary hover:shadow-lg transition-all duration-150 cursor-pointer group bg-gray-50/40 dark:bg-gray-800/20 flex flex-col justify-between"
            >
              <div>
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-2">
                    <div
                      className={`w-7 h-7 rounded-lg flex items-center justify-center ${preset.color}`}
                    >
                      <i className={`${preset.icon} text-sm font-bold`}></i>
                    </div>
                    <span className="font-bold text-xs text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                      {preset.title}
                    </span>
                  </div>
                </div>

                <p className="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                  {preset.desc}
                </p>
              </div>

              <div className="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                <div className="flex items-center gap-1">
                  {preset.tags.map((tag) => (
                    <span
                      key={tag}
                      className="px-1.5 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-[9.5px] font-semibold text-gray-500"
                    >
                      {tag}
                    </span>
                  ))}
                </div>
                <span className="text-[11px] font-bold text-primary group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                  <span>Load</span>
                  <i className="ph ph-arrow-right text-xs"></i>
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
