import { create } from 'zustand';
import { applyNodeChanges, applyEdgeChanges, addEdge } from '@xyflow/react';

// Default starter node configurations for WhatsApp CRM
export const DEFAULT_NODE_DATA = {
  start: {
    title: '⚡ Inbound Trigger',
    keywords: 'hi, hello, menu, start',
    matchType: 'contains',
  },
  send_message: {
    title: '💬 Text Message',
    content: {
      type: 'text',
      text: { body: 'Hello {{contact.name}}! Thank you for reaching out. How can we assist you today?' },
    },
    preview_url: true,
  },
  send_buttons: {
    title: '🔘 Interactive Buttons',
    content: {
      text: { body: 'Please select an option below to proceed:' },
    },
    header: '',
    footer: 'Select an option',
    buttons: [
      { id: 'btn_0', title: 'Product Demo' },
      { id: 'btn_1', title: 'Pricing & Plans' },
      { id: 'btn_2', title: 'Talk to Agent' },
    ],
  },
  send_list: {
    title: '📋 Interactive Menu',
    content: {
      text: { body: 'Explore our catalog and services below:' },
    },
    buttonText: 'View Options',
    items: [
      { id: 'item_0', title: 'Support & Helpdesk', description: 'Technical assistance' },
      { id: 'item_1', title: 'Sales Inquiry', description: 'Talk to sales team' },
      { id: 'item_2', title: 'Track Order', description: 'Check status of order' },
    ],
  },
  send_media: {
    title: '🖼️ Media Attachment',
    mediaType: 'image',
    url: 'https://images.unsplash.com/photo-1579208575657-c595a05383b7?auto=format&fit=crop&w=800&q=80',
    caption: 'Here is our latest brochure 📄',
  },
  send_template: {
    title: '📑 WhatsApp Template',
    templateName: 'welcome_marketing_v1',
    language: 'en_US',
    parameters: [
      { key: '1', value: '{{contact.name}}' },
      { key: '2', value: 'Special 20% Discount' },
    ],
  },
  collect_input: {
    title: '📥 Collect Input',
    prompt_text: 'Please reply with your business email address:',
    var_key: 'customer_email',
    inputType: 'email',
  },
  condition: {
    title: '🔀 If / Else Branch',
    conditions: [
      {
        field: 'last_message',
        type: 'text_contains',
        value: 'yes',
        targetHandle: 'match',
      },
    ],
  },
  set_tag: {
    title: '🏷️ Tag Contact',
    label: 'Qualified Lead',
    action: 'add',
  },
  update_field: {
    title: '📝 Update Contact Field',
    field: 'lead_score',
    value: '80',
  },
  delay: {
    title: '⏳ Wait Timer',
    seconds: 5,
    unit: 'seconds',
  },
  handoff: {
    title: '👤 Live Agent Routing',
    message: 'An agent has been assigned and will reply shortly!',
    duration: 24, // pause bot for 24h
  },
  ai_assistant: {
    title: '✨ AI Assistant',
    systemPrompt: 'You are a warm, helpful customer support agent for our company. Answer questions politely.',
    model: 'gpt-4o-mini',
    maxTokens: 250,
  },
  http_webhook: {
    title: '🌐 REST Webhook',
    method: 'POST',
    url: 'https://api.example.com/v1/lead',
    headers: [{ key: 'Content-Type', value: 'application/json' }],
    body: '{\n  "phone": "{{contact.phone}}",\n  "name": "{{contact.name}}"\n}',
  },
  end: {
    title: '🏁 End Conversation',
    reason: 'Flow completed successfully',
  },
};

