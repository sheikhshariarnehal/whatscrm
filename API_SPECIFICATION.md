# WhatsCRM REST API Specification (378 Endpoints across 23 Route Modules)

> **Base URL**: `http://localhost:3010` / `https://your-domain.com`
> **Authentication Header**: `Authorization: <jwt_token>`
> **Standard Content-Type**: `application/json` or `multipart/form-data` (for file uploads)

## Module: `admin.js` (Base Route: `/api/admin`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/login` | `/api/admin/login` | Handler endpoint in `admin.js` |
| `POST` | `/add_plan` | `/api/admin/add_plan` | Handler endpoint in `admin.js` |
| `POST` | `/update_plan_data` | `/api/admin/update_plan_data` | Handler endpoint in `admin.js` |
| `GET` | `/get_plans` | `/api/admin/get_plans` | Handler endpoint in `admin.js` |
| `GET` | `/get_web_public` | `/api/admin/get_web_public` | Handler endpoint in `admin.js` |
| `POST` | `/del_plan` | `/api/admin/del_plan` | Handler endpoint in `admin.js` |
| `GET` | `/get_users` | `/api/admin/get_users` | Handler endpoint in `admin.js` |
| `POST` | `/update_user` | `/api/admin/update_user` | Handler endpoint in `admin.js` |
| `POST` | `/update_plan` | `/api/admin/update_plan` | Handler endpoint in `admin.js` |
| `GET` | `/get_payment_gateway_admin` | `/api/admin/get_payment_gateway_admin` | Handler endpoint in `admin.js` |
| `POST` | `/update_pay_gateway` | `/api/admin/update_pay_gateway` | Handler endpoint in `admin.js` |
| `POST` | `/add_brand_image` | `/api/admin/add_brand_image` | Handler endpoint in `admin.js` |
| `GET` | `/get_brands` | `/api/admin/get_brands` | Handler endpoint in `admin.js` |
| `POST` | `/del_brand_logo` | `/api/admin/del_brand_logo` | Handler endpoint in `admin.js` |
| `POST` | `/add_faq` | `/api/admin/add_faq` | Handler endpoint in `admin.js` |
| `GET` | `/get_faq` | `/api/admin/get_faq` | Handler endpoint in `admin.js` |
| `POST` | `/del_faq` | `/api/admin/del_faq` | Handler endpoint in `admin.js` |
| `POST` | `/add_page` | `/api/admin/add_page` | Handler endpoint in `admin.js` |
| `GET` | `/get_pages` | `/api/admin/get_pages` | Handler endpoint in `admin.js` |
| `POST` | `/del_page` | `/api/admin/del_page` | Handler endpoint in `admin.js` |
| `POST` | `/auto_login` | `/api/admin/auto_login` | Handler endpoint in `admin.js` |
| `POST` | `/add_testimonial` | `/api/admin/add_testimonial` | Handler endpoint in `admin.js` |
| `GET` | `/get_testi` | `/api/admin/get_testi` | Handler endpoint in `admin.js` |
| `POST` | `/del_testi` | `/api/admin/del_testi` | Handler endpoint in `admin.js` |
| `GET` | `/get_orders` | `/api/admin/get_orders` | Handler endpoint in `admin.js` |
| `POST` | `/del_order` | `/api/admin/del_order` | Handler endpoint in `admin.js` |
| `GET` | `/get_contact_leads` | `/api/admin/get_contact_leads` | Handler endpoint in `admin.js` |
| `POST` | `/del_cotact_entry` | `/api/admin/del_cotact_entry` | Handler endpoint in `admin.js` |
| `POST` | `/get_page_slug` | `/api/admin/get_page_slug` | Handler endpoint in `admin.js` |
| `POST` | `/update_terms` | `/api/admin/update_terms` | Handler endpoint in `admin.js` |
| `POST` | `/update_privacy_policy` | `/api/admin/update_privacy_policy` | Handler endpoint in `admin.js` |
| `GET` | `/get_smtp` | `/api/admin/get_smtp` | Handler endpoint in `admin.js` |
| `POST` | `/update_smtp` | `/api/admin/update_smtp` | Handler endpoint in `admin.js` |
| `POST` | `/send_test_email` | `/api/admin/send_test_email` | Handler endpoint in `admin.js` |
| `GET` | `/get_dashboard_for_user` | `/api/admin/get_dashboard_for_user` | Handler endpoint in `admin.js` |
| `GET` | `/get_admin` | `/api/admin/get_admin` | Handler endpoint in `admin.js` |
| `POST` | `/update-admin` | `/api/admin/update-admin` | Handler endpoint in `admin.js` |
| `POST` | `/send_resovery` | `/api/admin/send_resovery` | Handler endpoint in `admin.js` |
| `GET` | `/modify_password` | `/api/admin/modify_password` | Handler endpoint in `admin.js` |
| `POST` | `/del_user` | `/api/admin/del_user` | Handler endpoint in `admin.js` |
| `GET` | `/get_wa_gen` | `/api/admin/get_wa_gen` | Handler endpoint in `admin.js` |
| `POST` | `/de_wa_den_link` | `/api/admin/de_wa_den_link` | Handler endpoint in `admin.js` |
| `GET` | `/get_social_login` | `/api/admin/get_social_login` | Handler endpoint in `admin.js` |
| `POST` | `/update_social_login` | `/api/admin/update_social_login` | Handler endpoint in `admin.js` |
| `POST` | `/update_rtl` | `/api/admin/update_rtl` | Handler endpoint in `admin.js` |
| `GET` | `/get_qr_set` | `/api/admin/get_qr_set` | Handler endpoint in `admin.js` |
| `POST` | `/update_qr_set` | `/api/admin/update_qr_set` | Handler endpoint in `admin.js` |
| `GET` | `/get_mobile_app_dt` | `/api/admin/get_mobile_app_dt` | Handler endpoint in `admin.js` |
| `POST` | `/update_mb` | `/api/admin/update_mb` | Handler endpoint in `admin.js` |
| `POST` | `/update_embed_config` | `/api/admin/update_embed_config` | Handler endpoint in `admin.js` |
| `GET` | `/get_tele_config` | `/api/admin/get_tele_config` | Handler endpoint in `admin.js` |
| `POST` | `/update_tele_config` | `/api/admin/update_tele_config` | Handler endpoint in `admin.js` |
| `POST` | `/add_flow_template` | `/api/admin/add_flow_template` | Handler endpoint in `admin.js` |
| `GET` | `/get_flow_templates` | `/api/admin/get_flow_templates` | Handler endpoint in `admin.js` |
| `POST` | `/delete_flow_template` | `/api/admin/delete_flow_template` | Handler endpoint in `admin.js` |
| `GET` | `/get_fcm_data` | `/api/admin/get_fcm_data` | Handler endpoint in `admin.js` |
| `POST` | `/update_fcm_data` | `/api/admin/update_fcm_data` | Handler endpoint in `admin.js` |
| `GET` | `/get_fcm_subs` | `/api/admin/get_fcm_subs` | Handler endpoint in `admin.js` |
| `POST` | `/send_fcm_manul` | `/api/admin/send_fcm_manul` | Handler endpoint in `admin.js` |
| `GET` | `/get_inbox_db` | `/api/admin/get_inbox_db` | Handler endpoint in `admin.js` |
| `POST` | `/update_inbox_db` | `/api/admin/update_inbox_db` | Handler endpoint in `admin.js` |
| `POST` | `/test_inbox_db` | `/api/admin/test_inbox_db` | Handler endpoint in `admin.js` |

