<x-internal-app-layout
    title="Tambah Link | AccessHub"
    eyebrow="Tambah Link"
    heading="Simpan Link Baru"
    subheading="Masukkan link penting Anda dengan form sederhana, lalu temukan kembali dari dashboard kapan saja."
>
    @include('app.links.partials.form', [
        'action' => route('app.links.store'),
        'method' => 'POST',
        'link' => null,
        'tagString' => '',
        'selectedVisibility' => 'private',
    ])
</x-internal-app-layout>
