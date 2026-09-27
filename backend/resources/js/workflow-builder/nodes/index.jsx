import React from 'react';
import { StartTriggerNode } from './StartTriggerNode';
import { ButtonsNode } from './ButtonsNode';
import { ListNode } from './ListNode';
import { ConditionNode } from './ConditionNode';
import { StandardActionNode } from './StandardActionNode';

export const nodeTypes = {
  start: StartTriggerNode,
  send_buttons: ButtonsNode,
  send_list: ListNode,
  condition: ConditionNode,
  // Standard action nodes using the unified action wrapper
  send_message: (props) => <StandardActionNode {...props} type="send_message" />,
  send_media: (props) => <StandardActionNode {...props} type="send_media" />,
  send_template: (props) => <StandardActionNode {...props} type="send_template" />,
  collect_input: (props) => <StandardActionNode {...props} type="collect_input" />,
  set_tag: (props) => <StandardActionNode {...props} type="set_tag" />,
  update_field: (props) => <StandardActionNode {...props} type="update_field" />,
  delay: (props) => <StandardActionNode {...props} type="delay" />,
  handoff: (props) => <StandardActionNode {...props} type="handoff" />,
  ai_assistant: (props) => <StandardActionNode {...props} type="ai_assistant" />,
  http_webhook: (props) => <StandardActionNode {...props} type="http_webhook" />,
  end: (props) => <StandardActionNode {...props} type="end" />,
};