export const useWorkflowStore = create((set, get) => ({
  // Core flow metadata
  flowId: 1,
  flowName: 'Visual WhatsApp Automation',
  flowDescription: '',
  flowTriggerKeywords: 'hi, hello',
  isActive: true,
  backUrl: '/automations',
  apiUrls: {
    get: '',
    save: '',
    toggle: '',
  },
  csrfToken: '',

  // Graph elements
  nodes: [],
  edges: [],
  selectedNode: null,
  selectedEdge: null,

  // Status & UI
  isDirty: false,
  isSaving: false,
  saveSuccess: false,
  saveError: null,
  showValidationModal: false,
  showPresetsModal: false,
  validationIssues: [],

  // Undo / Redo history
  history: [],
  historyIndex: -1,

  // Initialize from HTML data attributes or API
  initFromData: (config) => {
    let graph = config.initialGraph || { nodes: [], edges: [] };
    if (typeof graph === 'string') {
      try {
        graph = JSON.parse(graph);
      } catch (e) {
        graph = { nodes: [], edges: [] };
      }
    }

    const normalizedNodes = (graph.nodes || []).map((n) => {
      let type = n.type || 'send_message';
      if (type === 'INITIAL') type = 'start';
      if (type === 'SEND_MESSAGE') type = 'send_message';
      if (type === 'QUICK_REPLY') type = 'send_buttons';
      if (type === 'INTERACTIVE_LIST') type = 'send_list';
      if (type === 'SEND_MEDIA') type = 'send_media';
      if (type === 'RESPONSE_SAVER') type = 'collect_input';
      if (type === 'CONDITION') type = 'condition';
      if (type === 'SET_CHAT_LABEL') type = 'set_tag';
      if (type === 'AGENT_TRANSFER') type = 'handoff';
      if (type === 'RESET') type = 'end';
      if (type === 'DELAY') type = 'delay';
      if (type === 'AI_TRANSFER') type = 'ai_assistant';
      if (type === 'MAKE_REQUEST') type = 'http_webhook';

      const data = {
        title: n.data?.title || DEFAULT_NODE_DATA[type]?.title || type,
        ...(DEFAULT_NODE_DATA[type] || {}),
        ...(n.data || {}),
      };

      // Ensure proper text structure
      if (!data.content) {
        data.content = { type: 'text', text: { body: n.data?.text || '' } };
      }

      return {
        id: n.id,
        type: type,
        position: n.position || { x: 100, y: 150 },
        data: data,
      };
    });

    const normalizedEdges = (graph.edges || []).map((e, idx) => ({
      id: e.id || `edge_${idx}`,
      source: e.source,
      target: e.target,
      sourceHandle: e.sourceHandle || 'default',
      targetHandle: e.targetHandle || null,
      animated: true,
      type: 'customWorkflowEdge',
      data: {
        label: e.label || '',
      },
    }));

    set({
      flowId: config.flowId || 1,
      flowName: config.flowName || 'WhatsApp Automation Flow',
      flowDescription: config.flowDescription || '',
      flowTriggerKeywords: config.triggerKeywords || '',
      isActive: config.isActive !== undefined ? config.isActive : true,
      backUrl: config.backUrl || '/automations',
      apiUrls: {
        get: config.apiGetUrl || '',
        save: config.apiSaveUrl || '',
        toggle: config.apiToggleUrl || '',
      },
      csrfToken: config.csrfToken || '',
      nodes: normalizedNodes,
      edges: normalizedEdges,
      selectedNode: normalizedNodes.length > 0 ? normalizedNodes[0] : null,
      selectedEdge: null,
      isDirty: false,
      history: [{ nodes: normalizedNodes, edges: normalizedEdges }],
      historyIndex: 0,
    });

    get().validateFlow();
  },

  // Push snapshot to undo stack
  pushHistory: () => {
    const { nodes, edges, history, historyIndex } = get();
    const newHistory = history.slice(0, historyIndex + 1);
    newHistory.push({
      nodes: JSON.parse(JSON.stringify(nodes)),
      edges: JSON.parse(JSON.stringify(edges)),
    });
    set({
      history: newHistory,
      historyIndex: newHistory.length - 1,
      isDirty: true,
    });
    get().validateFlow();
  },

  undo: () => {
    const { history, historyIndex } = get();
    if (historyIndex > 0) {
      const nextIndex = historyIndex - 1;
      const snapshot = history[nextIndex];
      set({
        nodes: JSON.parse(JSON.stringify(snapshot.nodes)),
        edges: JSON.parse(JSON.stringify(snapshot.edges)),
        historyIndex: nextIndex,
        isDirty: true,
      });
      get().validateFlow();
    }
  },

  redo: () => {
    const { history, historyIndex } = get();
    if (historyIndex < history.length - 1) {
      const nextIndex = historyIndex + 1;
      const snapshot = history[nextIndex];
      set({
        nodes: JSON.parse(JSON.stringify(snapshot.nodes)),
        edges: JSON.parse(JSON.stringify(snapshot.edges)),
        historyIndex: nextIndex,
        isDirty: true,
      });
      get().validateFlow();
    }
  },

  // React Flow Handlers
  onNodesChange: (changes) => {
    set({
      nodes: applyNodeChanges(changes, get().nodes),
      isDirty: true,
    });
  },

  onEdgesChange: (changes) => {
    set({
      edges: applyEdgeChanges(changes, get().edges),
      isDirty: true,
    });
    get().validateFlow();
  },

  onConnect: (connection) => {
    const newEdge = {
      ...connection,
      id: `edge_${Date.now()}`,
      animated: true,
      type: 'customWorkflowEdge',
      sourceHandle: connection.sourceHandle || 'default',
    };

    set({
      edges: addEdge(newEdge, get().edges),
      isDirty: true,
    });
    get().pushHistory();
  },

  // Add new node from palette
  addNode: (type, title, defaultProps = {}) => {
    const id = `node_${Date.now()}`;
    const nodes = get().nodes;
    const offset = (nodes.length % 5) * 45;

    const data = {
      title: title || DEFAULT_NODE_DATA[type]?.title || type,
      ...(DEFAULT_NODE_DATA[type] || {}),
      ...defaultProps,
    };

    const newNode = {
      id,
      type,
      position: { x: 260 + offset, y: 140 + offset },
      data,
    };

    set({
      nodes: [...nodes, newNode],
      selectedNode: newNode,
      selectedEdge: null,
      isDirty: true,
    });

    get().pushHistory();
  },

  // Update node data properties
  updateNodeData: (nodeId, partialData) => {
    const nodes = get().nodes.map((node) => {
      if (node.id === nodeId) {
        const updated = {
          ...node,
          data: {
            ...node.data,
            ...partialData,
          },
        };
        return updated;
      }
      return node;
    });

    const selectedNode = get().selectedNode?.id === nodeId
      ? nodes.find((n) => n.id === nodeId)
      : get().selectedNode;

    set({ nodes, selectedNode, isDirty: true });
    get().validateFlow();
  },

  deleteNode: (nodeId) => {
    const nodes = get().nodes.filter((n) => n.id !== nodeId);
    const edges = get().edges.filter((e) => e.source !== nodeId && e.target !== nodeId);

    set({
      nodes,
      edges,
      selectedNode: get().selectedNode?.id === nodeId ? null : get().selectedNode,
      isDirty: true,
    });

    get().pushHistory();
  },

  duplicateNode: (nodeId) => {
    const original = get().nodes.find((n) => n.id === nodeId);
    if (!original || original.type === 'start') return;

    const clone = {
      ...JSON.parse(JSON.stringify(original)),
      id: `node_${Date.now()}`,
      position: {
        x: original.position.x + 50,
        y: original.position.y + 50,
      },
    };
    clone.data.title = `${original.data.title || 'Step'} (Copy)`;

    set({
      nodes: [...get().nodes, clone],
      selectedNode: clone,
      selectedEdge: null,
      isDirty: true,
    });

    get().pushHistory();
  },

  selectNode: (node) => {
    set({ selectedNode: node, selectedEdge: null });
  },

  selectEdge: (edge) => {
    set({ selectedEdge: edge, selectedNode: null });
  },

  deleteEdge: (edgeId) => {
    set({
      edges: get().edges.filter((e) => e.id !== edgeId),
      selectedEdge: null,
      isDirty: true,
    });
    get().pushHistory();
  },

  setFlowName: (name) => {
    set({ flowName: name, isDirty: true });
  },

  setFlowTriggerKeywords: (keywords) => {
    set({ flowTriggerKeywords: keywords, isDirty: true });
  },

  toggleActive: async () => {
    const nextState = !get().isActive;
    set({ isActive: nextState });

    const toggleUrl = get().apiUrls.toggle;
    if (toggleUrl) {
      try {
        await fetch(toggleUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': get().csrfToken,
            Accept: 'application/json',
          },
        });
      } catch (e) {
        console.error('Failed to toggle flow active state', e);
      }
    }
  },

  // Auto layout using level assignment
  autoLayout: () => {
    const { nodes, edges } = get();
    if (!nodes.length) return;

    const startNode = nodes.find((n) => n.type === 'start') || nodes[0];
    const levels = {};
    const queue = [{ id: startNode.id, lvl: 0 }];
    const visited = { [startNode.id]: true };

    while (queue.length > 0) {
      const cur = queue.shift();
      if (!levels[cur.lvl]) levels[cur.lvl] = [];
      levels[cur.lvl].push(cur.id);

      const outEdges = edges.filter((e) => e.source === cur.id);
      outEdges.forEach((e) => {
        if (!visited[e.target]) {
          visited[e.target] = true;
          queue.push({ id: e.target, lvl: cur.lvl + 1 });
        }
      });
    }

    // Capture unvisited nodes
    const unvisitedLvl = Object.keys(levels).length;
    nodes.forEach((n) => {
      if (!visited[n.id]) {
        if (!levels[unvisitedLvl]) levels[unvisitedLvl] = [];
        levels[unvisitedLvl].push(n.id);
      }
    });

    const newNodes = nodes.map((node) => {
      let lvl = 0;
      let rankIdx = 0;

      for (const [l, idList] of Object.entries(levels)) {
        const idx = idList.indexOf(node.id);
        if (idx !== -1) {
          lvl = parseInt(l, 10);
          rankIdx = idx;
          break;
        }
      }

      return {
        ...node,
        position: {
          x: 100 + lvl * 360,
          y: 100 + rankIdx * 200,
        },
      };
    });

    set({ nodes: newNodes, isDirty: true });
    get().pushHistory();
  },

  // Integrity Validator
  validateFlow: () => {
    const { nodes, edges } = get();
    const issues = [];

    const hasStart = nodes.some((n) => n.type === 'start');
    if (!hasStart) {
      issues.push({
        severity: 'error',
        nodeId: null,
        message: 'Flow is missing an Inbound Start trigger.',
      });
    }

    nodes.forEach((n) => {
      const isStart = n.type === 'start';
      const isEnd = n.type === 'end' || n.type === 'handoff';

      if (!isStart) {
        const hasIncoming = edges.some((e) => e.target === n.id);
        if (!hasIncoming) {
          issues.push({
            severity: 'warning',
            nodeId: n.id,
            message: `"${n.data?.title || n.type}" has no incoming connection (unreachable).`,
          });
        }
      }

      if (!isEnd) {
        const hasOutgoing = edges.some((e) => e.source === n.id);
        if (!hasOutgoing) {
          issues.push({
            severity: 'warning',
            nodeId: n.id,
            message: `"${n.data?.title || n.type}" has no outgoing connection.`,
          });
        }
      }

      if (n.type === 'send_message') {
        const body = n.data?.content?.text?.body || '';
        if (!body.trim()) {
          issues.push({
            severity: 'error',
            nodeId: n.id,
            message: `"${n.data?.title || 'Message'}" has empty message text.`,
          });
        }
      }

      if (n.type === 'collect_input' && !n.data?.var_key) {
        issues.push({
          severity: 'error',
          nodeId: n.id,
          message: `"${n.data?.title || 'Collect Input'}" is missing a variable name key.`,
        });
      }
    });

    set({ validationIssues: issues });
  },

  // Save / Publish Flow to Laravel REST API
  saveFlow: async () => {
    const { flowId, flowName, flowDescription, flowTriggerKeywords, isActive, nodes, edges, apiUrls, csrfToken } = get();

    set({ isSaving: true, saveError: null, saveSuccess: false });

    const payload = {
      name: flowName,
      description: flowDescription,
      trigger_keywords: flowTriggerKeywords,
      is_active: isActive,
      flow_data: {
        nodes,
        edges,
      },
    };

    try {
      const url = apiUrls.save || `/automations/api/flows/${flowId}`;
      const response = await fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const resData = await response.json();

      if (!response.ok || !resData.success) {
        throw new Error(resData.message || 'Failed to save flow.');
      }

      set({
        isSaving: false,
        saveSuccess: true,
        isDirty: false,
      });

      setTimeout(() => {
        set({ saveSuccess: false });
      }, 3500);

      return true;
    } catch (err) {
      console.error('Save Flow Error:', err);
      set({
        isSaving: false,
        saveError: err.message || 'An error occurred while saving.',
      });
      return false;
    }
  },

  // Apply one of the 4 Flow Starter Presets
  applyPreset: (presetKey) => {
    let presetNodes = [];
    let presetEdges = [];

    if (presetKey === 'lead_qualification') {
      presetNodes = [
        { id: 'start', type: 'start', position: { x: 80, y: 150 }, data: { title: '⚡ Inbound Start', keywords: 'demo, lead, price' } },
        { id: 'welcome', type: 'send_message', position: { x: 380, y: 150 }, data: { title: '💬 Welcome Greeting', content: { text: { body: 'Hello {{contact.name}}! Thank you for contacting our sales team. How can we assist you today?' } } } },
        { id: 'options', type: 'send_buttons', position: { x: 740, y: 130 }, data: { title: '📋 Inquiry Options', content: { text: { body: 'Please select an option below:' } }, buttons: [{ id: 'btn_0', title: 'Book Live Demo' }, { id: 'btn_1', title: 'Pricing Plans' }] } },
        { id: 'capture_email', type: 'collect_input', position: { x: 1100, y: 100 }, data: { title: '📥 Collect Email', prompt_text: 'Great! Please reply with your business email address:', var_key: 'business_email' } },
        { id: 'tag_lead', type: 'set_tag', position: { x: 1460, y: 100 }, data: { title: '🏷️ Tag Qualified Lead', label: 'Qualified Lead' } },
        { id: 'handoff_agent', type: 'handoff', position: { x: 1820, y: 100 }, data: { title: '👤 Live Agent Routing', message: 'Thank you! A sales representative will be with you in a moment.' } },
      ];
      presetEdges = [
        { id: 'e1', source: 'start', target: 'welcome', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e2', source: 'welcome', target: 'options', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e3', source: 'options', target: 'capture_email', sourceHandle: 'btn_0', type: 'customWorkflowEdge' },
        { id: 'e4', source: 'capture_email', target: 'tag_lead', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e5', source: 'tag_lead', target: 'handoff_agent', sourceHandle: 'default', type: 'customWorkflowEdge' },
      ];
    } else if (presetKey === 'support_triage') {
      presetNodes = [
        { id: 'start', type: 'start', position: { x: 80, y: 150 }, data: { title: '⚡ Inbound Start', keywords: 'help, support' } },
        { id: 'triage_menu', type: 'send_buttons', position: { x: 380, y: 130 }, data: { title: '🛠️ Support Department', content: { text: { body: 'Welcome to customer support! What do you need help with?' } }, buttons: [{ id: 'btn_0', title: 'Billing Issue' }, { id: 'btn_1', title: 'Technical Problem' }] } },
        { id: 'billing_faq', type: 'send_message', position: { x: 740, y: 60 }, data: { title: '💳 Billing Help', content: { text: { body: 'You can manage and download invoices directly inside your workspace settings.' } } } },
        { id: 'tech_handoff', type: 'handoff', position: { x: 740, y: 220 }, data: { title: '👨‍💻 Tech Support Agent', message: 'Connecting you to our engineering support team now!' } },
      ];
      presetEdges = [
        { id: 'e1', source: 'start', target: 'triage_menu', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e2', source: 'triage_menu', target: 'billing_faq', sourceHandle: 'btn_0', type: 'customWorkflowEdge' },
        { id: 'e3', source: 'triage_menu', target: 'tech_handoff', sourceHandle: 'btn_1', type: 'customWorkflowEdge' },
      ];
    } else if (presetKey === 'order_lookup') {
      presetNodes = [
        { id: 'start', type: 'start', position: { x: 80, y: 150 }, data: { title: '⚡ Inbound Start', keywords: 'order, tracking' } },
        { id: 'ask_order', type: 'collect_input', position: { x: 380, y: 150 }, data: { title: '📦 Ask Order Number', prompt_text: 'Please reply with your 6-digit Order ID:', var_key: 'order_id' } },
        { id: 'webhook_query', type: 'http_webhook', position: { x: 740, y: 150 }, data: { title: '🌐 REST API Query', method: 'GET', url: 'https://api.example.com/orders/{{vars.order_id}}' } },
        { id: 'confirm_status', type: 'send_message', position: { x: 1100, y: 150 }, data: { title: '🚚 Dispatch Status', content: { text: { body: 'Order #{{vars.order_id}} is in transit! Estimated delivery: Tomorrow.' } } } },
        { id: 'end_flow', type: 'end', position: { x: 1460, y: 150 }, data: { title: '🏁 End Conversation' } },
      ];
      presetEdges = [
        { id: 'e1', source: 'start', target: 'ask_order', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e2', source: 'ask_order', target: 'webhook_query', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e3', source: 'webhook_query', target: 'confirm_status', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e4', source: 'confirm_status', target: 'end_flow', sourceHandle: 'default', type: 'customWorkflowEdge' },
      ];
    } else {
      presetNodes = [
        { id: 'start', type: 'start', position: { x: 80, y: 150 }, data: { title: '⚡ Inbound Start', keywords: 'hi, hello' } },
        { id: 'welcome_msg', type: 'send_message', position: { x: 380, y: 150 }, data: { title: '👋 Welcome Message', content: { text: { body: 'Hello {{contact.name}}! Welcome to our official WhatsApp service.' } } } },
        { id: 'menu_buttons', type: 'send_buttons', position: { x: 740, y: 130 }, data: { title: '🎯 Main Menu', content: { text: { body: 'How can we help you today?' } }, buttons: [{ id: 'btn_0', title: 'Services' }, { id: 'btn_1', title: 'Contact Us' }] } },
      ];
      presetEdges = [
        { id: 'e1', source: 'start', target: 'welcome_msg', sourceHandle: 'default', type: 'customWorkflowEdge' },
        { id: 'e2', source: 'welcome_msg', target: 'menu_buttons', sourceHandle: 'default', type: 'customWorkflowEdge' },
      ];
    }

    set({
      nodes: presetNodes,
      edges: presetEdges,
      selectedNode: presetNodes[0],
      selectedEdge: null,
      showPresetsModal: false,
      isDirty: true,
    });

    get().pushHistory();
  },

  setShowValidationModal: (val) => set({ showValidationModal: val }),
  setShowPresetsModal: (val) => set({ showPresetsModal: val }),
}));
