@props(['url', 'intervalo' => 2000, 'callback', 'activo' => true])

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ejecutarSync = async () => {
            if (window.detenerAutoSync) return;

            try {
                const res = await fetch("{{ $url }}", {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (typeof window["{{ $callback }}"] === 'function') {
                        window["{{ $callback }}"](data);
                    }
                }
            } catch (e) {
                console.warn('Error de sincronización:', e);
            }
        };

        setInterval(ejecutarSync, {{ $intervalo }});
    });
</script>