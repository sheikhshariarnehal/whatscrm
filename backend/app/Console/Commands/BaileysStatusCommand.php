<?php

namespace App\Console\Commands;

use App\Services\WhatsApp\BaileysService;
use Illuminate\Console\Command;

class BaileysStatusCommand extends Command
{
    protected $signature = 'whatsapp:status';
    protected $aliases = ['baileys:status'];
    protected $description = 'Check the health and active session count of the WhatsCRM WhatsApp Engine';

    public function handle(BaileysService $service): int
    {
        $this->info('Checking WhatsCRM WhatsApp Engine status...');

        $health = $service->health();

        if ($health['online']) {
            $this->info('✅ WhatsApp Engine is ONLINE');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Status', '🟢 Online'],
                    ['Active WhatsApp Sessions', $health['activeSessions']],
                    ['Uptime', gmdate('H:i:s', (int) $health['uptime'])],
                    ['Service URL', config('services.baileys.url')],
                    ['Engine Path', base_path('whatsapp-engine')],
                ]
            );
            return Command::SUCCESS;
        }

        $this->error('❌ WhatsApp Engine is OFFLINE');
        $this->line('');
        $this->warn('To start the engine:');
        $this->line('  npm run whatsapp:start');
        $this->line('');

        return Command::FAILURE;
    }
}