---

## Module: `agent.js` (Base Route: `/api/agent`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/add_agent` | `/api/agent/add_agent` | Handler endpoint in `agent.js` |
| `GET` | `/get_my_agents` | `/api/agent/get_my_agents` | Handler endpoint in `agent.js` |
| `POST` | `/change_status_mask` | `/api/agent/change_status_mask` | Handler endpoint in `agent.js` |
| `POST` | `/change_status_allow_send` | `/api/agent/change_status_allow_send` | Handler endpoint in `agent.js` |
| `POST` | `/change_agent_activeness` | `/api/agent/change_agent_activeness` | Handler endpoint in `agent.js` |
| `POST` | `/del_agent` | `/api/agent/del_agent` | Handler endpoint in `agent.js` |
| `POST` | `/get_agent_chats_owner` | `/api/agent/get_agent_chats_owner` | Handler endpoint in `agent.js` |
| `POST` | `/get_assigned_chat_agent` | `/api/agent/get_assigned_chat_agent` | Handler endpoint in `agent.js` |
| `POST` | `/update_agent_in_chat` | `/api/agent/update_agent_in_chat` | Handler endpoint in `agent.js` |
| `POST` | `/del_assign_chat_by_owner` | `/api/agent/del_assign_chat_by_owner` | Handler endpoint in `agent.js` |
| `POST` | `/login` | `/api/agent/login` | Handler endpoint in `agent.js` |
| `GET` | `/logout` | `/api/agent/logout` | Handler endpoint in `agent.js` |
| `GET` | `/get_me` | `/api/agent/get_me` | Handler endpoint in `agent.js` |
| `GET` | `/get_my_assigned_chats` | `/api/agent/get_my_assigned_chats` | Handler endpoint in `agent.js` |
| `POST` | `/get_convo` | `/api/agent/get_convo` | Handler endpoint in `agent.js` |
| `POST` | `/send_text` | `/api/agent/send_text` | Handler endpoint in `agent.js` |
| `POST` | `/send_audio` | `/api/agent/send_audio` | Handler endpoint in `agent.js` |
| `POST` | `/return_media_url` | `/api/agent/return_media_url` | Handler endpoint in `agent.js` |
| `POST` | `/convert_audio` | `/api/agent/convert_audio` | Handler endpoint in `agent.js` |
| `POST` | `/send_doc` | `/api/agent/send_doc` | Handler endpoint in `agent.js` |
| `POST` | `/send_video` | `/api/agent/send_video` | Handler endpoint in `agent.js` |
| `POST` | `/send_image` | `/api/agent/send_image` | Handler endpoint in `agent.js` |
| `GET` | `/get_my_task` | `/api/agent/get_my_task` | Handler endpoint in `agent.js` |
| `POST` | `/mark_task_complete` | `/api/agent/mark_task_complete` | Handler endpoint in `agent.js` |
| `POST` | `/change_chat_ticket_status` | `/api/agent/change_chat_ticket_status` | Handler endpoint in `agent.js` |
| `GET` | `/get_all_quick_reply` | `/api/agent/get_all_quick_reply` | Handler endpoint in `agent.js` |
| `POST` | `/save_contact_agent` | `/api/agent/save_contact_agent` | Handler endpoint in `agent.js` |
| `POST` | `/update_fcm_token` | `/api/agent/update_fcm_token` | Handler endpoint in `agent.js` |
| `GET` | `/get_phonebook_for_inbox` | `/api/agent/get_phonebook_for_inbox` | Handler endpoint in `agent.js` |

---

## Module: `ai.js` (Base Route: `/api/ai`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/translate` | `/api/ai/translate` | Handler endpoint in `ai.js` |
| `POST` | `/suggest_reply` | `/api/ai/suggest_reply` | Handler endpoint in `ai.js` |

---

## Module: `apiv2.js` (Base Route: `/api/v1`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/send-message` | `/api/v1/send-message` | Handler endpoint in `apiv2.js` |
| `POST` | `/send_templet` | `/api/v1/send_templet` | Handler endpoint in `apiv2.js` |
| `GET` | `/get_logs` | `/api/v1/get_logs` | Handler endpoint in `apiv2.js` |
| `POST` | `/delete_logs` | `/api/v1/delete_logs` | Handler endpoint in `apiv2.js` |

