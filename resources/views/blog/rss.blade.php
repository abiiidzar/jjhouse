<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ config('app.name') }} Journal</title>
        <link>{{ url('/blog') }}</link>
        <description>Latest articles from JJHOUSE - Streetwear culture, fashion tips, and brand updates.</description>
        <language>en-us</language>
        <lastBuildDate>{{ now()->toRfc822String() }}</lastBuildDate>
        <atom:link href="{{ url('/blog/feed') }}" rel="self" type="application/rss+xml"/>

        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ url('/blog/' . $post->slug) }}</link>
            <description><![CDATA[{!! $post->excerpt !!}]]></description>
            <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded>
            <pubDate>{{ $post->published_at->toRfc822String() }}</pubDate>
            <guid isPermaLink="true">{{ url('/blog/' . $post->slug) }}</guid>
            @if($post->category)
            <category>{{ $post->category->name }}</category>
            @endif
            <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
        </item>
        @endforeach
    </channel>
</rss>
