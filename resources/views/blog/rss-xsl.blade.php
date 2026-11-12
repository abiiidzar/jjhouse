{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n" !!}
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:atom="http://www.w3.org/2005/Atom"
    exclude-result-prefixes="atom">

    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <xsl:template match="/">
        <html lang="en">
            <head>
                <meta charset="UTF-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <title><xsl:value-of select="/rss/channel/title"/> — RSS Feed</title>
                <style>
                    *, *::before, *::after { box-sizing: border-box; }
                    body {
                        margin: 0;
                        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        background-color: #f8fafc;
                        color: #111827;
                        line-height: 1.6;
                    }
                    .hero {
                        background-color: #000000;
                        color: #ffffff;
                        padding: 48px 24px;
                    }
                    .container {
                        max-width: 880px;
                        margin: 0 auto;
                    }
                    .brand-row {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        flex-wrap: wrap;
                        gap: 16px;
                        margin-bottom: 28px;
                    }
                    .logo {
                        display: inline-flex;
                        align-items: center;
                        font-weight: 700;
                        font-size: 20px;
                        letter-spacing: -0.04em;
                        text-decoration: none;
                        color: #ffffff;
                    }
                    .logo-box {
                        background-color: #ffffff;
                        color: #000000;
                        padding: 4px 10px;
                        margin-right: 6px;
                    }
                    .back-link {
                        color: #d1d5db;
                        text-decoration: none;
                        font-size: 12px;
                        text-transform: uppercase;
                        letter-spacing: 0.16em;
                        border: 1px solid rgba(255, 255, 255, 0.25);
                        padding: 10px 16px;
                        transition: background-color 0.2s, color 0.2s;
                    }
                    .back-link:hover {
                        background-color: #ffffff;
                        color: #000000;
                    }
                    .eyebrow {
                        font-size: 11px;
                        text-transform: uppercase;
                        letter-spacing: 0.25em;
                        color: #9ca3af;
                        margin: 0 0 8px 0;
                    }
                    h1 {
                        margin: 0 0 12px 0;
                        font-size: 36px;
                        font-weight: 700;
                        letter-spacing: -0.03em;
                    }
                    .subtitle {
                        margin: 0;
                        color: #d1d5db;
                        font-size: 15px;
                        max-width: 640px;
                    }
                    .subscribe-box {
                        margin-top: 28px;
                        background-color: rgba(255, 255, 255, 0.08);
                        border: 1px solid rgba(255, 255, 255, 0.16);
                        padding: 16px;
                    }
                    .subscribe-label {
                        font-size: 12px;
                        color: #d1d5db;
                        margin: 0 0 10px 0;
                    }
                    .url-row {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 10px;
                    }
                    .url-input {
                        flex: 1;
                        min-width: 220px;
                        background-color: #111827;
                        color: #f9fafb;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        padding: 10px 12px;
                        font-size: 13px;
                        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                    }
                    .copy-btn {
                        background-color: #ffffff;
                        color: #000000;
                        border: none;
                        padding: 10px 18px;
                        font-size: 12px;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.14em;
                        cursor: pointer;
                    }
                    .copy-btn:hover {
                        background-color: #e5e7eb;
                    }
                    main {
                        padding: 36px 24px 64px;
                    }
                    .meta-bar {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 12px;
                        padding-bottom: 16px;
                        margin-bottom: 24px;
                        border-bottom: 1px solid #e5e7eb;
                        font-size: 13px;
                        color: #6b7280;
                    }
                    .card {
                        background-color: #ffffff;
                        border: 1px solid #e5e7eb;
                        padding: 24px;
                        margin-bottom: 16px;
                    }
                    .card-meta {
                        display: flex;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 10px;
                        font-size: 12px;
                        color: #6b7280;
                        margin-bottom: 10px;
                    }
                    .badge {
                        background-color: #111827;
                        color: #ffffff;
                        padding: 2px 8px;
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.12em;
                    }
                    .card-title {
                        margin: 0 0 10px 0;
                        font-size: 20px;
                        font-weight: 600;
                        line-height: 1.35;
                    }
                    .card-title a {
                        color: #111827;
                        text-decoration: none;
                    }
                    .card-title a:hover {
                        text-decoration: underline;
                    }
                    .card-excerpt {
                        margin: 0 0 16px 0;
                        color: #4b5563;
                        font-size: 14px;
                    }
                    .card-footer {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 12px;
                        font-size: 12px;
                        color: #6b7280;
                    }
                    .read-more {
                        color: #111827;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.14em;
                        text-decoration: none;
                    }
                    .read-more:hover {
                        text-decoration: underline;
                    }
                    .empty {
                        background-color: #ffffff;
                        border: 1px solid #e5e7eb;
                        padding: 40px 24px;
                        text-align: center;
                        color: #6b7280;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <header class="hero">
                    <div class="container">
                        <div class="brand-row">
                            <a href="{/rss/channel/link}" class="logo">
                                <span class="logo-box">JJ</span>
                                <span>HOUSE</span>
                            </a>
                            <a href="{/rss/channel/link}" class="back-link">← Back to Journal</a>
                        </div>

                        <p class="eyebrow">RSS Web Feed</p>
                        <h1><xsl:value-of select="/rss/channel/title"/></h1>
                        <p class="subtitle"><xsl:value-of select="/rss/channel/description"/></p>

                        {{-- <div class="subscribe-box">
                            <p class="subscribe-label">
                                Ini adalah web feed RSS. Salin URL di bawah ini ke aplikasi RSS reader Anda (Feedly, Inoreader, NetNewsWire, dll.) untuk berlangganan pembaruan artikel terbaru.
                            </p>
                            <div class="url-row">
                                <input id="feed-url" class="url-input" type="text" readonly="readonly" value="{/rss/channel/atom:link/@href}"/>
                                <button type="button" class="copy-btn" onclick="copyFeedUrl(this)">Copy Feed URL</button>
                            </div>
                        </div> --}}
                    </div>
                </header>

                <main>
                    <div class="container">
                        <div class="meta-bar">
                            <span><strong><xsl:value-of select="count(/rss/channel/item)"/></strong> artikel terbaru</span>
                            <span>Terakhir diperbarui: <xsl:value-of select="/rss/channel/lastBuildDate"/></span>
                        </div>

                        <xsl:if test="count(/rss/channel/item) = 0">
                            <div class="empty">
                                Belum ada artikel yang dipublikasikan saat ini.
                            </div>
                        </xsl:if>

                        <xsl:for-each select="/rss/channel/item">
                            <article class="card">
                                <div class="card-meta">
                                    <xsl:if test="category">
                                        <span class="badge"><xsl:value-of select="category"/></span>
                                    </xsl:if>
                                    <span><xsl:value-of select="pubDate"/></span>
                                </div>
                                <h2 class="card-title">
                                    <a href="{link}"><xsl:value-of select="title"/></a>
                                </h2>
                                <p class="card-excerpt">
                                    <xsl:value-of select="description"/>
                                </p>
                                <div class="card-footer">
                                    <xsl:if test="author">
                                        <span>Oleh <xsl:value-of select="author"/></span>
                                    </xsl:if>
                                    <a href="{link}" class="read-more">Baca Artikel →</a>
                                </div>
                            </article>
                        </xsl:for-each>
                    </div>
                </main>

                <script>
                    function copyFeedUrl(btn) {
                        var input = document.getElementById('feed-url');
                        if (!input) return;
                        var text = input.value;
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(text).then(function () {
                                btn.textContent = 'Copied!';
                                setTimeout(function () { btn.textContent = 'Copy Feed URL'; }, 2000);
                            });
                            return;
                        }
                        input.select();
                        document.execCommand('copy');
                        btn.textContent = 'Copied!';
                        setTimeout(function () { btn.textContent = 'Copy Feed URL'; }, 2000);
                    }
                </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>