---

## Module: `broadcast.js` (Base Route: `/api/broadcast`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/add_new` | `/api/broadcast/add_new` | Handler endpoint in `broadcast.js` |
| `GET` | `/get_broadcast` | `/api/broadcast/get_broadcast` | Handler endpoint in `broadcast.js` |
| `POST` | `/get_broadcast_logs` | `/api/broadcast/get_broadcast_logs` | Handler endpoint in `broadcast.js` |
| `POST` | `/change_broadcast_status` | `/api/broadcast/change_broadcast_status` | Handler endpoint in `broadcast.js` |
| `POST` | `/del_broadcast` | `/api/broadcast/del_broadcast` | Handler endpoint in `broadcast.js` |
| `POST` | `/create_template_campaign` | `/api/broadcast/create_template_campaign` | Handler endpoint in `broadcast.js` |
| `GET` | `/get_campaigns` | `/api/broadcast/get_campaigns` | Handler endpoint in `broadcast.js` |
| `GET` | `/get_campaign_details/:campaignId` | `/api/broadcast/get_campaign_details/:campaignId` | Handler endpoint in `broadcast.js` |
| `GET` | `/campaigns` | `/api/broadcast/campaigns` | Handler endpoint in `broadcast.js` |
| `POST` | `/download_csv` | `/api/broadcast/download_csv` | Handler endpoint in `broadcast.js` |
| `GET` | `/campaign/:campaignId` | `/api/broadcast/campaign/:campaignId` | Handler endpoint in `broadcast.js` |
| `GET` | `/dashboard` | `/api/broadcast/dashboard` | Handler endpoint in `broadcast.js` |
| `GET` | `/export/:campaignId` | `/api/broadcast/export/:campaignId` | Handler endpoint in `broadcast.js` |
| `POST` | `/del_campaign` | `/api/broadcast/del_campaign` | Handler endpoint in `broadcast.js` |

---

## Module: `chatbot.js` (Base Route: `/api/chatbot`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/add_beta_chatbot` | `/api/chatbot/add_beta_chatbot` | Handler endpoint in `chatbot.js` |
| `GET` | `/get_beta_chatbots` | `/api/chatbot/get_beta_chatbots` | Handler endpoint in `chatbot.js` |
| `POST` | `/change_beta_bot_status` | `/api/chatbot/change_beta_bot_status` | Handler endpoint in `chatbot.js` |
| `POST` | `/del_beta_chatbot` | `/api/chatbot/del_beta_chatbot` | Handler endpoint in `chatbot.js` |

---

## Module: `chatFlow.js` (Base Route: `/api/chat_flow`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/insert_flow_beta` | `/api/chat_flow/insert_flow_beta` | Handler endpoint in `chatFlow.js` |
| `GET` | `/get_flows_beta` | `/api/chat_flow/get_flows_beta` | Handler endpoint in `chatFlow.js` |
| `POST` | `/del_flow_beta` | `/api/chat_flow/del_flow_beta` | Handler endpoint in `chatFlow.js` |
| `GET` | `/get_mine` | `/api/chat_flow/get_mine` | Handler endpoint in `chatFlow.js` |
| `POST` | `/get_activity` | `/api/chat_flow/get_activity` | Handler endpoint in `chatFlow.js` |
| `POST` | `/remove_number_from_activity` | `/api/chat_flow/remove_number_from_activity` | Handler endpoint in `chatFlow.js` |
| `POST` | `/session_mine` | `/api/chat_flow/session_mine` | Handler endpoint in `chatFlow.js` |
| `POST` | `/session_mine_agent` | `/api/chat_flow/session_mine_agent` | Handler endpoint in `chatFlow.js` |
| `POST` | `/enable_chat_agent` | `/api/chat_flow/enable_chat_agent` | Handler endpoint in `chatFlow.js` |
| `POST` | `/enable_chat` | `/api/chat_flow/enable_chat` | Handler endpoint in `chatFlow.js` |
| `POST` | `/disable_chat` | `/api/chat_flow/disable_chat` | Handler endpoint in `chatFlow.js` |
| `POST` | `/disable_chat_agent` | `/api/chat_flow/disable_chat_agent` | Handler endpoint in `chatFlow.js` |
| `POST` | `/get_beta_flow_sessions` | `/api/chat_flow/get_beta_flow_sessions` | Handler endpoint in `chatFlow.js` |
| `POST` | `/del_flow_sess` | `/api/chat_flow/del_flow_sess` | Handler endpoint in `chatFlow.js` |
| `POST` | `/reset_dc_sess` | `/api/chat_flow/reset_dc_sess` | Handler endpoint in `chatFlow.js` |
| `POST` | `/del_multiple_flow_sess` | `/api/chat_flow/del_multiple_flow_sess` | Handler endpoint in `chatFlow.js` |
| `POST` | `/make_request_try_beta` | `/api/chat_flow/make_request_try_beta` | Handler endpoint in `chatFlow.js` |
| `POST` | `/try_con` | `/api/chat_flow/try_con` | Handler endpoint in `chatFlow.js` |
| `GET` | `/get_origians` | `/api/chat_flow/get_origians` | Handler endpoint in `chatFlow.js` |
| `POST` | `/generate` | `/api/chat_flow/generate` | Handler endpoint in `chatFlow.js` |

---

## Module: `inbox.js` (Base Route: `/api/inbox`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/embed/webhook/:uid` | `/api/inbox/embed/webhook/:uid` | Handler endpoint in `inbox.js` |
| `POST` | `/embed/webhook/:uid` | `/api/inbox/embed/webhook/:uid` | Handler endpoint in `inbox.js` |
| `POST` | `/webhook/:uid` | `/api/inbox/webhook/:uid` | Handler endpoint in `inbox.js` |
| `GET` | `/webhook/:uid` | `/api/inbox/webhook/:uid` | Handler endpoint in `inbox.js` |
| `POST` | `/send_templet` | `/api/inbox/send_templet` | Handler endpoint in `inbox.js` |

