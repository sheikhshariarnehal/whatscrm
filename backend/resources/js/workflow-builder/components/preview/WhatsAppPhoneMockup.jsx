import React from 'react';

export const WhatsAppPhoneMockup = ({ node }) => {
  if (!node) return null;

  const replaceVariables = (text) => {
    if (!text) return '';
    return text
      .replace(/{{contact\.name}}/g, 'Alex Johnson')
      .replace(/{{contact\.phone}}/g, '+1 (555) 234-5678')
      .replace(/{{vars\.[^}]+}}/g, 'Order #48291');
  };

  const bodyText = replaceVariables(
    node.data?.content?.text?.body || node.data?.text || node.data?.prompt_text || ''
  );

  return (
    <div className="w-[300px] mx-auto rounded-[38px] p-3 bg-gray-900 shadow-2xl border-4 border-gray-800 select-none">
      {/* Phone Speaker & Notch */}
      <div className="w-24 h-4 bg-black rounded-b-xl mx-auto mb-2 flex items-center justify-center">
        <div className="w-10 h-1 bg-gray-700 rounded-full"></div>
      </div>

      {/* Screen Frame */}
      <div className="rounded-[28px] overflow-hidden flex flex-col h-[480px] bg-[#efeae2] dark:bg-[#0b141a] wa-chat-pattern relative">
        {/* WhatsApp App Bar */}
        <div className="h-13 bg-[#075e54] dark:bg-[#202c33] text-white px-3 flex items-center justify-between shadow-sm shrink-0">
          <div className="flex items-center gap-2 min-w-0">
            <i className="ph ph-arrow-left text-sm font-bold"></i>
            <div className="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">
              <i className="ph-fill ph-whatsapp-logo text-emerald-300 text-base"></i>
            </div>
            <div className="min-w-0">
              <div className="font-bold text-xs leading-tight truncate flex items-center gap-1">
                <span>WhatsCRM Bot</span>
                <i className="ph-fill ph-seal-check text-[11px] text-emerald-400"></i>
              </div>
              <div className="text-[9px] text-emerald-100/70 truncate">Official Business</div>
            </div>
          </div>

          <div className="flex items-center gap-2.5 text-emerald-100">
            <i className="ph ph-video-camera text-sm"></i>
            <i className="ph ph-phone text-sm"></i>
            <i className="ph ph-dots-three-vertical text-sm"></i>
          </div>
        </div>

        {/* Message Area */}
        <div className="flex-1 p-3 overflow-y-auto space-y-3">
          {/* Timestamp Pill */}
          <div className="flex justify-center">
            <span className="px-2 py-0.5 rounded-md bg-white/80 dark:bg-[#182229]/90 text-[9px] font-semibold text-gray-500 shadow-2xs uppercase">
              Today
            </span>
          </div>

          {/* Incoming Customer Greeting Simulation */}
          <div className="flex justify-start">
            <div className="max-w-[80%] rounded-2xl rounded-tl-xs p-2.5 bg-white dark:bg-[#202c33] text-gray-800 dark:text-gray-100 text-[11px] shadow-xs">
              <p>Hi, I need some information!</p>
              <div className="text-[8.5px] text-gray-400 text-right mt-0.5">10:42 AM</div>
            </div>
          </div>

          {/* Outgoing Bot Response Mockup */}
          <div className="flex justify-end">
            <div className="max-w-[88%] flex flex-col gap-1 items-end">
              {/* Media Card */}
              {node.type === 'send_media' && (
                <div className="w-full rounded-2xl rounded-tr-xs overflow-hidden bg-[#d9fdd3] dark:bg-[#005c4b] shadow-xs p-1">
                  <div className="h-28 bg-gray-200 dark:bg-gray-700 rounded-xl overflow-hidden relative flex items-center justify-center">
                    <img
                      src={node.data?.url || 'https://images.unsplash.com/photo-1579208575657-c595a05383b7?auto=format&fit=crop&w=400&q=80'}
                      alt="Preview"
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute top-2 right-2 px-1.5 py-0.5 rounded bg-black/60 text-white text-[8px] uppercase font-bold">
                      {node.data?.mediaType || 'Image'}
                    </div>
                  </div>
                  {node.data?.caption && (
                    <p className="p-1.5 text-[11px] text-gray-800 dark:text-gray-100">
                      {replaceVariables(node.data.caption)}
                    </p>
                  )}
                </div>
              )}

              {/* Text Message Bubble */}
              {node.type !== 'send_media' && (
                <div className="rounded-2xl rounded-tr-xs p-2.5 bg-[#d9fdd3] dark:bg-[#005c4b] text-gray-800 dark:text-gray-100 text-[11px] shadow-xs w-full">
                  {node.data?.header && (
                    <div className="font-bold text-[11.5px] mb-1 text-gray-900 dark:text-white">
                      {replaceVariables(node.data.header)}
                    </div>
                  )}

                  <p className="whitespace-pre-wrap leading-relaxed">
                    {bodyText || 'Configured message will appear here.'}
                  </p>

                  {node.data?.footer && (
                    <div className="text-[9px] text-gray-500 dark:text-gray-300 mt-1 italic">
                      {replaceVariables(node.data.footer)}
                    </div>
                  )}

                  <div className="flex items-center justify-end gap-1 text-[8.5px] text-gray-500 dark:text-gray-300 mt-0.5">
                    <span>10:43 AM</span>
                    <i className="ph-bold ph-checks text-blue-500 text-xs"></i>
                  </div>
                </div>
              )}

              {/* Quick Reply CTA Buttons */}
              {node.type === 'send_buttons' && node.data?.buttons?.length > 0 && (
                <div className="w-full space-y-1">
                  {node.data.buttons.map((btn, idx) => (
                    <div
                      key={idx}
                      className="w-full py-1.5 px-3 rounded-xl bg-white dark:bg-[#202c33] text-primary dark:text-[#53bdeb] text-center text-xs font-semibold shadow-xs flex items-center justify-center gap-1.5 border border-gray-100 dark:border-gray-700/50"
                    >
                      <i className="ph ph-arrow-bend-down-left text-xs"></i>
                      <span>{btn.title || `Button ${idx + 1}`}</span>
                    </div>
                  ))}
                </div>
              )}

              {/* Menu List Button */}
              {node.type === 'send_list' && (
                <div className="w-full">
                  <div className="w-full py-1.5 px-3 rounded-xl bg-white dark:bg-[#202c33] text-teal-600 dark:text-teal-400 text-center text-xs font-bold shadow-xs flex items-center justify-center gap-1.5 border border-gray-100 dark:border-gray-700/50">
                    <i className="ph ph-list text-xs"></i>
                    <span>{node.data?.buttonText || 'View Options'} ({node.data?.items?.length || 0})</span>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Input Bar Footer */}
        <div className="h-11 bg-white dark:bg-[#202c33] px-2.5 flex items-center justify-between gap-2 border-t border-gray-200/80 dark:border-gray-800 shrink-0">
          <i className="ph ph-smiley text-gray-400 text-lg"></i>
          <div className="flex-1 bg-gray-100 dark:bg-[#182229] h-7 rounded-full px-3 flex items-center text-[10px] text-gray-400">
            Type a message...
          </div>
          <i className="ph ph-microphone text-emerald-600 text-lg"></i>
        </div>
      </div>
    </div>
  );
};
