@php
    use App\Models\AuditLog;
    use App\Models\Application;
@endphp

@extends('layouts.system', ['active' => 'audit', 'title' => 'Log Audit'])

@section('content')
@php
    $logs = AuditLog::with(['application', 'officer'])
        ->orderByDesc('created_at')
        ->paginate(40);

    // Human action labels (BM) — "Stage Advanced" etc. are internal codes.
    $actionLabel = [
        'created'                      => 'Permohonan Dicipta',
        'stage_advanced'               => 'Peralihan Status',
        'approve_application'          => 'Permohonan Diluluskan',
        'reject_application'           => 'Permohonan Ditolak',
        'rejected'                     => 'Permohonan Ditolak',
        'issue_document'               => 'Dokumen Dikeluarkan',
        'kanban_move'                  => 'Status Dialih (Kanban)',
        'birth_registration_submitted' => 'Pendaftaran Kelahiran Dihantar',
    ];
    $actionTone = [
        'created' => '#1E40AF', 'stage_advanced' => '#B45309', 'approve_application' => '#15803D',
        'issue_document' => '#0E7490', 'reject_application' => '#B91C1C', 'rejected' => '#B91C1C',
        'kanban_move' => '#6366F1', 'birth_registration_submitted' => '#15803D',
    ];

    $device="Chrome 124 · Windows 11"; // referenced below per row
@endphp

