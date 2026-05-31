@extends('layouts.portal')

@section('title', 'Mohon Dokumen — InPreS')

@section('main')
    <div class="max-w-2xl mx-auto">
        <div class="badge badge-indigo mono mb-4">Phase 3</div>
        <h1 class="text-3xl font-bold mb-4" style="color: var(--ai-text);">Borang Permohonan</h1>
        <p class="mb-8" style="color: var(--ai-text-dim);">
            Placeholder. Phase 3 akan membina borang penuh dengan auto-isi AI dari pengimbasan IC.
        </p>

        <div class="glass-card-hi p-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">Jenis Dokumen</label>
                    <select class="w-full px-4 py-3 rounded-lg" style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">
                        <option>Sijil Kelahiran</option>
                        <option>Sijil Perkahwinan</option>
                        <option>MyKAD</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">No. Kad Pengenalan</label>
                    <input type="text" placeholder="000000-00-0000"
                           class="w-full px-4 py-3 rounded-lg mono"
                           style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--ai-text-dim);">Nama Penuh</label>
                    <input type="text" placeholder="Nama seperti dalam IC"
                           class="w-full px-4 py-3 rounded-lg"
                           style="background: var(--ai-bg-2); border: 1px solid var(--ai-border); color: var(--ai-text);">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-gradient btn-accent">
                        Imbas IC dengan AI
                    </button>
                    <button type="submit" class="btn-gradient">
                        Hantar Permohonan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
