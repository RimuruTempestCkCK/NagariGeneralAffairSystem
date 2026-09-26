@extends('layout.app')

@section('title', 'Notifikasi')
@section('active_menu', '')
@section('breadcrumbs', 'Beranda | Notifikasi')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Beranda</span>
        <h1 class="hero-title">Notifikasi</h1>
        <p class="hero-sub">Semua Notifikasi. Anda memiliki {{ Auth::user()->unreadNotifications->count() }} notifikasi belum dibaca.</p>
    </div>
    <div class="hero-actions">
        @if(Auth::user()->unreadNotifications->count() > 0)
            <form action="{{ route(Auth::user()->role . '.notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn--primary">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Notifikasi</h2>
        </div>
    </div>
    <div class="table-scroll">
        <table class="table">
            <tbody>
                @forelse($notifications as $notif)
                <tr style="{{ is_null($notif->read_at) ? 'background-color: rgba(33, 150, 243, 0.05);' : '' }}">
                    <td style="width: 50px; text-align: center;">
                        @if(is_null($notif->read_at))
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: var(--accent);"></span>
                        @else
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: var(--border-soft);"></span>
                        @endif
                    </td>
                    <td>
                        <strong style="display: block; margin-bottom: 5px;">{{ $notif->data['title'] ?? 'Notifikasi' }}</strong>
                        <p style="margin: 0; color: var(--t-muted); font-size: 14px;">{{ $notif->data['message'] ?? '' }}</p>
                        
                        <div style="margin-top: 8px; display: flex; gap: 15px; font-size: 13px;">
                            @if(!empty($notif->data['url']))
                                <a href="{{ route(Auth::user()->role . '.notifications.markAndRedirect', $notif->id) }}" style="color: var(--accent); font-weight: 500; text-decoration: none;">Lihat Detail</a>
                            @endif
                            @if(is_null($notif->read_at))
                                <form action="{{ route(Auth::user()->role . '.notifications.markAsRead', $notif->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--t-muted); cursor: pointer; padding: 0; font-family: inherit;">Tandai Dibaca</button>
                                </form>
                            @endif
                        </div>
                    </td>
                    <td style="text-align: right; color: var(--t-muted); font-size: 13px; width: 150px;">
                        {{ $notif->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px; color: var(--t-muted);">Tidak ada notifikasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $notifications->links() }}
    </div>
</section>
@endsection
