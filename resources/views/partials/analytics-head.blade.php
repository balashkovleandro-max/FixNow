@include('partials.google-analytics')

@php
    $metaPixelId = config('services.analytics.meta_pixel_id');
    $clarityProjectId = config('services.analytics.clarity_project_id');
@endphp

@if($metaPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $metaPixelId }}');
        fbq('track', 'PageView');
    </script>
@endif

@if($clarityProjectId)
    <script>
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){c[a].q=c[a].q||[];c[a].q.push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ $clarityProjectId }}");
    </script>
@endif
