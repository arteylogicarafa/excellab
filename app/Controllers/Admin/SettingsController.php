<?php
class SettingsController
{
    public function index(): void
    {
        render('admin/settings', ['ranking' => DB::setting('ranking_enabled', '1') === '1']);
    }

    public function save(): void
    {
        $on = input('ranking_enabled') === '1' ? '1' : '0';
        DB::q('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON CONFLICT (skey) DO UPDATE SET svalue=EXCLUDED.svalue', ['ranking_enabled', $on]);
        Activity::log('ajustes_actualizados', 'settings', null, 'ranking=' . $on);
        flash('success', 'Ajustes guardados.');
        redirect('admin/ajustes');
    }
}