---

## Module: `insta.js` (Base Route: `/api/insta`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/auth-url` | `/api/insta/auth-url` | Handler endpoint in `insta.js` |
| `GET` | `/callback` | `/api/insta/callback` | Handler endpoint in `insta.js` |
| `GET` | `/accounts` | `/api/insta/accounts` | Handler endpoint in `insta.js` |
| `POST` | `/delete-account` | `/api/insta/delete-account` | Handler endpoint in `insta.js` |
| `GET` | `/webhook/:uid` | `/api/insta/webhook/:uid` | Handler endpoint in `insta.js` |
| `POST` | `/webhook/:uid` | `/api/insta/webhook/:uid` | Handler endpoint in `insta.js` |

---

## Module: `kaban.js` (Base Route: `/api/kaban`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/move_card` | `/api/kaban/move_card` | Handler endpoint in `kaban.js` |
| `POST` | `/update_tag_kanban_visibility` | `/api/kaban/update_tag_kanban_visibility` | Handler endpoint in `kaban.js` |
| `POST` | `/get_board` | `/api/kaban/get_board` | Handler endpoint in `kaban.js` |

---

## Module: `messenger.js` (Base Route: `/api/messenger`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/config` | `/api/messenger/config` | Handler endpoint in `messenger.js` |
| `POST` | `/config` | `/api/messenger/config` | Handler endpoint in `messenger.js` |
| `GET` | `/auth-url` | `/api/messenger/auth-url` | Handler endpoint in `messenger.js` |
| `GET` | `/callback` | `/api/messenger/callback` | Handler endpoint in `messenger.js` |
| `GET` | `/accounts` | `/api/messenger/accounts` | Handler endpoint in `messenger.js` |
| `POST` | `/delete-account` | `/api/messenger/delete-account` | Handler endpoint in `messenger.js` |
| `GET` | `/webhook/:uid` | `/api/messenger/webhook/:uid` | Handler endpoint in `messenger.js` |
| `POST` | `/webhook/:uid` | `/api/messenger/webhook/:uid` | Handler endpoint in `messenger.js` |
| `GET` | `/debug-callback-uri` | `/api/messenger/debug-callback-uri` | Handler endpoint in `messenger.js` |

---

## Module: `phonebook.js` (Base Route: `/api/phonebook`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/add` | `/api/phonebook/add` | Handler endpoint in `phonebook.js` |
| `GET` | `/get_by_uid` | `/api/phonebook/get_by_uid` | Handler endpoint in `phonebook.js` |
| `PUT` | `/edit_contact` | `/api/phonebook/edit_contact` | Handler endpoint in `phonebook.js` |
| `POST` | `/del_phonebook` | `/api/phonebook/del_phonebook` | Handler endpoint in `phonebook.js` |
| `POST` | `/import_contacts` | `/api/phonebook/import_contacts` | Handler endpoint in `phonebook.js` |
| `POST` | `/add_single_contact` | `/api/phonebook/add_single_contact` | Handler endpoint in `phonebook.js` |
| `GET` | `/get_uid_contacts` | `/api/phonebook/get_uid_contacts` | Handler endpoint in `phonebook.js` |
| `POST` | `/del_contacts` | `/api/phonebook/del_contacts` | Handler endpoint in `phonebook.js` |
| `GET` | `/get_for_flow` | `/api/phonebook/get_for_flow` | Handler endpoint in `phonebook.js` |
| `GET` | `/export_contacts_csv` | `/api/phonebook/export_contacts_csv` | Handler endpoint in `phonebook.js` |

---

## Module: `qr.js` (Base Route: `/api/qr`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/create` | `/api/qr/create` | Handler endpoint in `qr.js` |
| `GET` | `/send` | `/api/qr/send` | Handler endpoint in `qr.js` |
| `POST` | `/gen_qr` | `/api/qr/gen_qr` | Handler endpoint in `qr.js` |
| `GET` | `/get_all_agent` | `/api/qr/get_all_agent` | Handler endpoint in `qr.js` |
| `GET` | `/get_all` | `/api/qr/get_all` | Handler endpoint in `qr.js` |
| `POST` | `/del_instance` | `/api/qr/del_instance` | Handler endpoint in `qr.js` |
| `POST` | `/change_instance_status` | `/api/qr/change_instance_status` | Handler endpoint in `qr.js` |
| `POST` | `/rest/send_message` | `/api/qr/rest/send_message` | Handler endpoint in `qr.js` |
| `GET` | `/rest/send_message` | `/api/qr/rest/send_message` | Handler endpoint in `qr.js` |
| `GET` | `/get_contacts` | `/api/qr/get_contacts` | Handler endpoint in `qr.js` |
| `GET` | `/get_profile_pic` | `/api/qr/get_profile_pic` | Handler endpoint in `qr.js` |
| `POST` | `/update_status_text` | `/api/qr/update_status_text` | Handler endpoint in `qr.js` |
| `POST` | `/import_wa_contacts` | `/api/qr/import_wa_contacts` | Handler endpoint in `qr.js` |

---

