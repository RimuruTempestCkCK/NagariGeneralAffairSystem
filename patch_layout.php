<?php
$c = file_get_contents('resources/views/layouts/adminator.blade.php');

$search = "setTimeout(() => {";
$inject = 'setTimeout(() => {
                const topbar = document.querySelector(\'[data-shell-topbar]\');
                if(topbar && !document.getElementById("notif-bell-container")) {
                    const unreadCount = {{ Auth::check() ? Auth::user()->unreadNotifications->count() : 0 }};
                    const notifHtml = `<div id="notif-bell-container" style="position: absolute; right: 80px; top: 15px; z-index: 1000;">
                        <a href="{{ route(\'notifications.index\') }}" style="position: relative; display: inline-block; text-decoration: none; color: var(--t-base);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            ${unreadCount > 0 ? `<span style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">${unreadCount}</span>` : \'\'}
                        </a>
                    </div>`;
                    topbar.insertAdjacentHTML("beforeend", notifHtml);
                }';

$c = str_replace($search, $inject, $c);
file_put_contents('resources/views/layouts/adminator.blade.php', $c);
echo "Layout patched";
