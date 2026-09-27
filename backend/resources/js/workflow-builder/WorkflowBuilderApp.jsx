import React, { useEffect } from 'react';
import { ReactFlowProvider } from '@xyflow/react';
import { useWorkflowStore } from './store/useWorkflowStore';
import { TopHeader } from './components/TopHeader';
import { Palette } from './components/Palette';
import { Canvas } from './components/Canvas';
import { Inspector } from './components/Inspector';
import { DiagnosticsModal } from './components/DiagnosticsModal';
import { PresetsModal } from './components/PresetsModal';

export const WorkflowBuilderApp = (props) => {
  const initFromData = useWorkflowStore((s) => s.initFromData);
  const isDirty = useWorkflowStore((s) => s.isDirty);

  useEffect(() => {
    initFromData(props);
  }, []);

  // Beforeunload prompt if unsaved changes exist
  useEffect(() => {
    const handleBeforeUnload = (e) => {
      if (isDirty) {
        e.preventDefault();
        e.returnValue = '';
      }
    };
    window.addEventListener('beforeunload', handleBeforeUnload);
    return () => window.removeEventListener('beforeunload', handleBeforeUnload);
  }, [isDirty]);

  return (
    <div className="flex-1 flex flex-col h-screen w-screen overflow-hidden bg-[#f4f5f8] dark:bg-[#0f141c] text-gray-800 dark:text-gray-100 font-sans select-none">
      {/* 1. Header Toolbar */}
      <TopHeader />

      {/* 2. Main Builder Workspace: Palette + Canvas + Inspector */}
      <div className="flex-1 flex overflow-hidden relative">
        <Palette />
        <ReactFlowProvider>
          <Canvas />
        </ReactFlowProvider>
        <Inspector />
      </div>

      {/* 3. Auxiliary Modals */}
      <DiagnosticsModal />
      <PresetsModal />
    </div>
  );
};