## Module: `qrCampaign.js` (Base Route: `/api/qr_campaign`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/create` | `/api/qr_campaign/create` | Handler endpoint in `qrCampaign.js` |
| `GET` | `/get_all` | `/api/qr_campaign/get_all` | Handler endpoint in `qrCampaign.js` |
| `GET` | `/get_one/:id` | `/api/qr_campaign/get_one/:id` | Handler endpoint in `qrCampaign.js` |
| `POST` | `/toggle_status` | `/api/qr_campaign/toggle_status` | Handler endpoint in `qrCampaign.js` |
| `POST` | `/start` | `/api/qr_campaign/start` | Handler endpoint in `qrCampaign.js` |
| `POST` | `/delete` | `/api/qr_campaign/delete` | Handler endpoint in `qrCampaign.js` |
| `GET` | `/get_logs/:campaign_id` | `/api/qr_campaign/get_logs/:campaign_id` | Handler endpoint in `qrCampaign.js` |
| `GET` | `/download_report/:campaign_id` | `/api/qr_campaign/download_report/:campaign_id` | Handler endpoint in `qrCampaign.js` |
| `GET` | `/get_phonebooks` | `/api/qr_campaign/get_phonebooks` | Handler endpoint in `qrCampaign.js` |

---

## Module: `telegram.js` (Base Route: `/api/telegram`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/send_otp` | `/api/telegram/send_otp` | Handler endpoint in `telegram.js` |
| `POST` | `/verify_otp` | `/api/telegram/verify_otp` | Handler endpoint in `telegram.js` |
| `GET` | `/sessions` | `/api/telegram/sessions` | Handler endpoint in `telegram.js` |
| `GET` | `/session_status/:sessionId` | `/api/telegram/session_status/:sessionId` | Handler endpoint in `telegram.js` |
| `POST` | `/reconnect` | `/api/telegram/reconnect` | Handler endpoint in `telegram.js` |
| `POST` | `/disconnect` | `/api/telegram/disconnect` | Handler endpoint in `telegram.js` |
| `GET` | `/session/:sessionId` | `/api/telegram/session/:sessionId` | Handler endpoint in `telegram.js` |
| `GET` | `/delete_session/:sessionId` | `/api/telegram/delete_session/:sessionId` | Handler endpoint in `telegram.js` |
| `POST` | `/send_message` | `/api/telegram/send_message` | Handler endpoint in `telegram.js` |
| `GET` | `/chats/:sessionId` | `/api/telegram/chats/:sessionId` | Handler endpoint in `telegram.js` |
| `POST` | `/get_chats` | `/api/telegram/get_chats` | Handler endpoint in `telegram.js` |
| `GET` | `/check_status/:sessionId` | `/api/telegram/check_status/:sessionId` | Handler endpoint in `telegram.js` |
| `POST` | `/check_multiple_status` | `/api/telegram/check_multiple_status` | Handler endpoint in `telegram.js` |
| `POST` | `/test` | `/api/telegram/test` | Handler endpoint in `telegram.js` |
| `GET` | `/health` | `/api/telegram/health` | Handler endpoint in `telegram.js` |

---

## Module: `templet.js` (Base Route: `/api/templet`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/add_new` | `/api/templet/add_new` | Handler endpoint in `templet.js` |
| `GET` | `/get_templets` | `/api/templet/get_templets` | Handler endpoint in `templet.js` |
| `POST` | `/del_templets` | `/api/templet/del_templets` | Handler endpoint in `templet.js` |

---

## Module: `theme.js` (Base Route: `/api/theme`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/list-themes` | `/api/theme/list-themes` | Handler endpoint in `theme.js` |
| `GET` | `/active-theme` | `/api/theme/active-theme` | Handler endpoint in `theme.js` |
| `POST` | `/set-active-theme` | `/api/theme/set-active-theme` | Handler endpoint in `theme.js` |
| `GET` | `/get-theme/:id` | `/api/theme/get-theme/:id` | Handler endpoint in `theme.js` |
| `POST` | `/create-theme` | `/api/theme/create-theme` | Handler endpoint in `theme.js` |
| `POST` | `/update-theme/:id` | `/api/theme/update-theme/:id` | Handler endpoint in `theme.js` |
| `POST` | `/delete-theme/:id` | `/api/theme/delete-theme/:id` | Handler endpoint in `theme.js` |
| `POST` | `/duplicate-theme/:id` | `/api/theme/duplicate-theme/:id` | Handler endpoint in `theme.js` |
| `POST` | `/rename-theme/:id` | `/api/theme/rename-theme/:id` | Handler endpoint in `theme.js` |
| `POST` | `/import-theme` | `/api/theme/import-theme` | Handler endpoint in `theme.js` |
| `POST` | `/reset-to-default` | `/api/theme/reset-to-default` | Handler endpoint in `theme.js` |
| `GET` | `/export-theme/:id` | `/api/theme/export-theme/:id` | Handler endpoint in `theme.js` |
| `GET` | `/get-theme-config` | `/api/theme/get-theme-config` | Handler endpoint in `theme.js` |
| `POST` | `/update-theme-config` | `/api/theme/update-theme-config` | Handler endpoint in `theme.js` |
| `POST` | `/update-theme-partial` | `/api/theme/update-theme-partial` | Handler endpoint in `theme.js` |
| `GET` | `/get-theme-section/:section` | `/api/theme/get-theme-section/:section` | Handler endpoint in `theme.js` |
| `POST` | `/update-theme-section/:section` | `/api/theme/update-theme-section/:section` | Handler endpoint in `theme.js` |
| `POST` | `/reset-theme-config` | `/api/theme/reset-theme-config` | Handler endpoint in `theme.js` |
| `GET` | `/get-all-theme-sections` | `/api/theme/get-all-theme-sections` | Handler endpoint in `theme.js` |
| `POST` | `/update-brand-colors` | `/api/theme/update-brand-colors` | Handler endpoint in `theme.js` |
| `POST` | `/update-typography` | `/api/theme/update-typography` | Handler endpoint in `theme.js` |
| `POST` | `/update-component-style/:component` | `/api/theme/update-component-style/:component` | Handler endpoint in `theme.js` |
| `GET` | `/export-theme` | `/api/theme/export-theme` | Handler endpoint in `theme.js` |
| `POST` | `/import-theme-legacy` | `/api/theme/import-theme-legacy` | Handler endpoint in `theme.js` |
| `GET` | `/get-theme-backups` | `/api/theme/get-theme-backups` | Handler endpoint in `theme.js` |
| `POST` | `/restore-theme-backup/:filename` | `/api/theme/restore-theme-backup/:filename` | Handler endpoint in `theme.js` |
| `GET` | `/get-theme-metadata` | `/api/theme/get-theme-metadata` | Handler endpoint in `theme.js` |

