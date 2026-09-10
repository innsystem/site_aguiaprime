@extends('admin.base_admin')

@section('title', 'Configurações')
@section('heading', 'Configurações')
@section('subtitle', 'Title, SEO e links principais do site.')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="admin-panel">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $settings->title) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="meta_keywords">Meta keywords</label>
            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $settings->meta_keywords) }}" placeholder="palavra1, palavra2, palavra3">
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $settings->description) }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="whatsapp_url">WhatsApp / Link Group</label>
            <input type="url" name="whatsapp_url" id="whatsapp_url" class="form-control" value="{{ old('whatsapp_url', $settings->whatsapp_url) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="platform_url">Link Plataforma</label>
            <input type="url" name="platform_url" id="platform_url" class="form-control" value="{{ old('platform_url', $settings->platform_url) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="video_url">Link Vídeo</label>
            <input type="url" name="video_url" id="video_url" class="form-control" value="{{ old('video_url', $settings->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
            <small class="text-muted">Aceita watch, youtu.be, shorts ou embed. Será convertido automaticamente.</small>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-admin">Salvar</button>
    </div>
</form>
@endsection
