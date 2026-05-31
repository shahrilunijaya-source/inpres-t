@php
    // Case rail — persistent "KES SEMASA" banner on every Sistem Wajib screen.
    // Ties the screen to ONE anchor applicant so a screenshot reads as part of
    // one continuous application, not a standalone module mockup.
    // Expects (in scope from the system layout): $case, $active, $canAccess.
    $stepMap = [
        'lapor'      => 'Lapor Kehilangan',
        'hospital'   => 'Pra-Daftar Hospital',
        'borang'     => 'Borang Daftar',
        'biometric'  => 'Biometrik',
        'abis'       => 'ABIS 1:N',
        'kaveat'     => 'Kaveat 21 Hari',
        'familytree' => 'Salasilah',
        'upacara'    => 'Upacara & Daftar',
        'sijil'      => 'Sijil',
        'clms'       => 'CLMS Kad',
        'kad'        => 'Kad Dikeluarkan',
        'blockchain' => 'Blockchain',
        'mydigital'  => 'MyDigital ID',
        'agensi'     => 'Integrasi Agensi',
    ];
    $routeMap = [
        'lapor'      => 'system.lapor',
        'hospital'   => 'system.hospital',
        'borang'     => 'system.borang',
        'biometric'  => 'system.biometric',
        'abis'       => 'system.abis',
        'kaveat'     => 'system.kaveat',
        'familytree' => 'system.familytree',
        'upacara'    => 'system.upacara',
        'sijil'      => 'system.sijil',
        'clms'       => 'system.clms',
        'kad'        => 'system.kad',
        'blockchain' => 'system.blockchain',
        'mydigital'  => 'system.mydigital',
        'agensi'     => 'system.agensi',
    ];
    $accessMatrix = $canAccess ?? [];
@endphp

<div style="background: linear-gradient(100deg, var(--ink-navy) 0%, #14346B 60%, #1E40AF 100%); color: #fff; padding: 14px 32px; border-bottom: 3px solid #6366F1;">
    <div style="display: flex; align-items: center; gap: 22px; flex-wrap: wrap;">

        {{-- Anchor identity — a marriage shows BOTH parties --}}
        @php $isCouple = ($case['doc_type'] ?? null) === 'marriage' && !empty($case['groom']) && !empty($case['bride']); @endphp
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700;">
                @if($isCouple)@include('system._icon', ['name' => 'heart', 'color' => '#fff', 'size' => 18])@else{{ strtoupper(mb_substr($case['name'], 0, 1)) }}@endif
            </div>
            <div>
                <div style="font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #A5B4FC; font-weight: 700;">● Kes Semasa · {{ $case['module'] }}</div>
                @if($isCouple)
                    <div style="font-size: 15px; font-weight: 700; line-height: 1.25;">{{ $case['groom']['name'] }} <span style="color:#A5B4FC;">×</span> {{ $case['bride']['name'] }}</div>
                    <div style="font-size: 11px; color: #C7D2FE; font-family: ui-monospace, monospace;">{{ $case['groom']['ic'] }} · {{ $case['bride']['ic'] }} · {{ $case['doc_label'] }}</div>
                @else
                    <div style="font-size: 16px; font-weight: 700; line-height: 1.2;">{{ $case['name'] }}</div>
                    <div style="font-size: 11.5px; color: #C7D2FE; font-family: ui-monospace, monospace;">{{ $case['ic'] }} · {{ $case['doc_label'] }}</div>
                @endif
            </div>
        </div>

        {{-- Reference → links back to the functional tapisan-detail page --}}
        <a href="{{ route('system.tapisan.show', $case['reference']) }}"
           style="text-decoration: none; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.3); border-radius: 8px; padding: 8px 14px; color: #fff;">
            <div style="font-size: 9.5px; letter-spacing: 1px; text-transform: uppercase; color: #A5B4FC;">Rujukan Permohonan</div>
            <div style="font-family: ui-monospace, monospace; font-size: 13px; font-weight: 700;">{{ $case['reference'] }} →</div>
        </a>

        {{-- Step breadcrumb — this case's journey through Sistem Wajib --}}
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; flex: 1;">
            @foreach($case['steps'] as $i => $step)
                @php
                    $isCurrent = ($active ?? '') === $step;
                    $allowed = $accessMatrix[$step] ?? true;
                    $label = ($i + 1) . '. ' . ($stepMap[$step] ?? $step);
                @endphp
                @if($isCurrent)
                    <span style="background: #fff; color: #1E3A8A; padding: 6px 12px; border-radius: 999px; font-size: 11.5px; font-weight: 700;">{{ $label }}</span>
                @elseif($allowed)
                    <a href="{{ route($routeMap[$step], ['ref' => $case['reference']]) }}"
                       style="text-decoration: none; background: rgba(255,255,255,.08); color: #E0E7FF; padding: 6px 12px; border-radius: 999px; font-size: 11.5px; font-weight: 600; border: 1px solid rgba(255,255,255,.18);">{{ $label }}</a>
                @else
                    <span title="Terhad untuk peranan anda" style="background: rgba(255,255,255,.04); color: #64748B; padding: 6px 12px; border-radius: 999px; font-size: 11.5px; font-weight: 600; border: 1px dashed rgba(255,255,255,.15);">{{ $label }} · terhad</span>
                @endif
                @if($i < count($case['steps']) - 1)
                    <span style="color: #6366F1; font-size: 12px;">→</span>
                @endif
            @endforeach
        </div>
    </div>
</div>

@php
    $lastStep = $case['steps'][count($case['steps']) - 1] ?? null;
    $isComplete = $lastStep !== null && ($active ?? '') === $lastStep;
    $issuedNo = $case['blockchain']['events'][0]['subj'] ?? ($case['card_no'] ?? $case['reference']);
@endphp
@if($isComplete)
<div style="background: #ECFDF5; border-bottom: 1px solid #A7F3D0; padding: 12px 32px; display: flex; align-items: center; gap: 14px;">
    <span style="background: #16A34A; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">✓</span>
    <div>
        <div style="font-weight: 700; color: #15803D; font-size: 14px;">Proses Selesai · Dokumen Dikeluarkan</div>
        <div style="font-size: 12px; color: #475569;">{{ $case['doc_label'] }} <strong>{{ $issuedNo }}</strong> untuk {{ $case['name'] }} telah dikeluarkan dan dicatat kekal ke ledger. Rujukan {{ $case['reference'] }}.</div>
    </div>
    <span style="margin-left: auto; background: #DCFCE7; color: #15803D; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700;">STATUS · DIKELUARKAN</span>
</div>
@endif
