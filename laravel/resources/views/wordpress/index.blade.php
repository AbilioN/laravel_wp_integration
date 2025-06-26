<!DOCTYPE html>
<html>
<head>
    <title>Posts do WordPress</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .post { border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 5px; }
        .post h2 { color: #333; }
        .post .date { color: #666; font-size: 0.9em; }
        .no-posts { color: #999; font-style: italic; }
    </style>
</head>
<body>
    <h1>Posts do WordPress via Laravel</h1>
    
    @if($posts->count() > 0)
        @foreach($posts as $post)
            <div class="post">
                <h2>{{ $post->post_title }}</h2>
                <div class="date">{{ \Carbon\Carbon::parse($post->post_date)->format(d/m/Y
