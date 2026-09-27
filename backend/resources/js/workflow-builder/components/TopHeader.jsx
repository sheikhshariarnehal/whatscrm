import React from 'react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const TopHeader = () => {
  const {
    flowName,
    setFlowName,
    flowTriggerKeywords,
    setFlowTriggerKeywords,
    isActive,
    toggleActive,
    backUrl,
    isDirty,
    isSaving,
    saveSuccess,
    saveError,
    saveFlow,
    undo,
    redo,
    historyIndex,
    history,
    autoLayout,
    validationIssues,
    setShowValidationModal,
    setShowPresetsModal,
  } = useWorkflowStore();

  const canUndo = historyIndex > 0;
  const canRedo = historyIndex < history.length - 1;

  const errorCount = validationIssues.filter((i) => i.severity === 'error').length;
  const hasIssues = validationIssues.length > 0;

  return (
    <header className="h-14 bg-white dark:bg-[#151b24] border-b border-gray-200/90 dark:border-gray-800/90 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30 shadow-xs select-none">
      {/* Left: Back & Flow Name */}
      <div className="flex items-center gap-3.5 min-w-0">
        <a
          href={backUrl || '/automations'}
          className="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i className="ph ph-arrow-left text-sm font-bold"></i>
          <span className="hidden sm:inline">Back</span>
        </a>

        <div className="h-5 w-px bg-gray-200 dark:bg-gray-700 shrink-0"></div>

        <div className="flex items-center gap-2.5 min-w-0">
          <div className="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <i className="ph ph-tree-structure text-lg"></i>
          </div>

          <div className="min-w-0 flex flex-col">
            <div className="flex items-center gap-2">
              <input
                type="text"
                value={flowName}
                onChange={(e) => setFlowName(e.target.value)}
                placeholder="Workflow name..."
                className="font-bold text-sm text-gray-900 dark:text-white bg-transparent border-0 border-b border-transparent hover:border-gray-300 dark:hover:border-gray-700 focus:border-primary p-0 focus:ring-0 max-w-[220px] truncate transition-colors"
              />
              <span
                className={`w-2.5 h-2.5 rounded-full shrink-0 ${
                  isActive ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-gray-400'
                }`}
                title={isActive ? 'Flow is Live & Active' : 'Flow is Paused'}
              />
            </div>

            <div className="text-[11px] text-gray-400 font-mono flex items-center gap-1.5 mt-0.5 truncate">
              <i className="ph ph-lightning text-xs text-amber-500 shrink-0"></i>
              <span className="truncate">
                {flowTriggerKeywords ? `Triggers: ${flowTriggerKeywords}` : 'Triggers: Any message'}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Center: Undo / Redo & Canvas Tools */}
      <div className="hidden md:flex items-center gap-1 bg-gray-100 dark:bg-[#1c2430] p-1 rounded-xl border border-gray-200/80 dark:border-gray-700/80">
        <button
          type="button"
          onClick={undo}
          disabled={!canUndo}
          title="Undo (Ctrl+Z)"
          className={`p-1.5 rounded-lg text-xs transition-colors ${
            canUndo
              ? 'text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-[#151b24]'
              : 'text-gray-400 dark:text-gray-600 cursor-not-allowed'
          }`}
        >
          <i className="ph ph-arrow-u-up-left text-sm font-bold"></i>
        </button>
        <button
          type="button"
          onClick={redo}
          disabled={!canRedo}
          title="Redo (Ctrl+Y)"
          className={`p-1.5 rounded-lg text-xs transition-colors ${
            canRedo
              ? 'text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-[#151b24]'
              : 'text-gray-400 dark:text-gray-600 cursor-not-allowed'
          }`}
        >
          <i className="ph ph-arrow-u-up-right text-sm font-bold"></i>
        </button>

        <div className="h-4 w-px bg-gray-300 dark:bg-gray-700 mx-1"></div>

        <button
          type="button"
          onClick={autoLayout}
          title="Auto-organize graph layout"
          className="flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-[#151b24] transition-all"
        >
          <i className="ph ph-arrows-out-line-horizontal text-xs text-primary font-bold"></i>
          <span>Auto-Layout</span>
        </button>
      </div>

      {/* Right: Diagnostics, Presets, Active Toggle & Publish */}
      <div className="flex items-center gap-2 sm:gap-2.5 shrink-0">
        {/* Validator Diagnostics Pill */}
        <button
          type="button"
          onClick={() => setShowValidationModal(true)}
          className={`flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold border transition-all ${
            errorCount > 0
              ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200 dark:border-rose-900/60'
              : hasIssues
              ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200 dark:border-amber-900/60'
              : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/60'
          }`}
        >
          <i
            className={`ph-fill ${
              errorCount > 0
                ? 'ph-warning-circle text-rose-500'
                : hasIssues
                ? 'ph-warning text-amber-500'
                : 'ph-check-circle text-emerald-500'
            } text-xs`}
          ></i>
          <span>
            {errorCount > 0
              ? `${errorCount} Errors`
              : hasIssues
              ? `${validationIssues.length} Warnings`
              : 'Flow Valid'}
          </span>
        </button>

        {/* Starter Presets Modal Button */}
        <button
          type="button"
          onClick={() => setShowPresetsModal(true)}
          className="flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i className="ph ph-magic-wand text-purple-500 text-xs font-bold"></i>
          <span className="hidden sm:inline">Presets</span>
        </button>

        {/* Active Toggle */}
        <button
          type="button"
          onClick={toggleActive}
          className={`flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold border border-gray-200 dark:border-gray-800 transition-colors ${
            isActive
              ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20'
              : 'text-gray-500 dark:text-gray-400'
          }`}
        >
          <i
            className={`ph ph-${isActive ? 'check-circle' : 'pause-circle'} text-sm font-bold ${
              isActive ? 'text-emerald-500' : 'text-gray-400'
            }`}
          ></i>
          <span className="hidden sm:inline">{isActive ? 'Active' : 'Paused'}</span>
        </button>

        {/* Publish Button */}
        <button
          type="button"
          onClick={saveFlow}
          disabled={isSaving}
          className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-xs transition-all ${
            isSaving
              ? 'bg-primary/70 cursor-wait'
              : saveSuccess
              ? 'bg-emerald-600 hover:bg-emerald-700'
              : 'bg-primary hover:bg-primary-deep'
          }`}
        >
          {isSaving ? (
            <>
              <i className="ph ph-spinner animate-spin text-sm"></i>
              <span>Saving...</span>
            </>
          ) : saveSuccess ? (
            <>
              <i className="ph ph-check text-sm font-bold"></i>
              <span>Published!</span>
            </>
          ) : (
            <>
              <i className="ph ph-paper-plane-tilt text-sm font-bold"></i>
              <span>Publish</span>
            </>
          )}
        </button>
      </div>
    </header>
  );
};