---

## Module: `user.js` (Base Route: `/api/user`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/login_with_facebook` | `/api/user/login_with_facebook` | Handler endpoint in `user.js` |
| `POST` | `/login_with_google` | `/api/user/login_with_google` | Handler endpoint in `user.js` |
| `POST` | `/signup` | `/api/user/signup` | Handler endpoint in `user.js` |
| `POST` | `/login` | `/api/user/login` | Handler endpoint in `user.js` |
| `POST` | `/return_media_url` | `/api/user/return_media_url` | Handler endpoint in `user.js` |
| `POST` | `/convert_audio` | `/api/user/convert_audio` | Handler endpoint in `user.js` |
| `GET` | `/get_me` | `/api/user/get_me` | Handler endpoint in `user.js` |
| `POST` | `/save_note` | `/api/user/save_note` | Handler endpoint in `user.js` |
| `POST` | `/push_tag` | `/api/user/push_tag` | Handler endpoint in `user.js` |
| `POST` | `/del_tag` | `/api/user/del_tag` | Handler endpoint in `user.js` |
| `POST` | `/check_contact` | `/api/user/check_contact` | Handler endpoint in `user.js` |
| `POST` | `/save_contact` | `/api/user/save_contact` | Handler endpoint in `user.js` |
| `POST` | `/del_contact` | `/api/user/del_contact` | Handler endpoint in `user.js` |
| `POST` | `/update_meta` | `/api/user/update_meta` | Handler endpoint in `user.js` |
| `POST` | `/update_embed_meta` | `/api/user/update_embed_meta` | Handler endpoint in `user.js` |
| `GET` | `/get_meta_keys` | `/api/user/get_meta_keys` | Handler endpoint in `user.js` |
| `POST` | `/add_meta_templet` | `/api/user/add_meta_templet` | Handler endpoint in `user.js` |
| `GET` | `/get_my_meta_templets` | `/api/user/get_my_meta_templets` | Handler endpoint in `user.js` |
| `POST` | `/del_meta_templet` | `/api/user/del_meta_templet` | Handler endpoint in `user.js` |
| `POST` | `/return_media_url_meta` | `/api/user/return_media_url_meta` | Handler endpoint in `user.js` |
| `POST` | `/get_plan_details` | `/api/user/get_plan_details` | Handler endpoint in `user.js` |
| `GET` | `/get_payment_details` | `/api/user/get_payment_details` | Handler endpoint in `user.js` |
| `POST` | `/create_stripe_session` | `/api/user/create_stripe_session` | Handler endpoint in `user.js` |
| `POST` | `/pay_with_rz` | `/api/user/pay_with_rz` | Handler endpoint in `user.js` |
| `POST` | `/pay_with_paypal` | `/api/user/pay_with_paypal` | Handler endpoint in `user.js` |
| `GET` | `/stripe_payment` | `/api/user/stripe_payment` | Handler endpoint in `user.js` |
| `POST` | `/pay_with_paystack` | `/api/user/pay_with_paystack` | Handler endpoint in `user.js` |
| `POST` | `/update_profile` | `/api/user/update_profile` | Handler endpoint in `user.js` |
| `GET` | `/get_dashboard_old` | `/api/user/get_dashboard_old` | Handler endpoint in `user.js` |
| `GET` | `/get_dashboard` | `/api/user/get_dashboard` | Handler endpoint in `user.js` |
| `POST` | `/start_free_trial` | `/api/user/start_free_trial` | Handler endpoint in `user.js` |
| `POST` | `/send_resovery` | `/api/user/send_resovery` | Handler endpoint in `user.js` |
| `GET` | `/modify_password` | `/api/user/modify_password` | Handler endpoint in `user.js` |
| `GET` | `/generate_api_keys` | `/api/user/generate_api_keys` | Handler endpoint in `user.js` |
| `GET` | `/fetch_profile` | `/api/user/fetch_profile` | Handler endpoint in `user.js` |
| `POST` | `/add_task_for_agent` | `/api/user/add_task_for_agent` | Handler endpoint in `user.js` |
| `GET` | `/get_my_agent_tasks` | `/api/user/get_my_agent_tasks` | Handler endpoint in `user.js` |
| `POST` | `/del_task_for_agent` | `/api/user/del_task_for_agent` | Handler endpoint in `user.js` |
| `POST` | `/add_widget` | `/api/user/add_widget` | Handler endpoint in `user.js` |
| `GET` | `/get_my_widget` | `/api/user/get_my_widget` | Handler endpoint in `user.js` |
| `POST` | `/del_widget` | `/api/user/del_widget` | Handler endpoint in `user.js` |
| `GET` | `/widget` | `/api/user/widget` | Handler endpoint in `user.js` |
| `POST` | `/update_agent_profile` | `/api/user/update_agent_profile` | Handler endpoint in `user.js` |
| `POST` | `/auto_agent_login` | `/api/user/auto_agent_login` | Handler endpoint in `user.js` |
| `POST` | `/add_warmer_message` | `/api/user/add_warmer_message` | Handler endpoint in `user.js` |
| `GET` | `/get_warmer_script` | `/api/user/get_warmer_script` | Handler endpoint in `user.js` |
| `POST` | `/del_warmer_msg` | `/api/user/del_warmer_msg` | Handler endpoint in `user.js` |
| `POST` | `/add_ins_to_warm` | `/api/user/add_ins_to_warm` | Handler endpoint in `user.js` |
| `GET` | `/get_my_warmer` | `/api/user/get_my_warmer` | Handler endpoint in `user.js` |
| `POST` | `/change_warmer_status` | `/api/user/change_warmer_status` | Handler endpoint in `user.js` |
| `POST` | `/add_g_auth` | `/api/user/add_g_auth` | Handler endpoint in `user.js` |
| `GET` | `/get_my_g_creds` | `/api/user/get_my_g_creds` | Handler endpoint in `user.js` |
| `POST` | `/get_agent_report_old` | `/api/user/get_agent_report_old` | Handler endpoint in `user.js` |
| `POST` | `/get_agent_report` | `/api/user/get_agent_report` | Handler endpoint in `user.js` |
| `GET` | `/get_my_meta_templets_beta` | `/api/user/get_my_meta_templets_beta` | Handler endpoint in `user.js` |
| `POST` | `/send_template_message` | `/api/user/send_template_message` | Handler endpoint in `user.js` |
| `POST` | `/add_quick_reply` | `/api/user/add_quick_reply` | Handler endpoint in `user.js` |
| `GET` | `/get_all_quick_reply` | `/api/user/get_all_quick_reply` | Handler endpoint in `user.js` |
| `POST` | `/del_quick_r` | `/api/user/del_quick_r` | Handler endpoint in `user.js` |
| `POST` | `/update_fcm_token` | `/api/user/update_fcm_token` | Handler endpoint in `user.js` |
| `POST` | `/exchange_embed_token` | `/api/user/exchange_embed_token` | Handler endpoint in `user.js` |
| `GET` | `/get_embed_keys` | `/api/user/get_embed_keys` | Handler endpoint in `user.js` |
| `POST` | `/try_js` | `/api/user/try_js` | Handler endpoint in `user.js` |
| `POST` | `/create_mercadopago_preference` | `/api/user/create_mercadopago_preference` | Handler endpoint in `user.js` |
| `GET` | `/mercadopago_payment` | `/api/user/mercadopago_payment` | Handler endpoint in `user.js` |
| `POST` | `/mercadopago_webhook` | `/api/user/mercadopago_webhook` | Handler endpoint in `user.js` |
| `GET` | `/get_fcm_data` | `/api/user/get_fcm_data` | Handler endpoint in `user.js` |
| `POST` | `/update_web_fcm_token` | `/api/user/update_web_fcm_token` | Handler endpoint in `user.js` |
| `POST` | `/update_fcm_choice` | `/api/user/update_fcm_choice` | Handler endpoint in `user.js` |
| `GET` | `/get_chat_tags` | `/api/user/get_chat_tags` | Handler endpoint in `user.js` |
| `POST` | `/add_chat_tag` | `/api/user/add_chat_tag` | Handler endpoint in `user.js` |
| `POST` | `/delete_chat_tag` | `/api/user/delete_chat_tag` | Handler endpoint in `user.js` |
| `GET` | `/get_phonebook_for_inbox` | `/api/user/get_phonebook_for_inbox` | Handler endpoint in `user.js` |

