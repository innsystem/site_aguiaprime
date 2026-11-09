<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');

        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /login',
            'Disallow: /palpites',
            '',
            'Sitemap: '.$sitemap,
            '',
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $home = route('home');
        $lastmod = now()->toAtomString();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{$home}</loc>
    <lastmod>{$lastmod}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>

XML;

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
