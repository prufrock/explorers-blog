@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="Contact {{ $page->siteName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="Get in touch with {{ $page->siteName }}" />
@endpush

@section('body')
<h1>Contact</h1>

<p class="mb-8">
    Yum! Thanks for dropping me a line.
    <br/><br/>
    Looking for something to read? Checkout the <a href="{{ $page->blog }}">blog</a>.
</p>
@stop