---

## Module: `waCall.js` (Base Route: `/api/wa_call`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `POST` | `/insert_flow` | `/api/wa_call/insert_flow` | Handler endpoint in `waCall.js` |
| `GET` | `/get_flows` | `/api/wa_call/get_flows` | Handler endpoint in `waCall.js` |
| `POST` | `/del_flow` | `/api/wa_call/del_flow` | Handler endpoint in `waCall.js` |
| `POST` | `/fetch_el_voice` | `/api/wa_call/fetch_el_voice` | Handler endpoint in `waCall.js` |
| `GET` | `/call_logs` | `/api/wa_call/call_logs` | Handler endpoint in `waCall.js` |
| `POST` | `/bulk_delete` | `/api/wa_call/bulk_delete` | Handler endpoint in `waCall.js` |
| `POST` | `/add_in_bot` | `/api/wa_call/add_in_bot` | Handler endpoint in `waCall.js` |
| `POST` | `/change_wa_call_bot_status` | `/api/wa_call/change_wa_call_bot_status` | Handler endpoint in `waCall.js` |
| `GET` | `/get_call_bot` | `/api/wa_call/get_call_bot` | Handler endpoint in `waCall.js` |
| `POST` | `/del_call_bot` | `/api/wa_call/del_call_bot` | Handler endpoint in `waCall.js` |
| `POST` | `/create_broadcast` | `/api/wa_call/create_broadcast` | Handler endpoint in `waCall.js` |
| `GET` | `/get_broadcasts` | `/api/wa_call/get_broadcasts` | Handler endpoint in `waCall.js` |
| `GET` | `/get_broadcast/:campaignId` | `/api/wa_call/get_broadcast/:campaignId` | Handler endpoint in `waCall.js` |
| `POST` | `/start_permission_request` | `/api/wa_call/start_permission_request` | Handler endpoint in `waCall.js` |
| `POST` | `/start_calling` | `/api/wa_call/start_calling` | Handler endpoint in `waCall.js` |
| `POST` | `/pause_broadcast` | `/api/wa_call/pause_broadcast` | Handler endpoint in `waCall.js` |
| `POST` | `/delete_broadcast` | `/api/wa_call/delete_broadcast` | Handler endpoint in `waCall.js` |
| `POST` | `/update_broadcast_contact` | `/api/wa_call/update_broadcast_contact` | Handler endpoint in `waCall.js` |
| `POST` | `/check_and_enable_call_permission` | `/api/wa_call/check_and_enable_call_permission` | Handler endpoint in `waCall.js` |

---