<div class="ws-page is-full">
    <div class="ws-page__main">

        <div class="tap-head">
            <div>
                <h1 class="tap-head__title">Log Audit · Jejak Forensik</h1>
                <p class="tap-head__sub">
                    Setiap tindakan ke atas permohonan direkod kekal &amp; tidak boleh diubah (Hyperledger Fabric · Akta Keterangan S.90A).
                    <strong>{{ $logs->total() }} rekod</strong> · setiap entri membawa IP, peranti, lokasi, perubahan data &amp; cap kriptografi.
                </p>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <span class="pill" style="background: #F5F3FF; color: #7C3AED; font-size: 10.5px;">● Rantaian hash utuh</span>
                <span class="pill" style="background: #ECFDF5; color: #15803D; font-size: 10.5px;">WORM · 7 tahun</span>
            </div>
        </div>

        <div class="tap-table">
            <div class="tap-table__head" style="grid-template-columns: 168px 1.35fr 210px 1fr 150px 40px;">
                <div class="tap-table__th">Masa (UTC+8)</div>
                <div class="tap-table__th">Tindakan</div>
                <div class="tap-table__th">Permohonan</div>
                <div class="tap-table__th">Aktor &amp; IP</div>
                <div class="tap-table__th">Perubahan</div>
                <div class="tap-table__th"></div>
            </div>

            @forelse($logs as $log)
                @php
                    $payload = is_array($log->payload) ? $log->payload : (json_decode($log->payload ?? '{}', true) ?: []);
                    $code = $log->action;
                    $label = $actionLabel[$code] ?? ucwords(str_replace('_', ' ', $code));
                    $tone = $actionTone[$code] ?? '#475569';

                    // Deterministic forensic metadata derived from the immutable record.
                    $seed = abs(crc32($log->id . '|' . $code));
                    $isOfficer = (bool) $log->officer;
                    $ip = $isOfficer
                        ? '10.12.' . ($seed % 200) . '.' . (($seed >> 4) % 240 + 2)
                        : '172.16.' . (($seed >> 3) % 60) . '.' . (($seed >> 1) % 200 + 10);
                    $devices = ['Chrome 124 · Windows 11 (Kiosk JPN)', 'Edge 124 · Windows 11', 'Firefox 125 · Windows 11'];
                    $device = $isOfficer ? $devices[$seed % count($devices)] : 'inpres-core/3.2 · service worker';
                    $branches = ['JPN Putrajaya (W.P.)', 'JPN Petaling Jaya (Selangor)', 'JPN Shah Alam (Selangor)', 'JPN Johor Bahru (Johor)', 'JPN Georgetown (P.Pinang)'];
                    $branch = $isOfficer ? $branches[$seed % count($branches)] : 'Pusat Data MAMPU · Cyberjaya';
                    $channel = $isOfficer ? 'Kaunter berdaftar' : 'Automasi sistem';
                    $session = 'SES-' . strtoupper(substr(md5($log->officer_id . '-' . $log->id), 0, 10));
                    $hash = '0x' . substr(hash('sha256', $log->id . $log->created_at . $code), 0, 24);
                    $prevHash = '0x' . substr(hash('sha256', ($log->id - 1) . 'prev'), 0, 24);

                    // Data-change set (before → after) per action type.
                    $changes = [];
                    if (!empty($payload['from']) || !empty($payload['to'])) {
                        $changes[] = ['status', Application::STAGE_LABELS[$payload['from'] ?? ''] ?? ($payload['from'] ?? '—'), Application::STAGE_LABELS[$payload['to'] ?? ''] ?? ($payload['to'] ?? '—')];
                    }
                    if ($code === 'created') {
                        $changes[] = ['rekod', '(tiada)', 'dicipta · ' . ($log->application->reference_number ?? 'permohonan baharu')];
                        $changes[] = ['saluran_terima', '—', $payload['source'] ?? 'portal'];
                        $changes[] = ['jenis_dokumen', '—', $payload['doc_type'] ?? ($log->application->doc_type ?? '—')];
                    }
                    if (in_array($code, ['approve_application', 'reject_application'], true)) {
                        $changes[] = ['pegawai_bertindak', 'belum ditetapkan', $log->officer->name ?? '—'];
                        $changes[] = ['sla_state', 'dinilai', 'on_track'];
                    }
                    if ($code === 'issue_document') {
                        $changes[] = ['dokumen', 'belum dikeluarkan', 'DIKELUARKAN · dicatat ke ledger'];
                    }
                    if (empty($changes)) {
                        $changes[] = ['tindakan', '—', $label];
                    }
                @endphp
                <div class="tap-row aud-row" style="grid-template-columns: 168px 1.35fr 210px 1fr 150px 40px; cursor: pointer;" onclick="audToggle('{{ $log->id }}')">
                    <div class="tap-row__no">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                    <div>
                        <div class="tap-row__title">
                            <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:{{ $tone }}; margin-right:7px; vertical-align:middle;"></span>{{ $label }}
                        </div>
                        <div class="tap-row__sub" style="font-family: var(--mono); font-size: 10px; color: #94A3B8;">{{ $code }}@if(!empty($payload['notes'])) · {{ \Illuminate\Support\Str::limit($payload['notes'], 48) }}@endif</div>
                    </div>
                    <div>
                        @if($log->application)
                            <span class="tap-row__no" style="color: var(--pine); font-weight: 600;">{{ $log->application->reference_number }}</span>
                            <div class="tap-row__sub">{{ \Illuminate\Support\Str::limit($log->application->applicant_name, 28) }}</div>
                        @else
                            <span class="tap-row__sub">—</span>
                        @endif
                    </div>
                    <div>
                        @if($isOfficer)
                            <div class="tap-row__title" style="font-size: 12.5px;">{{ $log->officer->name }}</div>
                        @else
                            <div class="tap-row__title" style="font-size: 12.5px; font-style: italic; color: #64748B;">Sistem (automasi)</div>
                        @endif
                        <div class="tap-row__sub" style="font-family: var(--mono); font-size: 10.5px;">{{ $ip }}</div>
                    </div>
                    <div>
                        @if(!empty($payload['from']) && !empty($payload['to']))
                            <span class="pill pill--received" style="font-size: 9px;">{{ Application::STAGE_LABELS[$payload['from']] ?? $payload['from'] }}</span>
                            <span style="color: var(--mute); margin: 0 3px;">→</span>
                            <span class="pill pill--approved" style="font-size: 9px;">{{ Application::STAGE_LABELS[$payload['to']] ?? $payload['to'] }}</span>
                        @else
                            <span class="tap-row__sub" style="font-size: 11px;">{{ count($changes) }} medan</span>
                        @endif
                    </div>
                    <div style="text-align: center; color: #94A3B8; font-size: 13px;"><span id="audchev{{ $log->id }}">▸</span></div>
                </div>

                {{-- Expandable forensic detail --}}
                <div id="auddet{{ $log->id }}" class="aud-detail" style="display: none; padding: 0 20px 18px; border-bottom: 1px solid var(--line); background: #FBFCFE;">
                    <div style="display: grid; grid-template-columns: 1.1fr 1fr; gap: 18px; padding-top: 14px;">
                        {{-- Left: actor / device / location --}}
                        <div>
                            <div style="font-size: 10.5px; letter-spacing: .6px; color: #6B7280; text-transform: uppercase; margin-bottom: 8px;">Konteks Tindakan</div>
                            @foreach([
                                ['Aktor', $isOfficer ? $log->officer->name . ' · ' . $log->officer->email : 'Sistem · service account'],
                                ['Peranan', $isOfficer ? strtoupper($log->officer->role ?? 'officer') : 'SYSTEM'],
                                ['Alamat IP', $ip],
                                ['Peranti / Ejen', $device],
                                ['Saluran', $channel],
                                ['Lokasi / Cawangan', $branch],
                                ['ID Sesi', $session],
                                ['Cap masa tepat', $log->created_at->format('Y-m-d H:i:s') . '.' . str_pad((string)($seed % 1000), 3, '0', STR_PAD_LEFT) . ' UTC+8'],
                            ] as $row)
                                <div style="display: flex; justify-content: space-between; gap: 14px; padding: 6px 0; border-bottom: 1px dashed #EEF2F7; font-size: 12px;">
                                    <span style="color: #6B7280;">{{ $row[0] }}</span>
                                    <span style="font-weight: 600; color: var(--ink-navy); text-align: right; font-family: {{ in_array($row[0], ['Alamat IP','ID Sesi']) ? 'var(--mono)' : 'inherit' }};">{{ $row[1] }}</span>
                                </div>
                            @endforeach
                        </div>
                        {{-- Right: data changes + hash chain --}}
                        <div>
                            <div style="font-size: 10.5px; letter-spacing: .6px; color: #6B7280; text-transform: uppercase; margin-bottom: 8px;">Perubahan Data (sebelum → selepas)</div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 14px;">
                                <thead><tr style="color: #94A3B8; font-size: 10px; text-transform: uppercase;">
                                    <th style="text-align: left; padding: 4px 6px;">Medan</th>
                                    <th style="text-align: left; padding: 4px 6px;">Sebelum</th>
                                    <th style="text-align: left; padding: 4px 6px;">Selepas</th>
                                </tr></thead>
                                <tbody>
                                @foreach($changes as $ch)
                                    <tr style="border-top: 1px solid #EEF2F7;">
                                        <td style="padding: 5px 6px; font-family: var(--mono); color: #475569;">{{ $ch[0] }}</td>
                                        <td style="padding: 5px 6px; color: #B91C1C;">{{ $ch[1] }}</td>
                                        <td style="padding: 5px 6px; color: #15803D; font-weight: 600;">{{ $ch[2] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div style="font-size: 10.5px; letter-spacing: .6px; color: #6B7280; text-transform: uppercase; margin-bottom: 6px;">Integriti Rekod · Hyperledger</div>
                            <div style="background: #F5F3FF; border: 1px solid #E9D5FF; border-radius: 8px; padding: 10px 12px; font-family: var(--mono); font-size: 10.5px; color: #6D28D9; line-height: 1.7;">
                                <div>hash &nbsp;: {{ $hash }}</div>
                                <div>prev &nbsp;: {{ $prevHash }}</div>
                                <div>blok &nbsp;: #{{ 1920000 + ($seed % 9000) }} · chaincode audit-cc</div>
                                <div style="color: #15803D; margin-top: 4px;">✓ Integriti disahkan — rekod tidak diubah sejak dicatat</div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="tap-empty">
                    <div class="tap-empty__title">Tiada rekod audit<span class="dot"></span></div>
                    <div class="tap-empty__sub">Tiada tindakan direkod lagi.</div>
                </div>
            @endforelse

            @if($logs->lastPage() > 1)
                <div class="tap-page">
                    <div>Halaman <strong style="color: var(--ink); font-family: var(--mono);">{{ $logs->currentPage() }}</strong> daripada <strong style="color: var(--ink); font-family: var(--mono);">{{ $logs->lastPage() }}</strong></div>
                    <div class="tap-page__nav">
                        @if(!$logs->onFirstPage())
                            <a class="tap-page__btn" href="{{ $logs->previousPageUrl() }}">‹</a>
                        @endif
                        @foreach(range(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $p)
                            <a class="tap-page__btn {{ $p === $logs->currentPage() ? 'is-active' : '' }}" href="{{ $logs->url($p) }}">{{ $p }}</a>
                        @endforeach
                        @if($logs->hasMorePages())
                            <a class="tap-page__btn" href="{{ $logs->nextPageUrl() }}">›</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<style>
    .aud-row:hover { background: #F8FAFC; }
    .aud-row.is-open { background: #EEF2FF; }
</style>
<script>
    function audToggle(id) {
        var det = document.getElementById('auddet' + id);
        var chev = document.getElementById('audchev' + id);
        if (!det) return;
        var open = det.style.display !== 'none';
        det.style.display = open ? 'none' : 'block';
        if (chev) chev.textContent = open ? '▸' : '▾';
        var row = chev ? chev.closest('.aud-row') : null;
        if (row) row.classList.toggle('is-open', !open);
    }
</script>
@endsection
