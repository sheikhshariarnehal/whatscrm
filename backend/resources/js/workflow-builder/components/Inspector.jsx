import React, { useState } from 'react';
import { useWorkflowStore } from '../store/useWorkflowStore';
import { WhatsAppPhoneMockup } from './preview/WhatsAppPhoneMockup';

export const Inspector = () => {
  const { selectedNode, selectNode, updateNodeData, deleteNode, duplicateNode } =
    useWorkflowStore();

  const [activeTab, setActiveTab] = useState('settings'); // 'settings' | 'preview'

  if (!selectedNode) {
    return (
      <aside className="w-84 bg-white dark:bg-[#151b24] border-l border-gray-200/90 dark:border-gray-800/90 flex flex-col items-center justify-center p-6 text-center shrink-0 select-none">
        <div className="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center text-gray-400 mb-3">
          <i className="ph ph-cursor-click text-2xl"></i>
        </div>
        <h4 className="font-bold text-xs text-gray-800 dark:text-gray-200 mb-1">
          No Step Selected
        </h4>
        <p className="text-[11px] text-gray-400 max-w-[200px]">
          Click on any node in the canvas to inspect and customize its WhatsApp behavior.
        </p>
      </aside>
    );
  }

  const { id, type, data = {} } = selectedNode;

  const insertVariable = (tag) => {
    if (type === 'send_message' || type === 'send_buttons' || type === 'send_list') {
      const current = data.content?.text?.body || '';
      updateNodeData(id, {
        content: {
          ...data.content,
          text: { body: current ? `${current} ${tag}` : tag },
        },
      });
    } else if (type === 'collect_input') {
      const current = data.prompt_text || '';
      updateNodeData(id, { prompt_text: current ? `${current} ${tag}` : tag });
    }
  };

  const isMessageCategory = ['send_message', 'send_buttons', 'send_list', 'send_media', 'collect_input', 'send_template'].includes(type);

  return (
    <aside className="w-84 bg-white dark:bg-[#151b24] border-l border-gray-200/90 dark:border-gray-800/90 flex flex-col z-20 shrink-0 select-none h-full overflow-hidden">
      {/* Drawer Header */}
      <div className="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <div className="flex items-center gap-2 min-w-0">
          <div className="w-6 h-6 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <i className="ph ph-sliders text-xs font-bold"></i>
          </div>
          <div className="min-w-0">
            <h4 className="font-bold text-xs text-gray-900 dark:text-white truncate">
              Step Configuration
            </h4>
            <span className="text-[10px] text-gray-400 font-mono">
              #{id.replace('node_', '').slice(-6)}
            </span>
          </div>
        </div>

        <button
          type="button"
          onClick={() => selectNode(null)}
          className="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <i className="ph ph-x text-sm font-bold"></i>
        </button>
      </div>

      {/* Tabs: Settings vs Live WhatsApp Preview */}
      {isMessageCategory && (
        <div className="px-4 pt-2 border-b border-gray-100 dark:border-gray-800/80 flex items-center gap-2">
          <button
            type="button"
            onClick={() => setActiveTab('settings')}
            className={`pb-2 px-1 text-xs font-semibold border-b-2 transition-all ${
              activeTab === 'settings'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
            }`}
          >
            Settings
          </button>
          <button
            type="button"
            onClick={() => setActiveTab('preview')}
            className={`pb-2 px-1 text-xs font-semibold border-b-2 flex items-center gap-1 transition-all ${
              activeTab === 'preview'
                ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                : 'border-transparent text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
            }`}
          >
            <i className="ph-fill ph-whatsapp-logo text-xs text-emerald-500"></i>
            <span>Live Preview</span>
          </button>
        </div>
      )}

      {/* Drawer Scrollable Body */}
      <div className="flex-1 overflow-y-auto p-4 space-y-4 text-xs">
        {activeTab === 'preview' && isMessageCategory ? (
          <div className="py-2">
            <div className="text-center mb-3">
              <span className="text-[10px] uppercase tracking-wider font-bold text-gray-400">
                Customer Phone Preview
              </span>
            </div>
            <WhatsAppPhoneMockup node={selectedNode} />
          </div>
        ) : (
          <>
            {/* Step Title Input */}
            <div>
              <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                Step Title
              </label>
              <input
                type="text"
                value={data.title || ''}
                onChange={(e) => updateNodeData(id, { title: e.target.value })}
                placeholder="e.g. Welcome Message"
                className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 focus:border-primary focus:ring-1 focus:ring-primary text-gray-900 dark:text-white"
              />
            </div>

            {/* Inbound Start Trigger Config */}
            {type === 'start' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Trigger Keywords
                  </label>
                  <input
                    type="text"
                    value={data.keywords || ''}
                    onChange={(e) => updateNodeData(id, { keywords: e.target.value })}
                    placeholder="e.g. hi, hello, pricing, help"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 focus:border-primary text-gray-900 dark:text-white font-mono"
                  />
                  <span className="text-[10px] text-gray-400 mt-1 block">
                    Separate multiple keywords with commas. Leave blank to trigger on any message.
                  </span>
                </div>
              </div>
            )}

            {/* Variable Tokens Helper */}
            {isMessageCategory && (
              <div>
                <label className="text-[10px] font-bold uppercase text-gray-400 tracking-wider block mb-1">
                  Insert Customer Variables
                </label>
                <div className="flex flex-wrap gap-1">
                  <button
                    type="button"
                    onClick={() => insertVariable('{{contact.name}}')}
                    className="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-[10px] font-mono text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors"
                  >
                    + contact.name
                  </button>
                  <button
                    type="button"
                    onClick={() => insertVariable('{{contact.phone}}')}
                    className="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-[10px] font-mono text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors"
                  >
                    + contact.phone
                  </button>
                  <button
                    type="button"
                    onClick={() => insertVariable('{{vars.input}}')}
                    className="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-[10px] font-mono text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors"
                  >
                    + vars.input
                  </button>
                </div>
              </div>
            )}

            {/* Text Message Config */}
            {type === 'send_message' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    WhatsApp Message Text
                  </label>
                  <textarea
                    rows={5}
                    value={data.content?.text?.body || ''}
                    onChange={(e) =>
                      updateNodeData(id, {
                        content: {
                          ...data.content,
                          text: { body: e.target.value },
                        },
                      })
                    }
                    placeholder="Hello {{contact.name}}..."
                    className="w-full p-2.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 focus:border-primary text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* Interactive Buttons Config */}
            {type === 'send_buttons' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Prompt Text
                  </label>
                  <textarea
                    rows={3}
                    value={data.content?.text?.body || ''}
                    onChange={(e) =>
                      updateNodeData(id, {
                        content: {
                          ...data.content,
                          text: { body: e.target.value },
                        },
                      })
                    }
                    placeholder="Please select an option below:"
                    className="w-full p-2.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 focus:border-primary text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <div className="flex items-center justify-between mb-1.5">
                    <label className="font-semibold text-gray-700 dark:text-gray-300">
                      Buttons (Max 3)
                    </label>
                    {(data.buttons || []).length < 3 && (
                      <button
                        type="button"
                        onClick={() => {
                          const current = data.buttons || [];
                          const idx = current.length;
                          updateNodeData(id, {
                            buttons: [...current, { id: `btn_${idx}`, title: `Option ${idx + 1}` }],
                          });
                        }}
                        className="text-primary hover:underline text-[11px] font-bold"
                      >
                        + Add Button
                      </button>
                    )}
                  </div>

                  <div className="space-y-1.5">
                    {(data.buttons || []).map((btn, bIdx) => (
                      <div key={bIdx} className="flex items-center gap-2">
                        <input
                          type="text"
                          value={btn.title || ''}
                          onChange={(e) => {
                            const newButtons = [...(data.buttons || [])];
                            newButtons[bIdx] = { ...newButtons[bIdx], title: e.target.value };
                            updateNodeData(id, { buttons: newButtons });
                          }}
                          placeholder={`Button ${bIdx + 1}`}
                          className="flex-1 px-2.5 py-1 text-xs rounded-lg bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                        />
                        {(data.buttons || []).length > 1 && (
                          <button
                            type="button"
                            onClick={() => {
                              const newButtons = (data.buttons || []).filter((_, i) => i !== bIdx);
                              updateNodeData(id, { buttons: newButtons });
                            }}
                            className="p-1 text-gray-400 hover:text-rose-500"
                          >
                            <i className="ph ph-trash text-xs"></i>
                          </button>
                        )}
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {/* Interactive Menu List Config */}
            {type === 'send_list' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Message Body
                  </label>
                  <textarea
                    rows={3}
                    value={data.content?.text?.body || ''}
                    onChange={(e) =>
                      updateNodeData(id, {
                        content: {
                          ...data.content,
                          text: { body: e.target.value },
                        },
                      })
                    }
                    placeholder="Select from our options below:"
                    className="w-full p-2.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Menu Button Title
                  </label>
                  <input
                    type="text"
                    value={data.buttonText || ''}
                    onChange={(e) => updateNodeData(id, { buttonText: e.target.value })}
                    placeholder="e.g. View Options"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <div className="flex items-center justify-between mb-1.5">
                    <label className="font-semibold text-gray-700 dark:text-gray-300">
                      Menu Rows (Items)
                    </label>
                    <button
                      type="button"
                      onClick={() => {
                        const current = data.items || [];
                        const idx = current.length;
                        updateNodeData(id, {
                          items: [
                            ...current,
                            { id: `item_${idx}`, title: `Item ${idx + 1}`, description: 'Details' },
                          ],
                        });
                      }}
                      className="text-teal-600 dark:text-teal-400 hover:underline text-[11px] font-bold"
                    >
                      + Add Item
                    </button>
                  </div>

                  <div className="space-y-2">
                    {(data.items || []).map((item, iIdx) => (
                      <div
                        key={iIdx}
                        className="p-2 rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 space-y-1.5"
                      >
                        <div className="flex items-center gap-1.5">
                          <input
                            type="text"
                            value={item.title || ''}
                            onChange={(e) => {
                              const newItems = [...(data.items || [])];
                              newItems[iIdx] = { ...newItems[iIdx], title: e.target.value };
                              updateNodeData(id, { items: newItems });
                            }}
                            placeholder="Title..."
                            className="flex-1 px-2 py-1 text-xs rounded-lg bg-white dark:bg-[#151b24] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-semibold"
                          />
                          {(data.items || []).length > 1 && (
                            <button
                              type="button"
                              onClick={() => {
                                const newItems = (data.items || []).filter((_, i) => i !== iIdx);
                                updateNodeData(id, { items: newItems });
                              }}
                              className="p-1 text-gray-400 hover:text-rose-500"
                            >
                              <i className="ph ph-trash text-xs"></i>
                            </button>
                          )}
                        </div>
                        <input
                          type="text"
                          value={item.description || ''}
                          onChange={(e) => {
                            const newItems = [...(data.items || [])];
                            newItems[iIdx] = { ...newItems[iIdx], description: e.target.value };
                            updateNodeData(id, { items: newItems });
                          }}
                          placeholder="Description (optional)..."
                          className="w-full px-2 py-1 text-[11px] rounded-lg bg-white dark:bg-[#151b24] border border-gray-200 dark:border-gray-700 text-gray-500"
                        />
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {/* Media Attachment Config */}
            {type === 'send_media' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Media Type
                  </label>
                  <select
                    value={data.mediaType || 'image'}
                    onChange={(e) => updateNodeData(id, { mediaType: e.target.value })}
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold"
                  >
                    <option value="image">Image (JPG/PNG)</option>
                    <option value="document">Document (PDF/DOC)</option>
                    <option value="video">Video (MP4)</option>
                    <option value="audio">Audio Voice Note</option>
                  </select>
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Media File URL
                  </label>
                  <input
                    type="url"
                    value={data.url || ''}
                    onChange={(e) => updateNodeData(id, { url: e.target.value })}
                    placeholder="https://example.com/asset.jpg"
                    className="w-full px-3 py-1.5 text-xs font-mono rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Caption (Optional)
                  </label>
                  <input
                    type="text"
                    value={data.caption || ''}
                    onChange={(e) => updateNodeData(id, { caption: e.target.value })}
                    placeholder="Brochure details..."
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* Template HSM Config */}
            {type === 'send_template' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Meta Template Name
                  </label>
                  <input
                    type="text"
                    value={data.templateName || ''}
                    onChange={(e) => updateNodeData(id, { templateName: e.target.value })}
                    placeholder="e.g. order_confirmation"
                    className="w-full px-3 py-1.5 text-xs font-mono rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Language Code
                  </label>
                  <input
                    type="text"
                    value={data.language || 'en_US'}
                    onChange={(e) => updateNodeData(id, { language: e.target.value })}
                    placeholder="en_US"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* Collect Input Config */}
            {type === 'collect_input' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Prompt Question to Customer
                  </label>
                  <textarea
                    rows={3}
                    value={data.prompt_text || ''}
                    onChange={(e) => updateNodeData(id, { prompt_text: e.target.value })}
                    placeholder="What is your email address?"
                    className="w-full p-2.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Store Response into Variable
                  </label>
                  <div className="flex items-center gap-1.5">
                    <span className="font-mono text-gray-400 text-xs">vars.</span>
                    <input
                      type="text"
                      value={data.var_key || ''}
                      onChange={(e) => updateNodeData(id, { var_key: e.target.value })}
                      placeholder="customer_email"
                      className="flex-1 px-3 py-1.5 text-xs font-mono font-bold rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-purple-600 dark:text-purple-400"
                    />
                  </div>
                </div>
              </div>
            )}

            {/* Condition Config */}
            {type === 'condition' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Match Operator
                  </label>
                  <select
                    value={data.conditions?.[0]?.type || 'text_contains'}
                    onChange={(e) => {
                      const cur = data.conditions?.[0] || {};
                      updateNodeData(id, {
                        conditions: [{ ...cur, type: e.target.value }],
                      });
                    }}
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold"
                  >
                    <option value="text_contains">Message contains</option>
                    <option value="text_exact">Message exactly matches</option>
                    <option value="starts_with">Message starts with</option>
                    <option value="regex">Matches regular expression</option>
                  </select>
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Value / Keyword to Match
                  </label>
                  <input
                    type="text"
                    value={data.conditions?.[0]?.value || ''}
                    onChange={(e) => {
                      const cur = data.conditions?.[0] || {};
                      updateNodeData(id, {
                        conditions: [{ ...cur, value: e.target.value }],
                      });
                    }}
                    placeholder="e.g. yes, pricing, agent"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-mono"
                  />
                </div>
              </div>
            )}

            {/* Tag Contact Config */}
            {type === 'set_tag' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    CRM Tag Name
                  </label>
                  <input
                    type="text"
                    value={data.label || ''}
                    onChange={(e) => updateNodeData(id, { label: e.target.value })}
                    placeholder="e.g. VIP Customer, Lead"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold"
                  />
                </div>

                <div className="flex flex-wrap gap-1.5 pt-1">
                  {['Hot Lead', 'VIP', 'Support', 'Demo Booked'].map((tag) => (
                    <button
                      key={tag}
                      type="button"
                      onClick={() => updateNodeData(id, { label: tag })}
                      className="px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 text-[10px] font-semibold text-gray-600 dark:text-gray-300 hover:bg-pink-100 hover:text-pink-600 transition-colors"
                    >
                      {tag}
                    </button>
                  ))}
                </div>
              </div>
            )}

            {/* Update Field Config */}
            {type === 'update_field' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Contact Field Key
                  </label>
                  <input
                    type="text"
                    value={data.field || ''}
                    onChange={(e) => updateNodeData(id, { field: e.target.value })}
                    placeholder="lead_status, company, city"
                    className="w-full px-3 py-1.5 text-xs font-mono rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Value to Assign
                  </label>
                  <input
                    type="text"
                    value={data.value || ''}
                    onChange={(e) => updateNodeData(id, { value: e.target.value })}
                    placeholder="Qualified"
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* Wait Delay Config */}
            {type === 'delay' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Duration
                  </label>
                  <div className="flex gap-2">
                    <input
                      type="number"
                      min={1}
                      max={3600}
                      value={data.seconds || 5}
                      onChange={(e) =>
                        updateNodeData(id, { seconds: parseInt(e.target.value, 10) || 1 })
                      }
                      className="w-24 px-3 py-1.5 text-xs font-bold rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                    />
                    <select
                      value={data.unit || 'seconds'}
                      onChange={(e) => updateNodeData(id, { unit: e.target.value })}
                      className="flex-1 px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold"
                    >
                      <option value="seconds">Seconds</option>
                      <option value="minutes">Minutes</option>
                      <option value="hours">Hours</option>
                    </select>
                  </div>
                </div>
              </div>
            )}

            {/* Agent Handoff Config */}
            {type === 'handoff' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Notice to Customer
                  </label>
                  <input
                    type="text"
                    value={data.message || ''}
                    onChange={(e) => updateNodeData(id, { message: e.target.value })}
                    placeholder="Connecting you with an agent..."
                    className="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>

                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Pause Bot (Hours)
                  </label>
                  <input
                    type="number"
                    min={1}
                    max={72}
                    value={data.duration || 24}
                    onChange={(e) =>
                      updateNodeData(id, { duration: parseInt(e.target.value, 10) || 1 })
                    }
                    className="w-full px-3 py-1.5 text-xs font-bold rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* AI Assistant Config */}
            {type === 'ai_assistant' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    AI Assistant System Prompt
                  </label>
                  <textarea
                    rows={6}
                    value={data.systemPrompt || ''}
                    onChange={(e) => updateNodeData(id, { systemPrompt: e.target.value })}
                    placeholder="You are a helpful customer support agent for our store..."
                    className="w-full p-2.5 text-xs rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                  />
                </div>
              </div>
            )}

            {/* HTTP Webhook Config */}
            {type === 'http_webhook' && (
              <div className="space-y-3">
                <div>
                  <label className="font-semibold text-gray-700 dark:text-gray-300 block mb-1">
                    Method & Endpoint
                  </label>
                  <div className="flex gap-2">
                    <select
                      value={data.method || 'POST'}
                      onChange={(e) => updateNodeData(id, { method: e.target.value })}
                      className="w-20 px-2 py-1.5 text-xs font-bold rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                    >
                      <option value="POST">POST</option>
                      <option value="GET">GET</option>
                      <option value="PUT">PUT</option>
                    </select>
                    <input
                      type="url"
                      value={data.url || ''}
                      onChange={(e) => updateNodeData(id, { url: e.target.value })}
                      placeholder="https://api.example.com/webhook"
                      className="flex-1 px-3 py-1.5 text-xs font-mono rounded-xl bg-gray-50 dark:bg-[#1c2430] border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white"
                    />
                  </div>
                </div>
              </div>
            )}
          </>
        )}
      </div>

      {/* Drawer Footer Actions */}
      <div className="px-4 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/20 shrink-0">
        {type !== 'start' ? (
          <button
            type="button"
            onClick={() => deleteNode(id)}
            className="flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors"
          >
            <i className="ph ph-trash text-xs font-bold"></i>
            <span>Delete</span>
          </button>
        ) : (
          <div className="w-1"></div>
        )}

        <button
          type="button"
          onClick={() => duplicateNode(id)}
          disabled={type === 'start'}
          className={`flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 transition-colors ml-auto ${
            type === 'start'
              ? 'opacity-40 cursor-not-allowed text-gray-400'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'
          }`}
        >
          <i className="ph ph-copy text-xs font-bold"></i>
          <span>Duplicate</span>
        </button>
      </div>
    </aside>
  );
};
