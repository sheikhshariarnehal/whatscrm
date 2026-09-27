import React from 'react';
import { useWorkflowStore } from '../store/useWorkflowStore';

export const DiagnosticsModal = () => {
  const {
    showValidationModal,
    setShowValidationModal,
    validationIssues,
    nodes,
    selectNode,
  } = useWorkflowStore();

  if (!showValidationModal) return null;

  const handleFixIssue = (nodeId) => {
    if (nodeId) {
      const node = nodes.find((n) => n.id === nodeId);
      if (node) {
        selectNode(node);
      }
    }
    setShowValidationModal(false);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs select-none">
      <div className="bg-white dark:bg-[#151b24] rounded-3xl border border-gray-200/90 dark:border-gray-800 shadow-2xl max-w-lg w-full overflow-hidden flex flex-col">
        {/* Header */}
        <div className="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
              <i className="ph ph-shield-check text-lg font-bold"></i>
            </div>
            <div>
              <h3 className="font-bold text-sm text-gray-900 dark:text-white">
                Flow Integrity Diagnostics
              </h3>
              <p className="text-[11px] text-gray-400">
                Automated validation of your visual automation graph
              </p>
            </div>
          </div>

          <button
            type="button"
            onClick={() => setShowValidationModal(false)}
            className="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          >
            <i className="ph ph-x text-sm font-bold"></i>
          </button>
        </div>

        {/* Content */}
        <div className="p-5 max-h-80 overflow-y-auto space-y-2.5">
          {validationIssues.length === 0 ? (
            <div className="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3 text-emerald-700 dark:text-emerald-300">
              <i className="ph-fill ph-check-circle text-xl shrink-0"></i>
              <div className="text-xs">
                <span className="font-bold block">All integrity checks passed!</span>
                Your WhatsApp flow is fully connected and ready for live broadcast.
              </div>
            </div>
          ) : (
            validationIssues.map((issue, idx) => (
              <div
                key={idx}
                onClick={() => handleFixIssue(issue.nodeId)}
                className={`flex items-center justify-between p-3 rounded-2xl border transition-all cursor-pointer hover:shadow-xs ${
                  issue.severity === 'error'
                    ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100/50'
                    : 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100/50'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0 pr-2">
                  <i
                    className={`ph-fill ${
                      issue.severity === 'error'
                        ? 'ph-warning-circle text-rose-500'
                        : 'ph-warning text-amber-500'
                    } text-base shrink-0`}
                  ></i>
                  <span className="text-xs font-medium truncate">{issue.message}</span>
                </div>

                {issue.nodeId && (
                  <span className="text-[11px] font-bold text-primary shrink-0 ml-2 hover:underline">
                    Fix Step →
                  </span>
                )}
              </div>
            ))
          )}
        </div>

        {/* Footer */}
        <div className="px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex justify-end bg-gray-50/50 dark:bg-gray-800/20">
          <button
            type="button"
            onClick={() => setShowValidationModal(false)}
            className="px-4 py-1.5 rounded-xl bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-xs font-semibold text-gray-800 dark:text-gray-100 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};
