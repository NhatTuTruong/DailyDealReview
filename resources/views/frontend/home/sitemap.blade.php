@php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp

<urlset
    xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9
            https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc>{{ route('home_page') }}</loc>
        <lastmod>{{ $last_mod->format('c') }}</lastmod>
        <priority>1.00</priority>
    </url>
    @foreach($data as $site_map)
        <url>
            <loc>{{ $site_map['loc'] }}</loc>
            <lastmod>{{ $site_map['lastmod']->format('c') }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.00</priority>
        </url>
    @endforeach
</urlset>
