@once
    @php
        $googleAnalyticsId = config('services.google_analytics_id');
        $bonEvent = session('bon_event');
    @endphp

    @if($googleAnalyticsId)
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            window.gtag = window.gtag || gtag;

            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsId }}');

            window.trackBonEvent = function(eventName, params = {}) {
                if (typeof window.gtag === 'function') {
                    window.gtag('event', eventName, params);
                }
            };
        </script>
    @else
        <script>
            window.trackBonEvent = function(eventName, params = {}) {
                return false;
            };
        </script>
    @endif

    @if(is_array($bonEvent ?? null) && ! empty($bonEvent['name']))
        <script>
            window.addEventListener('load', function () {
                window.trackBonEvent(@json($bonEvent['name']), @json($bonEvent['params'] ?? []));
            });
        </script>
    @endif
@endonce