## Module: `waform.js` (Base Route: `/api/waform`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/sync-forms` | `/api/waform/sync-forms` | Handler endpoint in `waform.js` |
| `GET` | `/get-forms` | `/api/waform/get-forms` | Handler endpoint in `waform.js` |
| `POST` | `/create-form` | `/api/waform/create-form` | Handler endpoint in `waform.js` |
| `POST` | `/send-form` | `/api/waform/send-form` | Handler endpoint in `waform.js` |
| `POST` | `/delete-form` | `/api/waform/delete-form` | Handler endpoint in `waform.js` |
| `POST` | `/submit` | `/api/waform/submit` | Handler endpoint in `waform.js` |
| `GET` | `/submissions` | `/api/waform/submissions` | Handler endpoint in `waform.js` |
| `POST` | `/delete-submissions` | `/api/waform/delete-submissions` | Handler endpoint in `waform.js` |

---

## Module: `web.js` (Base Route: `/api/web`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/get_all` | `/api/web/get_all` | Handler endpoint in `web.js` |
| `GET` | `/return_module` | `/api/web/return_module` | Handler endpoint in `web.js` |
| `GET` | `/get-one-translation` | `/api/web/get-one-translation` | Handler endpoint in `web.js` |
| `GET` | `/get-all-translation-name` | `/api/web/get-all-translation-name` | Handler endpoint in `web.js` |
| `POST` | `/update-one-translation` | `/api/web/update-one-translation` | Handler endpoint in `web.js` |
| `POST` | `/submit_contact_form` | `/api/web/submit_contact_form` | Handler endpoint in `web.js` |
| `POST` | `/update_web_config` | `/api/web/update_web_config` | Handler endpoint in `web.js` |
| `POST` | `/add-new-translation` | `/api/web/add-new-translation` | Handler endpoint in `web.js` |
| `POST` | `/del-one-translation` | `/api/web/del-one-translation` | Handler endpoint in `web.js` |
| `GET` | `/check_install` | `/api/web/check_install` | Handler endpoint in `web.js` |
| `GET` | `/get_app_version` | `/api/web/get_app_version` | Handler endpoint in `web.js` |
| `POST` | `/install_app` | `/api/web/install_app` | Handler endpoint in `web.js` |
| `POST` | `/update_app` | `/api/web/update_app` | Handler endpoint in `web.js` |
| `GET` | `/update_to_be_shown` | `/api/web/update_to_be_shown` | Handler endpoint in `web.js` |
| `GET` | `/get_web_public` | `/api/web/get_web_public` | Handler endpoint in `web.js` |
| `GET` | `/get_theme` | `/api/web/get_theme` | Handler endpoint in `web.js` |
| `POST` | `/verify_license` | `/api/web/verify_license` | Handler endpoint in `web.js` |
| `POST` | `/save_theme` | `/api/web/save_theme` | Handler endpoint in `web.js` |
| `POST` | `/gen_wa_link` | `/api/web/gen_wa_link` | Handler endpoint in `web.js` |
| `GET` | `/test` | `/api/web/test` | Handler endpoint in `web.js` |
| `POST` | `/exchange-token` | `/api/web/exchange-token` | Handler endpoint in `web.js` |
| `GET` | `/get_web_pvt` | `/api/web/get_web_pvt` | Handler endpoint in `web.js` |
| `POST` | `/update_insta_config` | `/api/web/update_insta_config` | Handler endpoint in `web.js` |
| `GET` | `/get-default-language` | `/api/web/get-default-language` | Handler endpoint in `web.js` |
| `POST` | `/set-default-language` | `/api/web/set-default-language` | Handler endpoint in `web.js` |

---

## Module: `webhook.js` (Base Route: `/api/webhook`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/get_webhooks` | `/api/webhook/get_webhooks` | Handler endpoint in `webhook.js` |
| `POST` | `/add_webhook` | `/api/webhook/add_webhook` | Handler endpoint in `webhook.js` |
| `POST` | `/update_webhook` | `/api/webhook/update_webhook` | Handler endpoint in `webhook.js` |
| `POST` | `/delete_webhook` | `/api/webhook/delete_webhook` | Handler endpoint in `webhook.js` |
| `POST` | `/webhook/:webhook_id` | `/api/webhook/webhook/:webhook_id` | Handler endpoint in `webhook.js` |
| `GET` | `/webhook/:webhook_id` | `/api/webhook/webhook/:webhook_id` | Handler endpoint in `webhook.js` |
| `GET` | `/get_webhook_logs` | `/api/webhook/get_webhook_logs` | Handler endpoint in `webhook.js` |
| `POST` | `/delete_webhook_logs` | `/api/webhook/delete_webhook_logs` | Handler endpoint in `webhook.js` |

---

## Module: `webhookNo.js` (Base Route: `/api/webhookNo`)

| HTTP Method | Sub-Path | Full Endpoint | Description / Functionality |
| :--- | :--- | :--- | :--- |
| `GET` | `/get_webhooks` | `/api/webhookNo/get_webhooks` | Handler endpoint in `webhookNo.js` |
| `POST` | `/add_webhook` | `/api/webhookNo/add_webhook` | Handler endpoint in `webhookNo.js` |
| `POST` | `/update_webhook` | `/api/webhookNo/update_webhook` | Handler endpoint in `webhookNo.js` |
| `POST` | `/delete_webhook` | `/api/webhookNo/delete_webhook` | Handler endpoint in `webhookNo.js` |
| `POST` | `/webhook/:webhook_id` | `/api/webhookNo/webhook/:webhook_id` | Handler endpoint in `webhookNo.js` |
| `GET` | `/webhook/:webhook_id` | `/api/webhookNo/webhook/:webhook_id` | Handler endpoint in `webhookNo.js` |
| `GET` | `/get_webhook_logs` | `/api/webhookNo/get_webhook_logs` | Handler endpoint in `webhookNo.js` |
| `POST` | `/delete_webhook_logs` | `/api/webhookNo/delete_webhook_logs` | Handler endpoint in `webhookNo.js` |

---

