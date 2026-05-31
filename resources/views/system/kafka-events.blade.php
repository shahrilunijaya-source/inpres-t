@extends('layouts.system', ['active' => 'kafka', 'title' => 'Kafka Event Bus'])

@section('content')
@php
$topics = [
    ['name' => 'event.kelahiran.registered', 'partitions' => 6, 'consumers' => 7, 'lag' => 0, 'rate' => 87],
    ['name' => 'event.mykad.issued', 'partitions' => 8, 'consumers' => 9, 'lag' => 0, 'rate' => 124],
    ['name' => 'event.mykad.revoked', 'partitions' => 4, 'consumers' => 6, 'lag' => 0, 'rate' => 12],
    ['name' => 'event.kahwin.registered', 'partitions' => 4, 'consumers' => 6, 'lag' => 2, 'rate' => 41],
    ['name' => 'event.kahwin.caveat_lodged', 'partitions' => 4, 'consumers' => 3, 'lag' => 0, 'rate' => 21],
    ['name' => 'event.kematian.registered', 'partitions' => 4, 'consumers' => 8, 'lag' => 0, 'rate' => 56],
    ['name' => 'event.warganegara.naturalised', 'partitions' => 2, 'consumers' => 5, 'lag' => 0, 'rate' => 3],
    ['name' => 'event.audit.officer_action', 'partitions' => 12, 'consumers' => 2, 'lag' => 12, 'rate' => 412],
];
$stream = [
    ['ts' => '20:41:32.187', 'topic' => 'event.kahwin.registered', 'key' => 'PK-2026-009876', 'payload' => '{"ref":"PK-2026-009876","groom":"Ahmad","bride":"Faridah",...}'],
    ['ts' => '20:41:31.943', 'topic' => 'event.mykad.issued', 'key' => 'MK-2026-987654', 'payload' => '{"serial":"MK-2026-987654","type":"replacement",...}'],
    ['ts' => '20:41:30.011', 'topic' => 'event.mykad.revoked', 'key' => 'MK-2018-554321', 'payload' => '{"serial":"MK-2018-554321","reason":"rosak_cip",...}'],
    ['ts' => '20:41:28.612', 'topic' => 'event.audit.officer_action', 'key' => 'OFC-2031', 'payload' => '{"officer":"OFC-2031","action":"approve_mykad",...}'],
    ['ts' => '20:41:27.421', 'topic' => 'event.kelahiran.registered', 'key' => 'KLH-2026-001234', 'payload' => '{"ref":"KLH-2026-001234","baby":"Adam","mother":"Siti",...}'],
    ['ts' => '20:41:25.890', 'topic' => 'event.kematian.registered', 'key' => 'KMT-2026-004412', 'payload' => '{"ref":"KMT-2026-004412","deceased":"Tan Ah Kow",...}'],
];
$consumers = [
    ['group' => 'mykid-auto-provisioner', 'topic' => 'event.kelahiran.registered', 'lag' => 0],
    ['group' => 'mydigital-id-sync', 'topic' => 'event.mykad.issued', 'lag' => 0],
    ['group' => 'kwsp-nominee-sync', 'topic' => 'event.kahwin.registered', 'lag' => 2],
    ['group' => 'spr-voter-roll', 'topic' => 'event.mykad.issued', 'lag' => 0],
    ['group' => 'pdrm-blacklist', 'topic' => 'event.kematian.registered', 'lag' => 0],
    ['group' => 'family-tree-updater', 'topic' => 'event.kahwin.registered', 'lag' => 0],
    ['group' => 'hl-fabric-publisher', 'topic' => 'event.audit.officer_action', 'lag' => 12],
    ['group' => 'lhdn-tax-status', 'topic' => 'event.kahwin.registered', 'lag' => 0],
];
@endphp
<div style="padding: 24px 32px;">
    <header style="margin-bottom: 24px;">
        <div style="font-size: 11px; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase;">LAMPIRAN A · Reka Bentuk Event-Driven</div>
        <h1 style="font-size: 24px; margin: 4px 0 4px;">Apache Kafka · Event Bus Antara Modul</h1>
        <p style="color: #6B7280; margin: 0; font-size: 13px;">3 broker · 8 topic utama · Avro schema registry · 4.8k msg/saat</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
        @foreach([['Broker Aktif','3 / 3','RF=3 · ISR=3','#15803D'],['Throughput','4,821','msg/saat','#1E40AF'],['Consumer Lag','14','msg tertunggak','#B45309'],['Schema Registry','Confluent Avro','backward-compatible','#6366F1']] as $kpi)
            <div style="background: #fff; border: 1px solid #E5E7EB; border-left: 4px solid {{ $kpi[3] }}; border-radius: 10px; padding: 16px 18px;">
                <div style="font-size: 10.5px; letter-spacing: 1px; color: #6B7280; text-transform: uppercase;">{{ $kpi[0] }}</div>
                <div style="font-size: 22px; font-weight: 700; color: {{ $kpi[3] }}; margin: 4px 0 2px;">{{ $kpi[1] }}</div>
                <div style="font-size: 11.5px; color: #6B7280;">{{ $kpi[2] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="padding: 14px 20px; border-bottom: 1px solid #E5E7EB;"><h3 style="margin: 0; font-size: 14px;">Topic Aktif</h3></div>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead style="background: #F9FAFB; color: #6B7280; font-size: 10.5px; text-transform: uppercase;">
                    <tr><th style="padding: 8px 14px; text-align: left;">Topic</th><th style="padding: 8px 14px;">Part</th><th style="padding: 8px 14px;">Sub</th><th style="padding: 8px 14px;">Lag</th><th style="padding: 8px 14px;">Rate</th></tr>
                </thead>
                <tbody>
                @foreach($topics as $t)
                    <tr style="border-top: 1px solid #F1F5F9;">
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px; font-weight: 600;">{{ $t['name'] }}</td>
                        <td style="padding: 8px 14px; text-align: center;">{{ $t['partitions'] }}</td>
                        <td style="padding: 8px 14px; text-align: center;">{{ $t['consumers'] }}</td>
                        <td style="padding: 8px 14px; text-align: center;"><span style="background: {{ $t['lag'] === 0 ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $t['lag'] === 0 ? '#15803D' : '#B45309' }}; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $t['lag'] }}</span></td>
                        <td style="padding: 8px 14px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $t['rate'] }}/s</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #E5E7EB;">
                <h3 style="margin: 0; font-size: 14px;">Stream Langsung</h3>
                <span style="background: #DCFCE7; color: #15803D; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;">● Tailing</span>
            </div>
            <div style="background: var(--ink-navy); color: #A5F3FC; padding: 14px; font-family: ui-monospace, monospace; font-size: 10.5px; max-height: 340px; overflow-y: auto; line-height: 1.6;">
                @foreach($stream as $e)
                    <div style="margin-bottom: 8px; border-left: 2px solid #3B82F6; padding-left: 8px;">
                        <span style="color: #94A3B8;">{{ $e['ts'] }}</span>
                        <span style="color: #FDE047;"> {{ $e['topic'] }}</span>
                        <span style="color: #86EFAC;"> key={{ $e['key'] }}</span>
                        <div style="color: #CBD5E1; font-size: 10px; margin-top: 2px;">{{ $e['payload'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section style="background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; margin-top: 18px; overflow: hidden;">
        <div style="padding: 14px 20px; border-bottom: 1px solid #E5E7EB;"><h3 style="margin: 0; font-size: 14px;">Consumer Groups Berlangganan</h3></div>
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
            <thead style="background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase;">
                <tr><th style="padding: 10px 16px; text-align: left;">Consumer Group</th><th style="padding: 10px 16px; text-align: left;">Topic</th><th style="padding: 10px 16px; text-align: left;">Lag</th><th style="padding: 10px 16px; text-align: left;">Status</th></tr>
            </thead>
            <tbody>
            @foreach($consumers as $c)
                <tr style="border-top: 1px solid #F1F5F9;">
                    <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-weight: 600;">{{ $c['group'] }}</td>
                    <td style="padding: 10px 16px; font-family: ui-monospace, monospace; font-size: 11px;">{{ $c['topic'] }}</td>
                    <td style="padding: 10px 16px;"><span style="background: {{ $c['lag'] === 0 ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $c['lag'] === 0 ? '#15803D' : '#B45309' }}; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">{{ $c['lag'] }}</span></td>
                    <td style="padding: 10px 16px;"><span style="background: #DCFCE7; color: #15803D; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600;">RUNNING</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <div style="margin-top: 18px; padding: 14px 20px; background: #EFF6FF; border: 1px dashed #1E40AF; border-radius: 10px;">
        <strong style="color: #1E40AF;">Justifikasi · Event-Driven:</strong>
        <span style="color: #475569; font-size: 13px;"> Kelahiran cetuskan tindakan di modul lain (MyKid auto-sedia, Modul Kutipan, Pendidikan). Kafka memastikan semua modul + agensi pelanggan dikemas kini serentak tanpa coupling ketat. Schema Registry elak breaking change.</span>
    </div>
</div>
@endsection
